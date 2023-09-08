<?php
    session_start();
    if ($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2 ) {
        header("Location:../");
    }
include "../php/conexion.php";

if (!empty($_POST)) {
    $msg = '';
    if (empty($_POST['provedor'])|| empty($_POST['producto']) || empty($_POST['precio']) || $_POST['precio'] <=  0 || empty($_POST['cantidad']) || $_POST['cantidad'] <= 0) {
        $msg = '<p class="danger">Todos los campos son obligatorios</p>';
    }else{

        $proveedor = $_POST['provedor'];
        $producto = $_POST['producto'];
        $detalle = $_POST['detalle'];
        $precio = $_POST['precio'];
        $cantidad = $_POST['cantidad'];
        $usuario_id = $_SESSION['iduser'];

        $foto = $_FILES['foto'];
        $nombre_foto = $foto['name'];
        $type = $foto['type'];
        $url_temp = $foto['tmp_name'];

        $imgProducto = 'img_producto.png';

        if ($nombre_foto != '') {
            $destino = 'img/uploads/';
            $img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
            $imgProducto = $img_nombre.'.jpg';
            $src = $destino.$imgProducto;
        }

        if ($detalle == "") {
            $detalle = "No hay descripcion";
        }

        $query_insert = mysqli_query($conexion, "INSERT INTO producto(proveedor, descripcion, detalle, precio, existencia, usuario_id, foto) VALUES('$proveedor', '$producto', '$detalle', '$precio', '$cantidad', '$usuario_id', '$imgProducto')");

            if ($query_insert) {
                if ($nombre_foto != '') {
                    move_uploaded_file($url_temp, $src);
                }
                $msg= '<p class="success">Producto guardado correctamente</p>';
                $usuario_actual = $_SESSION['nombre'];
                $rol_actual = $_SESSION['rol_name'];
                $date =date("H:i:s");
                $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha guardado un producto a las $date')");
            }else{
                $msg= '<p class="danger">Error al guardar el producto</p>';
            }
        }
    }
       // mysqli_close($conexion);


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include "./resourses/scripts.php"; ?>
	<title>Registro Producto</title>
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

            <form action="" method="post" class="box" enctype="multipart/form-data">
            <h1><i class="fas fa-cubes"></i> Registro Producto</h1>
                <?php $query_proveedor = mysqli_query($conexion, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");
                    $result_proveedor = mysqli_num_rows($query_proveedor);
                    mysqli_close($conexion);
                
                ?>
                <select name="provedor" id="provedor" class="select">
                    <?php if ($result_proveedor > 0) {
                        while ($proveedor = mysqli_fetch_array($query_proveedor)) {
                ?>
                <option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>
                <?php
                        }
                    }
                    
                    ?>
                    
                </select>
                <input type="text" name="producto" id="producto" placeholder="Nombre del producto" class="form_input">
                <textarea name="detalle" id="detalle" placeholder="Descripcion(Opcional)"></textarea>
                <input type="number" name="precio" id="precio" placeholder="precio del producto" class="form_input">
                <input type="number" name="cantidad" id="cantidad" placeholder="cantidad del producto" min="1" class="form_input">
                <div class="photo">
                    <label for="foto">Foto</label>
                    <div class="prevPhoto">
                    <span class="delPhoto notBlock">X</span>
                    <label for="foto"></label>
                    </div>
                    <div class="upimg">
                    <input type="file" name="foto" id="foto">
                    </div>
                    <div id="form_alert"></div>
                </div>
                
                <button type="submit" class="btn_save"><i class="fas fa-save"></i>Guardar producto</button>
                <div class="alert"><?php echo isset($msg) ? $msg : ''; ?></div>
            </form>
        </div>

	<?php include "./resourses/footer.php"; ?>
</body>
</html>