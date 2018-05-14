<?php		

    require_once('./paths.php');

    require_once(__CLASS_PATH__ . 'PesquisaNomeCpf.class.php');
    
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');
    header("Access-Control-Allow-Headers: X-Requested-With");
    header('Content-Type: application/json; charset=utf-8');

    $associados = array();

    if(isset($_REQUEST['query']) && $_REQUEST['query'] != '') {

        $pesquisaNomeCpf = new PesquisaNomeCpf();   
        $pesquisaNomeCpf->main($_REQUEST['query']);
    }    	   

