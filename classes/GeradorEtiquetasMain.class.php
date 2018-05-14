<?php 

    require_once(__FPDF_PATH__ . 'fpdf.php');
    require_once(__ROOT__ . DS . 'services' . DS . 'DataHoraFactory.class.php');
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    use \ForceUTF8\Encoding;

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
        private $associadoDAO = NULL;
        private $associadoObj = NULL;  

        private $associados = array();      

        private $saidaEm = 'pdf';

        public function __construct($saidaEm = '') {

            $this->saidaEm = $saidaEm;

            $this->logDepuracao = ArquivoLogFactory::getArquivoLog(); 
            $this->associadoDAO = new AssociadoDAO();
        }

        public function geraEtiquetasNomeCpf($listaIdsAssociados) {

            $this->associados = $this->associadoDAO->obterAssociadosAPartirDeListaDeIDs($listaIdsAssociados);

            if($this->saidaEm == 'pdf')
                $this->__iniciaFPDF();            
            elseif($this->saidaEm == 'xlsx') 
                $this->__iniciaXlsx(); 

            $this->__main();           
        }

        private function __main() {

            $this->logDepuracao = ArquivoLogFactory::getArquivoLog();         

            $log_string = 'CONSULTA SQL EXECUTADA: ' . $this->associadoDAO->getStringConsultaSql() . PHP_EOL . PHP_EOL;            

            foreach($this->associados as $key => $associado) {

                $associadoObj = new Associado($associado);

                $nome = trim(str_ireplace(array('pensionista', 'pencionista', '(', ')'), '', $associado['NOME_TITULAR']));        

                $log_string .= 'ASSOCIADO LINHA ' . (string)($key + 1) . PHP_EOL .
                                'Nome: ' . $nome . PHP_EOL .
                                'Matricula: ' . $associado['MATRICULA'] . PHP_EOL;        

                if(empty(trim($associado['ENDERECO']))) {

                    $log_string .= 'ETIQUETA NAO GERADA! CAMPO ENDERECO VAZIO!' . PHP_EOL . PHP_EOL;
                    continue;
                }                    

                $cep = MascaraHelper::formataMascara($associado['CEP'], '#####-###');        
                $validadorCEP = new ValidadorCEP();

                if(empty($cep)) {
                    
                    $log_string .= 'ETIQUETA NAO GERADA! CAMPO CEP VAZIO!' . PHP_EOL . PHP_EOL;            
                    continue;
                }

                $validadorCEP->validar($cep);
            
                if($validadorCEP->cep != '') {
                    
                    $log_string .= 'CEP validado nos Correios (https://viacep.com.br/)' . PHP_EOL;
                }
                else {
                    
                    $log_string .= 'ETIQUETA NAO GERADA! CEP ' . $cep . ' NAO FOI VALIDADO NOS CORREIOS (https://viacep.com.br/ws/' . $cep . '/json/)' . PHP_EOL . PHP_EOL;
                    continue;
                }           

                $bairro_cidade_estado = NULL;
                if(isset($associado['BAIRRO']) && $associado['BAIRRO'] != '') {

                    $log_string .= 'Associado com cadastro na tabela funcional.' . PHP_EOL;

                    $log_string .= 'INICIANDO análise/comparação do endereço cadastrado com o obtido a partir do CEP nos Correios' . PHP_EOL;
                    $analise_comparacao = $associadoObj->compararEnderecoCadastradoComValidadoNosCorreios($validadorCEP);
                    $log_string .= $analise_comparacao;
                    $log_string .= 'Análise/comparação FINALIZADA' . PHP_EOL;

                    if(!empty($associado['NUMERO']))
                        $associado['ENDERECO'] = str_replace(array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0'), '', $associado['ENDERECO']);

                    $ende = ((isset($validadorCEP->endereco) && $validadorCEP->endereco != '') ? $validadorCEP->endereco : $associado['ENDERECO']) . ' ' .
                        $associado['NUMERO'] . ' ' .
                        $validadorCEP->complemento;

                    $bairro_cidade_estado = ((isset($validadorCEP->bairro) && $validadorCEP->bairro != '') ? $validadorCEP->bairro : $associado['BAIRRO']) . ', ' .
                        ((isset($validadorCEP->cidade) && $validadorCEP->cidade != '') ? $validadorCEP->cidade : $associado['CIDADE']) .
                        ' - ' .
                        ((isset($validadorCEP->estado) && $validadorCEP->estado != '') ? $validadorCEP->estado : $associado['SIGLA']);                        
                }
                else {

                    $log_string .= 'ASSOCIADO AINDA COM CADASTRO DE ENDEREÇO ANTIGO!' . PHP_EOL;
                    $ende = $associado['ENDERECO'] . ' CEP: ' . $cep;
                }

                $log_string .= 'Endereco: ' . $ende . PHP_EOL;

                $log_string .= (empty($associado['NUMERO'])) ?
                    'ENDERECO INCOMPLETO! CAMPO NUMERO VAZIO!' . PHP_EOL
                    : 'NUMERO: ' . $associado['NUMERO'] . PHP_EOL;        

                $log_string .= (empty($associado['COMPLEMENTO'])) ?
                    'ENDERECO INCOMPLETO! CAMPO COMPLEMENTO VAZIO!' . PHP_EOL
                    : 'Complemento: ' . PHP_EOL;  

                $log_string .= (empty($associado['BAIRRO'])) ? 
                    'ENDERECO INCOMPLETO! CAMPO BAIRRO VAZIO!' . PHP_EOL :
                     'Bairro: ' . $associado['BAIRRO'] . PHP_EOL;

                $log_string .= (empty($associado['CIDADE'])) ? 
                    'ENDERECO INCOMPLETO! CAMPO CIDADE VAZIO!' . PHP_EOL :
                     'Cidade: ' . $associado['CIDADE'] . PHP_EOL;

                $log_string .= (empty($associado['SIGLA'])) ? 
                    'ENDERECO INCOMPLETO! CAMPO SIGLA ESTADO VAZIO!' . PHP_EOL :
                     'Estado: ' . $associado['SIGLA'] . PHP_EOL;             
                
                if(is_object($this->fpdf) && (new \ReflectionClass($this->fpdf))->getShortName() == 'FPDF')
                    $this->__montaEtiquetaFPDF($nome, $cep, $ende, $bairro_cidade_estado);
                elseif(is_object($this->planilhaExcel) && (new \ReflectionClass($this->planilhaExcel))->getShortName() == 'Spreadsheet')
                    $this->__montaCelulasLinhaPlanilha($nome, $cep, $ende, $bairro_cidade_estado);

                $log_string .= 'Etiqueta gerada.' . PHP_EOL . PHP_EOL;
            }

            $this->logDepuracao->fwrite($log_string);
        }

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

        private function __montaEtiquetaFPDF($nome, $cep, $endereco, $bairro_cidade_estado = NULL) {

            if($this->linha == 10) {

                $this->fpdf->AddPage();
                $this->linha = 0;
            }
            
            if($this->coluna == 2) { // Se for a terceira coluna

                $this->coluna = 0; // $coluna volta para o valor inicial
                $this->linha = $this->linha +1; // $linha é igual ela mesma +1
            }
            
            if($this->linha == 10) { // Se for a última linha da página

                $this->fpdf->AddPage(); // Adiciona uma nova página
                $linha = 0; // $linha volta ao seu valor inicial
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

        private function __iniciaXlsx() {

            $this->planilhaExcel = new Spreadsheet();
            $this->folhaAtivaPlanilha = $this->planilhaExcel->getActiveSheet();
            $this->escritorPlanilha = new Xlsx($this->planilhaExcel);

            $this->linha = 0;            
        }

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