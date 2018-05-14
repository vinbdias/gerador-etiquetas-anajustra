<?php 
        
    require_once(__SERVICES_PATH__ . 'DataHoraFactory.class.php');

    /**
     * Classe factory que abstrai instanciação de SplFileObject para um arquivo de log
     */
    abstract class ArquivoLogFactory {

        /**
         * Método que retorna a instanciação de SplFileObject
         */
        static function getArquivoLog() {

            return new SplFileObject('arquivos_saida' . DS . 'logs' . DS . DataHoraFactory::getDataHora()->format('Y-m-d_H-i') . '.txt', 'w+');
        }
    }