<?php
    error_reporting(1);
    ini_set('display_errors', '1');

    define('DS', DIRECTORY_SEPARATOR);
    define('__ROOT__', str_replace(array('\\', '/'), DS, $_SERVER['DOCUMENT_ROOT']) . 'gerador-etiquetas' . DS);

    require_once(__ROOT__ . DS . 'classes' . DS . 'Associado.class.php');

    require_once(__ROOT__ . DS . 'vendor' . DS . 'autoload.php');    
    //require_once(__ROOT__ . DS . 'vendor' . DS . 'neitanod' . DS . 'forceutf8' . DS . 'src' . DS . 'ForceUTF8' . DS . 'Encoding.php');    

    require_once(__ROOT__ . DS . 'helpers' . DS . 'MascaraHelper.class.php');

    require_once(__ROOT__ . DS . 'services' . DS . 'ValidadorCEP.class.php');
    require_once(__ROOT__ . DS . 'services' . DS . 'ArquivoLogFactory.class.php');

    require_once(__ROOT__ . 'dao' . DS . 'AssociadoDAO.class.php');

    require_once(__ROOT__ . DS . 'fpdf' . DS . 'fpdf.php');

    require_once(__ROOT__ . DS . 'classes' . DS . 'GeradorEtiquetasMain.class.php');


    $logDepuracao = ArquivoLogFactory::getArquivoLog();
    

    if(isset($_REQUEST['ids_associados']) && $_REQUEST['ids_associados'] != '') {

        $associadoDAO = new AssociadoDAO();
        $associados = $associadoDAO->obterAssociadosAPartirDeListaDeIDs($_REQUEST['ids_associados']);

        
        $logDepuracao = ArquivoLogFactory::getArquivoLog();                   

        $geradorEtiquetasMain = new GeradorEtiquetasMain('fpdf');


        $log_string = 'CONSULTA SQL EXECUTADA: ' . $associadoDAO->getStringConsultaSql() . PHP_EOL . PHP_EOL;

        //MONTA A ARRAY PARA ETIQUETAS       
        foreach($associados as $key => $associado) {

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

                $log_string .= 'ETIQUETA NAO GERADA! CEP ' . $cep . ' NAO FOI VALIDADO NOS CORREIOS (https://viacep.com.br/)' . PHP_EOL . PHP_EOL;
                continue;
            }           

            $bairro_cidade_estado = NULL;
            if(isset($associado['BAIRRO']) && $associado['BAIRRO'] != '') {

                $log_string .= 'Associado com cadastro na tabela funcional.' . PHP_EOL;

                $log_string .= 'INICIANDO análise/comparação do endereço cadastrado com o obtido a partir do CEP nos Correios' . PHP_EOL;
                $analise_comparacao = $associadoObj->compararEnderecoCadastradoComValidadoNosCorreios($validadorCEP);
                $log_string .= $analise_comparacao;
                $log_string .= 'Análise/comparação FINALIZADA' . PHP_EOL;

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
            

            $geradorEtiquetasMain->montaEtiquetaFPDF($nome, $cep, $ende, $bairro_cidade_estado);

            $log_string .= 'Etiqueta gerada.' . PHP_EOL . PHP_EOL;
        }

        $logDepuracao->fwrite($log_string);

        sqlsrv_free_stmt( $stmt);
        sqlsrv_close( $conn);

        $geradorEtiquetasMain->saida();        
    }

    die('IDs DE ASSOCIADOS VAZIO!');