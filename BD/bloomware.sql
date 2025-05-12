-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 11-05-2025 a las 23:30:24
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
-- Estructura de tabla para la tabla `detalle_venta`
--

DROP TABLE IF EXISTS `detalle_venta`;
CREATE TABLE IF NOT EXISTS `detalle_venta` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_venta` int DEFAULT NULL,
  `id_producto` int DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_venta_venta` (`id_venta`),
  KEY `fk_detalle_venta_producto` (`id_producto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--
DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `ID_Producto` int NOT NULL AUTO_INCREMENT,
  `Nombre_Producto` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `Cantidad` int NOT NULL,
  `Precio` decimal(10,0) DEFAULT NULL,
  `Categoria` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Lote` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `Fecha_Vencimiento` date NOT NULL,
  PRIMARY KEY (`ID_Producto`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`ID_Producto`, `Nombre_Producto`, `Cantidad`, `Precio`, `Categoria`, `Lote`, `Fecha_Vencimiento`) VALUES
(1, 'Brillo Essence Bomb Shiny', 10, 8500, 'Maquillaje de labios', '20250115A', '2026-05-11'),
(2, 'Pestañina Vogue Curvas Perfectas', 9, 24950, 'Maquillaje de ojos', '20241025B', '2025-12-19'),
(3, 'Polvo Suelto Samy Translucido Matte', 8, 19950, 'Maquillaje Facial', '20240926C', '2026-09-26'),
(4, 'Labial Ruby Rose Lip Oil Sandía', 4, 13560, 'Maquillaje de labios', '20268967B', '2025-05-13'),
(5, 'Pestañina Maybelline Sky High Waterproof', 7, 64950, 'Maquillaje de ojos', '20267936A', '2026-11-23'),
(6, 'Polvo Compacto Ana María Spf 25 Avellana', 15, 27950, 'Maquillaje de ojos', '20368946N', '2025-12-08'),
(7, 'Rubor Barra Essence Baby Tn10', 6, 16500, 'Maquillaje Facial', '21348046F', '2025-03-08'),
(8, 'Lapiz Essence Designer Brown Cejas', 1, 10950, 'Maquillaje cejas', '26789059F', '2026-08-08'),
(9, 'Polvo Compacto Aclarante Ana Maria SPF25 Ave', 15, 32950, 'Maquillaje Facial', '23649047F', '2026-12-05'),
(10, 'Labial Vogue Liq Colorissimo Extra Brillo', 5, 14950, 'Maquillaje de labios', '20316798B', '2026-05-29'),
(11, 'Brillo Maybelline Lip Lifter Gloss Petal', 5, 60950, 'Maquillaje de labios', '20316798B', '2026-01-29'),
(12, 'BrPolvo Compacto Maybelline Super Nat Beige', 12, 35950, 'Maquillaje Facial', '20786708C', '2024-04-29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `rol`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `roles_id` int NOT NULL,
  `estado` enum('activo','suspendido') DEFAULT 'activo',
  PRIMARY KEY (`id`),
  KEY `fk_usuarios_roles` (`roles_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `email`, `pass`, `roles_id`, `estado`) VALUES
(1, 'Marcela Quiroga', 'Marceq', 'marceq0725@gmail.com', '$2y$10$/GrEv/Op6I5zTmaBPojRGe3K9dKVV8SnUMgU8iqEgx.W4ok39GQSW', 1, 'activo'),
(2, 'Stephanie Martinez', 'StephanieM', 'smz021093@gmail.com', '$2y$10$5UXy27DFjq/C6V0LiZkgue.QZS97uRqxZcEvTRc02J6o3DNI.DFFO', 1, 'activo'),
(3, 'Marity Castillo', 'mari52', 'm@gmail.com', '$2y$10$fxMSGeADxlI/n.4NeucuNehA7rhoYwSn5LlpyvmSBaVYnhpK8aWJi', 2, 'activo');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
