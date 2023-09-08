<?php
    session_start();
include "../php/conexion.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include "./resourses/scripts.php"; ?>
    <link rel="stylesheet" href="css/media.css">
	<title>Lista de Productos</title>
</head>
<body>
<div class="modal">
        <div class="bodyModal"></div>
    </div>
<div class="container">
    <?php include "resourses/header.php" ?>
    <main>
    <div class="date">
        <input type="text" value="<?php $date =date("d-m-Y"); echo $date;  ?>" name="" id="date">
    </div>
    <form action="busqueda_producto.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Buscar" >
            <button type="submit" class="btn_search"><i class="fas fa-search fa-sm"></i></button>
        </form>
        
    

    <div class="recent-orders">
        <h2>Lista de Productos</h2>
        <table>
            <tr>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Precio</th>
                <th>Existencia</th>
                <th>
                <?php 
					$query_proveedor=mysqli_query($conexion,"SELECT codproveedor, proveedor FROM proveedor WHERE estatus=1 ORDER BY proveedor ASC");
					$result_proveedor=mysqli_num_rows($query_proveedor);
	
				?>

				<select name="proveedor" id="search_proveedor">
                <option value="" selected>Provedor</option>
					<?php 
						if ($result_proveedor>0) {
							while ($proveedor=mysqli_fetch_array($query_proveedor)) {
					?>
						<option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>
					<?php 			
							}
						}
					?>
				</select>
                </th>
                <th>Foto</th>
                <th>Acciones</th>
            </tr>

            <?php
            // paginador

            $sql_register = mysqli_query($conexion, "SELECT COUNT(*) AS total_registro FROM producto WHERE estatus = 1");
            $result_register = mysqli_fetch_array($sql_register);
            $total_registro = $result_register['total_registro'];


            $por_pagina = 3;

            if (empty($_GET['pagina'])) {
                $pagina = 1;
            }else{
                $pagina = $_GET['pagina'];
            }

            $desde = ($pagina-1) *  $por_pagina;
            $total_paginas = ceil($total_registro / $por_pagina);


            $query = mysqli_query($conexion,"SELECT p.codproducto, p.descripcion, p.detalle, p.precio, p.existencia,
            pr.proveedor, p.foto FROM producto p INNER JOIN proveedor pr ON p.proveedor = pr.codproveedor
            WHERE p.estatus = 1 ORDER BY p.codproducto DESC LIMIT $desde, $por_pagina ");
            mysqli_close($conexion);
            $result = mysqli_num_rows($query);

            // mostrar datos

            if ($result > 0) {
                    while ($data = mysqli_fetch_array($query)) {
                        if ($data['foto'] != 'img_producto.png') {
                            $foto = 'img/uploads/'.$data['foto'];
                        }else{
                            $foto = 'img/'.$data['foto'];
                        }
                    ?>
                    <tr class="row<?php echo $data['codproducto']; ?>">
                        <td><?php echo $data['codproducto']; ?></td>
                        <td><?php echo $data['descripcion']; ?> <div class="tooltip" data-tooltip="<?php echo $data['detalle']; ?>">?</div></td>
                        <td class="celPrecio"><?php echo $data['precio']; ?></td>
                        <td class="celExistencia"><?php echo $data['existencia']; ?></td>
                        <td><?php echo $data['proveedor']; ?></td>
                        <td><img class="img_producto" src="<?php echo $foto; ?>" alt="<?php echo $data['descripcion']; ?>"></td>
                        <?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {?>
                <td>
                    <a class="link_add add_product success" product="<?php echo $data['codproducto'];?>" href="#"><i class="fas fa-plus"></i> Agregar</a>
                    
                    <a class="link_edit primary" href="editar_producto.php?id=<?php echo $data['codproducto'];?>"><i class="fas fa-edit"></i> Editar</a>
                    
                    
                    <a class="link_delete del_product danger" product="<?php echo $data['codproducto'];?>"  href="#"><i class="fas fa-trash-alt"></i> Eliminar</a>
                    <?php }?>
                </td>
            </tr>
            <?php
        }
    }

    ?>
        </table>
        <div class="paginador">
            <ul>

            <?php

                if ($pagina != 1) {
                
                
            ?>
                <li><a href="?pagina=<?php echo 1;?>"><i class="fas fa-step-backward"></i></a></li>
                <li><a href="?pagina=<?php echo $pagina - 1;?>"><i class="fas fa-angle-double-left"></i></a></li>

                <?php  
                }
                for ($i=1; $i <= $total_paginas; $i++) {
                    if ($i == $pagina) {
                        echo '<li class="pageSelected">'.$i.'</li>';
                    }else {
                        echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
                    }
                    
                }

                if ($pagina != $total_paginas) {
                ?>
                
                <li><a href="?pagina=<?php echo $pagina + 1;?>"><i class="fas fa-angle-double-right"></i></a></li>
                <li><a href="?pagina=<?php echo $total_paginas;?>"><i class="fas fa-step-forward"></i></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

</main>
<!-- * ------- END OF MAIN ---------  -->
<div class="right">
    <div class="top">
        <button id="menu-btn">
            <span class="material-icons-sharp">menu</span>
        </button>
        <div class="theme-toggler">
            <span class="material-icons-sharp active">light_mode</span>
            <span class="material-icons-sharp">dark_mode</span>
        </div>
        <div class="profile">
            <div class="info">
            <p>Hey, <b><?php echo $_SESSION['nombre']; ?></b></p>
                <small class="text-muted"><?php echo $_SESSION['rol_name']; ?></small>
            </div>
            <div class="profile-photo">
                <img src="img/user.png" alt="user">
            </div>
        </div>
    </div>
    <!-- * ------ END OF TOP ------- -->
    <div class="recent-updates">
        <h2>Recent Updates</h2>
        <div class="updates">
            <div class="update">
                <div class="profile-photo">
                    <img src="img/user.png" alt="updates">
                </div>
                <div class="message">
                    <p><b>Mike Tyson</b>Recive This Order of Night Lion Tech GPS Drone</p>
                    <small class="text-muted">2 Minutes Ago</small>
                </div>
            </div>
            <div class="update">
                <div class="profile-photo">
                    <img src="img/user.png" alt="updates">
                </div>
                <div class="message">
                    <p><b>Mike Tyson</b>Recive This Order of Night Lion Tech GPS Drone</p>
                    <small class="text-muted">2 Minutes Ago</small>
                </div>
            </div>
            <div class="update">
                <div class="profile-photo">
                    <img src="img/user.png" alt="updates">
                </div>
                <div class="message">
                    <p><b>Mike Tyson</b>Recive This Order of Night Lion Tech GPS Drone</p>
                    <small class="text-muted">2 Minutes Ago</small>
                </div>
            </div>
        </div>
    </div>
     <!-- * ------ END OF RECENT UPDATES ------- -->
    <div class="sales-analytics">
    <?php if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){ ?>

        <div class="item add-product">
            <a href="registro_producto.php" class="primary">
            <div>
                <span class="material-icons-sharp">add</span>
                <h3>Crear Producto</h3>
            </div>
            </a>
        </div>
        <?php } ?>
    </div>
</div>
    </div>
<?php include "resourses/footer.php" ?>
</body>
</html>