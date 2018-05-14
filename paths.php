<?php 

    define('DS', DIRECTORY_SEPARATOR);
    define('__ROOT__', str_replace(array('\\', '/'), DS, $_SERVER['DOCUMENT_ROOT']) . 'gerador-etiquetas-anajustra' . DS);
    define('__MODELS_PATH__', __ROOT__ . 'models' . DS);
    define('__VENDOR_PATH__', __ROOT__ . DS . 'vendor' . DS);
    define('__HELPERS_PATH__', __ROOT__ . DS . 'helpers' . DS);
    define('__SERVICES_PATH__', __ROOT__ . DS . 'services' . DS);
    define('__DAO_PATH__', __ROOT__ . 'dao' . DS);    
    define('__FPDF_PATH__', __ROOT__ . DS . 'fpdf' . DS);
    define('__CLASS_PATH__', __ROOT__ . 'classes' . DS);