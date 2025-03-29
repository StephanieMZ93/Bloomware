-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 29-03-2025 a las 23:49:24
-- Versión del servidor: 8.4.3
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
  `Nombre` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Apellidos` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Contraseña` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
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
  `ID_Cliente` int NOT NULL,
  `ID_Producto` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Nombre` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Apellidos` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`ID_Cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

DROP TABLE IF EXISTS `empleado`;
CREATE TABLE IF NOT EXISTS `empleado` (
  `ID_Empleado` int NOT NULL,
  `Nombre` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Apellidos` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Cargo` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Contraseña` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`ID_Empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestionar`
--

DROP TABLE IF EXISTS `gestionar`;
CREATE TABLE IF NOT EXISTS `gestionar` (
  `Producto_ID_Producto` int NOT NULL,
  `Cliente_ID_Cliente` int NOT NULL,
  PRIMARY KEY (`Producto_ID_Producto`,`Cliente_ID_Cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `ID_Producto` int NOT NULL AUTO_INCREMENT,
  `Nombre_Producto` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `Cantidad` int NOT NULL,
  `Precio` int NOT NULL,
  `Categoria` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Lote` int NOT NULL,
  `Fecha_Vencimiento` date NOT NULL,
  `Administrador_ID_Administrador` int NOT NULL,
  PRIMARY KEY (`ID_Producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE IF NOT EXISTS `proveedores` (
  `Nit_Proveedor` int NOT NULL,
  `Nombre` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Direccion` varchar(60) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Categoria` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`Nit_Proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `realizacion`
--

DROP TABLE IF EXISTS `realizacion`;
CREATE TABLE IF NOT EXISTS `realizacion` (
  `Reporte_ID_Reporte` int NOT NULL,
  `Empleado_ID_Empleado` int NOT NULL,
  `Fecha` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`Reporte_ID_Reporte`,`Empleado_ID_Empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte`
--

DROP TABLE IF EXISTS `reporte`;
CREATE TABLE IF NOT EXISTS `reporte` (
  `ID_Reporte` int NOT NULL AUTO_INCREMENT,
  `Fecha y hora` date NOT NULL,
  `ID_Clientes` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ID_Productos` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ID_Usuario` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Nit_Proveedor` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Administrador_ID_Administrador` int NOT NULL,
  PRIMARY KEY (`ID_Reporte`,`Administrador_ID_Administrador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suministra`
--

DROP TABLE IF EXISTS `suministra`;
CREATE TABLE IF NOT EXISTS `suministra` (
  `Producto_ID_Producto` int NOT NULL,
  `Proveedores_Nit_Proveedor` int NOT NULL,
  PRIMARY KEY (`Producto_ID_Producto`,`Proveedores_Nit_Proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `ID_Usuario` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Apellido` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Contraseña` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vende`
--

DROP TABLE IF EXISTS `vende`;
CREATE TABLE IF NOT EXISTS `vende` (
  `Empleado_ID_Empleado` int NOT NULL,
  `Producto_ID_Producto` int NOT NULL,
  PRIMARY KEY (`Empleado_ID_Empleado`,`Producto_ID_Producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
