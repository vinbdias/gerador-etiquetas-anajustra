<?php 
    
    require_once('./paths.php');

    require_once(__VENDOR_PATH__ . 'autoload.php');
    

    require_once(__SERVICES_PATH__ . 'ViaCEPService.class.php');

    if(!isset($_REQUEST['cep'])) {

        die('CEP não informado!');
    }    

    print_r(ViaCEPService::validaCEP($_REQUEST['cep']));