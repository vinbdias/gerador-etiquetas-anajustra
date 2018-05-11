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
            
            return json_decode(file_get_contents('http://localhost/gerador-etiquetas/validar-cep.php?cep=' . $cep));
        }

        public function validar($cep) {

            $respostaViaCep = $this->_pesquisarCEP($cep);

            $this->_armazenaEnderecoTraduzido($respostaViaCep);
        }

        private function _armazenaEnderecoTraduzido($respostaViaCep) {

            $this->cep = $respostaViaCep->zipCode;
            $this->endereco = $respostaViaCep->street;
            $this->complemento = $respostaViaCep->complement;
            $this->bairro = $respostaViaCep->neighborhood;
            $this->cidade = $respostaViaCep->city;
            $this->estado = $respostaViaCep->state;          

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