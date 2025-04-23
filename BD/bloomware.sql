-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 22-04-2025 a las 20:47:58
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bloomware`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

DROP TABLE IF EXISTS `administrador`;
CREATE TABLE IF NOT EXISTS `administrador` (
  `ID_Administrador` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Apellidos` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Contraseña` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`ID_Administrador`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`ID_Administrador`, `Nombre`, `Apellidos`, `Email`, `Telefono`, `Contraseña`) VALUES
(1, 'Stephanie', 'Martinez', 'smz021093@gmail.com', '3116250040', 'Mazu93.'),
(2, 'Marcela', 'Quiroga', 'marceq0725@gmail.com', '3507740524', 'Marce123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `ID_Cliente` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ID_Producto` int NOT NULL,
  `Apellidos` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`ID_Cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`ID_Cliente`, `Nombre`, `ID_Producto`, `Apellidos`, `Email`, `Telefono`) VALUES
(1, 'Nina', 2, 'Quiroga', 'ani-05@hotmail.com', '3132424236'),
(2, 'Martin', 4, 'Vivas', 'Martin@hotmail.com', '3167800000'),
(3, 'Antonio', 8, 'Cortes', 'antonioc@hotmail.com', '3125607928'),
(4, 'Andrea', 8, 'Martinez', 'aandre.fff@hotmail.com', '3444446788'),
(5, 'Aura', 11, 'Bermudez', 'auraa.0@hotmail.com', '3667778900'),
(6, 'Lorena', 10, 'Cardona', 'Lorencar@hotmail.com', '360000000'),
(7, 'Dora', 3, 'Cruz', 'doraz@hotmail.com', '3000009997');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

DROP TABLE IF EXISTS `empleado`;
CREATE TABLE IF NOT EXISTS `empleado` (
  `ID_Empleado` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Apellidos` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Cargo` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`ID_Empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`ID_Empleado`, `Nombre`, `Apellidos`, `Email`, `Telefono`, `Cargo`) VALUES
(1, 'Adriana', 'Perez', 'adrip@bloom.com', '35444444', 'Asesor de ventas'),
(2, 'Marina', 'Cruz', 'Maric@bloom.com', '3178000', 'Cajero'),
(3, 'Luz', 'Diaz', 'Luzdiaz@bloom.com', '3125698', 'Lider de marketing');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `ID_Producto` int NOT NULL AUTO_INCREMENT,
  `Nombre_Producto` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `Cantidad` int NOT NULL,
  `Precio` decimal(10,0) DEFAULT NULL,
  `Categoria` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Lote` varchar(10) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `Fecha_Vencimiento` date NOT NULL,
  `Administrador_ID_Administrador` int NOT NULL,
  PRIMARY KEY (`ID_Producto`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`ID_Producto`, `Nombre_Producto`, `Cantidad`, `Precio`, `Categoria`, `Lote`, `Fecha_Vencimiento`, `Administrador_ID_Administrador`) VALUES
(1, 'Brillo_Essence_Juicy_Bomb_Shiny', 10, 8500, 'brillo', '20250115A', '2026-05-11', 1),
(2, 'Pestañina_Vogue_Curvas_Perfectas', 9, 24950, 'pestañinas', '20241025B', '2025-12-19', 2),
(3, 'Polvo_Suelto_Samy_Translucido_Matte_Touch', 8, 19950, 'polvo', '20240926C', '2026-09-26', 2),
(4, 'Labial_Ruby_Rose_Lip_Oil_Sandía', 4, 13560, 'brillo', '20268967B', '2025-05-13', 2),
(5, 'Pestañina_Maybelline_Sky_High_Black_Waterproo', 7, 64950, 'pestañinas', '20267936A', '2026-11-23', 2),
(6, 'Polvo_Compacto_Ana_María_Spf_25_Avellana', 15, 27950, 'pestañinas', '20368946N', '2025-12-08', 2),
(7, 'Rubor_Barra_Essence_Baby_Got_Blush_Tn10', 6, 16500, 'Maquillaje_Facial', '21348046F', '2025-03-08', 2),
(8, 'Lapiz_Essence_Designer_Brown_Cejas', 1, 10950, 'Lapiz', '26789059F', '2026-08-08', 2),
(9, 'Polvo_Compacto_Aclarante_Ana_María_Spf_25_Ave', 15, 32950, 'polvo compacto', '23649047F', '2026-12-05', 2),
(10, 'Labial_Vogue_Líquido_Colorissimo_Extra_Brillo', 5, 14950, 'labiales_liquidos', '20316798B', '2026-05-29', 2),
(11, 'Brillo_Maybelline_Lip_Lifter_Gloss_Petal', 5, 60950, 'brillo', '20316798B', '2026-01-29', 2),
(12, 'BrPolvo_Compacto_Maybelline_Super_Natural_Bei', 12, 35950, 'polvos', '20786708C', '2024-04-29', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE IF NOT EXISTS `proveedores` (
  `Nit_proveedor` bigint NOT NULL,
  `Nombre` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Direccion` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Categoria` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`Nit_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`Nit_proveedor`, `Nombre`, `Direccion`, `Telefono`, `Categoria`, `Email`) VALUES
(860038671, 'Productos De Belleza Ana María S.a.s.', 'KR 62 14 - 75', '3173716780', 'Fabricante', 'servicioalcliente@cosmeticosanamaria.com'),
(8002128402, 'Maybelline Colombia', ' Autopista norte # 123 - 60 Piso 95', '6511300', 'Fabricante', 'notificaciones.loreal@loreal.com'),
(8002480925, 'COSMETICOS MARLIOU PARIS LTDA', 'CARRERA 19B # 168-50', '6789851', 'Fabricante', 'servicioalcliente@cosmeticosmarliou.com'),
(8110083834, 'COSMETICOS SAMY SA', 'KR 51 No 14-52', '4487269', 'Fabricante', 'samy@samycosmetics.com'),
(8600299972, 'Laboratorios de Cosméticos Vogue S.A.S', ' Kilómetro 7 vía Autopista Medellín – Par Industrial Interpa', '443 4660', 'Distribuidor', 'atc@loreal.com.co'),
(8909050321, 'Beautyholics', ' Calle 10 # 58-59', '018000914990', 'Distribuidor', 'servicio.cliente@prebel.com.co'),
(9004647945, 'INVERSIONES KAUTIVA S.A.S.', 'Cra 54 # 46 – 71  Loc 99 79', '4482015', 'Distribuidor', 'administracion@inversioneskautiva.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte`
--

DROP TABLE IF EXISTS `reporte`;
CREATE TABLE IF NOT EXISTS `reporte` (
  `ID_Reporte` int NOT NULL AUTO_INCREMENT,
  `Fecha y hora` date NOT NULL,
  `ID_Clientes` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ID_Productos` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ID_Usuario` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Nit_Proveedor` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Administrador_ID_Administrador` int NOT NULL,
  PRIMARY KEY (`ID_Reporte`,`Administrador_ID_Administrador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `ID_Usuario` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Apellido` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Contraseña` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Administrador_ID_Administrador` int NOT NULL,
  PRIMARY KEY (`ID_Usuario`,`Administrador_ID_Administrador`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`ID_Usuario`, `Nombre`, `Apellido`, `Email`, `Telefono`, `Contraseña`, `Administrador_ID_Administrador`) VALUES
(1, 'Stephanie', 'Martinez', 'smz021093@gmail.com', '3116250040', 'Mazu93.', 1),
(2, 'Marcela', 'Quiroga', 'marceq0725@gmail.com', '3507740524', 'Marce123', 2),
(3, 'Vendedor', '1', 'vendedor1@gmail.com', '3205428756', 'Vendedor1.', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
