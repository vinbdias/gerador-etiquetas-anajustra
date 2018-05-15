<?php 

    require_once(__MODELS_PATH__ . 'Associado.class.php');
    require_once(__MODELS_PATH__ . 'TmpAssociado.class.php');

    require_once(__VENDOR_PATH__ . 'autoload.php');        

    require_once(__HELPERS_PATH__ . 'MascaraHelper.class.php');

    require_once(__SERVICES_PATH__ . 'ValidadorCEP.class.php');
    require_once(__SERVICES_PATH__ . 'ArquivoLogFactory.class.php');

    require_once(__DAO_PATH__ . 'AssociadoDAO.class.php');
    require_once(__DAO_PATH__ . 'TmpAssociadoDAO.class.php');

    require_once(__FPDF_PATH__ . 'fpdf.php');

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
        private $linhaLogString = '';

        private $validadorCEP = NULL; 

        private $associadoDAO = NULL;
        private $associado = NULL;
        private $tmpAssociado = NULL;
        private $tmpAssociadoDAO = NULL;  

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

            $this->__main();           
        }

        /**
         * Método que implementa a execução da página gera-etiquetas-regiaotrts.php
         */
        public function geraEtiquetasRegiao($regiao) {

            $this->associados = $this->associadoDAO->obterAssociadosAPartirDeRegiao($regiao);

            $this->__main();           
        }        

        /**
         * Método que implementa aspectos comuns a todas as execuções de páginas que geram arquivos para impressão de etiquetas
         */
        private function __main() { 

            //Iniciar o arquivo de log
            $this->logDepuracao = ArquivoLogFactory::getArquivoLog();         
            $this->logString = 'CONSULTA SQL EXECUTADA: ' . $this->associadoDAO->getStringConsultaSql() . PHP_EOL . PHP_EOL;            

            if($this->saidaEm == 'pdf')
                $this->__iniciaFPDF();            
            elseif($this->saidaEm == 'xlsx') 
                $this->__iniciaXlsx();  
            elseif($this->saidaEm == 'tmp_associados')    
                $this->__iniciaTmpAssociados();       

            //Percorrer pelos associados armazenados
            foreach($this->associados as $key => $associado) {

                $this->linhaLogString = '';
                //Iniciar classe modelo
                $this->associado = new Associado($associado);               

                //Iniciar log da linha do associado em questão / iteração do loop
                $this->linhaLogString .= 'ASSOCIADO LINHA ' . (string)($key + 1) . PHP_EOL .
                                'Nome: ' . $this->associado->nome . PHP_EOL .
                                'Matricula: ' . $this->associado->matricula . PHP_EOL;      

                $cep = MascaraHelper::formataMascara($this->associado->cep, '#####-###');        
                $this->validadorCEP = new ValidadorCEP();                                  

                if(!$this->__validarSeDeveContinuarComAssociado($cep))
                    continue;

                $bairroCidadeEstadoString = NULL;

                $enderecoString = '';
                $this->__tratamentosEAnalisesEmFuncaoDoCep($enderecoString, $bairroCidadeEstadoString, $cep);
           
                $this->__consideracoesFinais($enderecoString);

                $this->__montaConteudoSaida($cep, $enderecoString, $bairroCidadeEstadoString);

                $this->logString .= $this->linhaLogString;                
            }

            //Salvar arquivo de log
            $this->logDepuracao->fwrite($this->linhaLogString);
        }

        /**
         * Método que verifica se deve dar prosseguimento à geração de etiqueta do associado, ou já pode ser desconsiderado
         * @param string $cep
         * @return boolean
         */
        private function __validarSeDeveContinuarComAssociado($cep) {

            //Se o endereço for vazio, pular linha e não gerar etiqueta para o associado em questão
            if(empty($this->associado->endereco)) {

                $this->linhaLogString .= 'ENDERECO INADEQUADO PARA GERAR ETIQUETA! CAMPO ENDERECO VAZIO!' . PHP_EOL . PHP_EOL;
                return false;
            }              

            //Se o CEP for vazio, pular linha e não gerar etiqueta para o associado em questão                
            if(empty($cep)) {
                
                $this->linhaLogString .= 'ENDERECO INADEQUADO PARA GERAR ETIQUETA! CAMPO CEP VAZIO!' . PHP_EOL . PHP_EOL;            
                return false;
            }

            $this->validadorCEP->validar($cep);
            
            //Se o CEP não for válido, pular linha e não gerar etiqueta para o associado em questão
            if(!empty($this->validadorCEP->cep)) {
                
                $this->linhaLogString .= 'CEP validado nos Correios (https://viacep.com.br/)' . PHP_EOL;
            }
            else {
                
                $this->linhaLogString .= 'ENDERECO INADEQUADO PARA GERAR ETIQUETA! CEP ' . $cep . ' NAO FOI VALIDADO NOS CORREIOS (https://viacep.com.br/ws/' . $cep . '/json/)' . PHP_EOL . PHP_EOL;
                return false;
            }      

            return true;        
        }

        /**
         * Método que faz tratamentos e análises a serem executados no fluxo de __main
         * @param string $enderecoString
         * @param string $bairroCidadeEstadoString
         * @param string $cep
         */
        private function __tratamentosEAnalisesEmFuncaoDoCep(&$enderecoString, &$bairroCidadeEstadoString, $cep) {

            //Caso o campo bairro esteja preenchido, assumir que o associado passou por recadastramento de endereço
            if(isset($this->associado->bairro) && $this->associado->bairro != '') {

                $this->linhaLogString .= 'Associado com cadastro na tabela funcional.' . PHP_EOL;

                $this->linhaLogString .= 'INICIANDO análise/comparação do endereço cadastrado com o obtido a partir do CEP nos Correios' . PHP_EOL;
                $this->linhaLogString .= $this->associado->compararEnderecoCadastradoComValidadoNosCorreios($this->validadorCEP);                    
                $this->linhaLogString .= 'Análise/comparação FINALIZADA' . PHP_EOL;

                $enderecoString = ((isset($this->validadorCEP->endereco) && $this->validadorCEP->endereco != '') ? $this->validadorCEP->endereco : $this->associado->endereco) . ' ' .
                    $this->associado->numero . ' ' .
                    $this->validadorCEP->complemento;

                $bairroCidadeEstadoString = ((isset($this->validadorCEP->bairro) && $this->validadorCEP->bairro != '') ? $this->validadorCEP->bairro : $this->associado->bairro) . ', ' .
                    ((isset($this->validadorCEP->cidade) && $this->validadorCEP->cidade != '') ? $this->validadorCEP->cidade : $this->associado->cidade) .
                    ' - ' .
                    ((isset($this->validadorCEP->estado) && $this->validadorCEP->estado != '') ? $this->validadorCEP->estado : $this->associado->estado);                        
            }
            else {

                $this->linhaLogString .= 'ASSOCIADO AINDA COM CADASTRO DE ENDEREÇO ANTIGO!' . PHP_EOL;
                $enderecoString = $this->associado->endereco . ' CEP: ' . $cep;
            }            
        }


        /**
         * Método responsável por adicionar ao log as considerações finais acerca do cadastro de endereço do associado
         * @param string $enderecoString
         */
        private function __consideracoesFinais($enderecoString) {

                $this->linhaLogString .= 'Endereco: ' . $enderecoString . PHP_EOL;

                $this->linhaLogString .= (empty($this->associado->numero)) ?
                    'ENDERECO INCOMPLETO! CAMPO NUMERO VAZIO!' . PHP_EOL
                    : 'NUMERO: ' . $this->associado->numero . PHP_EOL;        

                $this->linhaLogString .= (empty($this->associado->complemento)) ?
                    'ENDERECO INCOMPLETO! CAMPO COMPLEMENTO VAZIO!' . PHP_EOL
                    : 'Complemento: ' . PHP_EOL;  

                $this->linhaLogString .= (empty($this->associado->bairro)) ? 
                    'ENDERECO INCOMPLETO! CAMPO BAIRRO VAZIO!' . PHP_EOL :
                     'Bairro: ' . $this->associado->bairro . PHP_EOL;

                $this->linhaLogString .= (empty($this->associado->cidade)) ? 
                    'ENDERECO INCOMPLETO! CAMPO CIDADE VAZIO!' . PHP_EOL :
                     'Cidade: ' . $this->associado->cidade . PHP_EOL;

                $this->linhaLogString .= (empty($this->associado->estado)) ? 
                    'ENDERECO INCOMPLETO! CAMPO SIGLA ESTADO VAZIO!' . PHP_EOL :
                     'Estado: ' . $this->associado->estado . PHP_EOL;              
        }

        /**
         * Método responsável por verificar o tipo de saída e chamar a função respectiva para tal
         * @param string $cep
         * @param string $enderecoString
         * @param string $bairroCidadeEstadoString
         */
        private function __montaConteudoSaida($cep, $enderecoString, $bairroCidadeEstadoString) {

            //Montar linha do PDF ou célula da planilha Xlsx (planilha excel) de acordo com o tipo de saída informada em $saidaEm
            if(is_object($this->fpdf) && (new \ReflectionClass($this->fpdf))->getShortName() == 'FPDF')
                $this->__montaEtiquetaFPDF($this->associado->nome, $cep, $enderecoString, $bairroCidadeEstadoString);
            elseif(is_object($this->planilhaExcel) && (new \ReflectionClass($this->planilhaExcel))->getShortName() == 'Spreadsheet')
                $this->__montaCelulasLinhaPlanilha($this->associado->nome, $cep, $enderecoString, $bairroCidadeEstadoString);

            $this->linhaLogString .= 'Etiqueta gerada.' . PHP_EOL . PHP_EOL;
        }

        private function __iniciaTmpAssociados() {

            $this->tmpAssociadoDAO = new TmpAssociadoDAO();
            $this->tmpAssociadoDAO->limpaTabela();
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
        private function __montaEtiquetaFPDF($nome, $cep, $enderecoString, $bairroCidadeEstadoString = NULL) {

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

            $this->fpdf->Text($somaH,$somaV+4,Encoding::toWin1252($enderecoString)); // Imprime o endereço da pessoa de acordo com as coordenadas

            if(isset($bairroCidadeEstadoString) && $bairroCidadeEstadoString != '') {

                $this->fpdf->Text($somaH,$somaV+8,Encoding::toWin1252($bairroCidadeEstadoString)); // Imprime o cep da pessoa de acordo com as bairroCidadeEstadoString
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
        private function __montaCelulasLinhaPlanilha($nome, $cep, $enderecoString, $bairroCidadeEstadoString = '') {

            $this->folhaAtivaPlanilha->setCellValue('A' . $this->linha, $nome);
            $this->folhaAtivaPlanilha->setCellValue('B' . $this->linha, $enderecoString . ' ' . $bairroCidadeEstadoString);
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