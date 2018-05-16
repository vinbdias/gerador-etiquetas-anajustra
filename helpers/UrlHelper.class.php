<?php 
    
    /**
     * Classe que ajuda com operações em cima de URL's
     */
    abstract class UrlHelper {

        /**
         * Método que obtém a base URL
         * @return string
         */
        public static function obtemBaseUrl() {

            $httpProtocol = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') ? 'http://' : 'https://'; 

            $pathParts = pathinfo($_SERVER['SCRIPT_FILENAME']);
            $requestUriArr = explode('?', $_SERVER['REQUEST_URI']);
            $baseUrl = $httpProtocol . $_SERVER['SERVER_NAME'] . str_replace($pathParts['basename'], '', $requestUriArr[0]);

            return $baseUrl;             
        }
    }