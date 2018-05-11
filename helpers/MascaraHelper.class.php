<?php 

    abstract class MascaraHelper {

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