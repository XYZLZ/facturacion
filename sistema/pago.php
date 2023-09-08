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
    <title>tienda</title>
    <?php include "resourses/scripts.php" ?>
</head>
<body>
<div class="loader_bg">
        <div class="loader"></div>
    </div>

    <div id="paypal-button-container"></div>
    <script src="https://www.paypal.com/sdk/js?client-id=AbVIb2O-EFswrWNlL0nRnM3zKWgx_8zN27BTrELhXef2a_mEpEgQVqLO9Xc-GhfMcLDs3S1ml5b1meX5&currency=USD"></script>
    
    <script>
        paypal.Buttons({
            style:{
                color:'blue',
                shape: 'pill',
                label: 'pay'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: 10
                        }
                    }]
                });
            },

            onApprove: function(data, actions){
                actions.order.capture().then(function(detalles){
                    console.log(detalles);
                });
            },

            onCancel: function(data){
                console.log(data);
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>