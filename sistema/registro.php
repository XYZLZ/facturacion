<?php
    session_start();
    if ($_SESSION['rol'] != 1) {
        header("Location:../");
    }
include "../php/conexion.php";

if (!empty($_POST)) {
    $msg = '';
    if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave']) || empty($_POST['rol'])) {
        $msg = '<p class="danger">Todos los campos son obligatorios</p>';
    }else{

        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $user = $_POST['usuario'];
        $clave = md5($_POST['clave']);
        $rol = $_POST['rol'];

        $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario= '$user' OR correo= '$correo' ");
        
        $result = mysqli_fetch_array($query);

        if ($result > 0 ) {
            $msg= '<p class="warning">El correo o usuario ya existe</p>';
        }else{
            $query_insert = mysqli_query($conexion, "INSERT INTO usuario(nombre,correo,usuario,clave,rol) VALUES('$nombre', '$correo', '$user', '$clave', '$rol')");
           // mysqli_close($conexion);
            if ($query_insert) {
                $msg= '<p class="success">Usuario creado correctamente</p>';
                $usuario_actual = $_SESSION['nombre'];
                $rol_actual = $_SESSION['rol_name'];
                $date =date("H:i:s");
                $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha creado un usuario a las $date')"); // accion para la tabla updates
            }else{
                $msg= '<p class="danger">Error al crear el usuario</p>';
            }
        }
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
	<title>Registro Usuario</title>
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

            <form action="" method="post" class="box">
            <h1><i class="fas fa-user-plus"></i> Registro Usuario</h1>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo" class="form_input">
                <input type="email" name="correo" id="correo" placeholder="Correo electronico" class="form_input">
                <input type="text" name="usuario" id="usuario" placeholder="Usuario" class="form_input">
                <input type="password" name="clave" id="clave" placeholder="Clave de acceso" class="form_input">
                <?php 
                    $query_rol = mysqli_query($conexion, "SELECT * FROM rol");
                    mysqli_close($conexion);
                    $result_rol  = mysqli_num_rows($query_rol);
                ?>
                <select name="rol" id="rol" class="select">
                    <?php
                    
                    if ($result_rol > 0){
                        while ($rol = mysqli_fetch_array($query_rol)) {
                    ?>
                    <option value="<?php echo $rol['idrol']; ?>"><?php echo $rol['rol']; ?></option>

                    <?php 

                        
                        }
                    }
                    ?>
                    
                </select>
                <button type="submit" class="btn_save"><i class="fas fa-save"></i> Crear Usuario</button>
                <div class="alert"><?php echo isset($msg) ? $msg : ''; ?></div>
            </form>
            

	<?php include "./resourses/footer.php"; ?>
</body>
</html>