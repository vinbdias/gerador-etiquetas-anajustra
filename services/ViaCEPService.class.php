<?php     

    use FlyingLuscas\ViaCEP\ZipCode;
    
    /**
     * Classe abstrata que abstrai os aspectos e disponibiliza os serviços de FlyingLuscas\ViaCEP\ZipCode
     */
    abstract class ViaCEPService {

        static function validaCEP($cep) {

            $viaCEP = new ZipCode;

            return $viaCEP->find($cep)->toJson();
        }
        
        static private function _pesquisarCEP($cep) {  

            $httpProtocol = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') ? 'http://' : 'https://'; 

            $pathParts = pathinfo($_SERVER['SCRIPT_FILENAME']);
            $requestUriArr = explode('?', $_SERVER['REQUEST_URI']);
            $validaCEPUrl = $httpProtocol . $_SERVER['SERVER_NAME'] . str_replace($pathParts['basename'], '', $requestUriArr[0]) . 'validar-cep.php?cep=' . $cep;             

            return json_decode(file_get_contents($validaCEPUrl));
        }

        static function ehValido($cep) {

            $respostaViaCep = self::_pesquisarCEP($cep);

            if(!isset($respostaViaCep->ibge) || trim($respostaViaCep->ibge) == '') {

                return false;
            }

            return true;
        }        
    }