<?php 

    require_once(__ROOT__ . DS . 'services' . DS . 'ConexaoPDOFactory.class.php');
    require_once(__ROOT__ . DS . 'vendor' . DS . 'phpclasses' . DS . 'dao-for-php' . DS . 'DAO.php');

    abstract class AppDao extends DAO {

        public function __construct() {

            $this->con = ConexaoPDOFactory::obterConexao();
        }

        public function executaConsulta($stringConsultaSql) {

            $this->executeQuery($stringConsultaSql, false);
        }

        public function getStringConsultaSql() {

            return $this->query->queryString;
        }
    } 