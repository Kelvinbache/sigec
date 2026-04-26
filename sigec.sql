-- Crear y seleccionar la base de datos
CREATE DATABASE IF NOT EXISTS `sigec`;
USE `sigec`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-04-2026 a las 19:04:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sigec`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diagnosticos`
--

CREATE TABLE IF NOT EXISTS `diagnosticos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `habitante_id` int(11) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `medicacion_requerida` text DEFAULT NULL,
  `otro_diagnostico` text DEFAULT NULL,
  `fecha_diagnostico` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_diagnosticos_habitante` (`habitante_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `discapacidades`
--

CREATE TABLE IF NOT EXISTS `discapacidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `habitante_id` int(11) NOT NULL,
  `tipo_discapacidad` varchar(200) NOT NULL,
  `porcentaje` decimal(5,2) DEFAULT NULL CHECK (`porcentaje` >= 0 and `porcentaje` <= 100),
  `otro_tipo` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_discapacidades_habitante` (`habitante_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitantes`
--

CREATE TABLE IF NOT EXISTS `habitantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vivienda_id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  `numero_identificacion` varchar(50) DEFAULT NULL,
  `es_pensionado` tinyint(1) DEFAULT 0 COMMENT 'Si es persona pensionada',
  `ingreso_mensual` decimal(10,2) DEFAULT 0.00 COMMENT 'Ingreso mensual en Bs/USD',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_identificacion` (`numero_identificacion`),
  KEY `idx_habitantes_vivienda` (`vivienda_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `detalle` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_logs_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT IGNORE INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'super_usuario', 'Administrador del sistema con acceso total'),
(2, 'encargado', 'Usuario encargado de gestionar personas y viviendas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(100) NOT NULL,
  `contrasenia_hash` varchar(255) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'activo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  UNIQUE KEY `correo` (`correo`),
  KEY `idx_usuarios_rol` (`rol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT IGNORE INTO `usuarios` (`id`, `nombre_usuario`, `contrasenia_hash`, `correo`, `rol_id`, `creado_en`, `actualizado_en`, `estado`) VALUES
(1, 'admin', '123', 'admin@sigec.com', 1, '2026-04-24 19:16:37', '2026-04-24 19:16:37', 'activo'),
(2, 'encargado', '123', 'encargado@sigec.com', 2, '2026-04-24 19:16:37', '2026-04-24 19:16:37', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `viviendas`
--

CREATE TABLE IF NOT EXISTS `viviendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `direccion` text NOT NULL COMMENT 'Dirección completa',
  `calle` varchar(100) DEFAULT NULL COMMENT 'Calle para filtros',
  `tipo_construccion` varchar(50) DEFAULT NULL,
  `tipo_vivienda` enum('Casa','Apartamento','Quinta','Rancho','Comercio','Otro') DEFAULT 'Casa' COMMENT 'Tipo de inmueble',
  `condiciones_habitabilidad` text DEFAULT NULL,
  `estado_vivienda` enum('Buena','Regular','Mala','En construcción','Abandonada') DEFAULT 'Buena' COMMENT 'Estado de la vivienda',
  `capacidad_economica` enum('Baja','Media','Media-Alta','Alta') DEFAULT 'Media' COMMENT 'Capacidad económica de los habitantes',
  `tiene_agua` tinyint(1) DEFAULT 1 COMMENT 'Servicio de agua potable',
  `tiene_luz` tinyint(1) DEFAULT 1 COMMENT 'Servicio de energía eléctrica',
  `tiene_gas` tinyint(1) DEFAULT 1 COMMENT 'Servicio de gas',
  `derecho_frente` tinyint(1) DEFAULT 0 COMMENT 'Derecho de frente (inmueble propio)',
  `cantidad_familias` int(11) DEFAULT 1 COMMENT 'Número de familias que habitan la vivienda',
  `numero_comercios` int(11) DEFAULT 0 COMMENT 'Cantidad de comercios en la propiedad',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_viviendas_calle` (`calle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_conteo_discapacidad_por_vivienda`
--

DROP VIEW IF EXISTS `v_conteo_discapacidad_por_vivienda`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_conteo_discapacidad_por_vivienda` AS
SELECT
  `v`.`id` AS `vivienda_id`,
  `v`.`direccion` AS `direccion`,
  `v`.`calle` AS `calle`,
  `d`.`tipo_discapacidad` AS `tipo_discapacidad`,
  COUNT(DISTINCT `d`.`habitante_id`) AS `cantidad_personas`
FROM ((`viviendas` `v`
  LEFT JOIN `habitantes` `h` ON(`h`.`vivienda_id` = `v`.`id`))
  LEFT JOIN `discapacidades` `d` ON(`d`.`habitante_id` = `h`.`id`))
WHERE `d`.`tipo_discapacidad` IS NOT NULL
  AND `d`.`tipo_discapacidad` <> 'Ninguna'
GROUP BY `v`.`id`, `v`.`direccion`, `v`.`calle`, `d`.`tipo_discapacidad`
ORDER BY `v`.`id` ASC, `d`.`tipo_discapacidad` ASC;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_estadisticas_generales`
--

DROP VIEW IF EXISTS `v_estadisticas_generales`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_estadisticas_generales` AS
SELECT
  (SELECT COUNT(0) FROM `habitantes`) AS `total_habitantes`,
  (SELECT COUNT(0) FROM `viviendas`) AS `total_viviendas`,
  (SELECT COUNT(0) FROM `viviendas` WHERE `viviendas`.`tipo_vivienda` = 'Casa') AS `total_casas`,
  (SELECT COUNT(0) FROM `viviendas` WHERE `viviendas`.`tipo_vivienda` = 'Apartamento') AS `total_apartamentos`,
  (SELECT COUNT(0) FROM `viviendas` WHERE `viviendas`.`tipo_vivienda` = 'Comercio') AS `total_comercios`,
  (SELECT COUNT(0) FROM `viviendas` WHERE `viviendas`.`tipo_vivienda` = 'Quinta') AS `total_quintas`,
  (SELECT COUNT(0) FROM `habitantes` WHERE `habitantes`.`es_pensionado` = 1) AS `total_pensionados`,
  (SELECT AVG(`habitantes`.`ingreso_mensual`) FROM `habitantes` WHERE `habitantes`.`ingreso_mensual` > 0) AS `ingreso_promedio_general`,
  (SELECT COUNT(0) FROM `viviendas` WHERE `viviendas`.`tiene_agua` = 1) AS `viviendas_con_agua`,
  (SELECT COUNT(0) FROM `viviendas` WHERE `viviendas`.`tiene_luz` = 1) AS `viviendas_con_luz`,
  (SELECT COUNT(0) FROM `viviendas` WHERE `viviendas`.`tiene_gas` = 1) AS `viviendas_con_gas`,
  (SELECT COUNT(0) FROM `viviendas` WHERE `viviendas`.`derecho_frente` = 1) AS `viviendas_con_derecho_frente`;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_habitantes_con_discapacidad`
--

DROP VIEW IF EXISTS `v_habitantes_con_discapacidad`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_habitantes_con_discapacidad` AS
SELECT
  `h`.`id` AS `habitante_id`,
  `h`.`nombres` AS `nombres`,
  `h`.`apellidos` AS `apellidos`,
  `h`.`numero_identificacion` AS `numero_identificacion`,
  `h`.`genero` AS `genero`,
  `h`.`es_pensionado` AS `es_pensionado`,
  `h`.`ingreso_mensual` AS `ingreso_mensual`,
  `v`.`direccion` AS `direccion_vivienda`,
  `v`.`calle` AS `calle`,
  `v`.`tipo_vivienda` AS `tipo_vivienda`,
  `v`.`estado_vivienda` AS `estado_vivienda`,
  `v`.`capacidad_economica` AS `capacidad_economica`,
  `v`.`tiene_agua` AS `tiene_agua`,
  `v`.`tiene_luz` AS `tiene_luz`,
  `v`.`tiene_gas` AS `tiene_gas`,
  `v`.`derecho_frente` AS `derecho_frente`,
  `v`.`cantidad_familias` AS `cantidad_familias`,
  `d`.`tipo_discapacidad` AS `tipo_discapacidad`,
  `d`.`porcentaje` AS `porcentaje`,
  `d`.`otro_tipo` AS `otro_tipo`,
  `d`.`fecha_registro` AS `fecha_registro`,
  TIMESTAMPDIFF(YEAR, `h`.`fecha_nacimiento`, CURDATE()) AS `edad`
FROM ((`habitantes` `h`
  JOIN `viviendas` `v` ON(`v`.`id` = `h`.`vivienda_id`))
  JOIN `discapacidades` `d` ON(`d`.`habitante_id` = `h`.`id`))
WHERE `d`.`tipo_discapacidad` IS NOT NULL
  AND `d`.`tipo_discapacidad` <> 'Ninguna';

-- --------------------------------------------------------

--
-- Estructura para la vista `v_habitantes_con_medicacion`
--

DROP VIEW IF EXISTS `v_habitantes_con_medicacion`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_habitantes_con_medicacion` AS
SELECT
  `h`.`id` AS `habitante_id`,
  `h`.`nombres` AS `nombres`,
  `h`.`apellidos` AS `apellidos`,
  `h`.`numero_identificacion` AS `numero_identificacion`,
  `h`.`genero` AS `genero`,
  `h`.`es_pensionado` AS `es_pensionado`,
  `h`.`ingreso_mensual` AS `ingreso_mensual`,
  `v`.`direccion` AS `direccion_vivienda`,
  `v`.`calle` AS `calle`,
  `v`.`tipo_vivienda` AS `tipo_vivienda`,
  `v`.`estado_vivienda` AS `estado_vivienda`,
  `d`.`descripcion` AS `diagnostico`,
  `d`.`medicacion_requerida` AS `medicacion_requerida`,
  `d`.`otro_diagnostico` AS `otro_diagnostico`,
  `d`.`fecha_diagnostico` AS `fecha_diagnostico`
FROM ((`habitantes` `h`
  JOIN `viviendas` `v` ON(`v`.`id` = `h`.`vivienda_id`))
  JOIN `diagnosticos` `d` ON(`d`.`habitante_id` = `h`.`id`))
WHERE `d`.`medicacion_requerida` IS NOT NULL
  AND `d`.`medicacion_requerida` <> '';

-- --------------------------------------------------------

--
-- Estructura para la vista `v_resumen_viviendas`
--

DROP VIEW IF EXISTS `v_resumen_viviendas`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_resumen_viviendas` AS
SELECT
  `v`.`id` AS `id`,
  `v`.`direccion` AS `direccion`,
  `v`.`calle` AS `calle`,
  `v`.`tipo_vivienda` AS `tipo_vivienda`,
  `v`.`estado_vivienda` AS `estado_vivienda`,
  `v`.`capacidad_economica` AS `capacidad_economica`,
  `v`.`cantidad_familias` AS `cantidad_familias`,
  `v`.`numero_comercios` AS `numero_comercios`,
  `v`.`tiene_agua` AS `tiene_agua`,
  `v`.`tiene_luz` AS `tiene_luz`,
  `v`.`tiene_gas` AS `tiene_gas`,
  `v`.`derecho_frente` AS `derecho_frente`,
  COUNT(DISTINCT `h`.`id`) AS `total_habitantes`,
  SUM(CASE WHEN `h`.`es_pensionado` = 1 THEN 1 ELSE 0 END) AS `pensionados`,
  AVG(`h`.`ingreso_mensual`) AS `ingreso_promedio`,
  GROUP_CONCAT(DISTINCT `d`.`descripcion` SEPARATOR ', ') AS `diagnosticos_comunes`,
  GROUP_CONCAT(DISTINCT `dc`.`tipo_discapacidad` SEPARATOR ', ') AS `discapacidades_comunes`
FROM (((`viviendas` `v`
  LEFT JOIN `habitantes` `h` ON(`h`.`vivienda_id` = `v`.`id`))
  LEFT JOIN `diagnosticos` `d` ON(`d`.`habitante_id` = `h`.`id`))
  LEFT JOIN `discapacidades` `dc` ON(`dc`.`habitante_id` = `h`.`id`))
GROUP BY `v`.`id`;

--
-- Restricciones para tablas
--

--
-- Filtros para la tabla `diagnosticos`
--
ALTER TABLE `diagnosticos`
  ADD CONSTRAINT `diagnosticos_ibfk_1` FOREIGN KEY (`habitante_id`) REFERENCES `habitantes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `discapacidades`
--
ALTER TABLE `discapacidades`
  ADD CONSTRAINT `discapacidades_ibfk_1` FOREIGN KEY (`habitante_id`) REFERENCES `habitantes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `habitantes`
--
ALTER TABLE `habitantes`
  ADD CONSTRAINT `habitantes_ibfk_1` FOREIGN KEY (`vivienda_id`) REFERENCES `viviendas` (`id`);

--
-- Filtros para la tabla `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
