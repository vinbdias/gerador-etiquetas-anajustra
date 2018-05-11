<?php 

    require_once(__ROOT__ . DS . 'dao' . DS . 'AppDAO.class.php');

    class AssociadoDAO extends AppDao {

        public function obterAssociadosAPartirDeListaDeIDs($listaIds) {

            $stringConsultaSql = "SELECT TOP(5) ASS.ID, ASS.MATRICULA, ASS.NOME_TITULAR,  
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
    }