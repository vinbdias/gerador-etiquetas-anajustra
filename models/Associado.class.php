<?php 

    class Associado {

        private $id;
        private $nome;
        private $endereco;
        private $numero;
        private $complemento;
        private $bairro;
        private $cidade;
        private $estado;
        private $sigla;
        private $cep;

        public function __construct($atributos) {

            foreach ($atributos as $chave => $valor) {
                
                $chave = strtolower($chave);

                if($chave == 'sigla') {

                    $this->estado = $valor;
                }
                elseif($chave == 'nome_titular') {

                    $this->nome = trim(str_ireplace(array('pensionista', 'pencionista', '(', ')'), '', $valor));
                }
                else 
                    $this->$chave = $valor;
            }
        }

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

        public function __get($name) {

            if(isset($this->$name)) {

                return $this->$name;
            }

            $trace = debug_backtrace();
            trigger_error(
                'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
            return null;
        }

        public function __set($name, $value) {

            $this->$name = $value;
        }        
    }