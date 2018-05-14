<?php 
    
    //Definir a constante para o caracter separador de diretórios. ('/' em Linux e OSX '\'' Windows)
    define('DS', DIRECTORY_SEPARATOR);

    //Definir os caminhos a serem utilizados em constantes
    define('__ROOT__', str_replace(array('\\', '/'), DS, $_SERVER['DOCUMENT_ROOT']) . 'gerador-etiquetas-anajustra' . DS);

    define('__MODELS_PATH__', __ROOT__ . 'models' . DS);// /models
    define('__VENDOR_PATH__', __ROOT__ . DS . 'vendor' . DS);// /vendor
    define('__HELPERS_PATH__', __ROOT__ . DS . 'helpers' . DS);// /helpers 
    define('__SERVICES_PATH__', __ROOT__ . DS . 'services' . DS);// /services
    define('__DAO_PATH__', __ROOT__ . 'dao' . DS);// /dao    
    define('__FPDF_PATH__', __ROOT__ . DS . 'fpdf' . DS);// /fpdf
    define('__CLASS_PATH__', __ROOT__ . 'classes' . DS);// /classes