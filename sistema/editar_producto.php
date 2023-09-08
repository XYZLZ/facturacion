<?php
    session_start();
    
    if ($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2 ) {
        header("Location:../");
    }
include "../php/conexion.php";

if (!empty($_POST)) {
    $msg= '';
    if (empty($_POST['provedor'])|| empty($_POST['producto']) || empty($_POST['precio']) || $_POST['precio'] <=  0 || empty($_POST['id']) || empty($_POST['foto_actual']) || empty($_POST['foto_remove'])){
        $msg = '<p class="danger">Todos los campos son obligatorios</p>';
    }else{

        $codproducto = $_POST['id'];
        $proveedor = $_POST['provedor'];
        $producto = $_POST['producto'];
        $precio = $_POST['precio'];
        $imgProducto = $_POST['foto_actual'];
        $imgRemove = $_POST['foto_remove'];

        $foto = $_FILES['foto'];
        $nombre_foto = $foto['name'];
        $type = $foto['type'];
        $url_temp = $foto['tmp_name'];

        $upd = '';

        if ($nombre_foto != '') {
            $destino = 'img/uploads/';
            $img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
            $imgProducto = $img_nombre.'.jpg';
            $src = $destino.$imgProducto;
        }else {
            if ($_POST['foto_actual'] != $_POST['foto_remove'] ) {
                $imgProducto = 'img_producto.png';
            }
        }

        $query_update = mysqli_query($conexion, "UPDATE producto SET descripcion = '$producto', proveedor = $proveedor, precio = $precio, foto = '$imgProducto' WHERE codproducto =  $codproducto");

            if ($query_update) {

                if (($nombre_foto != '' && ($_POST['foto_actual'] != 'img_producto.png')) || ($_POST['foto_actual'] != $_POST['foto_remove'])) {
                    unlink('img/uploads/'.$_POST['foto_actual']);
                }

                if ($nombre_foto != '') {
                    move_uploaded_file($url_temp, $src);
                }
                $msg= '<p class="success">Producto actualizado correctamente</p>';
                $usuario_actual = $_SESSION['nombre'];
                $rol_actual = $_SESSION['rol_name'];
                $date =date("H:i:s");
                $update_query = mysqli_query($conexion, "INSERT INTO updates(usuario, rol, r_update) VALUES('$usuario_actual', '$rol_actual', 'El $rol_actual $usuario_actual ha editado el producto $producto a las $date')");
            }else{
                $msg= '<p class="danger">Error al actualizar el producto</p>';
            }
        }
    }
    // validar producto
    if (empty($_REQUEST['id'])) {
        header("Location: lista_productos.php");
    }else {
        $id_producto = $_REQUEST['id'];

        if (!is_numeric($id_producto)) {
            header("Location: lista_productos.php");
        }

        $query_producto = mysqli_query($conexion, "SELECT p.codproducto, p.descripcion, p.precio, p.foto, pr.codproveedor, pr.proveedor FROM producto p INNER JOIN proveedor pr ON p.proveedor = pr.codproveedor WHERE p.codproducto= $id_producto AND p.estatus= 1");
        $result_producto = mysqli_num_rows($query_producto);

        $foto = '';
        $classRemove = 'notBlock';

        

        if ($result_producto > 0) {
            $data_producto = mysqli_fetch_assoc($query_producto);

            if ($data_producto['foto'] != 'img_producto') {
                $classRemove = '';
                $foto = '<img id="img" src="img/uploads/'.$data_producto['foto'].'" alt="producto">';
            }

        }else {
            header("Location: lista_productos.php");
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
	<title>Actualizar Producto</title>
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

            <form action="" method="post" enctype="multipart/form-data" class="box">
            <h1><i class="fas fa-edit"></i> Actualizar Producto</h1>
                <input type="hidden" name="id" value="<?php echo $data_producto['codproducto']; ?>">
                <input type="hidden" name="foto_actual" value="<?php echo $data_producto['foto']; ?>">
                <input type="hidden" name="foto_remove" value="<?php echo $data_producto['foto']; ?>">
                <?php $query_proveedor = mysqli_query($conexion, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");
                    $result_proveedor = mysqli_num_rows($query_proveedor);
                    mysqli_close($conexion);
                
                ?>
                <select name="provedor" id="provedor" class="notItemOne select" 
                    <option value="<?php echo $data_producto['codproveedor'] ?>" selected><?php echo $data_producto['proveedor'] ?></option>
                    <?php if ($result_proveedor > 0) {
                        while ($proveedor = mysqli_fetch_array($query_proveedor)) {
                ?>
                <option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>
                <?php
                        }
                    }
                    
                    ?>
                    
                </select>
                <input type="text" name="producto" id="producto" placeholder="Nombre del producto" value="<?php echo $data_producto['descripcion'] ?>" class="form_input">
                <input type="number" name="precio" id="precio" placeholder="precio del producto" value="<?php echo $data_producto['precio'] ?>" class="form_input">
                <div class="photo">
                    <label for="foto">Foto</label>
                    <div class="prevPhoto">
                    <span class="delPhoto <?php echo $classRemove; ?>">X</span>
                    <label for="foto"></label>
                    <?php echo $foto; ?>
                    </div>
                    <div class="upimg">
                    <input type="file" name="foto" id="foto">
                    </div>
                    <div id="form_alert"></div>
                </div>
                
                <button type="submit" class="btn_save"><i class="fas fa-save"></i>Actualizar producto</button>
                <div class="alert"><?php echo isset($msg) ? $msg : ''; ?></div>
            </form>
        </div>
	</section>

	<?php include "./resourses/footer.php"; ?>
</body>
</html>