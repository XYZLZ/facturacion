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
	<title>Lista de Clientes</title>
</head>
<body>
<div class="container">
    <?php include "resourses/header.php" ?>
        <?php
        
        $busqueda = strtolower($_REQUEST['busqueda']);

        if (empty($busqueda)) {
            header("Location:lista_clientes.php");
            mysqli_close($conexion);
        }
    ?>
    <main>
    <div class="date">
        <input type="text" value="<?php $date =date("d-m-Y"); echo $date;  ?>" name="" id="date">
    </div>
    <form action="busqueda_cliente.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Buscar" >
            <button type="submit" class="btn_search"><i class="fas fa-search fa-sm"></i></button>
        </form>
        
    

    <div class="recent-orders">
        <h2>Lista de clientes</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>NIT</th>
                <th>Nombre</th>
                <th>Telefono</th>
                <th>Direccion</th>
                <th>Accciones</th>
            </tr>

            <?php
            // paginador

            $sql_register = mysqli_query($conexion, "SELECT COUNT(*) AS total_registro FROM cliente WHERE (idcliente LIKE '%$busqueda%' OR nit LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR telefono LIKE '%$busqueda%' OR direccion LIKE '%$busqueda%') AND estatus = 1");
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


            $query = mysqli_query($conexion,"SELECT * FROM cliente WHERE (idcliente LIKE '%$busqueda%' OR nit LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR telefono LIKE '%$busqueda%' OR direccion LIKE '%$busqueda%') AND estatus = 1 ORDER BY idcliente ASC LIMIT $desde, $por_pagina");
            mysqli_close($conexion);
            $result = mysqli_num_rows($query);

            // mostrar datos

            if ($result > 0) {
                while ($data = mysqli_fetch_array($query)) {
                    if ($data["nit"] == 0) {
                        $nit = 'C/F';
                    }else{
                        $nit = $data["nit"];
                    }
                    ?>
                    <tr>
                        <td><?php echo $data['idcliente']; ?></td>
                        <td><?php echo $nit; ?></td>
                        <td><?php echo $data['nombre']; ?></td>
                        <td><?php echo $data['telefono']; ?></td>
                        <td><?php echo $data['direccion']; ?></td>
                <td>
                    <a class="link_edit primary" href="editar_cliente.php?id=<?php echo $data['idcliente'];?>"><i class="fas fa-edit"></i> Editar</a>
                    <?php 
                        if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
                            
                        
                    ?>
                    
                    <a class="link_delete danger" href="eliminar_cliente.php?id=<?php echo $data['idcliente'];?>"><i class="fas fa-trash-alt"></i> Eliminar</a>
                    <?php }?>
                </td>
            </tr>
            <?php
        }
    }

    ?>
        </table>
        <?php 
            if ($total_registro != 0) {
                
            
        ?>
        <div class="paginador">
            <ul>

            <?php

                if ($pagina != 1) {
                
                
            ?>
                <li><a href="?pagina=<?php echo 1;?>&busqueda=<?php echo $busqueda;?>"><i class="fas fa-step-backward"></i></a></li>
                <li><a href="?pagina=<?php echo $pagina - 1;?>&busqueda=<?php echo $busqueda;?>"><i class="fas fa-angle-double-left"></i></a></li>

                <?php  
                }
                for ($i=1; $i <= $total_paginas; $i++) {
                    if ($i == $pagina) {
                        echo '<li class="pageSelected">'.$i.'</li>';
                    }else {
                        echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
                    }
                    
                }

                if ($pagina != $total_paginas) {
                ?>
                
                <li><a href="?pagina=<?php echo $pagina + 1;?>&busqueda=<?php echo $busqueda;?>"><i class="fas fa-angle-double-right"></i></a></li>
                <li><a href="?pagina=<?php echo $total_paginas;?>&busqueda=<?php echo $busqueda;?>"><i class="fas fa-step-forward"></i></a></li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
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
            <a href="registro.php" class="primary">
            <div>
                <span class="material-icons-sharp">add</span>
                <h3>Crear Cliente</h3>
            </div>
            </a>
        </div>
    </div>
</div>
    </div>
<?php include "resourses/footer.php" ?>
</body>
</html>