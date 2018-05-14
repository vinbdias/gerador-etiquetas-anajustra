<?php
 
    require_once(__DAO_PATH__ . 'AssociadoDAO.class.php');
    class PesquisaNomeCpf {

        private $associadoDAO = NULL;

        public function __construct() {

            $this->associadoDAO = new associadoDAO();
        }    

        public function main($stringConsulta) {

            echo json_encode($this->_pesquisaAssociadosPorNomeECpf($stringConsulta));
        }    

        private function _pesquisaAssociadosPorNomeECpf($stringConsulta) {
            
            $resultadosPesquisa = $this->associadoDAO->pesquisaTop5PorNomeOuCpf($stringConsulta);
            
            foreach ($resultadosPesquisa as $key => $resultado) {

                $associados[] = array(
                            'nome' => utf8_encode($resultado['NOME_TITULAR']),
                            'id' => $resultado['ID'],
                            'cpf' => $resultado['CPF']
                        );
            } 

            return $associados;             
        }        
    }