<?php
session_start();
include "../php/conexion.php";
if ($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2) {
    header("Location:../");
    exit;
}


if (!empty($_POST)) {

    if ($_POST['action'] == 'delete') {
        
        if (empty($_POST['idproveedor'])) {
            header("Location:lista_provedores.php");
            exit;
        }
        $idproveedor = $_POST['idproveedor'];
    
        $query_delete = mysqli_query($conexion, "UPDATE proveedor SET estatus = 0 WHERE codproveedor= $idproveedor");
    
        $n_query = mysqli_query($conexion, "SELECT proveedor FROM proveedor WHERE codproveedor = $idproveedor ");
        $n_result = mysqli_num_rows($n_query);
        if ($n_result > 0) {
            $data = mysqli_fetch_array($n_query);
            $user = $data['proveedor'];
        }
        
        if ($query_delete) {
            $usuario_actual = $_SESSION['nombre'];
                    $rol_actual = $_SESSION['rol_name'];
                    $date =date("H:i:s");
                    $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha eliminado el provedor $user a las $date')");
                    mysqli_close($conexion);
            echo 'success';
        }else {
            echo "Error al eliminar";
        }

        exit;
    }

    if ($_POST['action'] == 'crearUsuario') {
    
        if (empty($_POST['provedor'])|| empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
            die('code1');
        }else{
    
            $proveedor = $_POST['provedor'];
            $contacto = $_POST['contacto'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $usuario_id = $_SESSION['iduser'];
    
            $query_insert = mysqli_query($conexion, "INSERT INTO proveedor(proveedor, contacto, telefono, direccion, usuario_id) VALUES('$proveedor', '$contacto', '$telefono', '$direccion', '$usuario_id')");
    
                if ($query_insert) {
                    die('code3');
                    $usuario_actual = $_SESSION['nombre'];
                    $rol_actual = $_SESSION['rol_name'];
                    $date =date("H:i:s");
                    $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha guardado un provedor a las $date')");
                }else{
                    die('Error al eliminar el provedor');
                }
            }

        exit;
    }

    if ($_POST['action'] == 'editarUsuario') {

        if (empty($_POST['provedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
            die('code1');
        }else{
            $idproveedor = $_POST['id'];
            $proveedor = $_POST['provedor'];
            $contacto = $_POST['contacto'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            
    
            $sql_update = mysqli_query($conexion, "UPDATE proveedor SET proveedor= '$proveedor', contacto= '$contacto', telefono= '$telefono', direccion= '$direccion' WHERE codproveedor= $idproveedor"); 
                
                if ($sql_update) {
                    die('code3');
                    $usuario_actual = $_SESSION['nombre'];
                    $rol_actual = $_SESSION['rol_name'];
                    $date =date("H:i:s");
                    $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha editado el provedor $proveedor a las $date')");
                }else{
                    die('Error al editar el provedor');
                
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