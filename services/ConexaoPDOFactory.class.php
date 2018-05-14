<?php 

    final class ConexaoPDOFactory {

        private static $conexao;

        private static $arquivo = __DIR__ . DS . 'configdb.ini';

        /**
         * Singleton: Método construtor privado para impedir classe de gerar instâncias
         *
         */
        private function __construct() {}
     
        private function __clone() {}
     
        private function __wakeup() {} 

        /**
         * Método estático privado que permite o carregamento do arquivo
         * @param $arquivo string
         * @return array
         */
        private static function __carregarArquivoDeConfiguracao($arquivo) {

            if(file_exists($arquivo)) {

                $dados = parse_ini_file($arquivo);
            } else {

                throw new Exception('Erro: Arquivo não encontrado');
            }

            return $dados;
        }

        /**
        * Método montar string de conexao e gerar o objeto PDO
        * @param $dados array
        * @return PDO
        */
        private static function __montarStringDeConexao($dados) {
            // capturar dados
            $sgdb     = isset($dados['sgdb']) ? $dados['sgdb'] : NULL;
            $usuario  = isset($dados['usuario']) ? $dados['usuario'] : NULL;
            $senha    = isset($dados['senha']) ? $dados['senha'] : NULL;
            $banco    = isset($dados['banco']) ? $dados['banco'] : NULL;
            $servidor = isset($dados['servidor']) ? $dados['servidor'] : NULL;
            $porta    = isset($dados['porta']) ? $dados['porta'] : NULL;

            if(!is_null($sgdb)) {
                // selecionar banco - criar string de conexão
                switch (strtoupper($sgdb)) {

                    case 'MYSQL': 
                        $porta = isset($porta) ? $porta : 3306 ; 
                        return new PDO("mysql:host={$servidor};port={$porta};dbname={$banco}", $usuario, $senha);
                        break;

                    case 'SQLSRV': 
                        $porta = isset($porta) ? $porta : 1433 ;
                        return new PDO("sqlsrv:Server={$servidor},{$porta};Database={$banco}", $usuario, $senha);
                        break;                        

                    case 'MSSQL': 
                        $porta = isset($porta) ? $porta : 1433 ;
                        return new PDO("mssql:host={$servidor},{$porta};dbname={$banco}", $usuario, $senha);
                        break;

                    case 'PGSQL': $porta = isset($porta) ? $porta : 5432 ;
                        return new PDO("pgsql:dbname={$banco}; user={$usuario}; password={$senha}, host={$servidor};port={$porta}");
                        break;

                    case 'SQLITE': 
                        return new PDO("sqlite:{$banco}");
                        break;

                    case 'OCI8': 
                        return new PDO("oci:dbname={$banco}", $usuario, $senha);
                        break;

                    case 'FIREBIRD': 
                        return new PDO("firebird:dbname={$banco}",$usuario, $senha);
                        break;
                }
            } 
            else {

                throw new Exception('Erro: tipo de banco de dados não informado');
            }
        }

        /**
         * Método estático que devolve a instancia ativa
         *
         */
        public static function obterConexao($arquivo = '') {

            $arquivo = (!empty($arquivo)) ? $arquivo : self::$arquivo;

            if(self::$conexao == NULL) {

               // Receber os dados do arquivo
               self::$conexao = self::__montarStringDeConexao(self::__carregarArquivoDeConfiguracao($arquivo));
               self::$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
               //self::$conexao->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
               //self::$conexao->exec("set names utf8");
            }

            return self::$conexao;
        }        
    }