<?php 

    require_once(__ROOT__ . DS . 'services' . DS . 'ConexaoPDOFactory.class.php');
    require_once(__ROOT__ . DS . 'vendor' . DS . 'phpclasses' . DS . 'dao-for-php' . DS . 'DAO.php');

    /**
     * Classe abstrata que extende de DAO (Data Access Object) e serve como camada de acesso aos dados
     */
    abstract class AppDao extends DAO {

        /**
         * Método construtor
         */
        public function __construct() {

            //Obter conexão a partir de uma fábrica de conexões
            $this->con = ConexaoPDOFactory::obterConexao();
        }

        /**
         * Método que execulta consultas SQL
         * @param string $stringConsultaSql
         */
        public function executaConsulta($stringConsultaSql) {

            $this->executeQuery($stringConsultaSql, false);
        }

        /**
         * Método que retorna string do último comando SQL execultado pelo DAO
         * @return string
         */
        public function getStringConsultaSql() {

            return $this->query->queryString;
        }
    } 