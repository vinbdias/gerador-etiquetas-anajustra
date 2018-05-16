<?php 

    require_once(__DAO_PATH__ . 'AppDAO.class.php');

    /**
     * Classe DAO que serve de acesso aos dados referentes a associados
     */
    class AssociadoDAO extends AppDao {

        /**
         * Método que obtém associados a partir de uma lista de ID's de associados, separados por vírgula
         * @param string $listaIds
         * @return array
         */
        public function obterAssociadosAPartirDeListaDeIDs($listaIds) {

            $stringConsultaSql = "SELECT ASS.ID, ASS.MATRICULA, ASS.NOME_TITULAR,  
                                         ASS.ENDERECO, ASS.NUMERO, ASS.COMPLEMENTO,
                                         ASS.BAIRRO, ASS.CIDADE, EST.SIGLA, ASS.CEP                                
                                  FROM [INTRANET_ANAJUSTRA].[dbo].ASSOCIADOS_COMPLETO ASS 
                                  INNER JOIN INTRANET_ANAJUSTRA.[dbo].cd_estado EST ON ASS.CD_UF = EST.ID_ESTADO 
                                  WHERE ASS.ID IN (".$_REQUEST['ids_associados'].")"; 

            try {

                $this->executaConsulta($stringConsultaSql);

                if(!isset($this->result['data']) || !is_array($this->result['data'])) {

                    throw new Exception('Não foi possível obter os associados.');
                }

            }
            catch(Exception $e) {

                throw $e;
            }            

            return $this->result['data'];
        }

        /**
         * Método que obtém os 5 primeiros associados encontrados a partir de uma string que é parte do nome ou CPF
         * @param string $stringConsulta
         * @return array
         */
        public function pesquisaTop5PorNomeOuCpf($stringConsulta) {

            $stringConsultaSql = "SELECT TOP(10) ASS.ID, ASS.NOME_TITULAR,ASS.CPF FROM [INTRANET_ANAJUSTRA].[dbo].ASSOCIADOS_COMPLETO ASS 
                    WHERE NOME_TITULAR LIKE '".$stringConsulta."%' OR CPF LIKE '".$stringConsulta."%'";

            try {

                $this->executaConsulta($stringConsultaSql);

                if(!isset($this->result['data']) || !is_array($this->result['data'])) {

                    throw new Exception('Não foi possível obter os associados.');
                }

            }
            catch(Exception $e) {

                throw $e;
            }

            return $this->result['data'];                     
        }

        /**
         * Método que obtém os associados de uma dada região
         * @param int $regiaoID
         * @return array
         */
        public function obterAssociadosAPartirDeRegiao($regiaoID) {

            $stringConsultaSql = "SELECT TOP (50) ASS.ID, ASS.MATRICULA, ASS.NOME_TITULAR,  
                                         ASS.ENDERECO, ASS.NUMERO, ASS.COMPLEMENTO,
                                         ASS.BAIRRO, ASS.CIDADE, EST.SIGLA, ASS.CEP                                
                                  FROM [INTRANET_ANAJUSTRA].[dbo].ASSOCIADOS_COMPLETO ASS 
                                  INNER JOIN INTRANET_ANAJUSTRA.[dbo].cd_estado EST ON ASS.CD_UF = EST.ID_ESTADO 
                                  WHERE ASS.CD_LOTACAO = " . $regiaoID;

            try {

                $this->executaConsulta($stringConsultaSql);

                if(!isset($this->result['data']) || !is_array($this->result['data'])) {

                    throw new Exception('Não foi possível obter os associados.');
                }

            }
            catch(Exception $e) {

                throw $e;
            }                                  

            return $this->result['data'];
        }

    }