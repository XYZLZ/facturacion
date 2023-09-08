<?php
session_start();
include "../php/conexion.php";

if (!empty($_POST)) {

    if ($_POST['action'] == 'delete') {
        
        if (empty($_POST['idcliente'])) {
            header("Location:lista_clientes.php");
        }
        $idcliente = $_POST['idcliente'];
    
        $query_delete = mysqli_query($conexion, "UPDATE cliente SET estatus = 0 WHERE idcliente= $idcliente");
        mysqli_close($conexion);
        
        if ($query_delete) {
                    die('success');
        }else {
            die('error al eliminar');
        }

        exit;
    }

    if ($_POST['action'] == 'crearUsuario') {
        if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
            die('code1');
        }else{
    
            $nit = $_POST['nit'];
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $usuario_id = $_SESSION['iduser'];
    
            $result = 0;
    
            if (is_numeric($nit) and $nit != 0) {
                $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE nit= '$nit' ");
                $result = mysqli_fetch_array($query);
            }
    
            if ($result > 0) {
                die('code2'); // el nit ya existe
            }else{
                $query_insert = mysqli_query($conexion, "INSERT INTO cliente(nit, nombre, telefono, direccion, usuario_id) VALUES('$nit', '$nombre', '$telefono', '$direccion', '$usuario_id')");
    
                if ($query_insert) {
                    die('code3');
                }else{
                    die('error al guardar el cliente');
                }
            }
        }
            mysqli_close($conexion);

        exit;
    }

    if ($_POST['action'] == 'editarUsuario') {

        if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
            die('code1');
        }else{
            $idcliente = $_POST['id'];
            $nit = $_POST['nit'];
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            
            $result = 0;
    
            if (is_numeric($nit) and $nit != 0) {
                $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE (nit = '$nit' AND idcliente != $idcliente)");
                $result = mysqli_fetch_array($query);
                $result = count((array) $result);
            }
    
            if ($result > 0 ) {
                die('code2');
            }else{
    
                if ($nit == '') {
                    $nit = 0;
                }
    
                $sql_update = mysqli_query($conexion, "UPDATE cliente SET nit= '$nit', nombre= '$nombre', telefono= '$telefono', direccion= '$direccion' WHERE idcliente= $idcliente"); 
                
                if ($sql_update) {
                    die('code3');
                }else{
                    die('Error al actualizar el cliente');
                }
            }
        }

        exit;
    }
}

if ($_GET['action'] == 'showRows' ) {
        $query = mysqli_query($conexion, "SELECT * FROM usuario");

        if (!$query) {
            die('Rows error'. mysqli_error($conexion));
        }

        $json = array();
        while ($row = mysqli_fetch_array($query)) {
            $json[] = array(
                'idusuario' => $row['idusuario'],
                'nombre' => $row['nombre'],
                'correo' => $row['correo'],
                'usuario' => $row['usuario']
            );
        }

        $jsonString = json_encode($json);
        echo $jsonString;

        exit;
    }

?>