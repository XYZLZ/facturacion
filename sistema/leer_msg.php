<?php

session_start();
include "../php/conexion.php";

$id = $_GET['id'];
$sql = "SELECT * FROM mensaje WHERE para='".$_SESSION['user']."' and ID='".$id."'";
$res = mysqli_query($conexion, $sql) or die("error");
$data = mysqli_fetch_assoc($res);


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include "./resourses/scripts.php"; ?>
    <!-- <link rel="stylesheet" href="css/media.css"> -->
	<title>Mensaje de <?php echo $data['de']; ?> </title>
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
        <!-- Reciclando el codigo de recent updates -->
    <div class="recent-updates">
        <div class="updates">
            <div class="box">   
            <strong>De:</strong> <?php echo $data['de']?><br />
            <strong>Fecha:</strong> <?php echo $data['fecha']?><br />
            <strong>Asunto:</strong> <?php echo $data['asunto']?><br /><br />
            <strong>Mensaje</strong><br />
            <?php echo $data['texto']?>
            </div>
        </div>
    </div>

	<?php include "./resourses/footer.php"; ?>
</body>
</html>

<?php 

if($data['leido'] != "si")
{
	mysqli_query($conexion, "UPDATE mensaje SET leido='si' WHERE ID='".$id."'") or die("error");
}

?>