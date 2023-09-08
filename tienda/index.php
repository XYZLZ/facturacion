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
    <title>Tineda Online</title>
    <?php include 'resourses/scripts.php' ?>
</head>
<body>

<header>
<div class="navbar  navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
    <a href="#" class="navbar-brand">
        <strong>Tienda Online</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarHeader">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a href="#" class="nav-link active">Catalogo</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Contacto</a>
            </li>
        </ul>
        <a href="carrito.php" class="btn btn-primary">Carrito</a>
        <a href="../sistema/salir.php" class="btn btn-primary ms-5">Cerrar Session</a>
    </div>
    </div>
</div>
</header>

    <main>
        <div class="contaier">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                    <?php
                        $query = mysqli_query($conexion, "SELECT * FROM producto WHERE estatus = 1");

                        $result = mysqli_num_rows($query);

                        if ($result > 0) {
                            while ($data = mysqli_fetch_array($query)) {
                                if ($data['foto'] != 'img_producto.png') {
                                    $foto = '../sistema/img/uploads/'.$data['foto'];
                                }else{
                                    $foto = '../sistema/img/'.$data['foto'];
                                }
                    ?>
        <div class="col d-flex">
            <div class="card shadow-sm">  
                <div class="card shadow-sm">
                    <img width="100%" src="<?php echo $foto ?>" alt="<?php echo $data['descripcion']; ?>">
                    <div class="card-body">
                    <h5 class="card-title"><?php echo $data['descripcion']; ?></h5>
                    <p class="card-text"><?php echo $data['precio']; ?></p>
                    <div class="d-flex justify-content-between align-content-center">
                        <div class="btn-group">
                        <a href="#" class="btn btn-primary">Detalles</a>
                        </div>
                        <a href="#" class="btn btn-success">Agregar</a>
                    </div>
                    </div>
                </div>
            </div>

            <?php
                    }
                }
            ?>
        </div>
        </div>
    </main>

</body>
</html>