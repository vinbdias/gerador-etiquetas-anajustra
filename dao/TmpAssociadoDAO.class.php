<?php 

    require_once(__DAO_PATH__ . 'AppDAO.class.php');

    class TmpAssociadoDAO extends AppDao {

        public function limpaTabela() {

            $this->executeQuery('TRUNCATE TABLE [INTRANET_ANAJUSTRA].[dbo].tmp_associados');
        }
    }