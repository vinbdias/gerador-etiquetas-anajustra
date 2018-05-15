<?php 

    require_once(__MODELS_PATH__ . 'Model.class.php');

    /**
     * Classe modelo de Associado
     */
    class Associado extends Model {

        private $id;
        private $nome;
        private $matricula;
        private $endereco;
        private $numero;
        private $complemento;
        private $bairro;
        private $cidade;
        private $estado;
        private $sigla;
        private $cep;

        /**
         * Método construtor
         * @param Array $atributos
         */
        public function __construct($atributos) {

            foreach ($atributos as $chave => $valor) {
                
                $chave = strtolower($chave);

                if($chave == 'sigla')
                    $this->estado = trim($valor);
                elseif($chave == 'nome_titular')
                    $this->nome = trim(str_ireplace(array('pensionista', 'pencionista', '(', ')'), '', $valor));                
                else 
                    $this->$chave = trim($valor);
            }

            if(!empty($this->numero)) {

                $this->endereco =  str_replace(array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0'), '', $this->endereco);
            }

        }

        /**
         * Método que analisa e compara o endereço cadastrado do associado com o retornado a partir da validação do CEP
         * @param services/ValidadorCEP
         * @return string
         */
        public function compararEnderecoCadastradoComValidadoNosCorreios($validadorCEP) {

            $string_analise = '';

            $reflexoValidadorCEP = new ReflectionClass($validadorCEP);
            foreach($reflexoValidadorCEP->getProperties() as $key => $value) {

                $value->setAccessible(true);

                $atributoEndereco = $value->getName();
                $valor = $value->getValue($validadorCEP);

                if($atributoEndereco == 'cep') continue;

                if(mb_strtolower ($this->$atributoEndereco) == mb_strtolower ($valor)) {

                    $string_analise .= 'O atributo "' . $atributoEndereco . '", do endereco cadastrado do associado ' .
                        'confere com o validado a partir do CEP nos correios.' . PHP_EOL;
                }                
                else {

                    $string_analise .= 'O ATRIBUTO "' . strtoupper($atributoEndereco) . '", DO ENDERECO CADASTRADO DO ASSOCIADO ' 
                    . 'NAO CONFERE COM O VALIDADO A PARTIR DO CEP NOS CORREIOS.' . PHP_EOL;
                }

                $string_analise .= 'BASE DE DADOS: ' . $this->$atributoEndereco . ' => ' . 'CORREIOS: ' . $valor . PHP_EOL;
            }


            return $string_analise;
        }
    }