<?php 
    
    require_once('./paths.php');
    require_once(__DAO_PATH__ . 'RegiaoDAO.class.php');

    $resultadoRegioes = (new RegiaoDAO())->obterRegioes();

    $regioes = array();
    foreach($resultadoRegioes as $regiao) {

        $regioes[] = array('id' => $regiao['ID'], 'lotacao' => $regiao['LOTACAO']);
    }

    echo json_encode($regioes);