<?php     

    use FlyingLuscas\ViaCEP\ZipCode;
    
    abstract class ViaCEPService {

        static function validaCEP($cep) {

            $viaCEP = new ZipCode;

            return $viaCEP->find($cep)->toJson();
        }
        
        static private function _pesquisarCEP($cep) {        
            
            return json_decode(file_get_contents('http://localhost/gerador-etiquetas/validar-cep.php?cep=' . $cep));
        }

        static function ehValido($cep) {

            $respostaViaCep = self::_pesquisarCEP($cep);

            if(!isset($respostaViaCep->ibge) || trim($respostaViaCep->ibge) == '') {

                return false;
            }

            return true;
        }        
    }