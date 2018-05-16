<?php 
    
    /**
     * Classe modelo de TmpAssociado
     */
    class TmpAssociado {

        private $nome = '';
        private $matricula = '';
        private $endereco= '';
        private $numero = '';
        private $complemento = '';
        private $bairro = '';
        private $cidade = '';
        private $estado = '';
        private $cep = '';

        private $enderecoViaCEP = '';
        private $complementoViaCEP = '';
        private $bairroViaCEP = '';
        private $cidadeViaCEP = '';
        private $estadoViaCEP = '';

        private $textoAnalise = '';

        public function __construct($associado, $validadorCEP = NULL, $textoAnalise) {

            if(isset($textoAnalise) && $textoAnalise != '')
                $this->textoAnalise = $textoAnalise;

            if(isset($associado))
                $this->__carregaDadosAssociado($associado);

            if(isset($validadorCEP))
                $this->__carregaDadosViaCep($validadorCEP);
        }

        private function __carregaDadosAssociado($associado) {

            $this->nome = $associado->nome;
            $this->matricula = $associado->matricula;
            $this->endereco = $associado->endereco;
            $this->numero = $associado->numero;
            $this->complemento = $associado->complemento;
            $this->bairro = $associado->bairro;
            $this->cidade = $associado->cidade;
            $this->estado = $associado->estado;
            $this->cep = $associado->cep;
        }

        private function __carregaDadosViaCep($validadorCEP) {

            $this->enderecoViaCEP = $validadorCEP->endereco;
            $this->complementoViaCEP = $validadorCEP->complemento;
            $this->bairroViaCEP = $validadorCEP->bairro;
            $this->cidadeViaCEP = $validadorCEP->cidade;
            $this->estadoViaCEP = $validadorCEP->estado;            
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