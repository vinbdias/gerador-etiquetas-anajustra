<?php

    set_time_limit(0);

    require_once('./paths.php');

    require_once(__CLASS_PATH__ . 'GeradorEtiquetasMain.class.php');

    if(isset($_REQUEST['ids_associados']) && $_REQUEST['ids_associados'] != '') {                  

        $geradorEtiquetasMain = new GeradorEtiquetasMain(array('pdf'), true);
        $geradorEtiquetasMain->geraEtiquetasNomeCpf($_REQUEST['ids_associados']);
        $geradorEtiquetasMain->saida();        
    }
    else 
        die('IDs DE ASSOCIADOS VAZIO!');