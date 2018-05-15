<?php 
    
    require_once(__MODELS_PATH__ . 'Model.class.php');

    /**
     * Classe modelo de TmpAssociado
     */
    class TmpAssociado extends Model {

        private $nome;
        private $matricula;
        private $endereco;
        private $numero;
        private $complemento;
        private $bairro;
        private $cidade;
        private $estado;
        private $cep;

        private $enderecoViaCEP;
        private $complementoViaCEP;
        private $bairroViaCEP;
        private $cidadeViaCEP;
        private $estadoViaCEP;
        private $regiaoID;
        private $textoAnalise;

        public function __construct($associado = NULL, $validadorCEP = NULL) {

            if(isset($associado))
                $this->__carregaDadosAssociado($associado);

            if(isset($validadorCEP))
                $this->__carregaDadosViaCep($validadorCEP);
        }

        private function __carregaDadosAssociado($associado) {


        }

        private function __carregaDadosViaCep($validadorCEP) {


        }
    }