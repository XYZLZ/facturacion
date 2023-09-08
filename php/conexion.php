<?php

use FontLib\Table\Type\head;

$host = "localhost";
$user = "root";
$pass = "";
$DB = "facturacion3";

// if ($host == "" || $user ==  "" || $pass == "" || $DB == "") {
//     header("Location:install.php");
// }

$conexion = mysqli_connect($host, $user, $pass, $DB);

if (!$conexion) {
    echo "error de conexion";
}


?>