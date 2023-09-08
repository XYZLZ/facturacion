<?php
session_start();
    include "../php/conexion.php";
    //mysqli_query($conexion, "SELECT * FROM mensaje WHERE para='".$_SESSION['usuario']."'");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes</title>
    <?php include "resourses/scripts.php" ?>
    <link rel="stylesheet" href="css/media.css">
</head>
<body>
    <div class="container">
    <?php include "resourses/header.php" ?>
    <main>
    <div class="date">
        <input type="text" value="<?php $date =date("d-m-Y"); echo $date;  ?>" name="" id="date">
    </div>
    <form action="busqueda_usuario.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Buscar" >
            <button type="submit" class="btn_search"><i class="fas fa-search fa-sm"></i></button>
        </form>
        
    

    <div class="recent-orders">
        <h2>Mensajes</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Asunto</th>
                <th>De</th>
                <th>Fecha</th>
                <th>Rol</th>
                <th>Accciones</th>
            </tr>

            <?php
            // paginador

            $sql_register = mysqli_query($conexion, "SELECT COUNT(*) AS total_registro FROM mensaje WHERE para='".$_SESSION['user']."'");
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
            $query =mysqli_query($conexion, "SELECT * FROM mensaje  WHERE para='".$_SESSION['user']."' LIMIT $desde, $por_pagina");
            mysqli_close($conexion);
            $result = mysqli_num_rows($query);

            // mostrar datos

            if ($result > 0) {
                while ($data = mysqli_fetch_array($query)) {
                    ?>
                    <tr>
                        <td><?php echo $data['ID']; ?></td>
                        <td><?php echo $data['asunto']; ?></td>
                        <td><?php echo $data['de']; ?></td>
                        <td><?php echo $data['fecha']; ?></td>
                        <td><?php echo $data['rol']; ?></td>
                <td>
                    <?php
                        if ($data['leido'] == "si") {
                            $style = "success";
                        }else{
                            $style = "primary";
                        }
                    ?>
                    <a class="link_edit <?php echo $style; ?>" href="leer_msg.php?id=<?php echo $data['ID']?>"><i class="fas fa-eye"></i> Ver</a>

                    
                <!-- | -->
                    <a class="link_delete danger" href="borrar_msg.php?id=<?php echo $data['ID']?>" ><i class="fas fa-trash-alt"> </i> Eliminar</a>
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
        
        <div class="item add-product">
            <a href="crear_msg.php" class="primary">
            <div>
                <span class="material-icons-sharp">add</span>
                <h3>Nuevo Mensaje</h3>
            </div>
            </a>
        </div>
    </div>
</div>
    </div>
<?php include "resourses/footer.php" ?>
</body>
</html>