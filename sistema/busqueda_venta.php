<?php

//use FontLib\Table\Type\head;

    session_start();
include "../php/conexion.php";

    $busqueda = '';
    $fecha_de = '';
    $fecha_a = '';

    if (isset($_REQUEST['busqueda']) && $_REQUEST['busqueda'] == '') {
        header("Location:ventas.php");
    }

    if (isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a']) ) {
        if ($_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == '') {
            header("Location:ventas.php");
        }
    }

    if (!empty($_REQUEST['busqueda'])) {
        if (!is_numeric($_REQUEST['busqueda'])) {
            header("Location:ventas.php");
        }

        $busqueda = strtolower($_REQUEST['busqueda']);
        $where = "nofactura = $busqueda";
        $buscar = "busqueda= $busqueda";
    }

    if (!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])) {
        $fecha_de = $_REQUEST['fecha_de'];
        $fecha_a = $_REQUEST['fecha_a'];

        $buscar = '';

        if ($fecha_de > $fecha_a) {
            header("Location:ventas.php");
        }elseif ($fecha_de == $fecha_a) {
            $where = "fecha LIKE '$fecha_de%'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
        }else{
            $f_de = $fecha_de.' 00:00:00';
            $f_a = $fecha_a.' 23:59:59';
            $where = "fecha BETWEEN '$f_de' AND '$f_a'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";  
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
    <link rel="stylesheet" href="css/media.css">
	<title>Lista de Ventas</title>
</head>
<body>
<div class="container">
    <?php include "resourses/header.php" ?>
    <main>
    <div class="date">
        <input type="text" value="<?php $date =date("d-m-Y"); echo $date;  ?>" name="" id="date">
    </div>
    <form action="busqueda_venta.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Buscar" >
            <button type="submit" class="btn_search"><i class="fas fa-search fa-sm"></i></button>
        </form>

        <div class="s_f_d">
            <form action="busqueda_venta.php" method="get" class="form_search_date">
                <label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" required class="date">
                <label>A:</label>
                <input type="date" name="fecha_a" id="fecha_a" required class="date">
                <button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
            </form>
        </div>
        
    

    <div class="recent-orders">
        <h2>Lista de Ventas</h2>
        <table>
            <tr>
                <th>No.</th>
                <th>Fecha / Hora</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Estado</th>
                <th class="textright">Total Factura</th>
                <th class="textright">Acciones</th>
            </tr>

            <?php
            // paginador

            $sql_register = mysqli_query($conexion, "SELECT COUNT(*) AS total_registro FROM factura WHERE $where ");
            $result_register = mysqli_fetch_array($sql_register);
            $total_registro = $result_register['total_registro'];


            $por_pagina = 5;

            if (empty($_GET['pagina'])) {
                $pagina = 1;
            }else{
                $pagina = $_GET['pagina'];
            }

            $desde = ($pagina-1) *  $por_pagina;
            $total_paginas = ceil($total_registro / $por_pagina);

            $query = mysqli_query($conexion, "SELECT f.nofactura,f.fecha,f.totalfactura,f.codcliente,
            f.estatus,u.nombre AS vendedor,cl.nombre AS cliente FROM factura f INNER JOIN usuario u ON
            f.usuario = u.idusuario INNER JOIN cliente cl ON f.codcliente = cl.idcliente WHERE $where AND f.estatus
            != 10 ORDER BY f.fecha DESC LIMIT $desde,$por_pagina");
            mysqli_close($conexion);
            $result = mysqli_num_rows($query);

            // mostrar datos

            if ($result > 0) {
                while ($data = mysqli_fetch_array($query)) {

                    if ($data['estatus'] == 1) {
                        $estado = '<span class="pagada">Pagada</span>';
                    }else{
                        $estado = '<span class="anulada">Anulada</span>';
                    }
                    
                    ?>
                    <tr id="row_<?php echo $data['nofactura']; ?>">
                        <td><?php echo $data['nofactura']; ?></td>
                        <td><?php echo $data['fecha']; ?></td>
                        <td><?php echo $data['cliente']; ?></td>
                        <td><?php echo $data['vendedor']; ?></td>
                        <td class="estado"><?php echo $estado; ?></td>
                        <td class="textright totalfactura"><span>Q.</span><?php echo $data['totalfactura']; ?></td>
                <td>
                    <div class="div_acciones">
                        <div>
                            <button class="btn_view view_factura" type="button" cl="<?php echo $data['codcliente']; ?>" f="<?php echo $data['nofactura']; ?>"><i class="fas fa-eye"></i></button>
                        </div>
                    

                    <?php
                        if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
                            if ($data['estatus'] == 1) {
                                # code...
                            
                        
                    
                    ?>
                    <div class="div_factura">
                        <button class="btn_anular anular_factura" fac="<?php echo $data['nofactura']; ?>"><i class="fas fa-ban"></i></button>
                    </div>
                    <?php       }else{ ?>

                    <div class="div_factura">
                        <button type="button" class="btn_anular inactive"><i class="fas fa-ban"></i></button>
                    </div>

                    <?php }
                        }
                    ?>
                    </div>
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
                <li><a href="?pagina=<?php echo 1;?>&<?php echo $buscar; ?>"><i class="fas fa-step-backward"></i></a></li>
                <li><a href="?pagina=<?php echo $pagina - 1;?>&<?php echo $buscar; ?>"><i class="fas fa-angle-double-left"></i></a></li>

                <?php  
                }
                for ($i=1; $i <= $total_paginas; $i++) {
                    if ($i == $pagina) {
                        echo '<li class="pageSelected">'.$i.'</li>';
                    }else {
                        echo '<li><a href="?pagina='.$i.'&'.$buscar.'">'.$i.'</a></li>';
                    }
                    
                }

                if ($pagina != $total_paginas) {
                ?>
                
                <li><a href="?pagina=<?php echo $pagina + 1;?>&<?php echo $buscar; ?>"><i class="fas fa-angle-double-right"></i></a></li>
                <li><a href="?pagina=<?php echo $total_paginas;?>&<?php echo $buscar; ?>"><i class="fas fa-step-forward"></i></a></li>
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
                <p>Hey, <b>Benjamin</b></p>
                <small class="text-muted">Admin</small>
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
        
        <div class="item add-product">
            <a href="nueva_venta.php" class="primary">
            <div>
                <span class="material-icons-sharp">add</span>
                <h3>Nueva Venta</h3>
            </div>
            </a>
        </div>
    </div>
</div>
    </div>
<?php include "resourses/footer.php" ?>
</body>
</html>