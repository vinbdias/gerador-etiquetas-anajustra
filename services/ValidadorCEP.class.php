<?php 

    require_once(__HELPERS_PATH__ . 'UrlHelper.class.php');
    
    /**
     * Classe que faz validação do CEP do associado
     */        
    class ValidadorCEP {

        private $cep;
        private $endereco;
        private $complemento;
        private $bairro;
        private $cidade;
        private $estado;        

        /**
         * Método construtor
         */
        function __construct() {

            $this->cep = '';
            $this->endereco = '';
            $this->complemento = '';
            $this->bairro = '';
            $this->cidade = '';
            $this->estado = '';
        }

        /**
         * Método que pesquisa o CEP através da página validar-cep.php
         * @param string $cep
         * @return json
         */
        private function _pesquisarCEP($cep) {  

            $validaCEPUrl = UrlHelper::obtemBaseUrl() . 'validar-cep.php?cep=' . $cep; 

            return json_decode(file_get_contents($validaCEPUrl));
        }

        /**
         * Método que pesquisa o CEP e armazena o endereço obtido em objeto
         * @param string $cep
         */ 
        public function validar($cep) {

            $respostaViaCep = $this->_pesquisarCEP($cep); 

            $this->_armazenaEnderecoTraduzido($respostaViaCep);
        }

        /**
         * Método que armazena o endereço obtido em resposta do viaCEP em objeto
         * @param array $respostaViaCep
         */
        private function _armazenaEnderecoTraduzido($respostaViaCep = array()) {

            $this->cep = (!empty($respostaViaCep->zipCode)) ? $respostaViaCep->zipCode : '';
            $this->endereco = (!empty($respostaViaCep->street)) ? $respostaViaCep->street : '';
            $this->complemento = (!empty($respostaViaCep->complement)) ? $respostaViaCep->complement : '';
            $this->bairro = (!empty($respostaViaCep->neighborhood)) ? $respostaViaCep->neighborhood : '';
            $this->cidade = (!empty($respostaViaCep->city)) ? $respostaViaCep->city : '';
            $this->estado = (!empty($respostaViaCep->state)) ? $respostaViaCep->state : '';    
        }

        /**
         * Método "mágico" getter
         */
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

        /**
         * Método "mágico" setter
         */
        public function __set($name, $value) {

            $this->$name = $value;
        }

        /**
         * Método "mágico" invocado quando as funções isset ou empty são chamadas para uma propriedade do objeto
         */
        public function __isset($name){

            return isset($this->$name);
        }         
    }