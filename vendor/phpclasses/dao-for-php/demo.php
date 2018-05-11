<?php

require_once 'DAO.php';

// Your own class
// Tu clase propia
class UserDao extends DAO
{
    public function get_user($id)
    {
        // if void equals 'false', it will return data from query
        // si void es igual a 'false', devolverá los datos del query
        $this->executeQuery("SELECT * FROM users WHERE id = '{$id}'", false);
    }
    public function save_user($name)
    {
        // Set void to true and set custom success message to save data
        // Cambiar void a true y crear un mensaje de éxito para guardar datos
        $this->executeQuery("INSERT INTO users (name) VALUES ('{$name}')", true, 'User saved successfully!');        
    }
}

$user = new UserDao;

// Get user with id = 1
$user->get_user(1);

if (!$user->result['error']) {
    /* If correct, print name of user. Index 0 means get first element of array response
       Si no hay error imprimir el nombre del user
       El índice 0 indica que se obtiene el primer elemento del array de respuesta */
    echo $user->result['data'][0]['name'];
}
else {
    // If error, print error message
    // Si hay error imprimir el mensaje
    echo $user->result['data'];
}

// Close MySQL connection
// Cierra la conexión a MySQL
$user->close();