<?php 
    
    require_once(__ROOT__ . DS . 'services' . DS . 'DataHoraFactory.class.php');

    abstract class ArquivoLogFactory {

        static function getArquivoLog() {

            return new SplFileObject('arquivos_saida' . DS . DataHoraFactory::getDataHora()->format('Y-m-d_H-i') . '.txt', 'w+');
        }
    }