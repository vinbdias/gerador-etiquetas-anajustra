<?php 
        
    require_once(__SERVICES_PATH__ . 'DataHoraFactory.class.php');

    /**
     * Classe factory que abstrai instanciação de SplFileObject para um arquivo de log
     */
    abstract class PastaSaidaXlsxRegiaoFactory {

        /**
         * Método que cria a pasta para o arquivo pimaco de etiquetas de envio de uma região
         */
        public static function criaPastaSaidaXlsxPimaco($caminhoXlsx) {

            if(!file_exists($caminhoXlsx))
                mkdir($caminhoXlsx , 0777, true);
        }        

        /**
         * Método que cria a pasta de arquivos para cadastros não oks de uma região
         */
        public static function criaPastaSaidaXlsxOksRegiao($nomePastaRegiao) {

            if(!file_exists('arquivos_saida' . DS . 'xlsxs' . DS . $nomePastaRegiao . DS . 'oks'))
                mkdir('arquivos_saida' . DS . 'xlsxs' . DS . $nomePastaRegiao . DS . 'oks', 0777, true);

            //return new SplFileObject('arquivos_saida' . DS . 'xlsxs' . DS . $nomePastaRegiao . DS . 'oks' . DS . DataHoraFactory::getDataHora()->format('Y-m-d_H-i') . '.txt', 'w+');
        }

        /**
         * Método que cria a pasta de arquivos para cadastros não oks de uma região
         */
        public static function criaPastaSaidaXlsxNaoOksRegiao($nomePastaRegiao) {

            if(!file_exists('arquivos_saida' . DS . 'xlsxs' . DS . $nomePastaRegiao . DS . 'nao_oks'))
                mkdir('arquivos_saida' . DS . 'xlsxs' . DS . $nomePastaRegiao . DS . 'nao_oks', 0777, true);         

            //return new SplFileObject('arquivos_saida' . DS . 'xlsxs' . DS . $nomePastaRegiao . DS . 'nao_oks' . DS . DataHoraFactory::getDataHora()->format('Y-m-d_H-i') . '.txt', 'w+');
        }        
    }