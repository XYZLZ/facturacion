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
    <title>Lista usuarios</title>
    <?php include "resourses/scripts.php" ?>
    <link rel="stylesheet" href="css/media.css">
</head>
<body>
        <!-- Modal -->
    <div class="modal fade" id="modalUsers" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Registro Usuarios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <form id="userForm">
            <input type="hidden" id="userID">
            <div class="mb-3">
                <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo" class="form_input">
            </div>
            <div class="mb-3">
                <input type="email" name="correo" id="correo" placeholder="Correo electronico" class="form_input">
            </div>
            <div class="mb-3">
                <input type="text" name="usuario" id="usuario" placeholder="Usuario" class="form_input">
            </div>
            <div class="mb-3">
                <input type="password" name="clave" id="clave" placeholder="Clave de acceso" class="form_input">
            </div>
            <div class="mb-3">
            <?php 
                    $query_rol = mysqli_query($conexion, "SELECT * FROM rol");
                    mysqli_close($conexion);
                    $result_rol  = mysqli_num_rows($query_rol);
                ?>
                <select name="rol" id="rol" class="select" style="margin-left: 30px ;">
                    <?php
                    
                    if ($result_rol > 0){
                        while ($rol = mysqli_fetch_array($query_rol)) {
                    ?>
                    <option value="<?php echo $rol['idrol']; ?>"><?php echo $rol['rol']; ?></option>

                    <?php 

                        
                        }
                    }
                    ?>
                    
                </select>
            </div>
        
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn_modal closeModal" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary d-block envio btn_modal">Crear</button>
        <div id="mensaje"></div> <!--  Mensaje de validacion -->
        </div>
        </form>
    </div>
    </div>
</div>

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
        <h2>Lista de usuarios</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Accciones</th>
            </tr>
            </thead>
            <tbody id="listUsers">
                
            </tbody>
            <?php
            // paginador

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
                    <a class="link_edit primary btn_edit" href="#"><i class="fas fa-edit"></i> Editar</a>
                    
                    <?php if ($data["idusuario"] != 1){
                        ?> 
                        <!-- | -->
                    <a class="link_delete danger btn_delete" href="#"><i class="fas fa-trash-alt"> </i> Eliminar</a>
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
        
        <div class="item add-product">
        <button type="button" id="btnCrear" class="primary bg-transparent">
            <div>
                <span class="material-icons-sharp">add</span>
                <h3>Crear Usuario</h3>
            </div>
            </button>
        </div>
    </div>
</div>

    </div>


<?php include "resourses/footer.php" ?>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script> -->
<script src="js/usuarios.js" type="module"></script>
</body>
</html>