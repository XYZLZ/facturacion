<?php
session_start();
include "../php/conexion.php";


if (!empty($_POST)) {

    if ($_POST['action'] == 'delete') {
        
        if ($_POST['idusuario'] == 1) {
            header("Location:lista_usuarios.php");
            mysqli_close($conexion);
            exit;
        }
    
        $idusuario = $_POST['idusuario'];
    
       // $query_delete = mysqli_query($conexion, "DELETE FROM usuario WHERE idusuario= $idusuario");
        $query_delete = mysqli_query($conexion, "UPDATE usuario SET estatus = 0 WHERE idusuario= $idusuario");
    
    
        $n_query = mysqli_query($conexion, "SELECT nombre FROM usuario WHERE idusuario = $idusuario ");
        $n_result = mysqli_num_rows($n_query);
        if ($n_result > 0) {
            $data = mysqli_fetch_array($n_query);
            $user = $data['nombre'];
        }
        if ($query_delete) {
                    $usuario_actual = $_SESSION['nombre'];
                    $rol_actual = $_SESSION['rol_name'];
                    $date =date("H:i:s");
                    $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha eliminado el usuario $user a las $date')");
                    mysqli_close($conexion);
            //header("Location:lista_usuarios.php");
            echo 'success';
        }else {
            die("Error al eliminar el usuario") ;
        }

        exit;
    }

    if ($_POST['action'] == 'crearUsuario') {
        if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave']) || empty($_POST['rol'])) {
            die('code1');
        }else{
    
            $nombre = $_POST['nombre'];
            $correo = $_POST['correo'];
            $user = $_POST['usuario'];
            $clave = md5($_POST['clave']);
            $rol = $_POST['rol'];
    
            $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario= '$user' OR correo= '$correo' ");
            
            $result = mysqli_fetch_array($query);
    
            if ($result > 0 ) {
                die('code2');
            }else{
                $query_insert = mysqli_query($conexion, "INSERT INTO usuario(nombre,correo,usuario,clave,rol) VALUES('$nombre', '$correo', '$user', '$clave', '$rol')");
               // mysqli_close($conexion);
                if ($query_insert) {
                    // $date =date("H:i:s");
                    // $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha creado un usuario a las $date')"); // accion para la tabla updates
                    die('code3');
                }else{
                    die('Error al crear el usuario');
                }
            }
        }

        exit;
    }

    if ($_POST['action'] == 'editarUsuario') {

        if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['rol'])) {
            die('code1');
        }else{
            $idusuario = $_POST['id'];
            $nombre = $_POST['nombre'];
            $correo = $_POST['correo'];
            $user = $_POST['usuario'];
            $clave = md5($_POST['clave']);
            $rol = $_POST['rol'];
    
            $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE (usuario= '$user' AND idusuario!= $idusuario) OR (correo= '$correo' AND idusuario!= $idusuario)");
            $result = mysqli_fetch_array($query);
            $result = count((array) $result);
    
            if ($result > 0 ) {
                die('code2');
            }else{
                if (empty($_POST['clave'])) {
                    $sql_update = mysqli_query($conexion, "UPDATE usuario SET nombre= '$nombre', correo= '$correo', usuario= '$user', rol= '$rol' WHERE idusuario= $idusuario"); 
                }else {
                    $sql_update = mysqli_query($conexion, "UPDATE usuario SET nombre= '$nombre', correo= '$correo', usuario= '$user', clave= '$clave', rol= '$rol' WHERE idusuario= $idusuario"); 
                }
    
                if ($sql_update) {
                    die('code3');
                    // $usuario_actual = $_SESSION['nombre'];
                    // $rol_actual = $_SESSION['rol_name'];
                    // $date =date("H:i:s");
                    // $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha editado el usuario $user a las $date')");
                }else{
                    die('Error al actualizar el usuario');
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