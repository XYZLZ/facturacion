-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-09-2023 a las 19:29:48
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.0.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `facturacion3`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_precio_producto` (`n_cantidad` INT, `n_precio` DECIMAL(10,2), `codigo` INT)   BEGIN
        	DECLARE nueva_existencia int;
            DECLARE nuevo_total decimal(10,2);
            DECLARE nuevo_precio decimal(10,2);
            
            DECLARE cant_actual int;
            DECLARE pre_actual decimal(10,2);
            
            DECLARE actual_existencia int;
            DECLARE actual_precio decimal(10,2);
            
            SELECT precio, existencia INTO actual_precio, actual_existencia FROM producto WHERE 				codproducto = codigo;
            
            SET nueva_existencia = actual_existencia + n_cantidad;
            SET nuevo_total = (actual_existencia * actual_precio) + (n_cantidad * n_precio);
            SET nuevo_precio = nuevo_total / nueva_existencia;
            
            UPDATE producto SET existencia = nueva_existencia, precio = nuevo_precio WHERE 						codproducto = codigo;
            
            SELECT nueva_existencia, nuevo_precio;
       END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (IN `codigo` INT, IN `cantidad` INT, IN `token_user` VARCHAR(50))   BEGIN
    	
        DECLARE precio_actual decimal(10,2);
        SELECT precio INTO precio_actual FROM producto WHERE codproducto =  codigo;
        
        INSERT INTO detalle_temp(token_user, codproducto, cantidad, precio_venta) VALUES(token_user, codigo, cantidad, 				precio_actual);
        
        SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp INNER
        JOIN producto p ON tmp.codproducto = p.codproducto WHERE tmp.token_user = token_user;
        
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura` (`no_factura` INT)   BEGIN
    	DECLARE existe_factura int;
        DECLARE registros int;
        DECLARE a int;
        
        DECLARE cod_producto int;
        DECLARE cant_producto int;
        DECLARE existencia_actual int;
        DECLARE nueva_existencia int;
        
        SET existe_factura = (SELECT COUNT(*) FROM factura WHERE nofactura = no_factura and estatus = 1);
        
        IF existe_factura > 0 THEN
        	CREATE TEMPORARY TABLE tbl_tmp (
            id bigint NOT NULL  AUTO_INCREMENT PRIMARY KEY,
            cod_prod bigint,
            cant_prod int);
            SET a = 1;
            
            SET registros = (SELECT COUNT(*) FROM detallefactura WHERE nofactura = no_factura);
            
            IF registros > 0 THEN
            	INSERT INTO tbl_tmp(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detallefactura WHERE nofactura
                = no_factura;
                
                WHILE a <= registros DO
                	SELECT cod_prod,cant_prod INTO cod_producto,cant_producto FROM tbl_tmp WHERE id = a;
                    SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = cod_producto;
                    SET nueva_existencia = existencia_actual + cant_producto;
                    UPDATE producto SET existencia = nueva_existencia WHERE codproducto =cod_producto;
                    SET a=a+1;
                END WHILE;
                UPDATE factura SET estatus = 2 WHERE nofactura = no_factura;
                DROP TABLE tbl_tmp;
                SELECT * FROM factura WHERE nofactura = no_factura;
            END IF;
        ELSE
        	SELECT 0 factura;
        END IF;
    
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dataDashboard` ()   BEGIN
    
    	DECLARE usuarios int;
        DECLARE clientes int;
        DECLARE clientesN int;
        DECLARE proveedores int;
        DECLARE productos int;
        DECLARE ventas int;
        
        SELECT COUNT(*) INTO usuarios FROM usuario WHERE estatus != 10;
        SELECT COUNT(*) INTO clientes FROM cliente WHERE estatus != 10;
        SELECT COUNT(*) INTO proveedores FROM proveedor WHERE estatus != 10;
        SELECT COUNT(*) INTO productos FROM producto WHERE estatus != 10;
        SELECT COUNT(*) INTO ventas FROM factura WHERE fecha > CURDATE() AND estatus != 10;
        SELECT COUNT(*) INTO clientesN FROM cliente WHERE dateadd > CURDATE() AND estatus != 10;
        
        SELECT usuarios,clientes,clientesN,proveedores,productos,ventas;
    
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (IN `id_detalle` INT, IN `token` VARCHAR(50))   BEGIN
    	DELETE FROM detalle_temp WHERE correlativo = id_detalle;
        
        SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta FROM
        detalle_temp tmp INNER JOIN producto p ON tmp.codproducto = p.codproducto WHERE tmp.token_user
        = token;
      END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (`cod_usuario` INT, `cod_cliente` INT, `token` VARCHAR(50))   BEGIN
    		DECLARE factura int;
            
            DECLARE registros int;
            DECLARE total decimal(10,2);
            
            DECLARE nueva_existencia int;
            DECLARE existencia_actual int;
            
            DECLARE tmp_cod_producto int;
            DECLARE tmp_cant_producto int;
            DECLARE a int;
            SET a = 1;
            
            CREATE TEMPORARY TABLE tbl_tmp_tokenuser (
            	id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cod_prod bigint,
                cant_prod int);
            SET registros = (SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
            
          IF registros > 0 THEN 
            INSERT INTO tbl_tmp_tokenuser(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detalle_temp WHERE token_user = token;
            INSERT INTO factura(usuario, codcliente) VALUES(cod_usuario,cod_cliente);
            SET factura = LAST_INSERT_ID();
            INSERT INTO detallefactura(nofactura,codproducto,cantidad,precio_venta) SELECT (factura) AS nofactura,
            codproducto,cantidad,precio_venta FROM detalle_temp WHERE token_user = token;
            
            WHILE a <= registros DO
            SELECT cod_prod,cant_prod INTO tmp_cod_producto, tmp_cant_producto FROM tbl_tmp_tokenuser WHERE id = a;
            SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = tmp_cod_producto;
            
            SET nueva_existencia = existencia_actual - tmp_cant_producto;
            UPDATE producto SET existencia = nueva_existencia WHERE codproducto = tmp_cod_producto;
            SET a=a+1;
           END WHILE;
           
           SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp WHERE token_user = token);
           UPDATE factura SET totalfactura = total WHERE nofactura = factura;
           DELETE FROM detalle_temp WHERE token_user = token;
           TRUNCATE TABLE tbl_tmp_tokenuser;
           SELECT * FROM factura WHERE nofactura = factura;
         ELSE
         		SELECT 0;
         END IF;
    
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `nit` int(11) DEFAULT NULL,
  `usuario` varchar(100) NOT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(150) NOT NULL,
  `telefono` int(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `dateadd` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `nit`, `usuario`, `nombre`, `email`, `password`, `telefono`, `direccion`, `dateadd`, `usuario_id`, `estatus`) VALUES
(1, 101010101, 'marcos', 'Marcos ', 'marcos@marcos.com', '202cb962ac59075b964b07152d234b70', 878766787, 'Guatemala, Guatemala', '2018-02-15 21:55:51', 1, 5),
(2, 87654321, '', 'Marta Gonzales', '', '', 34343434, 'Calzada Buena Vista', '2018-02-15 21:57:03', 1, 1),
(3, 0, '', 'Elena HernÃ¡ndez', '', '', 987897987, 'Guatemala, Chimaltenango', '2018-02-15 21:59:20', 2, 0),
(4, 0, '', 'Julio Maldonado', '', '', 908098979, 'Avenida las Americas Zona 14', '2018-02-15 22:00:31', 3, 0),
(5, 203945, '', 'Helen', '', '', 98789798, 'Guatemala', '2018-02-18 10:53:53', 1, 1),
(6, 0, '', 'Juan', '', '', 7987987, 'Chimaltenango', '2018-02-18 10:56:44', 1, 0),
(7, 798798798, '', 'Jorge Maldonado', '', '', 2147483647, 'Colonia la Flores', '2018-02-18 11:10:07', 1, 1),
(8, 983845, '', 'Marta Cabrera', '', '', 987987987, 'Guatemala', '2018-02-18 11:11:40', 2, 1),
(9, 79879879, '', 'Julio Estrada', '', '', 897987987, 'Avenida Elena', '2018-02-18 11:13:23', 3, 1),
(10, 2147483647, '', 'Roberto Morazan', '', '', 2147483647, 'Chimaltenango, Guatemala', '2018-03-04 19:17:22', 1, 1),
(11, 898798798, '', 'Rosa Pineda', '', '', 987998788, 'Ciudad Quetzal', '2018-03-04 19:17:45', 1, 1),
(12, 0, '', 'Angel Molina', '', '', 2147483647, 'Calzada Buena Vista', '2018-03-04 19:18:21', 1, 1),
(13, 89898989, '', 'Roberto Estrada', '', '', 2147483647, 'Ensanche Espaillat', '2022-02-14 10:47:27', 18, 1),
(14, 12344, '', 'mario', '', '', 324567, 'asdasd', '2022-02-14 13:55:42', 18, 1),
(15, 0, '', '', '', '', 0, '', '2022-02-14 13:55:42', 18, 0),
(16, 0, '', '', '', '', 0, '', '2022-02-14 13:55:44', 18, 0),
(17, 0, '', 'Dionela', '', '', 809283456, '24 de abril', '2022-04-22 21:27:12', 1, 1),
(18, 8099090, '', 'Juan', '', '', 1919192, 'direccion', '2022-04-25 22:12:03', 1, 1),
(19, 123456, '', 'Dionela', '', '', 2147483647, '24 de abril', '2022-04-27 16:48:33', 1, 1),
(20, 0, '', 'Carlos Arturo', '', '', 12345, 'Nueva direccion678', '2022-07-10 15:42:55', 18, 1),
(21, 12723394, '', 'el nombre', '', '', 1234565467, 'Direccion Complete', '2022-07-16 08:25:12', 1, 0),
(22, NULL, 'newUser', NULL, 'new@new.com', '202cb962ac59075b964b07152d234b70', NULL, NULL, '2022-07-18 14:42:38', 20, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` bigint(20) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` bigint(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `direccion` text NOT NULL,
  `Iva` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nit`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `Iva`) VALUES
(1, '29389238', 'OverSonic Tech', 'Tecnologia de punta', 8099883747, 'oversonic@gmail.com', 'Santo Domingo, Rep Dom', '18.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
  `correlativo` bigint(11) NOT NULL,
  `nofactura` bigint(11) DEFAULT NULL,
  `codproducto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `detallefactura`
--

INSERT INTO `detallefactura` (`correlativo`, `nofactura`, `codproducto`, `cantidad`, `precio_venta`) VALUES
(1, 1, 1, 2, '114.62'),
(2, 1, 2, 1, '1500.00'),
(3, 1, 3, 1, '250.00'),
(4, 2, 1, 1, '114.62'),
(5, 2, 2, 1, '1500.00'),
(6, 2, 3, 1, '250.00'),
(7, 3, 1, 1, '114.62'),
(8, 3, 2, 1, '1500.00'),
(10, 4, 4, 1, '10000.00'),
(11, 4, 5, 1, '500.00'),
(13, 5, 4, 2, '10000.00'),
(14, 5, 5, 1, '500.00'),
(16, 6, 3, 2, '250.00'),
(17, 6, 1, 4, '114.62'),
(19, 7, 1, 2, '114.62'),
(20, 7, 4, 2, '10000.00'),
(22, 8, 3, 2, '250.00'),
(23, 8, 6, 1, '2000.00'),
(25, 9, 1, 2, '114.62'),
(26, 9, 3, 4, '250.00'),
(28, 10, 1, 1, '114.62'),
(29, 11, 2, 1, '1500.00'),
(30, 12, 5, 4, '500.00'),
(31, 13, 1, 1, '114.62'),
(32, 13, 2, 1, '1500.00'),
(33, 13, 3, 1, '250.00'),
(34, 14, 2, 1, '1500.00'),
(35, 15, 1, 3, '114.62'),
(36, 15, 8, 2, '125.71'),
(37, 16, 4, 1, '10000.00'),
(38, 17, 1, 1, '114.62'),
(39, 18, 1, 1, '114.62'),
(40, 18, 3, 1, '250.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `correlativo` int(11) NOT NULL,
  `token_user` varchar(50) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

CREATE TABLE `entradas` (
  `correlativo` int(11) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`correlativo`, `codproducto`, `fecha`, `cantidad`, `precio`, `usuario_id`) VALUES
(1, 1, '0000-00-00 00:00:00', 150, '110.00', 1),
(2, 2, '2018-04-05 00:12:15', 100, '1500.00', 1),
(3, 3, '2018-04-07 22:48:23', 200, '250.00', 9),
(4, 4, '2018-09-08 22:28:50', 50, '10000.00', 1),
(5, 5, '2018-09-08 22:34:38', 100, '500.00', 1),
(6, 6, '2018-09-08 22:35:27', 8, '2000.00', 1),
(7, 7, '2018-12-02 00:15:09', 75, '2200.00', 1),
(8, 8, '2018-12-02 00:39:42', 75, '160.00', 1),
(9, 9, '2022-02-09 12:33:48', 500, '250.00', 1),
(10, 9, '2022-02-11 09:30:44', 500, '500.00', 18),
(11, 8, '2022-02-11 09:39:33', 100, '100.00', 18),
(12, 9, '2022-04-22 18:31:20', 200, '200.00', 1),
(13, 10, '2022-04-26 20:51:05', 30, '200.00', 1),
(14, 10, '2022-04-27 15:34:44', 50, '200.00', 1),
(15, 8, '2022-05-15 10:44:51', 5, '125.00', 1),
(16, 11, '2022-07-18 14:07:09', 300, '300.00', 1),
(17, 9, '2023-06-30 18:11:15', 12, '1200.00', 1),
(18, 9, '2023-06-30 18:12:05', 12, '354.00', 1),
(19, 9, '2023-06-30 18:12:27', 100, '354.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `nofactura` bigint(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) DEFAULT NULL,
  `codcliente` int(11) DEFAULT NULL,
  `totalfactura` decimal(10,2) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`nofactura`, `fecha`, `usuario`, `codcliente`, `totalfactura`, `estatus`) VALUES
(1, '2022-02-15 17:01:35', 1, 7, '1979.24', 1),
(2, '2022-02-15 17:05:37', 1, 7, '1864.62', 2),
(3, '2022-02-16 09:39:50', 1, 1, '1614.62', 1),
(4, '2022-02-16 09:44:41', 1, 9, '10500.00', 1),
(5, '2022-02-16 10:40:19', 1, 13, '20500.00', 1),
(6, '2022-02-16 10:41:29', 1, 1, '958.48', 2),
(7, '2022-02-16 10:44:03', 1, 1, '20229.24', 1),
(8, '2022-02-16 10:52:55', 1, 2, '2500.00', 1),
(9, '2022-02-16 11:01:50', 1, 13, '1229.24', 1),
(10, '2022-02-16 11:11:21', 1, 1, '114.62', 2),
(11, '2022-02-16 11:16:02', 1, 1, '1500.00', 2),
(12, '2022-02-16 11:17:57', 1, 9, '2000.00', 1),
(13, '2022-02-18 09:35:42', 1, 11, '1864.62', 1),
(14, '2022-04-22 20:59:30', 1, 5, '1500.00', 1),
(15, '2022-04-25 22:14:03', 1, 18, '595.28', 2),
(16, '2022-04-27 15:36:56', 1, 8, '10000.00', 1),
(17, '2022-04-28 16:07:59', 20, 13, '114.62', 1),
(18, '2022-05-15 10:56:45', 1, 13, '364.62', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje`
--

CREATE TABLE `mensaje` (
  `ID` int(11) UNSIGNED NOT NULL,
  `para` varchar(180) DEFAULT NULL,
  `de` varchar(180) DEFAULT NULL,
  `leido` varchar(180) DEFAULT NULL,
  `fecha` varchar(180) DEFAULT NULL,
  `rol` varchar(20) NOT NULL,
  `asunto` varchar(180) DEFAULT NULL,
  `texto` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `mensaje`
--

INSERT INTO `mensaje` (`ID`, `para`, `de`, `leido`, `fecha`, `rol`, `asunto`, `texto`) VALUES
(10, 'carlos', 'Benjamin', 'si', '27/04/2022, 4:00 pm', 'Administrador', 'prueba', '123'),
(4, 'julio', 'juan', 'si', '27/04/2022, 12:05 am', '', 'nuevo mensaje', 'hola'),
(11, 'julio', 'Benjamin', NULL, '27/04/2022, 9:06 pm', 'Administrador', 'ASDAASDDA', 'ASDDAS'),
(7, 'julio', 'Benjamin', 'si', '27/04/2022, 12:13 am', '', 'julio', 'julio'),
(12, 'julio', 'Benjamin', NULL, '15/05/2022, 8:00 pm', 'Administrador', 'pruena', '123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `detalle` text NOT NULL,
  `proveedor` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `existencia` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1,
  `foto` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `descripcion`, `detalle`, `proveedor`, `precio`, `existencia`, `date_add`, `usuario_id`, `estatus`, `foto`) VALUES
(1, 'Mouse USB', '', 11, '114.62', 316, '2018-04-05 00:09:34', 1, 1, 'img_producto.png'),
(2, 'Monitor LCD', '', 3, '1500.00', 95, '2018-04-05 00:12:15', 1, 1, 'img_producto.png'),
(3, 'Teclado USB', '', 9, '250.00', 192, '2018-04-07 22:48:23', 9, 1, 'img_producto.png'),
(4, 'Cama', '', 5, '10000.00', 44, '2018-09-08 22:28:50', 1, 1, 'img_21084f55f7b61c8baa2726ad0b4a1dca.jpg'),
(5, 'Plancha', '', 6, '500.00', 94, '2018-09-08 22:34:38', 1, 1, 'img_25c1e2ae283b99e83b387bf800052939.jpg'),
(6, 'Monitor', '', 11, '2000.00', 7, '2018-09-08 22:35:27', 1, 1, 'img_producto.png'),
(7, 'Monitor LCD 17', '', 9, '2200.00', 75, '2018-12-02 00:15:09', 1, 0, 'img_1328286905ecc9eec8e81b94fa1786b9.jpg'),
(8, 'USG 32 GB', 'No lo compre', 11, '125.69', 180, '2018-12-02 00:39:42', 1, 1, 'img_fe57190c7837a5f7dd60bbf6b5152bfe.jpg'),
(9, 'Laptop HP', '', 11, '354.27', 1324, '2022-02-09 12:33:48', 1, 1, 'img_e82a97526430a3f78a6917c95ab8810b.jpg'),
(10, 'PC Delll', '', 7, '200.00', 80, '2022-04-26 20:51:05', 1, 1, 'img_producto.png'),
(11, 'Laptop Levono', 'Intel Core I5 10t, 16 ram 256SSD', 7, '300.00', 300, '2022-07-18 14:07:09', 1, 0, 'img_498cf6c3a56e107a6f96bbd9d7cbbb05.jpg');

--
-- Disparadores `producto`
--
DELIMITER $$
CREATE TRIGGER `entradas_A_I` AFTER INSERT ON `producto` FOR EACH ROW BEGIN

		INSERT INTO entradas(codproducto,cantidad,precio,usuario_id) 

		VALUES(new.codproducto,new.existencia,new.precio,new.usuario_id);    

	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `codproveedor` int(11) NOT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` bigint(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`codproveedor`, `proveedor`, `contacto`, `telefono`, `direccion`, `date_add`, `usuario_id`, `estatus`) VALUES
(1, 'BIC', 'Claudia Rosales', 789877889, 'Avenida las Americas', '2018-03-20 23:13:43', 1, 0),
(2, 'CASIO', 'Jorge Herrera', 565656565656, 'Calzada Las Flores', '2018-03-20 23:14:41', 2, 0),
(3, 'Omega', 'Julio Estrada', 982877488, 'Avenida Elena Zona 4, Guatemala', '2018-03-24 23:21:10', 1, 1),
(4, 'Dell Compani', 'Roberto Estrada', 2147483647, 'Guatemala, Guatemala', '2018-03-24 23:21:59', 1, 1),
(5, 'Olimpia S.A', 'Elena Franco Morales', 564535676, '5ta. Avenida Zona 4 Ciudad', '2018-03-24 23:22:45', 1, 1),
(6, 'Oster', 'Fernando Guerra', 78987678, 'Calzada La Paz, Guatemala', '2018-03-24 23:24:43', 1, 1),
(7, 'ACELTECSA S.A', 'Ruben PÃ©rez', 789879889, 'Colonia las Victorias', '2018-03-24 23:25:39', 1, 1),
(8, 'Sony', 'Julieta Contreras', 89476787, 'Antigua Guatemala', '2018-03-24 23:26:45', 1, 1),
(9, 'VAIO', 'Felix Arnoldo Rojas', 476378276, 'Avenida las Americas Zona 13', '2018-03-24 23:30:33', 1, 1),
(10, 'SUMAR', 'Oscar Maldonado', 788376787, 'Colonia San Jose, Zona 5 Guatemala', '2018-03-24 23:32:28', 1, 1),
(11, 'HP', 'Angel Cardona', 2147483647, '5ta. calle zona 4 Guatemala', '2018-03-24 23:52:20', 2, 1),
(12, 'new Provedor', 'Carlos Perez', 89898989, 'la direccion', '2022-07-16 09:00:05', 3, 0),
(13, 'Provedor3', 'Elena morales', 8093874578, 'Miami', '2022-07-16 09:12:54', 1, 1),
(14, 'hgjhgj', 'ghgjhgj', 78687687, 'hgjgjgjg', '2022-07-16 09:13:31', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Supervisor'),
(3, 'Vendedor'),
(4, 'Cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `updates`
--

CREATE TABLE `updates` (
  `idupdate` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `rol` varchar(30) NOT NULL,
  `r_update` varchar(200) NOT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `updates`
--

INSERT INTO `updates` (`idupdate`, `usuario`, `rol`, `r_update`, `fecha`) VALUES
(1, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el usuario ruben a las 01:02:51', '2022-04-22'),
(2, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el cliente Helen a las 20:23:33', '2022-04-22'),
(3, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el cliente Marcos  a las 20:29:22', '2022-04-22'),
(4, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el cliente Marcos  a las 20:32:21', '2022-04-22'),
(5, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha creado el cliente Dionela a las 21:27:13', '2022-04-22'),
(6, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el usuario julio a las 20:18:29', '2022-04-24'),
(7, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha creado un usuario a las 20:49:10', '2022-04-24'),
(8, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha creado un usuario a las 22:24:01', '2022-04-24'),
(9, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el usuario carlos a las 02:32:43', '2022-04-25'),
(10, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha creado un usuario a las 22:11:06', '2022-04-25'),
(11, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el usuario thebigpower a las 22:11:27', '2022-04-25'),
(12, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha creado el cliente Juan a las 22:12:04', '2022-04-25'),
(13, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el provedor Omega a las 15:35:57', '2022-04-27'),
(14, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el cliente Marcos  a las 15:37:43', '2022-04-27'),
(15, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha editado el usuario carlos a las 15:59:34', '2022-04-27'),
(16, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha creado el cliente Dionela a las 16:48:33', '2022-04-27'),
(17, '', '', 'El   ha eliminado el usuario Stela a las 02:26:04', '2022-07-08'),
(18, '', '', 'El   ha eliminado el usuario emmanuel a las 02:26:37', '2022-07-08'),
(19, '', '', 'El   ha eliminado el usuario Casio a las 02:26:49', '2022-07-08'),
(20, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha creado un usuario a las 02:29:32', '2022-07-08'),
(21, '', '', 'El   ha eliminado el usuario jjdj a las 02:29:53', '2022-07-08'),
(22, '', '', 'El   ha creado un usuario a las 21:48:27', '2022-07-10'),
(23, '', '', 'El   ha creado un usuario a las 22:15:28', '2022-07-10'),
(24, '', '', 'El   ha eliminado el usuario fdfdfd a las 22:16:37', '2022-07-10'),
(25, '', '', 'El   ha creado un usuario a las 22:17:38', '2022-07-10'),
(26, '', '', 'El   ha eliminado el usuario Efrain GÃ³mez a las 22:32:39', '2022-07-10'),
(27, '', '', 'El   ha eliminado el usuario Marta Elena Franco a las 23:57:06', '2022-07-10'),
(28, '', '', 'El   ha eliminado el usuario fdfdfd a las 17:05:44', '2022-07-16'),
(29, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha eliminado el provedor new Provedor a las 18:05:53', '2022-07-16'),
(30, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha eliminado el provedor hgjhgj a las 18:13:37', '2022-07-16'),
(31, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha guardado un producto a las 23:07:09', '2022-07-18'),
(32, 'Benjamin', 'Administrador', 'El Administrador Benjamin ha eliminado el usuario fdfdfd a las 00:57:11', '2022-07-18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `usuario` varchar(15) DEFAULT NULL,
  `clave` varchar(100) DEFAULT NULL,
  `rol` int(11) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `estatus`) VALUES
(1, 'Benjamin', 'overSonic@gmail.com', 'Benjamin', '202cb962ac59075b964b07152d234b70', 1, 1),
(2, 'Julio Estrada', 'julio@gmail.com', 'julio', '18eba6bb36aa9078c38ff2fe5a9f0d0d', 1, 1),
(3, 'Carlos Hernandez', 'carlos@gmail.com', 'carlos', '2a2e9a58102784ca18e2605a4e727b5f', 1, 1),
(5, 'Marta Elena Franco', 'marta@gmail.com', 'marta', 'a763a66f984948ca463b081bf0f0e6d0', 3, 0),
(7, 'Carol Cabrera', 'carol@gmail.com', 'carol', 'a9a0198010a6073db96434f6cc5f22a8', 2, 0),
(8, 'Marvin Solares ', 'marvin@gmail.com', 'marvin', 'dba0079f1cb3a3b56e102dd5e04fa2af', 3, 1),
(9, 'Alan Melgar', 'alan@gmail.com', 'alan', '02558a70324e7c4f269c69825450cec8', 2, 1),
(10, 'Efrain GÃ³mez', 'efrain@gmail.com', 'efrain', '69423f0c254e5c1d2b0f5ee202459d2c', 2, 0),
(11, 'Fran Escobar', 'fran@gmail.com', 'fran', '2c20cb5558626540a1704b1fe524ea9a', 1, 1),
(12, 'Hana Montenegro', 'hana@gmail.com', 'hana', '52fd46504e1b86d80cfa22c0a1168a9d', 3, 1),
(13, 'Fredy Miranda', 'fredy@gmail.com', 'fredy', 'b89845d7eb5f8388e090fcc151d618c8', 2, 1),
(14, 'Roberto Salazar', 'roberto@hotmail.com', 'roberto', 'c1bfc188dba59d2681648aa0e6ca8c8e', 3, 1),
(15, 'William Fernando PÃ©rez', 'william@hotmail.com', 'william', 'fd820a2b4461bddd116c1518bc4b0f77', 3, 1),
(16, 'Francisco Mora', 'frans@gmail.com', 'frans', '64dd0133f9fb666ca6f4692543844f31', 3, 1),
(17, 'Ruben Guevara', 'ruben@hotmail.es', 'ruben', '202cb962ac59075b964b07152d234b70', 3, 1),
(18, 'Juan', 'juan@gmail.com', 'juan', '202cb962ac59075b964b07152d234b70', 1, 1),
(19, 'Casio', 'casio@gmail.com', 'Casio', '73278a4a86960eeb576a8fd4c9ec6997', 3, 0),
(20, 'Administrador', 'admin@gmail.com', 'admin', '202cb962ac59075b964b07152d234b70', 1, 1),
(21, 'emmanuel', 'tureal09@gmail.com', 'thebigpower', '3dfb6163d3c040961dbb502c11d09613', 1, 0),
(22, 'Stela', 'stela@gmail.com', 'Stela', '202cb962ac59075b964b07152d234b70', 3, 0),
(23, 'jjdj', 'lll@lll.com', 'ksjdaksdh', '202cb962ac59075b964b07152d234b70', 3, 0),
(24, 'pedro', 'violaferrerasmodesto@gmail.com', 'pedro', '202cb962ac59075b964b07152d234b70', 3, 1),
(26, 'FD', 'fd@fd.com', 'FD', '81dc9bdb52d04dc20036dbd8313ed055', 2, 1),
(27, 'FK', 'fk@fk.com', 'FK', '81dc9bdb52d04dc20036dbd8313ed055', 3, 1),
(28, 'NJ', 'nj@gmail.com', 'NJ', '2a2e9a58102784ca18e2605a4e727b5f', 1, 1),
(29, 'comino', 'new@new.com', 'NewCominoX', '2a2e9a58102784ca18e2605a4e727b5f', 1, 1),
(30, 'fdfdfd', '12@12.com', '12', '3236b3179c1e0fd4171722e9c8170009', 1, 0),
(31, 'fdfdfd', 'vil@vil.com', 'vil', '3236b3179c1e0fd4171722e9c8170009', 1, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`),
  ADD KEY `nofactura` (`nofactura`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `nofactura` (`token_user`),
  ADD KEY `codproducto` (`codproducto`),
  ADD KEY `token_user` (`token_user`);

--
-- Indices de la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`nofactura`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `codcliente` (`codcliente`);

--
-- Indices de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`),
  ADD KEY `proveedor` (`proveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`codproveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `updates`
--
ALTER TABLE `updates`
  ADD PRIMARY KEY (`idupdate`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD KEY `rol` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  MODIFY `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `codproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `updates`
--
ALTER TABLE `updates`
  MODIFY `idupdate` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD CONSTRAINT `detallefactura_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`),
  ADD CONSTRAINT `detallefactura_ibfk_3` FOREIGN KEY (`nofactura`) REFERENCES `factura` (`nofactura`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_ibfk_1` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `entradas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`codcliente`) REFERENCES `cliente` (`idcliente`),
  ADD CONSTRAINT `factura_ibfk_3` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`proveedor`) REFERENCES `proveedor` (`codproveedor`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `proveedor_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`idrol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
