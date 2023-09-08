<?php
    session_start();
    include "../php/conexion.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include "./resourses/scripts.php" ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta</title>
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
        <main>
            <div class="new_sale">  
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4>Datos del Cliente</h4>
                <a href="#" class="btn_new btn_new_cliente" style="border-color: var(--color-primary);" ><i class="fas fa-plus"></i> Nuevo Cliente</a>
            </div>
            <form action="" name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos" method="post">
                <input type="hidden" name="action" value="addCliente">
                <input type="hidden" name="idcliente" id="idcliente" value="" required>
                <div class="wd30">
                    <label for="">Nit</label>
                    <input type="text" name="nit_cliente" id="nit_cliente">
                </div>

                <div class="wd30">
                    <label for="">Nombre</label>
                    <input type="text" name="nom_cliente" id="nom_cliente" disabled required>
                </div>

                <div class="wd30">
                    <label for="">Telefono</label>
                    <input type="number" name="tel_cliente" id="tel_cliente" disabled required>
                </div>

                <div class="wd30">
                    <label for="">Direccion</label>
                    <input type="text" name="dir_cliente" id="dir_cliente" disabled required>
                </div>
                <div class="wd100" id="div_registro_cliente">
                    <button type="submit" class="btn_save"><i class="fas fa-save fa-lg"></i>Guardar</button>
                </div>
            </form>
        </div>
        <div class="datos_venta">
            <h4>Datos de Venta</h4>
            <div class="datos">
                <div class="wd50">
                    <label for="">Vendedor</label>
                    <p><?php echo $_SESSION['nombre']; ?></p>
                </div>
                <div class="wd50">
                    <label for="">Acciones</label>
                    <div id="acciones_venta">
                        <a href="#" class="btn_ok textcenter" style="border-color: var(--color-danger);" id="btn_anular_venta"><i class="fas fa-ban"></i> Anular</a>
                        <a href="#" class="btn_new textcenter" id="btn_facturar_venta"><i class="fas fa-edit" style="display:none;"></i>Procesar</a>
                    </div>
                </div>
            </div>
        </div>
        <table class="tbl_venta recent-orders">
            <thead>
                <tr>
                    <th width="100px">Codigo</th>
                    <th>Descripcion</th>
                    <th>Existencia</th>
                    <th width="100px">Cantidad</th>
                    <th class="textright">Precio</th>
                    <th class="textright">Precio Total</th>
                    <th>Accion</th>
                </tr>
                <tr>
                    <td><input type="text" name="txt_cod_producto" id="txt_cod_producto"></td>
                    <td id="txt_descripcion">-</td>
                    <td id="txt_existencia">-</td>
                    <td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1" disabled></td>
                    <td id="txt_precio" class="textright">0.00</td>
                    <td id="txt_precio_total" class="textright">0.00</td>
                    <td><a href="#" id="add_product_venta" class="link_add success"><i class="fas fa-plus"></i> Agregar</a></td>
                </tr>
                <tr>
                    <th>Codigo</th>
                    <th colspan="2">Descripcion</th>
                    <th>Cantidad</th>
                    <th class="textright">Precio</th>
                    <th class="textright">Precio Total</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody id="detalle_venta">
                <!-- contenido Ajax -->
            </tbody>
            <tfoot id="detalle_totales">
                <!-- contenido Ajax -->
            </tfoot>
        </table>
        </div>
        </main>

	<?php include "./resourses/footer.php"; ?>

    <script>
        $(document).ready(function() {
            var usuarioid = '<?php echo $_SESSION['iduser']; ?>';
            searchForDetalle(usuarioid);
        });
    </script>
</body>
</html>