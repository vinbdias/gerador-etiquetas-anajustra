<?php 

    require_once(__DAO_PATH__ . 'AppDAO.class.php');

    /**
     * Classe DAO que serve de acesso aos dados referentes a regiões de TRTs
     */
    class RegiaoDAO extends AppDao {

        /**
         * Método que obtém as regiões dos TRTs
         * @param string $stringConsulta
         * @return array
         */
        public function obterRegioes() {

            $stringConsultaSql = "SELECT ID, ID_LOTACAO, LOTACAO, rowguid FROM cd_lotacao";

            try {

                $this->executaConsulta($stringConsultaSql);

                if(!isset($this->result['data']) || !is_array($this->result['data'])) {

                    throw new Exception('Não foi possível obter as regiões.');
                }

            }
            catch(Exception $e) {

                throw $e;
            }

            return $this->result['data'];                     
        }
    }