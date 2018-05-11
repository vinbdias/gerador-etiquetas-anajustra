<?php 
    require_once(__DIR__.'/vendor/autoload.php');
    

    require_once('services/ViaCEPService.class.php');

    if(!isset($_REQUEST['cep'])) {

        die('CEP não informado!');
    }    

    print_r(ViaCEPService::validaCEP($_REQUEST['cep']));