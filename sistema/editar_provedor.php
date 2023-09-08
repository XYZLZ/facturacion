<?php
session_start();
if ($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2 ) {
    header("Location:../");
}
include "../php/conexion.php";

if (!empty($_POST)) {
    $msg = '';

    if (empty($_POST['provedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $msg = '<p class="danger">Todos los campos son obligatorios</p>';
    }else{
        $idproveedor = $_POST['id'];
        $proveedor = $_POST['provedor'];
        $contacto = $_POST['contacto'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        

        $sql_update = mysqli_query($conexion, "UPDATE proveedor SET proveedor= '$proveedor', contacto= '$contacto', telefono= '$telefono', direccion= '$direccion' WHERE codproveedor= $idproveedor"); 
            
            if ($sql_update) {
                $msg= '<p class="success">provedor actualizado correctamente</p>';
                $usuario_actual = $_SESSION['nombre'];
                $rol_actual = $_SESSION['rol_name'];
                $date =date("H:i:s");
                $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha editado el provedor $proveedor a las $date')");
            }else{
                $msg= '<p class="danger">Error al actualizar el provedor</p>';
            
        }
    }
    //mysqli_close($conexion);
}

    // mostrar datos

    if (empty($_REQUEST['id'])) {
        header("Location:lista_provedores.php");
        mysqli_close($conexion);
    }

    $idproveedor = $_REQUEST['id'];

    $sql = mysqli_query($conexion, "SELECT * FROM proveedor WHERE codproveedor = $idproveedor AND estatus = 1");
    mysqli_close($conexion);

    $result_sql = mysqli_num_rows($sql);

    if ($result_sql == 0) {
        header("Location:lista_provedores.php");
    }else{

        while ($data = mysqli_fetch_array($sql)) {
            $idproveedor = $data['codproveedor'];
            $proveedor = $data['proveedor'];
            $contacto = $data['contacto'];
            $telefono = $data['telefono'];
            $direccion = $data['direccion'];

        }
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include "./resourses/scripts.php"; ?>
	<title>Actualizar Provedor</title>
</head>
<body>
<div class="container">
	<?php include "./resourses/header.php"; ?>
    </div>
    <button id="menu-btn">
            <span class="material-icons-sharp nodisplay">menu</span>
        </button>
        <div class="theme-toggler">
            <span class="material-icons-sharp active nodisplay"">light_mode</span>
            <span class="material-icons-sharp nodisplay">dark_mode</span>
        </div>
	
	<section id="container">
        <div class="form_register">
        
            <form action="" method="post" class="box">
            <h1><i class="fas fa-edit"></i> Editar Provedor</h1>
                <input type="hidden" name="id" value="<?php echo $idproveedor; ?>">
                <input type="text" name="provedor" id="provedor" placeholder="Nombre del provedor" class="form_input" value="<?php echo $proveedor; ?>">
                <input type="text" name="contacto" id="contacto" placeholder="Nombre Completo del contacto" class="form_input" value="<?php echo $contacto; ?>">
                <input type="number" name="telefono" id="telefono" placeholder="Telefono" class="form_input" value="<?php echo $telefono; ?>">
                <input type="text" name="direccion" id="direccion" placeholder="Direccion Completa" class="form_input" value="<?php echo $direccion; ?>">
                
                <button type="submit" class="btn_save"><i class="fas fa-edit"></i> Actualizar provedor</button>
                <div class="alert"><?php echo isset($msg) ? $msg : ""; ?></div>
            </form>

        </div>
	</section>

	<?php include "./resourses/footer.php"; ?>
</body>
</html>