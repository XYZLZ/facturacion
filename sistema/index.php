<?php
    session_start();
    include "../php/conexion.php";
    if ($_SESSION['status'] == 5) {
        header('location:../tienda/');
    }
    $query_dash = mysqli_query($conexion, "CALL dataDashboard();");
    $result_dash = mysqli_num_rows($query_dash);

    if ($result_dash > 0) {
        $data_dash = mysqli_fetch_assoc($query_dash);
        mysqli_close($conexion);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dahboard</title>
    <?php include "resourses/scripts.php" ?>
</head>
<body>
    <div class="loader_bg">
        <div class="loader"></div>
    </div>
    <div class="container">
    <?php include "resourses/header.php" ?>
    <main>
    <h1>Dashboard</h1>
    <div class="date">
        <input type="text" value="<?php $date =date("d-m-Y"); echo $date;  ?>" name="" id="date">
    </div>
        
    <div class="insights">
        <div class="sales">
            <span class="material-icons-sharp">analytics</span>
            <div class="middle">
                <div class="left">
                    <h3>Usuarios Totales</h3>
                    <h1>$<?= $data_dash['usuarios'] ?></h1>
                </div>
                <div class="progress">
                    <svg>
                        <circle cx="38" cy="38" r="36"></circle>
                    </svg>
                    <div class="number">
                        <p>81%</p>
                    </div>
                </div>
            </div>
            <small class="text-muted">Last 24 Hours</small>
        </div>
        <!-- * -------- END OF SALES -------- -->
        <div class="expenses">
            <span class="material-icons-sharp">bar_chart</span>
            <div class="middle">
                <div class="left">
                    <h3>Productos Totales</h3>
                    <h1>$<?= $data_dash['productos'] ?></h1>
                </div>
                <div class="progress">
                    <svg>
                        <circle cx="38" cy="38" r="36"></circle>
                    </svg>
                    <div class="number">
                        <p>62%</p>
                    </div>
                </div>
            </div>
            <small class="text-muted">Last 24 Hours</small>
        </div>
        <!-- * -------- END OF EXPENSES -------- -->
        <div class="income">
            <span class="material-icons-sharp">stacked_line_chart</span>
            <div class="middle">
                <div class="left">
                    <h3>Clientes Totales</h3>
                    <h1>$<?= $data_dash['clientes'] ?></h1>
                </div>
                <div class="progress">
                    <svg>
                        <circle cx="38" cy="38" r="36"></circle>
                    </svg>
                    <div class="number">
                        <p>44%</p>
                    </div>
                </div>
            </div>
            <small class="text-muted">Last 24 Hours</small>
        </div>
        <!-- * -------- END OF INCOME -------- -->
        
    </div>
     <!-- * -------- END OF INSIGHTS -------- -->

    <div class="recent-orders">
        <h2>Lista General</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Accciones</th>
            </tr>

            <?php
            // paginador
            include "../php/conexion.php";

            $sql_register = mysqli_query($conexion, "SELECT COUNT(*) AS total_registro FROM usuario WHERE estatus = 1");
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


            $query = mysqli_query($conexion,"SELECT u.idusuario, u.nombre, u.correo, u.usuario, r.rol FROM usuario u INNER JOIN rol r ON u.rol = r.idrol WHERE estatus = 1 ORDER BY u.idusuario ASC LIMIT $desde, $por_pagina ");
            mysqli_close($conexion);
            $result = mysqli_num_rows($query);

            // mostrar datos

            if ($result > 0) {
                while ($data = mysqli_fetch_array($query)) {
                    ?>
                    <tr>
                        <td><?php echo $data['idusuario']; ?></td>
                        <td><?php echo $data['nombre']; ?></td>
                        <td><?php echo $data['correo']; ?></td>
                        <td><?php echo $data['usuario']; ?></td>
                        <td><?php echo $data['rol']; ?></td>
                <td>
                    <a class="link_edit primary" href="editar_usuario.php?id=<?php echo $data['idusuario'];?>"><i class="fas fa-edit"></i> Editar</a>
                    
                    <?php if ($data["idusuario"] != 1){
                        ?> 
                        <!-- | -->
                    <a class="link_delete danger" href="eliminar_usuario.php?id=<?php echo $data['idusuario'];?>"><i class="fas fa-trash-alt"> </i> Eliminar</a>
                    <?php } ?>
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
                <li><a href="?pagina=<?php echo $pagina - 1;?>"><i class="fas fa-angle-double-left"></i> </a></li>

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
        <h2>Sales Analytics</h2>
        <div class="item online">
            <div class="icon">
                <span class="material-icons-sharp">shopping_cart</span>
            </div>
            <div class="right">
                <div class="info">
                    <h3>ONLINE ORDERS</h3>
                    <small class="text-muted">Last 24 Hours</small>
                </div>
                <h5 class="success">+39%</h5>
                <h3>3849</h3>
            </div>
        </div>
        <div class="item offline">
            <div class="icon">
                <span class="material-icons-sharp">mail</span>
            </div>
            <div class="right">
                <div class="info">
                    <h3>OFFLINE ORDERS</h3>
                    <small class="text-muted">Last 24 Hours</small>
                </div>
                    
                <?php
                // todo: revisar  
                $ventas_p = 0;
                    if ($data_dash['ventas'] >= 1) {
                        $porcentajeVentas = 'success';
                    }else{
                        $porcentajeVentas = 'danger'; 
                    }

                    if ($data_dash['clientesN'] >= 1) {
                        $porcentajecliente = 'success';
                    }else{
                        $porcentajecliente = 'danger'; 
                    }
                ?>
                <h5 class="<?php echo $porcentajeVentas ?>">-17%</h5>
                <h3><?= $data_dash['ventas'] ?></h3>
            </div>
        </div>
        <div class="item Customers">
            <div class="icon">
                <span class="material-icons-sharp">person</span>
            </div>
            <div class="right">
                <div class="info">
                    <h3>NEW CUSTOMERS</h3>
                    <small class="text-muted">Last 24 Hours</small>
                </div>
                <h5 class="<?php echo $porcentajecliente ?>">+25%</h5>
                <h3><?= $data_dash['clientesN'] ?></h3>
            </div>
        </div>
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