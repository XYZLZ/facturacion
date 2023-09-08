<aside>
            <div class="top">
                <div class="logo">
                    <img src="img/logo.png" alt="user">
                    <h2>Over<span class="danger">Sonic</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>
            <div class="sidebar">
                <a href="index.php" class="a active">
                    <span class="material-icons-sharp">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <?php
              //  session_start();
                
					if ($_SESSION['rol'] == 1) {
					
				?>
                <a href="lista_usuarios.php" class="a">
                    <span class="material-icons-sharp">person</span>
                    <h3>Usuarios</h3>
                </a>
                <?php } ?>
                <a href="ventas.php" class="a">
                    <span class="material-icons-sharp">receipt_long</span>
                    <h3>Ventas</h3>
                </a>
                <a href="lista_clientes.php" class="a">
                    <span class="material-icons-sharp">people</span>
                    <h3>Clientes</h3>
                </a>
                <a href="#" class="a">
                    <span class="material-icons-sharp">mail_outline</span>
                    <h3>Messages</h3>
                    <span class="message-count">26</span>
                </a>
                <a href="lista_productos.php" class="a">
                    <span class="material-icons-sharp">inventory</span>
                    <h3>Productos</h3>
                </a>
                <?php if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){ ?>

                <a href="lista_provedores.php" class="a">
                <span class="material-icons-sharp">business</span>
                    <h3>Proveedores</h3>
                </a>
                <?php }?>
                <a href="nueva_venta.php" class="a">
                    <span class="material-icons-sharp">add</span>
                    <h3>Nueva Venta</h3>
                </a>
                <a href="config.php" class="a">
                    <span class="material-icons-sharp">settings</span>
                    <h3>Settings</h3>
                </a>
                <a href="salir.php">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
         <!-- * -------- END OF ASIDE ---------- -->
        <script>
            // for change the active class in the sidebar
    $(document).on('click', '.sidebar a', function(){
    $(this).addClass('active').siblings().removeClass('active');

    
    }); 
</script>