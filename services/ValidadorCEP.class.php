<?php 
        
    class ValidadorCEP {

        private $cep;
        private $endereco;
        private $complemento;
        private $bairro;
        private $cidade;
        private $estado;

        function __construct() {

            $this->cep = '';
            $this->endereco = '';
            $this->complemento = '';
            $this->bairro = '';
            $this->cidade = '';
            $this->estado = '';
        }

        private function _pesquisarCEP($cep) {  

            $httpProtocol = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') ? 'http://' : 'https://'; 

            $pathParts = pathinfo($_SERVER['SCRIPT_FILENAME']);
            $requestUriArr = explode('?', $_SERVER['REQUEST_URI']);
            $validaCEPUrl = $httpProtocol . $_SERVER['SERVER_NAME'] . str_replace($pathParts['basename'], '', $requestUriArr[0]) . 'validar-cep.php?cep=' . $cep; 

            return json_decode(file_get_contents($validaCEPUrl));
        }

        public function validar($cep) {

            $respostaViaCep = $this->_pesquisarCEP($cep); 

            $this->_armazenaEnderecoTraduzido($respostaViaCep);
        }

        private function _armazenaEnderecoTraduzido($respostaViaCep = array()) {

            $this->cep = (!empty($respostaViaCep->zipCode)) ? $respostaViaCep->zipCode : '';
            $this->endereco = (!empty($respostaViaCep->street)) ? $respostaViaCep->street : '';
            $this->complemento = (!empty($respostaViaCep->complement)) ? $respostaViaCep->complement : '';
            $this->bairro = (!empty($respostaViaCep->neighborhood)) ? $respostaViaCep->neighborhood : '';
            $this->cidade = (!empty($respostaViaCep->city)) ? $respostaViaCep->city : '';
            $this->estado = (!empty($respostaViaCep->state)) ? $respostaViaCep->state : '';    
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