<?php 
    
    /**
     * Classe que ajuda com operações em cimas de valores string
     */
    abstract class StringHelper {

        /**
         * Método que formata uma dada string, removendo espaços duplos e deixando apenas a primeira letra de cada palavra em caixa alta
         * @param string $string
         * @return string
         */
        public static function limpaEspacosDuplosCaixaAlta($string) {

            return self::limpaEspacosDuplosEInicioFim(self::caixaAltaApenasPrimeiraLetra($string));
        }

        /**
         * Formata uma string de forma a deixar apenas as primeiras letras das palavras em caixa alta
         * @param string $string
         * @return string
         */
        public static function caixaAltaApenasPrimeiraLetra($string) {

            return ucwords(mb_strtolower(($string)));
        }

        /**
         * Remove espaços duplos e do início e fim de uma string
         * @param string $string
         * @return string
         */
        public static function limpaEspacosDuplosEInicioFim($string) {

            return trim(str_replace('  ', ' ', $string));
        }

        /**
         * Método que limpa string de caracteres especiais de uma dada string
         * @param string $string
         * @return string
         */
        public static function limpaCaracteresEspeciais($string) {

            $chars = array(
                'À'=>'A','Á'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A','Å'=>'A',
                'Æ'=>'A','Ç'=>'C','È'=>'E','É'=>'E','Ê'=>'E','Ë'=>'E',
                'Ì'=>'I','Í'=>'I','Î'=>'I','Ï'=>'I','Ð'=>'Dj','Ñ'=>'N',
                'Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O','Ø'=>'O',
                'Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','Þ'=>'B',
                'ß'=>'Ss','à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a',
                'å'=>'a','æ'=>'a','ç'=>'c','è'=>'e','é'=>'e','ê'=>'e',
                'ë'=>'e','ì'=>'i','í'=>'i','î'=>'i','ï'=>'i','ð'=>'o',
                'ñ'=>'n','ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
                'ø'=>'o','ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u','ý'=>'y',
                'þ'=>'b','ÿ'=>'y','Ă'=>'A','ă'=>'a','Ń'=>'N','ń'=>'n',
                'Š'=>'S','š'=>'s','Ž'=>'Z','ž'=>'z','ƒ'=>'f','Ș'=>'S',
                'ș'=>'s','Ț'=>'T','ț'=>'t', 'ª' => 'a', 'º'=> 'o'
            );

            return strtr($string, $chars);            
        }

        /**
         * Método que formata uma dada string para utilização em nomeação de arquivo
         * @param string $string
         * @return string
         */
        public static function formataParaNomeDeArquivo($string) {

            return strtolower(self::limpaCaracteresEspeciaisEEspaços($string));
        }

        /**
         * Método que limpa caracteres especiais e espaços de uma dada string
         * @param string $string
         * @return string
         */
        public static function limpaCaracteresEspeciaisEEspaços($string) {

            return str_replace(' ', '_', trim(self::limpaCaracteresEspeciais($string)));
        } 

        /**
         * Método que formata um dados valor de acordo com uma dada máscara
         * @param string $valor
         * @param string $mascara (i.e.: (##) #####-####, ###.###.###-##)
         * @return string
         */
        public static function formataMascara($valor, $mascara) {

            $mascarado = '';

            $valor = str_replace(array(' ', '-', ',', '.'), '', $valor);

            $k = 0;
            for($i = 0; $i <= strlen($mascara)-1; $i++) {

                if($mascara[$i] == '#') {

                    if(isset($valor[$k]))
                        $mascarado .= $valor[$k++];
                }
                elseif(isset($mascara[$i])) {
                    
                    $mascarado .= $mascara[$i];
                }
            }

            return $mascarado;
        }         
    }