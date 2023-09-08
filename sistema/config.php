<?php
	session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include "./resourses/scripts.php"; ?>
	<!-- <link rel="stylesheet" href="css/media.css"> -->
	<title>Configuracion</title>
</head>
<body>
	<?php
		include "../php/conexion.php";

		// Datos empresa
		$nit = '';
		$nombreEmpresa = '';
		$razonSocial = '';
		$telEmpresa = '';
		$emailEmpresa = '';
		$dirEmpresa = '';
		$iva = '';

		$query_empresa = mysqli_query($conexion, "SELECT * FROM configuracion");
		$row_empresa = mysqli_num_rows($query_empresa);
		if ($row_empresa > 0) {
			while ($arrInfoEmpresa = mysqli_fetch_assoc($query_empresa)) {
				$nit = $arrInfoEmpresa['nit'];
				$nombreEmpresa = $arrInfoEmpresa['nombre'];
				$razonSocial = $arrInfoEmpresa['razon_social'];
				$telEmpresa = $arrInfoEmpresa['telefono'];
				$emailEmpresa = $arrInfoEmpresa['email'];
				$dirEmpresa = $arrInfoEmpresa['direccion'];
				$iva = $arrInfoEmpresa['Iva'];
			}
		}

		// $query_dash = mysqli_query($conexion, "CALL dataDashboard();");
		// $result_dash = mysqli_num_rows($query_dash);

		// if ($result_dash > 0) {
		// 	$data_dash = mysqli_fetch_assoc($query_dash);
		// 	mysqli_close($conexion);
		// }
	?>
	
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

		<div class="divInfoSistema">
			<div class="containerPerfil">
				<div class="containerDataUser">
					<div class="logoUser">
						<img src="img/logoUser.png" alt="logoUser">
					</div>
					<div class="divDataUser">
						<h4>Informacion Personal</h4>
						<div>
							<label>Nombre: </label> <span><?= $_SESSION['nombre'];?></span>
						</div>

						<div>
							<label>Correo: </label> <span><?= $_SESSION['email'];?></span>
						</div>

						<h4>Datos Usuario</h4>
						<div>
							<label>Rol: </label> <span><?= $_SESSION['rol_name'];?></span>
						</div>
						
						<div>
							<label>Usuario: </label> <span><?= $_SESSION['user'];?></span>
						</div>

						<h4>Cambiar Clave</h4>
						<form action="" method="post" name="frmChangePass" id="frmChangePass">
							<div>
								<input type="password" name="txtPassUser" id="txtPassUser" placeholder="Clave Actual" required class="form_input">
							</div>

							<div>
								<input class="newPass form_input" type="password" name="txtNewPassUser" id="txtNewPassUser" placeholder="Clave Nueva" required>
							</div>
							
							<div>
								<input class="newPass form_input" type="password" name="txtPassConfirm" id="txtPassConfirm" placeholder="Confirmar Clave" required>
							</div>
								<div class="alertChangePass" style="display: none;"></div>
							<div>
								<button type="submit" class="btn_save btnChangePass"><i class="fas fa-key"></i> Cambiar Clave</button>
							</div>
						</form>
					</div>
				</div>
				<?php if ($_SESSION['rol'] == 1) {
					
				?>
				<div class="containerDataEmpresa">
				<div class="logoEmpresa">
						<img src="img/logoEmpresa.png" alt="logoUser">
					</div>
					<h4>Datos de la empresa</h4>

					<form action="" method="post" name="frmEmpresa" id="frmEmpresa">
						<input type="hidden" name="action" value="updateDataEmpresa">

						<div>
							<input type="text" name="txtNit" id="txtNit" placeholder="Nit de la empresa" value="<?php echo $nit; ?>" required class="form_input">
						</div>

						<div>
							<input type="text" name="txtNombre" id="txtNombre" placeholder="Nombre de la empresa" value="<?php echo $nombreEmpresa; ?>" required class="form_input"> 
						</div>

						<div>
							<input type="text" name="txtRSocial" id="txtRSocial" placeholder="Razon Social" value="<?php echo $razonSocial; ?>" class="form_input">
						</div>

						<div>
							<input type="text" name="txtTelEmpresa" id="txtTelEmpresa" placeholder="Numero de telefono" value="<?php echo $telEmpresa; ?>" required class="form_input">
						</div>

						<div>
							<input type="email" name="txtEmailEmpresa" id="txtEmailEmpresa" placeholder="Correo electronico" value="<?php echo $emailEmpresa; ?>" required class="form_input">
						</div>

						<div>
							<input type="text" name="txtDirEmpresa" id="txtDirEmpresa" placeholder="Direccion de la empresa" value="<?php echo $dirEmpresa; ?>" required class="form_input">
						</div>

						<div>
							<input type="text" name="txtIva" id="txtIva" placeholder="Impuesto al valor agregado (IVA)" value="<?php echo $iva; ?>" required class="form_input">
						</div>

						<div class="alertFormEmpresa" style="display: none;"></div>
						<div>
							<button type="submit" class="btn_save btnChangePass"><i class="far fa-save fa-lg"></i> Guardar Datos</button>
						</div>
					</form>
				</div>
				<?php } ?>
			</div>
		</div>			
	</section>

	<?php include "./resourses/footer.php"; ?>
</body>
</html>