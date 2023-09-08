<?php

    session_start();
    $alert = "";
    if (!empty($_SESSION['active'])) {
        header('location:./sistema/');
    }else{

    


    if (isset($_POST['login'])) {

        if (empty($_POST['user']) || empty($_POST['pass']) ) {
            $alert = "Ingrese su usuario y clave";
        }else{
            include "./php/conexion.php";
            $user = mysqli_real_escape_string($conexion,  $_POST['user']);
            $pass = md5(mysqli_real_escape_string($conexion, $_POST['pass'])) ;

            $query = mysqli_query($conexion, "SELECT u.idusuario, u.nombre, u.correo, u.usuario,
            r.idrol, r.rol FROM usuario u INNER JOIN rol r ON u.rol = r.idrol WHERE u.usuario=
            '$user' AND u.clave= '$pass'");
            //mysqli_close($conexion);
            $result = mysqli_num_rows($query);

            if ($result > 0) {
                $data = mysqli_fetch_array($query);

                
                $_SESSION['active'] = true;
                $_SESSION['iduser'] = $data['idusuario'];
                $_SESSION['nombre'] = $data['nombre'];
                $_SESSION['email'] = $data['correo'];
                $_SESSION['user'] = $data['usuario'];
                $_SESSION['rol'] = $data['idrol'];
                $_SESSION['rol_name'] = $data['rol'];
                $_SESSION['status'] = $data['estatus'];

                header('location:./sistema/');
            }elseif ($result == 0){
                include "./php/conexion.php";
                $r_user =  $_POST['user'];
                $r_pass = md5($_POST['pass']);
                $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE usuario= '$r_user' and password= '$r_pass' ");
                mysqli_close($conexion);
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    $data = mysqli_fetch_array($query);
                    $_SESSION['active'] = true;
                    $_SESSION['iduser'] = $data['idcliente'];
                    $_SESSION['nombre'] = $data['nombre'];
                    $_SESSION['email'] = $data['email'];
                    $_SESSION['user'] = $data['usuario'];
                    $_SESSION['rol'] = $data['idrol'];
                    $_SESSION['rol_name'] = $data['rol'];
                    $_SESSION['status'] = $data['estatus'];
                    
                    header('Location:./tienda/');
                }else{
                    $alert = "El usuario o la clave son incorectos";
                    session_destroy();
                }
            }


        }

    }

}

if (isset($_POST['register'])){
    if (empty($_POST["r_user"]) || empty($_POST["r_email"]) || empty($_POST["r_pass"])) {
        $msg = "Todos los campos son obligatorios";
    }else{
        include "./php/conexion.php";
        $r_user = mysqli_real_escape_string($conexion, $_POST["r_user"]);
        $r_email = mysqli_real_escape_string($conexion, $_POST["r_email"]);
        $r_pass = md5(mysqli_real_escape_string($conexion, $_POST["r_pass"]));

        $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE usuario= '$r_user' OR email= '$r_email'");
        $result = mysqli_num_rows($query);

        if ($result > 0) {
            $msg = "El usuario o correo ya existe";
        }else{
            $query_insert = mysqli_query($conexion, "INSERT INTO cliente(usuario, email, password, usuario_id) VALUES('$r_user', '$r_email', '$r_pass', 20)");
            header('Location:./tienda/');
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="sistema/js/icons.js"></script>
    <script src="sistema/js/jquery.js"></script>
    <link rel="stylesheet" href="css/login.css">
    <title>Login</title>
</head>
<body>
<div class="loader_bg">
        <div class="loader"></div>
    </div>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form action="" method="post" class="sign-in-form">
                    <h2 class="title">Iniciar Session</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="user" id="" placeholder="Username">
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="pass" id="" placeholder="Password">
                    </div>
                    <div class="alert"><?php echo isset($alert) ? $alert : '' ?></div>
                    <button type="submit" name="login" class="btn solid">login</button>
                    <p class="social-text">Mis redes sociales</p>
                    <div class="social-media">
                        <a href="https://es-la.facebook.com/PONSCA.simonbolivar/" class="social-icon"><i class="fab fa-facebook"></i></a>
                        <a href="https://twitter.com/ponscab?lang=es" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.google.com/search?q=ponsca" class="social-icon"><i class="fab fa-google"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
                    </div>
                </form>

                <form action="" method="post" class="sign-up-form">
                    <h2 class="title">Registrarse</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="r_user" id="r_user" placeholder="Username">
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="r_email" id="r_email" placeholder="Email">
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="r_pass" id="r_pass" placeholder="Password">
                    </div>
                    <div class="alert"><?php echo isset($msg) ? $msg : '' ?></div>
                    <button type="submit" name="register" class="btn solid">Registro</button>
                    <p class="social-text">Mis redes sociales</p>
                    <div class="social-media">
                        <a href="https://es-la.facebook.com/PONSCA.simonbolivar/" class="social-icon"><i class="fab fa-facebook"></i></a>
                        <a href="https://twitter.com/ponscab?lang=es" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.google.com/search?q=ponsca" class="social-icon"><i class="fab fa-google"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
                    </div>
                </form>
            </div>
        </div>
        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>Nuevo Aqui?</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    Esse incidunt, ullam sed debitis Esse incidunt, ullam sed debitis</p>
                    <button class="btn transparent" id="sign-up-btn">Sign Up</button>
                </div>
                <img src="img/rocket.svg" class="image" alt="svg">
            </div>

            <div class="panel right-panel">
                <div class="content">
                    <h3>Uno de nosotros?</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    Esse incidunt, ullam sed debitis Esse incidunt, ullam sed debitis</p>
                    <button class="btn transparent" id="sign-in-btn">Sign In</button>
                </div>
                <img src="img/desk.svg" class="image" alt="svg">
            </div>
        </div>
    </div>
    <script src="js/login.js"></script>
</body>
</html>