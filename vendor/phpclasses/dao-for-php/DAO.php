<?php

/****************************************
 * Abstract class DAO for PHP/PDO/MySQL *
 *                                      *
 * @author tr3nt                        *
 *                                      *
 ****************************************/

abstract class DAO
{
    protected $con;
    protected $query;
    public    $result;

    public function __construct()
    {
        // MySQL config data
        // Datos MySQL de configuración
        $user = '';
        $pass = '';
        $base = '';
        $host = 'localhost';

        $this->con = new PDO("mysql:host={$host};dbname={$base};charset=utf8", $user, $pass,
                             [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
    }

    protected function execute($void = true)
    {
        // If error executing, return PDO::errorInfo()
        // Si hay un error al ejecutar, devolver PDO::errorInfo()
        if (!$this->query->execute()) {
            $error = $this->query->errorInfo();
            $this->result = [
                'error' => true,
                'data'  => $error[2]
            ];
        }
        // If void equals 'false', return array/assoc data found in db
        // Si void es 'false' devolver los registros de la base en un array asociativo
        elseif (!$void) {
            $data = $this->query->fetchAll(PDO::FETCH_ASSOC);
            // Data found, no errors
            // Datos encontrados, no hay errores
            if (count($data) > 0) {
                $this->result = [
                    'error' => false,
                    'data'  => $data
                ];
            }
            // Empty response, return error
            // No hay datos, se devuelve un error
            else {
                $this->result = [
                    'error' => true,
                    'data'  => "Data not found"
                ];
            }
        }
        // void is true, return message
        // si void es true, se devuelve mensaje
        else {
            $this->result = [
                'error' => false,
                'data'  => 'Transaction done.'
            ];
        }
    }

    // Combine methods in one
    // Combina metodos en uno solo
    protected function executeQuery($query, $void = true, $message = false)
    {
        $this->query = $this->con->prepare($query);
        $this->execute($void);
        if (!$void && $message) { $this->set_msj($message); }
    }

    // Returns custom message as long as there is no error
    // Devuelve un mensaje personalizado siempre y cuando no haya error
    protected function set_msj($msj)
    {
        if (!$this->result['error']) {
            $this->result['data'] = $msj;
        }
    }

    // Close MySQL connection
    // Cierra la conexión a MySQL
    public function close()
    {
        $this->con = null;
    }
}
