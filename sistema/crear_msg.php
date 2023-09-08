<?php
    session_start();
include "../php/conexion.php";

if (isset($_POST['enviar'])) 
{
    $msg = '';
	if(!empty($_POST['para']) && !empty($_POST['asunto']) && !empty($_POST['texto']))
	{
		$fecha = date("j/m/Y, g:i a");
        $userRol = $_SESSION['rol_name'];
		$sql = "INSERT INTO mensaje (para,de,fecha,rol,asunto,texto) VALUES ('".$_POST['para']."','".$_SESSION['user']."','".$fecha."','".$userRol."','".$_POST['asunto']."','".$_POST['texto']."')";
		mysqli_query($conexion, $sql);
		$msg= '<p class="success">Mensaje enviado correctamente</p>';
	}else{
        $msg = '<p class="danger">Todos los campos son obligatorios</p>';
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
	<title>Nuevo Mensaje</title>
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
            <h1><i class="fas fa-user"></i> Nuevo Mensaje</h1>
                <input type="text" name="para" id="para" placeholder="Nombre del destinatario" class="form_input">
                <input type="text" name="asunto" id="asunto" placeholder="Asunto" class="form_input">
                <textarea name="texto" id="detalle" placeholder="Mensaje"></textarea>
                
                <button type="submit" name="enviar" class="btn_save"><i class="fas fa-save"></i> Enviar</button>
                <div class="alert"><?php echo isset($msg) ? $msg : ''; ?></div>
            </form>
        </div>

	<?php include "./resourses/footer.php"; ?>
</body>
</html>