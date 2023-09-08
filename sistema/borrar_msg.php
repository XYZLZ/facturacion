<?php

session_start();
include "../php/conexion.php";

$id = $_GET['id'];

$query = mysqli_query($conexion, "DELETE FROM mensaje WHERE para='".$_SESSION['user']."' and ID='".$id."'");
//mysqli_close($conexion);

if ($query) {
    header("Location:msg_dashboard.php");
}

?>