<?php

    set_time_limit(0);

    require_once('./paths.php');
    require_once(__CLASS_PATH__ . 'GeradorEtiquetasMain.class.php');
    require_once(__HELPERS_PATH__ . 'UrlHelper.class.php');
    
    $_JSONPOST = json_decode( file_get_contents( 'php://input' ), true );

    if(isset($_JSONPOST['regiao']) && $_JSONPOST['regiao'] > 0) {
        
        $regiaoID = $_JSONPOST['regiao'];    
    }
    elseif(isset($_REQUEST['regiao']) && $_REQUEST['regiao'] > 0) {

        $regiaoID = $_REQUEST['regiao'];
    }




    if(isset($regiaoID) && $regiaoID > 0) {

        $geradorEtiquetasMain = new GeradorEtiquetasMain(array('xlsx', 'xlsxNaoOks'), true);
        $geradorEtiquetasMain->geraEtiquetasRegiao((int) $regiaoID);
        echo json_encode($geradorEtiquetasMain->saida());        
    }
    else {

        echo json_encode(array('resposta' => false));
    }