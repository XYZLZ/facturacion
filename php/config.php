<?php 

define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');

$prueba = mysqli_connect("localhost", 'root', '', 'hola') or die('problemas con la conexion');

if ($prueba) {
    header('Location:install.php');
}

?>