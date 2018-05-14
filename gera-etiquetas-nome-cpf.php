<?php

    require_once('./paths.php');

    require_once(__MODELS_PATH__ . 'Associado.class.php');

    require_once(__VENDOR_PATH__ . 'autoload.php');        

    require_once(__HELPERS_PATH__ . 'MascaraHelper.class.php');

    require_once(__SERVICES_PATH__ . 'ValidadorCEP.class.php');
    require_once(__SERVICES_PATH__ . 'ArquivoLogFactory.class.php');

    require_once(__DAO_PATH__ . 'AssociadoDAO.class.php');

    require_once(__FPDF_PATH__ . 'fpdf.php');

    require_once(__CLASS_PATH__ . 'GeradorEtiquetasMain.class.php');

    if(isset($_REQUEST['ids_associados']) && $_REQUEST['ids_associados'] != '') {                  

        $geradorEtiquetasMain = new GeradorEtiquetasMain('xlsx');
        $geradorEtiquetasMain->geraEtiquetasNomeCpf($_REQUEST['ids_associados']);
        $geradorEtiquetasMain->saida();        
    }
    else 
        die('IDs DE ASSOCIADOS VAZIO!');