<?php

    set_time_limit(0);

    require_once('./paths.php');
    require_once(__CLASS_PATH__ . 'GeradorEtiquetasMain.class.php');
    
    $_JSONPOST = json_decode( file_get_contents( 'php://input' ), true );

    if(isset($_JSONPOST['regiao']) && $_JSONPOST['regiao'] > 0) {
        
        $regiaoID = $_JSONPOST['regiao'];    
    }
    elseif(isset($_REQUEST['regiao']) && $_REQUEST['regiao'] > 0) {

        $regiaoID = $_REQUEST['regiao'];
    }

    if(isset($regiaoID) && $regiaoID > 0) {

        $geradorEtiquetasMain = new GeradorEtiquetasMain('tmp_associados');
        $geradorEtiquetasMain->geraEtiquetasRegiao((int) $regiaoID);
        //$geradorEtiquetasMain->saida();     
    }
    else {

        echo json_encode(array('resposta' => 'Região não foi informada. Não foi possível gerar as etiquetas para a região.'));
    }