<?php 
    
    /**
     * Classe que auxilia na formatação de strings em uma dada máscara
     */
    abstract class MascaraHelper {

        /**
         * Método que formata um dados valor de acordo com uma dada máscara
         * @param string $valor
         * @param string $mascara (i.e.: (##) #####-####, ###.###.###-##)
         * @return string
         */
        static function formataMascara($valor, $mascara) {

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