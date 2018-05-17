<?php 

    require_once(__MODELS_PATH__ . 'Associado.class.php');
    require_once(__MODELS_PATH__ . 'TmpAssociado.class.php');

    require_once(__VENDOR_PATH__ . 'autoload.php');        
    
    require_once(__HELPERS_PATH__ . 'StringHelper.class.php');
    require_once(__HELPERS_PATH__ . 'UrlHelper.class.php');

    require_once(__SERVICES_PATH__ . 'ValidadorCEP.class.php');
    require_once(__SERVICES_PATH__ . 'ArquivoLogFactory.class.php');
    require_once(__SERVICES_PATH__ . 'PastaSaidaXlsxRegiaoFactory.class.php');

    require_once(__DAO_PATH__ . 'AssociadoDAO.class.php');
    require_once(__DAO_PATH__ . 'TmpAssociadoDAO.class.php');
    require_once(__DAO_PATH__ . 'RegiaoDAO.class.php');

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
        private $mesq;
        private $mdir;
        private $msup;
        private $leti;
        private $aeti;
        private $ehet;

        private $planilhaExcel = NULL;
        private $planilhaAnaliseRegiaoOks = NULL;
        private $planilhaAnaliseRegiaoNaoOks = NULL;

        private $planilhaArquivoParaEnvio = NULL;

        private $logDepuracao = NULL;
        private $logString = '';
        private $linhaLogString = '';

        private $linhaPdf = 0;
        private $colunaPdf = 0;

        private $linhaXlsx = 0;

        private $linhaXlsxOks;
        private $linhaXlsxNaoOks = 0;

        private $validadorCEP = NULL; 

        private $associadoDAO = NULL;
        private $associado = NULL;
        private $tmpAssociado = NULL;
        private $tmpAssociadoDAO = NULL;  

        private $associados = array();

        private $regiaoDAO = NULL;  
        private $nomeRegiao = '';    

        private $saidaEm = array('pdf');

        private $pularValidacaoViaCep = false;
        private $limiteConsultaAssociados = NULL;

        private $geraArquivoEnvio = false;

        private $salvarArquivoLog = false;

        /**
         * Método construtor
         * @param array $saidaEm
         */
        public function __construct($saidaEm = array('pdf'), $pularValidacaoViaCep = false, $salvarArquivoLog = false) {
        
            $this->saidaEm = $saidaEm;

            $this->pularValidacaoViaCep = $pularValidacaoViaCep;

            $this->salvarArquivoLog = $salvarArquivoLog;

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
            
            $this->associados = $this->associadoDAO->obterAssociadosAPartirDeRegiao($regiao, $this->limiteConsultaAssociados);

            $this->nomeRegiao = (new RegiaoDAO())->obterNomeRegiao($regiao);
            $this->nomePastaRegiao = StringHelper::formataParaNomeDeArquivo($this->nomeRegiao);        

            $this->__main();           
        }        

        /**
         * Método que implementa aspectos comuns a todas as execuções de páginas que geram arquivos para impressão de etiquetas
         */
        private function __main() { 

            if(in_array('pdf', $this->saidaEm))
                $this->__iniciaFPDF();            
            else {

                if(in_array('xlsx', $this->saidaEm)) 
                    $this->__iniciaXlsx();  

                if(in_array('xlsxOks', $this->saidaEm))    
                    $this->__iniciaAnaliseRegiaoOksXlsx();

                if(in_array('xlsxNaoOks', $this->saidaEm))
                    $this->__iniciaAnaliseRegiaoNaoOksXlsx();
            }             

            //Iniciar o arquivo de log
            if($this->salvarArquivoLog) {

                $this->logDepuracao = ArquivoLogFactory::getArquivoLog();         
                $this->logString = 'CONSULTA SQL EXECUTADA: ' . $this->associadoDAO->getStringConsultaSql() . PHP_EOL . PHP_EOL;             
            }
            

            //Percorrer pelos associados armazenados
            foreach($this->associados as $key => $associado) {

                $this->linhaLogString = '';
                //Iniciar classe modelo
                $this->associado = new Associado($associado); 

                //Iniciar log da linha do associado em questão / iteração do loop
                $this->linhaLogString .= 'ASSOCIADO LINHA ' . (string)($key + 1) . PHP_EOL .
                                'Nome: ' . $this->associado->nome . PHP_EOL .
                                'Matricula: ' . $this->associado->matricula . PHP_EOL;      

                $cep = StringHelper::formataMascara($this->associado->cep, '#####-###');        
                $this->validadorCEP = new ValidadorCEP();    


                $enderecoString = '';
                $bairroCidadeEstadoString = '';

                if(!$this->__validarSeDeveContinuarComAssociado($cep)) {

                    if($this->__issetPlanilhaAnaliseRegiaoNaoOks())
                        $this->__gravaNaoOk();                 
                }
                else {                

                    $this->__tratamentosEAnalisesEmFuncaoDoCep($enderecoString, $bairroCidadeEstadoString, $cep);
               
                    $this->__consideracoesFinais($enderecoString);

                    if($this->__issetPlanilhaAnaliseRegiaoOks()) 
                        $this->__gravaOk();
                }


                $this->__montaConteudoSaida($cep, $enderecoString, $bairroCidadeEstadoString);

                if(isset($this->logString)) {

                    $this->linhaLogString .= 'Etiqueta gerada.' . PHP_EOL . PHP_EOL;
                    $this->logString .= $this->linhaLogString;                
                }                

                $this->tmpAssociadoOk = NULL;
                $this->tmpAssociadoNaoOk = NULL;
            }

            //Salvar arquivo de log
            if(isset($this->logDepuracao))
                $this->logDepuracao->fwrite($this->logString);
        }

        /**
         * Método que grava associado com cadastro ok, instanciando e armazenando-o em objeto da classe TmpAssociado,
         *  para uso futuro ao montar as células da linha no arquivo excel
         */
        private function __gravaOk() {

            $this->tmpAssociadoOk = new TmpAssociado($this->associado, $this->validadorCEP, $this->linhaLogString);
        }

        /**
         * Método que grava associado com cadastro não ok, instanciando e armazenando-o em objeto da classe TmpAssociado,
         *  para uso futuro ao montar as células da linha no arquivo excel
         */
        private function __gravaNaoOk() {

            $this->tmpAssociadoNaoOk = new TmpAssociado($this->associado, $this->validadorCEP, $this->linhaLogString);       
        }

        /**
         * Método que verifica se deve dar prosseguimento à geração de etiqueta do associado, ou já pode ser desconsiderado
         * @param string $cep
         * @return boolean
         */
        private function __validarSeDeveContinuarComAssociado($cep) {

            //Se o endereço for vazio, pular linha e não gerar etiqueta para o associado em questão
            if(empty(trim($this->associado->endereco))) {

                $this->linhaLogString .= 'ENDERECO INADEQUADO PARA GERAR ETIQUETA! CAMPO ENDERECO VAZIO!' . PHP_EOL . PHP_EOL;                
                return false;
            }              

            //Se o CEP for vazio, pular linha e não gerar etiqueta para o associado em questão                
            if(empty(str_replace('-', '', $cep))) {

                $this->linhaLogString .= 'ENDERECO INADEQUADO PARA GERAR ETIQUETA! CAMPO CEP VAZIO!' . PHP_EOL . PHP_EOL;            
                return false;
            }

            if(!$this->pularValidacaoViaCep)
                $this->validadorCEP->validar($cep);
            
            //Se o CEP não for válido, pular linha e não gerar etiqueta para o associado em questão
            if(!empty($this->validadorCEP->cep) || $this->pularValidacaoViaCep) {
                
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
                $this->linhaLogString .= $this->associado->compararEnderecoCadastradoComValidadoViaCep($this->validadorCEP);                    
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
            if($this->__issetFpdf())
                $this->__montaEtiquetaFPDF($this->associado->nome, $cep, $enderecoString, $bairroCidadeEstadoString);
            else {

                if($this->__issetPlanilhaExcel())
                    $this->__montaCelulasLinhaPlanilha($this->associado->nome, $cep, $enderecoString, $bairroCidadeEstadoString);
                
                if($this->__issetPlanilhaAnaliseRegiaoOks() && $this->__issetTmpAssociadoOk())
                    $this->__montaCelulasLinhaplanilhaAnaliseRegiaoOks();

                if($this->__issetPlanilhaAnaliseRegiaoNaoOks() && $this->__issetTmpAssociadoNaoOk())
                    $this->__montaCelulasLinhaplanilhaAnaliseRegiaoNaoOks();  
            }          
        }

        /**
         * Método que inicia os objetos que abstraem os aspectos da implementação da saída em .xlsx da análise da região
         */
        private function __iniciaAnaliseRegiaoXlsx() {

            $this->__iniciaAnaliseRegiaoOksXlsx();
            $this->__iniciaAnaliseRegiaoNaoOksXlsx();
        }

        /**
         * Método que inicia o objeto que abstrai os aspectos da implementação da saída em .xlsx
         */
        private function __iniciaXlsx() {

            $this->planilhaExcel = new Spreadsheet();
            $this->folhaAtivaPlanilha = $this->planilhaExcel->getActiveSheet();
            $this->escritorPlanilha = new Xlsx($this->planilhaExcel);

            $this->folhaAtivaPlanilha->setCellValue('A1', 'NOME');
            $this->folhaAtivaPlanilha->setCellValue('B1', 'ENDERECO');
            $this->folhaAtivaPlanilha->setCellValue('C1', 'CEP');                        

            $this->linhaXlsx = 2;            
        }         

        /**
         * Método que inicia o objeto que abstrai os aspectos da implementação da saída em .xlsx dos cadastros oks da análise da região
         */
        private function __iniciaAnaliseRegiaoOksXlsx() {

            $this->planilhaAnaliseRegiaoOks = new Spreadsheet();
            $this->folhaAtivaplanilhaAnaliseRegiaoOks = $this->planilhaAnaliseRegiaoOks->getActiveSheet();
            $this->escritorplanilhaAnaliseRegiaoOks = new Xlsx($this->planilhaAnaliseRegiaoOks);

            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('A1', 'NOME');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('B1', 'MATRÍCULA');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('C1', 'ENDEREÇO');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('D1', 'Nº');            
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('E1', 'COMPLEMENTO');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('F1', 'BAIRRO');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('G1', 'CIDADE');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('H1', 'ESTADO');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('I1', 'CEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('J1', 'ENDEREÇO VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('K1', 'COMPLEMENTO VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('L1', 'BAIRRO VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('M1', 'CIDADE VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('N1', 'ESTADO VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('O1', 'ANÁLISE');            

            $this->linhaXlsxOks = 2;  
        }

        /**
         * Método que inicia o objeto que abstrai os aspectos da implementação da saída em .xlsx dos cadastros não oks da análise da região
         */
        private function __iniciaAnaliseRegiaoNaoOksXlsx() {

            $this->planilhaAnaliseRegiaoNaoOks = new Spreadsheet();
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks = $this->planilhaAnaliseRegiaoNaoOks->getActiveSheet();
            $this->escritorplanilhaAnaliseRegiaoNaoOks = new Xlsx($this->planilhaAnaliseRegiaoNaoOks);

            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('A1', 'NOME');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('B1', 'MATRÍCULA');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('C1', 'ENDEREÇO');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('D1', 'Nº');            
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('E1', 'COMPLEMENTO');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('F1', 'BAIRRO');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('G1', 'CIDADE');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('H1', 'ESTADO');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('I1', 'CEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('J1', 'ENDEREÇO VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('K1', 'COMPLEMENTO VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('L1', 'BAIRRO VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('M1', 'CIDADE VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('N1', 'ESTADO VIACEP');
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('O1', 'ANÁLISE');            

            $this->linhaXlsxNaoOks = 2;  
        }        

        /**
         * Método que inicia o objeto que abstrai os aspectos da implementação da saída em .pdf
         */
        private function __iniciaFPDF() {

            $this->mesq = 7;
            $this->mdir = 8;
            $this->msup = 22;
            $this->leti = 107;
            $this->aeti = 25.4;
            $this->ehet = 100;            

            $this->fpdf = new FPDF('P','mm','Letter'); // Cria um arquivo novo tipo carta, na vertical.

            //$this->fpdf->Open(); // inicia documento
            $this->fpdf->AddPage(); // adiciona a primeira pagina
            $this->fpdf->SetMargins('18','1,7'); // Define as margens do documento
            $this->fpdf->SetLeftMargin('1'); // Define as margens esquerda do documento
            $this->fpdf->SetAuthor("ANAJUSTRA"); // Define o autor
            $this->fpdf->SetFont('helvetica','B',8.5); // Define a fonte
            $this->fpdf->SetDisplayMode();

            $this->linhaPdf = 0;
            $this->colunaPdf = 0;
        }

        /**
         * Método que monta a etiqueta do associado para impressão no .pdf
         */
        private function __montaEtiquetaFPDF($nome, $cep, $enderecoString, $bairroCidadeEstadoString = NULL) {

            if($this->linhaPdf == 10) {

                $this->fpdf->AddPage();
                $this->linhaPdf = 0;
            }
            
            if($this->colunaPdf == 2) { // Se for a terceira coluna

                $this->colunaPdf = 0; // $coluna volta para o valor inicial
                $this->linhaPdf++; // $linha é igual ela mesma +1
            }
            
            if($this->linhaPdf == 10) { // Se for a última linha da página

                $this->fpdf->AddPage(); // Adiciona uma nova página
                $this->linhaPdf = 0; // $linha volta ao seu valor inicial
            }
            
            $posicaoV = $this->linhaPdf*$this->aeti;
            $posicaoH = $this->colunaPdf*$this->leti;
            
            if($this->colunaPdf == 0) { // Se a coluna for 0

                $somaH = $this->mesq; // Soma Horizontal é apenas a margem da esquerda inicial
            } 
            else { // Senão

                $somaH = $this->mesq+$posicaoH; // Soma Horizontal é a margem inicial mais a posiçãoH
            }
            
            if($this->linhaPdf == 0 ) { // Se a linha for 0

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
            $this->colunaPdf = $this->colunaPdf+1;
        }

        /**
         * Método que monta as células da linha do associado na planilha xlsx (excel)
         */ 
        private function __montaCelulasLinhaPlanilha($nome, $cep, $enderecoString, $bairroCidadeEstadoString = '') {

            $this->folhaAtivaPlanilha->setCellValue('A' . $this->linhaXlsx, $nome);
            $this->folhaAtivaPlanilha->setCellValue('B' . $this->linhaXlsx, $enderecoString . ' ' . $bairroCidadeEstadoString);
            $this->folhaAtivaPlanilha->setCellValue('C' . $this->linhaXlsx, $cep);
            $this->linhaXlsx++;
        } 

        /**
         * Método que chama a montagem de células da linha de associados para oks ou não oks
         */
        private function __montaCelulasLinhaplanilhaAnaliseRegiao() {

            if($this->__issetTmpAssociadoOk())
                $this->__montaCelulasLinhaplanilhaAnaliseRegiaoOks();
            elseif($this->__issetTmpAssociadoNaoOk())
                $this->__montaCelulasLinhaplanilhaAnaliseRegiaoNaoOks();
        }

        /**
         * Método que monta as células da linha do associado na planilha de oks da análise da região xlsx
         */
        private function __montaCelulasLinhaplanilhaAnaliseRegiaoOks() {

            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('A' . $this->linhaXlsxOks, $this->tmpAssociadoOk->nome);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('B' . $this->linhaXlsxOks, $this->tmpAssociadoOk->matricula);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('C' . $this->linhaXlsxOks, $this->tmpAssociadoOk->endereco);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('D' . $this->linhaXlsxOks, $this->tmpAssociadoOk->numero);            
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('E' . $this->linhaXlsxOks, $this->tmpAssociadoOk->complemento);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('F' . $this->linhaXlsxOks, $this->tmpAssociadoOk->bairro);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('G' . $this->linhaXlsxOks, $this->tmpAssociadoOk->cidade);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('H' . $this->linhaXlsxOks, $this->tmpAssociadoOk->estado);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('I' . $this->linhaXlsxOks, $this->tmpAssociadoOk->cep);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('J' . $this->linhaXlsxOks, $this->tmpAssociadoOk->enderecoViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('K' . $this->linhaXlsxOks, $this->tmpAssociadoOk->complementoViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('L' . $this->linhaXlsxOks, $this->tmpAssociadoOk->bairroViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('M' . $this->linhaXlsxOks, $this->tmpAssociadoOk->cidadeViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('N' . $this->linhaXlsxOks, $this->tmpAssociadoOk->estadoViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoOks->setCellValue('O' . $this->linhaXlsxOks, $this->tmpAssociadoOk->textoAnalise); 

            $this->linhaXlsxOks++;           
        }       

        /**
         * Método que monta as células da linha do associado na planilha de não oks da análise da região xlsx        
         */
        private function __montaCelulasLinhaplanilhaAnaliseRegiaoNaoOks() {

            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('A' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->nome);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('B' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->matricula);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('C' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->endereco);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('D' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->numero);            
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('E' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->complemento);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('F' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->bairro);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('G' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->cidade);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('H' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->estado);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('I' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->cep);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('J' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->enderecoViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('K' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->complementoViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('L' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->bairroViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('M' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->cidadeViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('N' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->estadoViaCEP);
            $this->folhaAtivaplanilhaAnaliseRegiaoNaoOks->setCellValue('O' . $this->linhaXlsxNaoOks, $this->tmpAssociadoNaoOk->textoAnalise);             
            $this->linhaXlsxNaoOks++;            
        }  

        /**
         * Método que escreve a saída
         * @return boolean
         */
        public function saida() {

            $retorno = array(
                'mensagem' => '',
                'nomeXlsx' => '',
                'nomeXlsxOk' => '',
                'nomeXlsxNaoOk' => '',
                'excecoes' => array()
            );
            $arquivoGerado = false;

            if($this->__issetFpdf()) {
                                
                $this->fpdf->Output();

                return true;
            } 
            else {

                if(empty($this->associados))
                    $retorno['excecoes'][] = 'Nenhum associado encontrado.';

                if($this->__issetPlanilhaExcel()) {  

                    $caminhoXlsx = (isset($this->nomePastaRegiao) && $this->nomePastaRegiao != '') ?
                     'arquivos_saida' . DS . 'xlsxs' . DS . $this->nomePastaRegiao . DS . 'pimaco' :
                      'arquivos_saida' . DS . 'xlsxs';                  
                    
                    $nomeXlsx = $caminhoXlsx . DS . DataHoraFactory::getDataHora()->format('Y-m-d_H-i') . '.xlsx';

                    PastaSaidaXlsxRegiaoFactory::criaPastaSaidaXlsxPimaco($caminhoXlsx);

                    $this->escritorPlanilha->save($nomeXlsx);

                    $arquivoGerado = true;
                    $retorno['nomeXlsx'] = UrlHelper::obtemBaseUrl() . str_replace(DS, '/', $nomeXlsx);
                }      

                if($this->__issetPlanilhaAnaliseRegiaoNaoOks() && $this->linhaXlsxNaoOks > 2) {

                   PastaSaidaXlsxRegiaoFactory::criaPastaSaidaXlsxNaoOksRegiao($this->nomePastaRegiao);

                   $nomeXlsxNaoOk = 'arquivos_saida' . DS . 'xlsxs' . DS . $this->nomePastaRegiao . DS . 'nao_oks' . DS . DataHoraFactory::getDataHora()->format('Y-m-d_H-i') . '.xlsx';
                   $this->escritorplanilhaAnaliseRegiaoNaoOks->save($nomeXlsxNaoOk);

                   $arquivoGerado = true;
                   $retorno['nomeXlsxNaoOk'] = UrlHelper::obtemBaseUrl() . str_replace(DS, '/', $nomeXlsxNaoOk);                
                }            
                
                if($this->__issetPlanilhaAnaliseRegiaoOks() && $this->linhaXlsxOks > 2) {
                    
                    PastaSaidaXlsxRegiaoFactory::criaPastaSaidaXlsxOksRegiao($this->nomePastaRegiao);

                    $nomeXlsxOk = 'arquivos_saida' . DS . 'xlsxs' . DS . $this->nomePastaRegiao . DS . 'oks' . DS . DataHoraFactory::getDataHora()->format('Y-m-d_H-i') . '.xlsx';
                    $this->escritorplanilhaAnaliseRegiaoOks->save($nomeXlsxOk); 

                    $arquivoGerado = true;
                    $retorno['nomeXlsxOk'] = UrlHelper::obtemBaseUrl() . str_replace(DS, '/', $nomeXlsxOk);
                } 

                if($arquivoGerado)
                    $retorno['mensagem'] = 'Arquivo(s) gerado(s) com sucesso';
                else
                    $retorno['excecoes'][] = 'Nenhum arquivo gerado.';

                return $retorno;  
            }

            return false;
        }

        /**
         *Método que verifica se o objeto que trabalha com o pdf foi criado
         * @return boolean
         */
        private function __issetFpdf() {

            if(isset($this->fpdf) && is_object($this->fpdf) &&
             (new \ReflectionClass($this->fpdf))->getShortName() == 'FPDF')
                return true;

            return false;
        }

        /**
         * Método que verifica se o objeto que trabalha com a planilha excel foi criado
         * @return boolean
         */
        private function __issetPlanilhaExcel() {

            if(isset($this->planilhaExcel) && is_object($this->planilhaExcel) &&
             (new \ReflectionClass($this->planilhaExcel))->getShortName() == 'Spreadsheet')
                return true;

            return false;
        }

        /**
         * Método que verifica se o objeto que trabalha com a planilha de análise para cadastros oks foi criada
         * @return boolean
         */
        private function __issetPlanilhaAnaliseRegiaoOks() {

            if(isset($this->planilhaAnaliseRegiaoOks) && is_object($this->planilhaAnaliseRegiaoOks) &&
             (new \ReflectionClass($this->planilhaAnaliseRegiaoOks))->getShortName() == 'Spreadsheet')
                return true;              

            return false;
        }   

        /**
         * Método que verifica se o objeto que trabalha com a planilha de análise para cadastros não oks foi criada
         * @return boolean
         */
        private function __issetPlanilhaAnaliseRegiaoNaoOks() {

                if(isset($this->planilhaAnaliseRegiaoNaoOks) && is_object($this->planilhaAnaliseRegiaoNaoOks) &&
                 (new \ReflectionClass($this->planilhaAnaliseRegiaoNaoOks))->getShortName() == 'Spreadsheet')
                    return true;

                return false;
        }

        private function __issetTmpAssociado() {

            if(isset($this->tmpAssociado) && is_object($this->tmpAssociado) &&
             (new \ReflectionClass($this->tmpAssociado))->getShortName() == 'TmpAssociado')            
                return true;

            return false;
        }

        private function __issetTmpAssociadoOk() {

            if(isset($this->tmpAssociadoOk) && is_object($this->tmpAssociadoOk) &&
             (new \ReflectionClass($this->tmpAssociadoOk))->getShortName() == 'TmpAssociado')
                return true;            

            return false;
        }

        private function __issetTmpAssociadoNaoOk() {

            if(isset($this->tmpAssociadoNaoOk) && is_object($this->tmpAssociadoNaoOk) &&
             (new \ReflectionClass($this->tmpAssociadoNaoOk))->getShortName() == 'TmpAssociado')
                return true;

            return false;            
        }

        /**
         * Método "mágico" invocado quando as funções isset ou empty são chamadas para uma propriedade do objeto
         */
        public function __isset($name){

            return isset($this->$name);
        }         
    }