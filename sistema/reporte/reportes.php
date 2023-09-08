<?php
session_start();
    if (empty($_SESSION['active'])) {
        header('location:../');
    }
    include '../../php/conexion.php';
    $date =date("d-m-Y"); // obtener la fecha actual
    ob_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body{
            display: grid;
            align-items: center;
            justify-content: center;
            background: #fff;
            font-size: 0.9rem;
            height: 100vh;
        }
        table{
            border-collapse: collapse;
            box-shadow: 0 5px 10px grey;
            background-color: #fff;
            text-align: left;
            overflow: hidden;
        }

        thead{
            box-shadow: 0 5px 10px grey;
            background: #7380ec !important ;
            color: #fff;
        }

        th{
            padding: 1rem 2rem;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            font-size: 0.7rem;
            font-weight: 900;
        }

        table tbody tr:nth-child(odd){
            background: #dce1eb;
        }

        td{
            padding: 1rem 2rem;
            color:#677483;

        }

        .generado{
            text-align: center;
            margin-top: 20px;
            font-size: 1.1rem;
            color: #363949;
        }
    </style>
</head>
<body>


<table class="table table-striped ">
    <thead>
            <tr>
                <th>No.</th>
                <th>Fecha / Hora</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Estado</th>
                <th class="textright">Total Factura</th>
            </tr>
            </thead>
            <tbody>

            <?php
            

            $query = mysqli_query($conexion, "SELECT f.nofactura,f.fecha,f.totalfactura,f.codcliente,
            f.estatus,u.nombre AS vendedor,cl.nombre AS cliente FROM factura f INNER JOIN usuario u ON
            f.usuario = u.idusuario INNER JOIN cliente cl ON f.codcliente = cl.idcliente WHERE f.estatus
            != 10 ORDER BY f.fecha ASC");
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
                        <td class="textright totalfactura"><span>$.</span><?php echo $data['totalfactura']; ?></td>
            </tr>
            </tbody>
            <?php
        }
    }

    ?>
        </table>

        <p class="generado">Reporte Generado el  <?php echo $date; ?> por <?php echo $_SESSION['nombre']; ?></p>
    
</body>
</html>

<?php

    $html = ob_get_clean();

    require_once '../pdf/autoload.inc.php';
	use Dompdf\Dompdf;
	use Dompdf\Options;

    // instantiate and use the dompdf class
			$dompdf = new Dompdf();

			// prevenir errores al cargar las imagenes
			$options = $dompdf->getOptions();
			$options->set(array('isRemoteEnabled' => true));
			$dompdf->setOptions($options);

			$dompdf->loadHtml($html);

			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('letter', 'portrait');

			// Render the HTML as PDF
			$dompdf->render();

            // ouput
            $dompdf->stream('reporte.pdf', array('Attachment' => false));

?>
