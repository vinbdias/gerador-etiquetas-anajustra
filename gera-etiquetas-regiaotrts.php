<?php

    set_time_limit(0);

    require_once('./paths.php');
    require_once(__CLASS_PATH__ . 'GeradorEtiquetasMain.class.php');
    require_once(__HELPERS_PATH__ . 'UrlHelper.class.php');

    const teste = false;

    $_JSONPOST = json_decode( file_get_contents( 'php://input' ), true );

    if(isset($_JSONPOST['regiao']['id']) && $_JSONPOST['regiao']['id'] > 0) {
        
        $regiaoID = $_JSONPOST['regiao']['id'];    
    }
    elseif(isset($_REQUEST['regiao']) && $_REQUEST['regiao'] > 0) {

        $regiaoID = $_REQUEST['regiao'];
    }

    if(isset($_JSONPOST['tiposSaida']) && $_JSONPOST['tiposSaida'] > 0) {
        
        $tiposSaida = $_JSONPOST['tiposSaida'];    
    }
    elseif(teste) {

        $tiposSaida = array(
            0 => array('id' => 'xlsx', 'valor' => 0),
            1 => array('id' => 'xlsxOks', 'valor' => 0),
            2 => array('id' => 'xlsxNaoOks', 'valor' => 1)
        );
    }   

    if(isset($regiaoID) && $regiaoID > 0) {

        $geradorEtiquetasMain = new GeradorEtiquetasMain(array_map(function($saida) {
            return ($saida['valor']) ? $saida['id'] : null;            
        }, $tiposSaida), true);

        $geradorEtiquetasMain->geraEtiquetasRegiao((int) $regiaoID);
        echo json_encode($geradorEtiquetasMain->saida());        
    }
    else {

        echo json_encode(array('resposta' => false));
    }