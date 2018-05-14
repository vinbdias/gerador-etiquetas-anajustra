<?php 

    require_once(__FPDF_PATH__ . 'fpdf.php');
    require_once(__ROOT__ . DS . 'services' . DS . 'DataHoraFactory.class.php');
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    use \ForceUTF8\Encoding;

    /**
     * Classe que implementa a execução de páginas com o intuito de gerar arquivos para impressão de etiquetas de endereço
     */
    class GeradorEtiquetasMain {

        private $fpdf = NULL;
        private $mesq = 7;
        private $mdir = 8;
        private $msup = 22;
        private $leti = 107;
        private $aeti = 25.4;
        private $ehet = 100;

        private $planilhaExcel;

        private $logDepuracao = NULL;
        private $logString = '';

        private $associadoDAO = NULL;
        private $associadoObj = NULL;  

        private $associados = array();      

        private $saidaEm = 'pdf';

        /**
         * Método construtor
         * @param string $saidaEm
         */
        public function __construct($saidaEm = '') {

            $this->saidaEm = $saidaEm;

            $this->logDepuracao = ArquivoLogFactory::getArquivoLog(); 
            $this->associadoDAO = new AssociadoDAO();
        }

        /**
         * Método que implementa a execução da página gera-etiquetas-nome-cpf.php
         */
        public function geraEtiquetasNomeCpf($listaIdsAssociados) {

            $this->associados = $this->associadoDAO->obterAssociadosAPartirDeListaDeIDs($listaIdsAssociados);

            if($this->saidaEm == 'pdf')
                $this->__iniciaFPDF();            
            elseif($this->saidaEm == 'xlsx') 
                $this->__iniciaXlsx(); 

            $this->__main();           
        }

        /**
         * Método que implementa aspectos comuns a todas as execuções de páginas que geram arquivos para impressão de etiquetas
         */
        private function __main() {

            //Iniciar o arquivo de log
            $this->logDepuracao = ArquivoLogFactory::getArquivoLog();         
            $this->logString = 'CONSULTA SQL EXECUTADA: ' . $this->associadoDAO->getStringConsultaSql() . PHP_EOL . PHP_EOL;            

            //Percorrer pelos associados armazenados
            foreach($this->associados as $key => $associado) {

                //Iniciar classe modelo
                $this->associadoObj = new Associado($associado);

                //Iniciar log da linha do associado em questão / iteração do loop
                $this->logString .= 'ASSOCIADO LINHA ' . (string)($key + 1) . PHP_EOL .
                                'Nome: ' . $this->associadoObj->nome . PHP_EOL .
                                'Matricula: ' . $this->associadoObj->matricula . PHP_EOL;        

                //Se o endereço for vazio, pular linha e não gerar etiqueta para o associado em questão
                if(empty($this->associadoObj->endereco)) {

                    $this->logString .= 'ETIQUETA NAO GERADA! CAMPO ENDERECO VAZIO!' . PHP_EOL . PHP_EOL;
                    continue;
                }         

                $cep = MascaraHelper::formataMascara($this->associadoObj->cep, '#####-###');        
                $validadorCEP = new ValidadorCEP();

                //Se o CEP for vazio, pular linha e não gerar etiqueta para o associado em questão                
                if(empty($cep)) {
                    
                    $this->logString .= 'ETIQUETA NAO GERADA! CAMPO CEP VAZIO!' . PHP_EOL . PHP_EOL;            
                    continue;
                }

                $validadorCEP->validar($cep);
                
                //Se o CEP não for válido, pular linha e não gerar etiqueta para o associado em questão
                if(!empty($validadorCEP->cep)) {
                    
                    $this->logString .= 'CEP validado nos Correios (https://viacep.com.br/)' . PHP_EOL;
                }
                else {
                    
                    $this->logString .= 'ETIQUETA NAO GERADA! CEP ' . $cep . ' NAO FOI VALIDADO NOS CORREIOS (https://viacep.com.br/ws/' . $cep . '/json/)' . PHP_EOL . PHP_EOL;
                    continue;
                }           

                $bairro_cidade_estado = NULL;

                //Caso o campo bairro esteja preenchido, assumir que o associado passou por recadastramento de endereço
                if(isset($this->associadoObj->bairro) && $this->associadoObj->bairro != '') {

                    $this->logString .= 'Associado com cadastro na tabela funcional.' . PHP_EOL;

                    $this->logString .= 'INICIANDO análise/comparação do endereço cadastrado com o obtido a partir do CEP nos Correios' . PHP_EOL;
                    $analise_comparacao = $this->associadoObj->compararEnderecoCadastradoComValidadoNosCorreios($validadorCEP);
                    $this->logString .= $analise_comparacao;
                    $this->logString .= 'Análise/comparação FINALIZADA' . PHP_EOL;

                    $ende = ((isset($validadorCEP->endereco) && $validadorCEP->endereco != '') ? $validadorCEP->endereco : $this->associadoObj->endereco) . ' ' .
                        $this->associadoObj->numero . ' ' .
                        $validadorCEP->complemento;

                    $bairro_cidade_estado = ((isset($validadorCEP->bairro) && $validadorCEP->bairro != '') ? $validadorCEP->bairro : $this->associadoObj->bairro) . ', ' .
                        ((isset($validadorCEP->cidade) && $validadorCEP->cidade != '') ? $validadorCEP->cidade : $this->associadoObj->cidade) .
                        ' - ' .
                        ((isset($validadorCEP->estado) && $validadorCEP->estado != '') ? $validadorCEP->estado : $this->associadoObj->estado);                        
                }
                else {

                    $this->logString .= 'ASSOCIADO AINDA COM CADASTRO DE ENDEREÇO ANTIGO!' . PHP_EOL;
                    $ende = $this->associadoObj->endereco . ' CEP: ' . $cep;
                }

                $this->logString .= 'Endereco: ' . $ende . PHP_EOL;

                $this->logString .= (empty($this->associadoObj->numero)) ?
                    'ENDERECO INCOMPLETO! CAMPO NUMERO VAZIO!' . PHP_EOL
                    : 'NUMERO: ' . $this->associadoObj->numero . PHP_EOL;        

                $this->logString .= (empty($this->associadoObj->complemento)) ?
                    'ENDERECO INCOMPLETO! CAMPO COMPLEMENTO VAZIO!' . PHP_EOL
                    : 'Complemento: ' . PHP_EOL;  

                $this->logString .= (empty($this->associadoObj->bairro)) ? 
                    'ENDERECO INCOMPLETO! CAMPO BAIRRO VAZIO!' . PHP_EOL :
                     'Bairro: ' . $this->associadoObj->bairro . PHP_EOL;

                $this->logString .= (empty($this->associadoObj->cidade)) ? 
                    'ENDERECO INCOMPLETO! CAMPO CIDADE VAZIO!' . PHP_EOL :
                     'Cidade: ' . $this->associadoObj->cidade . PHP_EOL;

                $this->logString .= (empty($this->associadoObj->estado)) ? 
                    'ENDERECO INCOMPLETO! CAMPO SIGLA ESTADO VAZIO!' . PHP_EOL :
                     'Estado: ' . $this->associadoObj->estado . PHP_EOL;             
                
                //Montar linha do PDF ou célula da planilha Xlsx (planilha excel) de acordo com o tipo de saída informada em $saidaEm
                if(is_object($this->fpdf) && (new \ReflectionClass($this->fpdf))->getShortName() == 'FPDF')
                    $this->__montaEtiquetaFPDF($this->associadoObj->nome, $cep, $ende, $bairro_cidade_estado);
                elseif(is_object($this->planilhaExcel) && (new \ReflectionClass($this->planilhaExcel))->getShortName() == 'Spreadsheet')
                    $this->__montaCelulasLinhaPlanilha($this->associadoObj->nome, $cep, $ende, $bairro_cidade_estado);

                $this->logString .= 'Etiqueta gerada.' . PHP_EOL . PHP_EOL;
            }

            //Salvar arquivo de log
            $this->logDepuracao->fwrite($this->logString);
        }

        /**
         * Método que inicia o objeto que abstrai os aspectos da implementação da saída em .pdf
         */
        private function __iniciaFPDF() {

            $this->fpdf = new FPDF('P','mm','Letter'); // Cria um arquivo novo tipo carta, na vertical.

            //$this->fpdf->Open(); // inicia documento
            $this->fpdf->AddPage(); // adiciona a primeira pagina
            $this->fpdf->SetMargins('18','1,7'); // Define as margens do documento
            $this->fpdf->SetLeftMargin('1'); // Define as margens esquerda do documento
            $this->fpdf->SetAuthor("ANAJUSTRA"); // Define o autor
            $this->fpdf->SetFont('helvetica','B',8.5); // Define a fonte
            $this->fpdf->SetDisplayMode();

            $this->linha = 0;
            $this->coluna = 0;
        }

        /**
         * Método que monta a etiqueta do associado para impressão no .pdf
         */
        private function __montaEtiquetaFPDF($nome, $cep, $endereco, $bairro_cidade_estado = NULL) {

            if($this->linha == 10) {

                $this->fpdf->AddPage();
                $this->linha = 0;
            }
            
            if($this->coluna == 2) { // Se for a terceira coluna

                $this->coluna = 0; // $coluna volta para o valor inicial
                $this->linha++; // $linha é igual ela mesma +1
            }
            
            if($this->linha == 10) { // Se for a última linha da página

                $this->fpdf->AddPage(); // Adiciona uma nova página
                $this->linha = 0; // $linha volta ao seu valor inicial
            }
            
            $posicaoV = $this->linha*$this->aeti;
            $posicaoH = $this->coluna*$this->leti;
            
            if($this->coluna == 0) { // Se a coluna for 0

                $somaH = $this->mesq; // Soma Horizontal é apenas a margem da esquerda inicial
            } 
            else { // Senão

                $somaH = $this->mesq+$posicaoH; // Soma Horizontal é a margem inicial mais a posiçãoH
            }
            
            if($this->linha == 0 ) { // Se a linha for 0

                $somaV = $this->msup; // Soma Vertical é apenas a margem superior inicial
            } 
            else { // Senão

                $somaV = $this->msup+$posicaoV; // Soma Vertical é a margem superior inicial mais a posiçãoV
            }
            
            $this->fpdf->Text($somaH,$somaV,Encoding::toWin1252($nome)); // Imprime o nome da pessoa de acordo com as coordenadas

            $this->fpdf->Text($somaH,$somaV+4,Encoding::toWin1252($endereco)); // Imprime o endereço da pessoa de acordo com as coordenadas

            if(isset($bairro_cidade_estado) && $bairro_cidade_estado != '') {

                $this->fpdf->Text($somaH,$somaV+8,Encoding::toWin1252($bairro_cidade_estado)); // Imprime o cep da pessoa de acordo com as coordenadas
                $cep_y_pos = $somaV+12;                
            }
            else {

                $cep_y_pos = $somaV+8 ;               
            }

            $this->fpdf->Text($somaH,$cep_y_pos, 'CEP: ' . $cep);                            
            $this->coluna = $this->coluna+1;
        }

        /**
         * Método que inicia o objeto que abstrai os aspectos da implementação da saída em .xlsx
         */
        private function __iniciaXlsx() {

            $this->planilhaExcel = new Spreadsheet();
            $this->folhaAtivaPlanilha = $this->planilhaExcel->getActiveSheet();
            $this->escritorPlanilha = new Xlsx($this->planilhaExcel);

            $this->linha = 0;            
        }

        /**
         * Método que monta as células da linha do associado na planilha xlsx (excel)
         */ 
        private function __montaCelulasLinhaPlanilha($nome, $cep, $endereco, $bairro_cidade_estado = '') {

            $this->folhaAtivaPlanilha->setCellValue('A' . $this->linha, $nome);
            $this->folhaAtivaPlanilha->setCellValue('B' . $this->linha, $endereco . ' ' . $bairro_cidade_estado);
            $this->folhaAtivaPlanilha->setCellValue('C' . $this->linha, $cep);
            $this->linha++;
        }        

        public function saida() {

            if(is_object($this->fpdf) && (new \ReflectionClass($this->fpdf))->getShortName() == 'FPDF') {
                                
                $this->fpdf->Output();
            } 
            elseif(is_object($this->planilhaExcel) && (new \ReflectionClass($this->planilhaExcel))->getShortName() == 'Spreadsheet') {
                
                $this->escritorPlanilha->save('arquivos_saida' . DS . DataHoraFactory::getDataHora()->format('Y-m-d_H-i') . '.xlsx');
            }
            else {

                die('Saída não definida.');
            }         
        }
    }