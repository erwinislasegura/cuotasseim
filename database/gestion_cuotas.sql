-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 04-05-2026 a las 05:14:50
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestion_cuotas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aportes`
--

CREATE TABLE `aportes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `socio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_aportante` varchar(140) DEFAULT NULL,
  `tipo_aporte_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_aporte` date DEFAULT NULL,
  `monto` decimal(12,2) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `comentario` varchar(255) DEFAULT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('aplicado','anulado','pendiente_revision') DEFAULT 'aplicado',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `modulo` varchar(80) DEFAULT NULL,
  `accion` varchar(80) DEFAULT NULL,
  `registro_id` bigint(20) UNSIGNED DEFAULT NULL,
  `id_registro` bigint(20) UNSIGNED DEFAULT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_anteriores`)),
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_nuevos`)),
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `usuario_id`, `modulo`, `accion`, `registro_id`, `id_registro`, `datos_anteriores`, `datos_nuevos`, `fecha`, `ip`, `user_agent`, `created_at`) VALUES
(16, 1, 'auth', 'logout', NULL, 1, '{\"usuario\":\"admin\",\"correo\":\"admin@demo.local\"}', NULL, '2026-05-03 22:18:29', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:18:29'),
(17, 1, 'auth', 'login', NULL, 1, NULL, '{\"usuario\":\"admin\",\"correo\":\"admin@demo.local\"}', '2026-05-03 22:19:13', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:19:13'),
(18, 1, 'auth', 'login', NULL, 1, NULL, '{\"usuario\":\"admin\",\"correo\":\"admin@demo.local\"}', '2026-05-03 22:32:08', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:32:08'),
(19, 1, 'socios', 'crear', NULL, 3, NULL, '{\"id\":3,\"numero_socio\":\"000001\",\"rut\":null,\"nombres\":\"Pedro\",\"apellidos\":\"Abarzua Yañez\",\"nombre_completo\":\"Pedro Abarzua Yañez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:43:24\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:43:24', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:43:24'),
(20, 1, 'socios', 'crear', NULL, 4, NULL, '{\"id\":4,\"numero_socio\":\"000002\",\"rut\":null,\"nombres\":\"Carlos\",\"apellidos\":\"Alcayaga Aranda\",\"nombre_completo\":\"Carlos Alcayaga Aranda\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:44:02\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:44:02', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:44:02'),
(21, 1, 'socios', 'crear', NULL, 5, NULL, '{\"id\":5,\"numero_socio\":\"000003\",\"rut\":null,\"nombres\":\"Jorge\",\"apellidos\":\"Acevedo Olave\",\"nombre_completo\":\"Jorge Acevedo Olave\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:44:15\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:44:15', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:44:15'),
(22, 1, 'socios', 'crear', NULL, 6, NULL, '{\"id\":6,\"numero_socio\":\"000004\",\"rut\":null,\"nombres\":\"Jose\",\"apellidos\":\"Alegria Hernández\",\"nombre_completo\":\"Jose Alegria Hernández\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:44:27\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:44:27', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:44:27'),
(23, 1, 'socios', 'crear', NULL, 7, NULL, '{\"id\":7,\"numero_socio\":\"000005\",\"rut\":null,\"nombres\":\"Gabriel\",\"apellidos\":\"Allendes Villarroel\",\"nombre_completo\":\"Gabriel Allendes Villarroel\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:44:42\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:44:42', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:44:42'),
(24, 1, 'socios', 'crear', NULL, 8, NULL, '{\"id\":8,\"numero_socio\":\"000006\",\"rut\":null,\"nombres\":\"Orlando\",\"apellidos\":\"Alarcon\",\"nombre_completo\":\"Orlando Alarcon\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:44:53\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:44:53', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:44:53'),
(25, 1, 'socios', 'crear', NULL, 9, NULL, '{\"id\":9,\"numero_socio\":\"000007\",\"rut\":null,\"nombres\":\"Victor\",\"apellidos\":\"Arismendi Mansilla\",\"nombre_completo\":\"Victor Arismendi Mansilla\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:45:03\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:45:03', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:45:03'),
(26, 1, 'socios', 'crear', NULL, 10, NULL, '{\"id\":10,\"numero_socio\":\"000008\",\"rut\":null,\"nombres\":\"Aristobulo\",\"apellidos\":\"Aillon Vega\",\"nombre_completo\":\"Aristobulo Aillon Vega\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:45:59\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:45:59', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:45:59'),
(27, 1, 'socios', 'crear', NULL, 11, NULL, '{\"id\":11,\"numero_socio\":\"000009\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Astete Insunza\",\"nombre_completo\":\"Luis Astete Insunza\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:46:37\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:46:37', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:46:37'),
(28, 1, 'socios', 'crear', NULL, 12, NULL, '{\"id\":12,\"numero_socio\":\"000010\",\"rut\":null,\"nombres\":\"Jose\",\"apellidos\":\"Ayala Mondaca\",\"nombre_completo\":\"Jose Ayala Mondaca\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:46:49\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:46:49', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:46:49'),
(29, 1, 'socios', 'crear', NULL, 13, NULL, '{\"id\":13,\"numero_socio\":\"000011\",\"rut\":null,\"nombres\":\"Javier\",\"apellidos\":\"Badilla Borquez\",\"nombre_completo\":\"Javier Badilla Borquez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:47:02\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:47:02', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:47:02'),
(30, 1, 'socios', 'crear', NULL, 14, NULL, '{\"id\":14,\"numero_socio\":\"000012\",\"rut\":null,\"nombres\":\"Juan\",\"apellidos\":\"Barra Alarcon\",\"nombre_completo\":\"Juan Barra Alarcon\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:47:13\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:47:13', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:47:13'),
(31, 1, 'socios', 'crear', NULL, 15, NULL, '{\"id\":15,\"numero_socio\":\"000013\",\"rut\":null,\"nombres\":\"Flavio\",\"apellidos\":\"Becerra\",\"nombre_completo\":\"Flavio Becerra\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:47:26\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:47:26', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:47:26'),
(32, 1, 'socios', 'crear', NULL, 16, NULL, '{\"id\":16,\"numero_socio\":\"000014\",\"rut\":null,\"nombres\":\"Pablo\",\"apellidos\":\"Bascour Nuñez\",\"nombre_completo\":\"Pablo Bascour Nuñez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:47:44\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:47:44', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:47:44'),
(33, 1, 'socios', 'crear', NULL, 17, NULL, '{\"id\":17,\"numero_socio\":\"000015\",\"rut\":null,\"nombres\":\"Guillermo\",\"apellidos\":\"Caballero Astudillo\",\"nombre_completo\":\"Guillermo Caballero Astudillo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:47:58\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 22:47:58', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:47:58'),
(34, 1, 'socios', 'eliminar', NULL, 17, '{\"id\":17,\"numero_socio\":\"000015\",\"rut\":null,\"nombres\":\"Guillermo\",\"apellidos\":\"Caballero Astudillo\",\"nombre_completo\":\"Guillermo Caballero Astudillo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 18:47:58\",\"updated_at\":null,\"deleted_at\":null}', NULL, '2026-05-03 22:49:14', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:49:14'),
(35, 1, 'auth', 'login', NULL, 1, NULL, '{\"usuario\":\"admin\",\"correo\":\"admin@demo.local\"}', '2026-05-03 22:52:05', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 22:52:05'),
(36, 1, 'socios', 'crear', NULL, 18, NULL, '{\"id\":18,\"numero_socio\":\"000001\",\"rut\":null,\"nombres\":\"Irma\",\"apellidos\":\"Diaz De Alcayaga\",\"nombre_completo\":\"Irma Diaz De Alcayaga\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:04:38\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:04:38', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:04:38'),
(37, 1, 'socios', 'crear', NULL, 19, NULL, '{\"id\":19,\"numero_socio\":\"000002\",\"rut\":null,\"nombres\":\"Sisy\",\"apellidos\":\"Alcayaga Diaz\",\"nombre_completo\":\"Sisy Alcayaga Diaz\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:04:49\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:04:49', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:04:49'),
(38, 1, 'socios', 'crear', NULL, 20, NULL, '{\"id\":20,\"numero_socio\":\"000003\",\"rut\":null,\"nombres\":\"Pierina\",\"apellidos\":\"Gomez Retamal\",\"nombre_completo\":\"Pierina Gomez Retamal\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:05:05\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:05:05', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:05:05'),
(39, 1, 'socios', 'crear', NULL, 21, NULL, '{\"id\":21,\"numero_socio\":\"000004\",\"rut\":null,\"nombres\":\"Barbara\",\"apellidos\":\"Canales\",\"nombre_completo\":\"Barbara Canales\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:05:15\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:05:15', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:05:15'),
(40, 1, 'socios', 'crear', NULL, 22, NULL, '{\"id\":22,\"numero_socio\":\"000005\",\"rut\":null,\"nombres\":\"Jose Mauricio\",\"apellidos\":\"Acuña Perez\",\"nombre_completo\":\"Jose Mauricio Acuña Perez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:05:29\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:05:29', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:05:29'),
(41, 1, 'socios', 'crear', NULL, 23, NULL, '{\"id\":23,\"numero_socio\":\"000006\",\"rut\":null,\"nombres\":\"Jose Carmen\",\"apellidos\":\"Acuña Nuñez\",\"nombre_completo\":\"Jose Carmen Acuña Nuñez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:05:49\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:05:49', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:05:49'),
(42, 1, 'socios', 'crear', NULL, 24, NULL, '{\"id\":24,\"numero_socio\":\"000007\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Ahumada Martinez\",\"nombre_completo\":\"Luis Ahumada Martinez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:06:00\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:06:00', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:06:00'),
(43, 1, 'socios', 'crear', NULL, 25, NULL, '{\"id\":25,\"numero_socio\":\"000008\",\"rut\":null,\"nombres\":\"Jacinto\",\"apellidos\":\"Ahumada Parada\",\"nombre_completo\":\"Jacinto Ahumada Parada\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:06:16\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:06:16', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:06:16'),
(44, 1, 'socios', 'crear', NULL, 26, NULL, '{\"id\":26,\"numero_socio\":\"000009\",\"rut\":null,\"nombres\":\"Gabriel\",\"apellidos\":\"Allendes Villarroel\",\"nombre_completo\":\"Gabriel Allendes Villarroel\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:06:28\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:06:28', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:06:28'),
(45, 1, 'socios', 'crear', NULL, 27, NULL, '{\"id\":27,\"numero_socio\":\"000010\",\"rut\":null,\"nombres\":\"Patricio\",\"apellidos\":\"Osvaldo Alvarado\",\"nombre_completo\":\"Patricio Osvaldo Alvarado\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:06:40\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:06:40', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:06:40'),
(46, 1, 'socios', 'crear', NULL, 28, NULL, '{\"id\":28,\"numero_socio\":\"000011\",\"rut\":null,\"nombres\":\"Jose\",\"apellidos\":\"Alegria Hernandez\",\"nombre_completo\":\"Jose Alegria Hernandez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:06:49\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:06:49', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:06:49'),
(47, 1, 'socios', 'crear', NULL, 29, NULL, '{\"id\":29,\"numero_socio\":\"000012\",\"rut\":null,\"nombres\":\"Hernan\",\"apellidos\":\"Aquiles Novoa\",\"nombre_completo\":\"Hernan Aquiles Novoa\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:07:01\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:07:01', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:07:01'),
(48, 1, 'socios', 'crear', NULL, 30, NULL, '{\"id\":30,\"numero_socio\":\"000013\",\"rut\":null,\"nombres\":\"Welingthone\",\"apellidos\":\"Araya\",\"nombre_completo\":\"Welingthone Araya\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:07:29\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:07:29', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:07:29'),
(49, 1, 'socios', 'crear', NULL, 31, NULL, '{\"id\":31,\"numero_socio\":\"000014\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Albornoz Castillo\",\"nombre_completo\":\"Luis Albornoz Castillo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:07:40\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:07:40', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:07:40'),
(50, 1, 'socios', 'crear', NULL, 32, NULL, '{\"id\":32,\"numero_socio\":\"000015\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Astete Insunza\",\"nombre_completo\":\"Luis Astete Insunza\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:07:51\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:07:51', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:07:51'),
(51, 1, 'socios', 'crear', NULL, 33, NULL, '{\"id\":33,\"numero_socio\":\"000016\",\"rut\":null,\"nombres\":\"Jose Arnold\",\"apellidos\":\"Ayamante Alvarado\",\"nombre_completo\":\"Jose Arnold Ayamante Alvarado\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:08:07\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:08:07', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:08:07'),
(52, 1, 'socios', 'crear', NULL, 34, NULL, '{\"id\":34,\"numero_socio\":\"000017\",\"rut\":null,\"nombres\":\"Luis Armando\",\"apellidos\":\"Bañados Carcamo\",\"nombre_completo\":\"Luis Armando Bañados Carcamo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:08:18\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:08:18', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:08:18'),
(53, 1, 'socios', 'crear', NULL, 35, NULL, '{\"id\":35,\"numero_socio\":\"000018\",\"rut\":null,\"nombres\":\"Juan Bautista\",\"apellidos\":\"Barra Alarcon\",\"nombre_completo\":\"Juan Bautista Barra Alarcon\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:08:29\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:08:29', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:08:29'),
(54, 1, 'socios', 'crear', NULL, 36, NULL, '{\"id\":36,\"numero_socio\":\"000019\",\"rut\":null,\"nombres\":\"Pablo Guillermo\",\"apellidos\":\"Bascour Nuñez\",\"nombre_completo\":\"Pablo Guillermo Bascour Nuñez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:08:40\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:08:40', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:08:40'),
(55, 1, 'socios', 'crear', NULL, 37, NULL, '{\"id\":37,\"numero_socio\":\"000020\",\"rut\":null,\"nombres\":\"Froilan\",\"apellidos\":\"Basoalto\",\"nombre_completo\":\"Froilan Basoalto\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:08:51\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:08:51', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:08:51'),
(56, 1, 'socios', 'crear', NULL, 38, NULL, '{\"id\":38,\"numero_socio\":\"000021\",\"rut\":null,\"nombres\":\"Mario Patricio\",\"apellidos\":\"Brante Ramirez\",\"nombre_completo\":\"Mario Patricio Brante Ramirez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:09:03\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:09:03', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:09:03'),
(57, 1, 'socios', 'crear', NULL, 39, NULL, '{\"id\":39,\"numero_socio\":\"000022\",\"rut\":null,\"nombres\":\"Flavio\",\"apellidos\":\"Becerra\",\"nombre_completo\":\"Flavio Becerra\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:09:15\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:09:15', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:09:15'),
(58, 1, 'socios', 'crear', NULL, 40, NULL, '{\"id\":40,\"numero_socio\":\"000023\",\"rut\":null,\"nombres\":\"Humberto\",\"apellidos\":\"Rene Belmar\",\"nombre_completo\":\"Humberto Rene Belmar\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:09:26\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:09:26', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:09:26'),
(59, 1, 'socios', 'crear', NULL, 41, NULL, '{\"id\":41,\"numero_socio\":\"000024\",\"rut\":null,\"nombres\":\"Carlos Raul\",\"apellidos\":\"Belmar Fuentes\",\"nombre_completo\":\"Carlos Raul Belmar Fuentes\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:09:45\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:09:45', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:09:45'),
(60, 1, 'socios', 'crear', NULL, 42, NULL, '{\"id\":42,\"numero_socio\":\"000025\",\"rut\":null,\"nombres\":\"Ernesto\",\"apellidos\":\"Calquin Lopez\",\"nombre_completo\":\"Ernesto Calquin Lopez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:10:00\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:10:00', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:10:00'),
(61, 1, 'socios', 'crear', NULL, 43, NULL, '{\"id\":43,\"numero_socio\":\"000026\",\"rut\":null,\"nombres\":\"Jose Raul\",\"apellidos\":\"Canales Millanao\",\"nombre_completo\":\"Jose Raul Canales Millanao\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:10:12\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:10:12', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:10:12'),
(62, 1, 'socios', 'crear', NULL, 44, NULL, '{\"id\":44,\"numero_socio\":\"000027\",\"rut\":null,\"nombres\":\"Reynaldo\",\"apellidos\":\"Carvajal Caceres\",\"nombre_completo\":\"Reynaldo Carvajal Caceres\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:10:24\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:10:24', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:10:24'),
(63, 1, 'socios', 'crear', NULL, 45, NULL, '{\"id\":45,\"numero_socio\":\"000028\",\"rut\":null,\"nombres\":\"Ricardo Ivan\",\"apellidos\":\"Castro Flores\",\"nombre_completo\":\"Ricardo Ivan Castro Flores\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:10:35\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:10:35', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:10:35'),
(64, 1, 'socios', 'crear', NULL, 46, NULL, '{\"id\":46,\"numero_socio\":\"000029\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Castro Martinez\",\"nombre_completo\":\"Luis Castro Martinez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:10:47\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:10:47', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:10:47'),
(65, 1, 'socios', 'crear', NULL, 47, NULL, '{\"id\":47,\"numero_socio\":\"000030\",\"rut\":null,\"nombres\":\"Sergio\",\"apellidos\":\"Catriao Lagos\",\"nombre_completo\":\"Sergio Catriao Lagos\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:10:58\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:10:58', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:10:58'),
(66, 1, 'socios', 'crear', NULL, 48, NULL, '{\"id\":48,\"numero_socio\":\"000031\",\"rut\":null,\"nombres\":\"Mario\",\"apellidos\":\"Cerda4\",\"nombre_completo\":\"Mario Cerda4\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:16:42\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:16:42', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:16:42'),
(67, 1, 'socios', 'actualizar', NULL, 48, '{\"id\":48,\"numero_socio\":\"000031\",\"rut\":null,\"nombres\":\"Mario\",\"apellidos\":\"Cerda4\",\"nombre_completo\":\"Mario Cerda4\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:16:42\",\"updated_at\":null,\"deleted_at\":null}', '{\"id\":48,\"numero_socio\":\"000031\",\"rut\":null,\"nombres\":\"Mario\",\"apellidos\":\"Cerda\",\"nombre_completo\":\"Mario Cerda\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:16:42\",\"updated_at\":\"2026-05-03 19:16:51\",\"deleted_at\":null}', '2026-05-03 23:16:51', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:16:51'),
(68, 1, 'socios', 'crear', NULL, 49, NULL, '{\"id\":49,\"numero_socio\":\"000032\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Cervando Lizama\",\"nombre_completo\":\"Luis Cervando Lizama\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:17:20\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:17:20', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:17:20'),
(69, 1, 'socios', 'crear', NULL, 50, NULL, '{\"id\":50,\"numero_socio\":\"000033\",\"rut\":null,\"nombres\":\"Ruben\",\"apellidos\":\"Ceron Hermosilla\",\"nombre_completo\":\"Ruben Ceron Hermosilla\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:17:30\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:17:30', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:17:30'),
(70, 1, 'socios', 'crear', NULL, 51, NULL, '{\"id\":51,\"numero_socio\":\"000034\",\"rut\":null,\"nombres\":\"Manuel Ramon\",\"apellidos\":\"Cofre Leiva\",\"nombre_completo\":\"Manuel Ramon Cofre Leiva\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:17:42\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:17:42', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:17:42'),
(71, 1, 'socios', 'crear', NULL, 52, NULL, '{\"id\":52,\"numero_socio\":\"000035\",\"rut\":null,\"nombres\":\"Raul Ernesto\",\"apellidos\":\"Collantes Bravo\",\"nombre_completo\":\"Raul Ernesto Collantes Bravo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:17:53\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:17:53', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:17:53'),
(72, 1, 'socios', 'crear', NULL, 53, NULL, '{\"id\":53,\"numero_socio\":\"000036\",\"rut\":null,\"nombres\":\"Hector\",\"apellidos\":\"Contreras Baez\",\"nombre_completo\":\"Hector Contreras Baez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:18:02\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:18:02', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:18:02'),
(73, 1, 'socios', 'crear', NULL, 54, NULL, '{\"id\":54,\"numero_socio\":\"000037\",\"rut\":null,\"nombres\":\"Raul Enrique\",\"apellidos\":\"Contreras Nuñez\",\"nombre_completo\":\"Raul Enrique Contreras Nuñez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:18:14\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:18:14', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:18:14'),
(74, 1, 'socios', 'crear', NULL, 55, NULL, '{\"id\":55,\"numero_socio\":\"000038\",\"rut\":null,\"nombres\":\"Heraldo\",\"apellidos\":\"Correa Trigo\",\"nombre_completo\":\"Heraldo Correa Trigo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:18:23\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:18:23', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:18:23'),
(75, 1, 'socios', 'crear', NULL, 56, NULL, '{\"id\":56,\"numero_socio\":\"000039\",\"rut\":null,\"nombres\":\"Jorge\",\"apellidos\":\"Devia\",\"nombre_completo\":\"Jorge Devia\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:18:32\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:18:32', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:18:32'),
(76, 1, 'socios', 'crear', NULL, 57, NULL, '{\"id\":57,\"numero_socio\":\"000040\",\"rut\":null,\"nombres\":\"Jorge Washington\",\"apellidos\":\"Diaz Herrera\",\"nombre_completo\":\"Jorge Washington Diaz Herrera\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:18:47\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:18:47', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:18:47'),
(77, 1, 'socios', 'crear', NULL, 58, NULL, '{\"id\":58,\"numero_socio\":\"000041\",\"rut\":null,\"nombres\":\"Jose Daniel\",\"apellidos\":\"Diaz Vega\",\"nombre_completo\":\"Jose Daniel Diaz Vega\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:18:58\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:18:58', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:18:58'),
(78, 1, 'socios', 'crear', NULL, 59, NULL, '{\"id\":59,\"numero_socio\":\"000042\",\"rut\":null,\"nombres\":\"Eulogio\",\"apellidos\":\"Diaz Hurtado\",\"nombre_completo\":\"Eulogio Diaz Hurtado\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:19:10\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:19:10', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:19:10'),
(79, 1, 'socios', 'crear', NULL, 60, NULL, '{\"id\":60,\"numero_socio\":\"000043\",\"rut\":null,\"nombres\":\"Lupercio\",\"apellidos\":\"Espinoza Mujica\",\"nombre_completo\":\"Lupercio Espinoza Mujica\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:19:22\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:19:22', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:19:22'),
(80, 1, 'socios', 'crear', NULL, 61, NULL, '{\"id\":61,\"numero_socio\":\"000044\",\"rut\":null,\"nombres\":\"Gloria Ines\",\"apellidos\":\"Eriza Due\",\"nombre_completo\":\"Gloria Ines Eriza Due\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:19:34\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:19:34', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:19:34'),
(81, 1, 'socios', 'crear', NULL, 62, NULL, '{\"id\":62,\"numero_socio\":\"000045\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Fernandez Pinto\",\"nombre_completo\":\"Luis Fernandez Pinto\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:19:48\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:19:48', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:19:48'),
(82, 1, 'socios', 'crear', NULL, 63, NULL, '{\"id\":63,\"numero_socio\":\"000046\",\"rut\":null,\"nombres\":\"Sylvia\",\"apellidos\":\"Fuentes Jara\",\"nombre_completo\":\"Sylvia Fuentes Jara\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:19:59\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:19:59', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:19:59'),
(83, 1, 'socios', 'crear', NULL, 64, NULL, '{\"id\":64,\"numero_socio\":\"000047\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Flores Veli\",\"nombre_completo\":\"Luis Flores Veli\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:20:13\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:20:13', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:20:13'),
(84, 1, 'socios', 'crear', NULL, 65, NULL, '{\"id\":65,\"numero_socio\":\"000048\",\"rut\":null,\"nombres\":\"Juan Manuel\",\"apellidos\":\"Gallardo Moya\",\"nombre_completo\":\"Juan Manuel Gallardo Moya\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:20:22\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:20:22', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:20:22'),
(85, 1, 'socios', 'crear', NULL, 66, NULL, '{\"id\":66,\"numero_socio\":\"000049\",\"rut\":null,\"nombres\":\"Justo Wlad\",\"apellidos\":\"Garrido Caceres\",\"nombre_completo\":\"Justo Wlad Garrido Caceres\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:20:42\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:20:42', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:20:42'),
(86, 1, 'socios', 'crear', NULL, 67, NULL, '{\"id\":67,\"numero_socio\":\"000050\",\"rut\":null,\"nombres\":\"Pedro\",\"apellidos\":\"Godoy Rebolledo\",\"nombre_completo\":\"Pedro Godoy Rebolledo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:20:54\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:20:54', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:20:54'),
(87, 1, 'socios', 'crear', NULL, 68, NULL, '{\"id\":68,\"numero_socio\":\"000051\",\"rut\":null,\"nombres\":\"Juan Carlos\",\"apellidos\":\"Gonzalez Castro\",\"nombre_completo\":\"Juan Carlos Gonzalez Castro\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:21:04\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:21:04', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:21:04'),
(88, 1, 'socios', 'crear', NULL, 69, NULL, '{\"id\":69,\"numero_socio\":\"000052\",\"rut\":null,\"nombres\":\"Roberto\",\"apellidos\":\"Gonzalez Marinao\",\"nombre_completo\":\"Roberto Gonzalez Marinao\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:21:16\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:21:16', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:21:16'),
(89, 1, 'socios', 'crear', NULL, 70, NULL, '{\"id\":70,\"numero_socio\":\"000053\",\"rut\":null,\"nombres\":\"Carlos\",\"apellidos\":\"Grandon Portilla\",\"nombre_completo\":\"Carlos Grandon Portilla\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:21:31\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:21:31', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:21:31'),
(90, 1, 'socios', 'crear', NULL, 71, NULL, '{\"id\":71,\"numero_socio\":\"000054\",\"rut\":null,\"nombres\":\"Raul\",\"apellidos\":\"Gutierrez Nilo\",\"nombre_completo\":\"Raul Gutierrez Nilo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:22:02\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:22:02', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:22:02');
INSERT INTO `auditoria` (`id`, `usuario_id`, `modulo`, `accion`, `registro_id`, `id_registro`, `datos_anteriores`, `datos_nuevos`, `fecha`, `ip`, `user_agent`, `created_at`) VALUES
(91, 1, 'socios', 'crear', NULL, 72, NULL, '{\"id\":72,\"numero_socio\":\"000055\",\"rut\":null,\"nombres\":\"Ramon\",\"apellidos\":\"Gutierrez Boilet\",\"nombre_completo\":\"Ramon Gutierrez Boilet\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:22:16\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:22:16', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:22:16'),
(92, 1, 'socios', 'crear', NULL, 73, NULL, '{\"id\":73,\"numero_socio\":\"000056\",\"rut\":null,\"nombres\":\"Angel\",\"apellidos\":\"Holstin Zelaya\",\"nombre_completo\":\"Angel Holstin Zelaya\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:22:29\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:22:29', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:22:29'),
(93, 1, 'socios', 'crear', NULL, 74, NULL, '{\"id\":74,\"numero_socio\":\"000057\",\"rut\":null,\"nombres\":\"Julio Patricio\",\"apellidos\":\"Lagos Gonzalez\",\"nombre_completo\":\"Julio Patricio Lagos Gonzalez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:22:41\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:22:41', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:22:41'),
(94, 1, 'socios', 'crear', NULL, 75, NULL, '{\"id\":75,\"numero_socio\":\"000058\",\"rut\":null,\"nombres\":\"Claudio\",\"apellidos\":\"Larreta Fonseca\",\"nombre_completo\":\"Claudio Larreta Fonseca\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:22:53\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:22:53', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:22:53'),
(95, 1, 'socios', 'crear', NULL, 76, NULL, '{\"id\":76,\"numero_socio\":\"000059\",\"rut\":null,\"nombres\":\"Maria Sonia\",\"apellidos\":\"Lefno\",\"nombre_completo\":\"Maria Sonia Lefno\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:23:06\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:23:06', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:23:06'),
(96, 1, 'socios', 'crear', NULL, 77, NULL, '{\"id\":77,\"numero_socio\":\"000060\",\"rut\":null,\"nombres\":\"Roberto\",\"apellidos\":\"Lizama Valenzuela\",\"nombre_completo\":\"Roberto Lizama Valenzuela\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:23:18\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:23:18', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:23:18'),
(97, 1, 'socios', 'crear', NULL, 78, NULL, '{\"id\":78,\"numero_socio\":\"000061\",\"rut\":null,\"nombres\":\"Nibaldo\",\"apellidos\":\"Mackenna Velarde\",\"nombre_completo\":\"Nibaldo Mackenna Velarde\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:23:30\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:23:30', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:23:30'),
(98, 1, 'socios', 'crear', NULL, 79, NULL, '{\"id\":79,\"numero_socio\":\"000062\",\"rut\":null,\"nombres\":\"Juan\",\"apellidos\":\"Matus Jara\",\"nombre_completo\":\"Juan Matus Jara\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:23:42\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:23:42', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:23:42'),
(99, 1, 'socios', 'crear', NULL, 80, NULL, '{\"id\":80,\"numero_socio\":\"000063\",\"rut\":null,\"nombres\":\"Tristan\",\"apellidos\":\"Mesias Meza\",\"nombre_completo\":\"Tristan Mesias Meza\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:23:57\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:23:57', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:23:57'),
(100, 1, 'socios', 'crear', NULL, 81, NULL, '{\"id\":81,\"numero_socio\":\"000064\",\"rut\":null,\"nombres\":\"Oscar\",\"apellidos\":\"Mendez Franco\",\"nombre_completo\":\"Oscar Mendez Franco\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:24:07\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:24:07', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:24:07'),
(101, 1, 'socios', 'crear', NULL, 82, NULL, '{\"id\":82,\"numero_socio\":\"000065\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Mettig\",\"nombre_completo\":\"Luis Mettig\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:24:18\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:24:18', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:24:18'),
(102, 1, 'socios', 'crear', NULL, 83, NULL, '{\"id\":83,\"numero_socio\":\"000066\",\"rut\":null,\"nombres\":\"Samuel Fermin\",\"apellidos\":\"Miranda Muñoz\",\"nombre_completo\":\"Samuel Fermin Miranda Muñoz\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:24:30\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:24:30', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:24:30'),
(103, 1, 'socios', 'crear', NULL, 84, NULL, '{\"id\":84,\"numero_socio\":\"000067\",\"rut\":null,\"nombres\":\"Jose\",\"apellidos\":\"Moya Rodriguez\",\"nombre_completo\":\"Jose Moya Rodriguez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:24:40\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:24:40', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:24:40'),
(104, 1, 'socios', 'crear', NULL, 85, NULL, '{\"id\":85,\"numero_socio\":\"000068\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Morales Orellana\",\"nombre_completo\":\"Luis Morales Orellana\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:24:51\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:24:51', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:24:51'),
(105, 1, 'socios', 'crear', NULL, 86, NULL, '{\"id\":86,\"numero_socio\":\"000069\",\"rut\":null,\"nombres\":\"Pedro Ismael\",\"apellidos\":\"Muñoz Castillo\",\"nombre_completo\":\"Pedro Ismael Muñoz Castillo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:25:02\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:25:02', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:25:02'),
(106, 1, 'socios', 'crear', NULL, 87, NULL, '{\"id\":87,\"numero_socio\":\"000070\",\"rut\":null,\"nombres\":\"Jorge Luis\",\"apellidos\":\"Muñoz Lizama\",\"nombre_completo\":\"Jorge Luis Muñoz Lizama\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:25:13\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:25:13', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:25:13'),
(107, 1, 'socios', 'crear', NULL, 88, NULL, '{\"id\":88,\"numero_socio\":\"000071\",\"rut\":null,\"nombres\":\"Victor Enrique\",\"apellidos\":\"Muñoz Montiel\",\"nombre_completo\":\"Victor Enrique Muñoz Montiel\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:25:25\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:25:25', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:25:25'),
(108, 1, 'socios', 'crear', NULL, 89, NULL, '{\"id\":89,\"numero_socio\":\"000072\",\"rut\":null,\"nombres\":\"Jaime\",\"apellidos\":\"Muñoz Neira\",\"nombre_completo\":\"Jaime Muñoz Neira\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:25:38\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:25:38', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:25:38'),
(109, 1, 'socios', 'crear', NULL, 90, NULL, '{\"id\":90,\"numero_socio\":\"000073\",\"rut\":null,\"nombres\":\"Jimmy\",\"apellidos\":\"Muñoz Owen\",\"nombre_completo\":\"Jimmy Muñoz Owen\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:26:12\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:26:12', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:26:12'),
(110, 1, 'socios', 'crear', NULL, 91, NULL, '{\"id\":91,\"numero_socio\":\"000074\",\"rut\":null,\"nombres\":\"Oscar\",\"apellidos\":\"Navas Ulloa\",\"nombre_completo\":\"Oscar Navas Ulloa\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:26:24\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:26:24', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:26:24'),
(111, 1, 'socios', 'crear', NULL, 92, NULL, '{\"id\":92,\"numero_socio\":\"000075\",\"rut\":null,\"nombres\":\"Victor\",\"apellidos\":\"Negron Gallardo\",\"nombre_completo\":\"Victor Negron Gallardo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:26:34\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:26:34', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:26:34'),
(112, 1, 'socios', 'crear', NULL, 93, NULL, '{\"id\":93,\"numero_socio\":\"000076\",\"rut\":null,\"nombres\":\"Emma\",\"apellidos\":\"Norambuena Villagran\",\"nombre_completo\":\"Emma Norambuena Villagran\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:26:45\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:26:45', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:26:45'),
(113, 1, 'socios', 'crear', NULL, 94, NULL, '{\"id\":94,\"numero_socio\":\"000077\",\"rut\":null,\"nombres\":\"Hugo\",\"apellidos\":\"Nuñez Seguel\",\"nombre_completo\":\"Hugo Nuñez Seguel\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:27:17\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:27:17', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:27:17'),
(114, 1, 'socios', 'crear', NULL, 95, NULL, '{\"id\":95,\"numero_socio\":\"000078\",\"rut\":null,\"nombres\":\"Victor\",\"apellidos\":\"Ojeda Diaz\",\"nombre_completo\":\"Victor Ojeda Diaz\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:27:27\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:27:27', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:27:27'),
(115, 1, 'socios', 'crear', NULL, 96, NULL, '{\"id\":96,\"numero_socio\":\"000079\",\"rut\":null,\"nombres\":\"Hector Manuel\",\"apellidos\":\"Orellana Salgado\",\"nombre_completo\":\"Hector Manuel Orellana Salgado\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:27:49\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:27:49', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:27:49'),
(116, 1, 'socios', 'crear', NULL, 97, NULL, '{\"id\":97,\"numero_socio\":\"000080\",\"rut\":null,\"nombres\":\"Luis Alberto\",\"apellidos\":\"Ortiz Rivas\",\"nombre_completo\":\"Luis Alberto Ortiz Rivas\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:28:02\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:28:02', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:28:02'),
(117, 1, 'socios', 'crear', NULL, 98, NULL, '{\"id\":98,\"numero_socio\":\"000081\",\"rut\":null,\"nombres\":\"David\",\"apellidos\":\"Osorio Valdebenito\",\"nombre_completo\":\"David Osorio Valdebenito\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:28:18\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:28:18', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:28:18'),
(118, 1, 'socios', 'crear', NULL, 99, NULL, '{\"id\":99,\"numero_socio\":\"000082\",\"rut\":null,\"nombres\":\"Abraham Roberto\",\"apellidos\":\"Otarola Jerez\",\"nombre_completo\":\"Abraham Roberto Otarola Jerez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:28:29\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:28:29', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:28:29'),
(119, 1, 'socios', 'crear', NULL, 100, NULL, '{\"id\":100,\"numero_socio\":\"000083\",\"rut\":null,\"nombres\":\"Luis Claudio\",\"apellidos\":\"Oyarzun Ojeda\",\"nombre_completo\":\"Luis Claudio Oyarzun Ojeda\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:28:44\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:28:44', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:28:44'),
(120, 1, 'socios', 'crear', NULL, 101, NULL, '{\"id\":101,\"numero_socio\":\"000084\",\"rut\":null,\"nombres\":\"Oscar\",\"apellidos\":\"Paredes Villar\",\"nombre_completo\":\"Oscar Paredes Villar\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:28:54\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:28:54', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:28:54'),
(121, 1, 'socios', 'crear', NULL, 102, NULL, '{\"id\":102,\"numero_socio\":\"000085\",\"rut\":null,\"nombres\":\"Pilar\",\"apellidos\":\"Perez\",\"nombre_completo\":\"Pilar Perez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:29:06\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:29:06', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:29:06'),
(122, 1, 'socios', 'crear', NULL, 103, NULL, '{\"id\":103,\"numero_socio\":\"000086\",\"rut\":null,\"nombres\":\"Raul Daniel\",\"apellidos\":\"Perez Hilliger\",\"nombre_completo\":\"Raul Daniel Perez Hilliger\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:29:18\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:29:18', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:29:18'),
(123, 1, 'socios', 'crear', NULL, 104, NULL, '{\"id\":104,\"numero_socio\":\"000087\",\"rut\":null,\"nombres\":\"Victor\",\"apellidos\":\"Pastene Varas\",\"nombre_completo\":\"Victor Pastene Varas\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:29:34\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:29:34', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:29:34'),
(124, 1, 'socios', 'crear', NULL, 105, NULL, '{\"id\":105,\"numero_socio\":\"000088\",\"rut\":null,\"nombres\":\"Luis Hernan\",\"apellidos\":\"Perez Nuñez\",\"nombre_completo\":\"Luis Hernan Perez Nuñez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:29:44\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:29:44', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:29:44'),
(125, 1, 'socios', 'crear', NULL, 106, NULL, '{\"id\":106,\"numero_socio\":\"000089\",\"rut\":null,\"nombres\":\"Sergio Eduardo\",\"apellidos\":\"Pineda Ramirez\",\"nombre_completo\":\"Sergio Eduardo Pineda Ramirez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:29:55\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:29:55', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:29:55'),
(126, 1, 'socios', 'crear', NULL, 107, NULL, '{\"id\":107,\"numero_socio\":\"000090\",\"rut\":null,\"nombres\":\"Patricio\",\"apellidos\":\"Pino Willenbrick\",\"nombre_completo\":\"Patricio Pino Willenbrick\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:30:05\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:30:05', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:30:05'),
(127, 1, 'socios', 'crear', NULL, 108, NULL, '{\"id\":108,\"numero_socio\":\"000091\",\"rut\":null,\"nombres\":\"Manuel\",\"apellidos\":\"Porras Pizarro\",\"nombre_completo\":\"Manuel Porras Pizarro\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:30:16\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:30:16', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:30:16'),
(128, 1, 'socios', 'crear', NULL, 109, NULL, '{\"id\":109,\"numero_socio\":\"000092\",\"rut\":null,\"nombres\":\"Jaime\",\"apellidos\":\"Quilodran Reyes\",\"nombre_completo\":\"Jaime Quilodran Reyes\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:30:29\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:30:29', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:30:29'),
(129, 1, 'socios', 'crear', NULL, 110, NULL, '{\"id\":110,\"numero_socio\":\"000093\",\"rut\":null,\"nombres\":\"Roque\",\"apellidos\":\"Quijada Muñoz\",\"nombre_completo\":\"Roque Quijada Muñoz\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:30:42\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:30:42', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:30:42'),
(130, 1, 'socios', 'crear', NULL, 111, NULL, '{\"id\":111,\"numero_socio\":\"000094\",\"rut\":null,\"nombres\":\"Javier\",\"apellidos\":\"Quiroz Rodriguez\",\"nombre_completo\":\"Javier Quiroz Rodriguez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:30:51\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:30:51', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:30:51'),
(131, 1, 'socios', 'crear', NULL, 112, NULL, '{\"id\":112,\"numero_socio\":\"000095\",\"rut\":null,\"nombres\":\"Oscar\",\"apellidos\":\"Rapiman\",\"nombre_completo\":\"Oscar Rapiman\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:31:05\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:31:05', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:31:05'),
(132, 1, 'socios', 'crear', NULL, 113, NULL, '{\"id\":113,\"numero_socio\":\"000096\",\"rut\":null,\"nombres\":\"Jose\",\"apellidos\":\"Rebolledo Palma\",\"nombre_completo\":\"Jose Rebolledo Palma\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:31:19\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:31:19', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:31:19'),
(133, 1, 'socios', 'crear', NULL, 114, NULL, '{\"id\":114,\"numero_socio\":\"000097\",\"rut\":null,\"nombres\":\"Juan Alberto\",\"apellidos\":\"Reyes Muñoz\",\"nombre_completo\":\"Juan Alberto Reyes Muñoz\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:31:30\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:31:30', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:31:30'),
(134, 1, 'socios', 'crear', NULL, 115, NULL, '{\"id\":115,\"numero_socio\":\"000098\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Rioseco Santander\",\"nombre_completo\":\"Luis Rioseco Santander\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:31:41\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:31:41', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:31:41'),
(135, 1, 'socios', 'crear', NULL, 116, NULL, '{\"id\":116,\"numero_socio\":\"000099\",\"rut\":null,\"nombres\":\"Orlando\",\"apellidos\":\"Rios Guerrero\",\"nombre_completo\":\"Orlando Rios Guerrero\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:31:52\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:31:52', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:31:52'),
(136, 1, 'socios', 'crear', NULL, 117, NULL, '{\"id\":117,\"numero_socio\":\"000100\",\"rut\":null,\"nombres\":\"Nector\",\"apellidos\":\"Riquelme Delgado\",\"nombre_completo\":\"Nector Riquelme Delgado\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:32:05\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:32:05', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:32:05'),
(137, 1, 'socios', 'crear', NULL, 118, NULL, '{\"id\":118,\"numero_socio\":\"000101\",\"rut\":null,\"nombres\":\"Pablo\",\"apellidos\":\"Robles Benitez\",\"nombre_completo\":\"Pablo Robles Benitez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:32:17\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:32:17', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:32:17'),
(138, 1, 'socios', 'crear', NULL, 119, NULL, '{\"id\":119,\"numero_socio\":\"000102\",\"rut\":null,\"nombres\":\"Rene Hipolito\",\"apellidos\":\"Rodriguez Ojeda\",\"nombre_completo\":\"Rene Hipolito Rodriguez Ojeda\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:32:30\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:32:30', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:32:30'),
(139, 1, 'socios', 'crear', NULL, 120, NULL, '{\"id\":120,\"numero_socio\":\"000103\",\"rut\":null,\"nombres\":\"Javier Jesus\",\"apellidos\":\"Rodriguez Quiroz\",\"nombre_completo\":\"Javier Jesus Rodriguez Quiroz\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:32:44\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:32:44', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:32:44'),
(140, 1, 'socios', 'crear', NULL, 121, NULL, '{\"id\":121,\"numero_socio\":\"000104\",\"rut\":null,\"nombres\":\"Fernando Luis\",\"apellidos\":\"Roman Gomez\",\"nombre_completo\":\"Fernando Luis Roman Gomez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:32:54\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:32:55', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:32:55'),
(141, 1, 'socios', 'crear', NULL, 122, NULL, '{\"id\":122,\"numero_socio\":\"000105\",\"rut\":null,\"nombres\":\"Victor Manuel\",\"apellidos\":\"Ross\",\"nombre_completo\":\"Victor Manuel Ross\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:33:05\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:33:05', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:33:05'),
(142, 1, 'socios', 'crear', NULL, 123, NULL, '{\"id\":123,\"numero_socio\":\"000106\",\"rut\":null,\"nombres\":\"Alfonso\",\"apellidos\":\"Roussel Santos\",\"nombre_completo\":\"Alfonso Roussel Santos\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:33:17\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:33:17', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:33:17'),
(143, 1, 'socios', 'crear', NULL, 124, NULL, '{\"id\":124,\"numero_socio\":\"000107\",\"rut\":null,\"nombres\":\"Enrique\",\"apellidos\":\"Ruiz Hernandez\",\"nombre_completo\":\"Enrique Ruiz Hernandez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:33:43\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:33:43', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:33:43'),
(144, 1, 'socios', 'crear', NULL, 125, NULL, '{\"id\":125,\"numero_socio\":\"000108\",\"rut\":null,\"nombres\":\"Pascuala\",\"apellidos\":\"Saez Navarrete\",\"nombre_completo\":\"Pascuala Saez Navarrete\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:33:52\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:33:52', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:33:52'),
(145, 1, 'socios', 'crear', NULL, 126, NULL, '{\"id\":126,\"numero_socio\":\"000109\",\"rut\":null,\"nombres\":\"Marcelo\",\"apellidos\":\"Salazar Inostroza\",\"nombre_completo\":\"Marcelo Salazar Inostroza\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:34:01\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:34:01', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:34:01'),
(146, 1, 'socios', 'crear', NULL, 127, NULL, '{\"id\":127,\"numero_socio\":\"000110\",\"rut\":null,\"nombres\":\"Juan Erminio\",\"apellidos\":\"Sanhueza Pino\",\"nombre_completo\":\"Juan Erminio Sanhueza Pino\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:34:12\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:34:12', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:34:12'),
(147, 1, 'socios', 'crear', NULL, 128, NULL, '{\"id\":128,\"numero_socio\":\"000111\",\"rut\":null,\"nombres\":\"Manuel\",\"apellidos\":\"Santelices Elgueta\",\"nombre_completo\":\"Manuel Santelices Elgueta\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:34:24\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:34:24', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:34:24'),
(148, 1, 'socios', 'crear', NULL, 129, NULL, '{\"id\":129,\"numero_socio\":\"000112\",\"rut\":null,\"nombres\":\"Egoberto\",\"apellidos\":\"Sepulveda Peña\",\"nombre_completo\":\"Egoberto Sepulveda Peña\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:34:43\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:34:43', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:34:43'),
(149, 1, 'socios', 'crear', NULL, 130, NULL, '{\"id\":130,\"numero_socio\":\"000113\",\"rut\":null,\"nombres\":\"Manuel Alfredo\",\"apellidos\":\"Tejada Tamayo\",\"nombre_completo\":\"Manuel Alfredo Tejada Tamayo\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:34:57\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:34:57', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:34:57'),
(150, 1, 'socios', 'crear', NULL, 131, NULL, '{\"id\":131,\"numero_socio\":\"000114\",\"rut\":null,\"nombres\":\"Rodolfo\",\"apellidos\":\"Tilleria Tilleria\",\"nombre_completo\":\"Rodolfo Tilleria Tilleria\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:35:11\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:35:11', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:35:11'),
(151, 1, 'socios', 'crear', NULL, 132, NULL, '{\"id\":132,\"numero_socio\":\"000115\",\"rut\":null,\"nombres\":\"Leonardo\",\"apellidos\":\"Trigo Almuna\",\"nombre_completo\":\"Leonardo Trigo Almuna\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:35:22\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:35:22', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:35:22'),
(152, 1, 'socios', 'crear', NULL, 133, NULL, '{\"id\":133,\"numero_socio\":\"000116\",\"rut\":null,\"nombres\":\"Oscar\",\"apellidos\":\"Toro Calderon\",\"nombre_completo\":\"Oscar Toro Calderon\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:35:34\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:35:34', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:35:34'),
(153, 1, 'socios', 'crear', NULL, 134, NULL, '{\"id\":134,\"numero_socio\":\"000117\",\"rut\":null,\"nombres\":\"Manuel\",\"apellidos\":\"Troncoso Madariaga\",\"nombre_completo\":\"Manuel Troncoso Madariaga\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:35:51\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:35:51', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:35:51'),
(154, 1, 'socios', 'crear', NULL, 135, NULL, '{\"id\":135,\"numero_socio\":\"000118\",\"rut\":null,\"nombres\":\"Alejandro\",\"apellidos\":\"Torres Sanhueza\",\"nombre_completo\":\"Alejandro Torres Sanhueza\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:36:02\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:36:02', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:36:02'),
(155, 1, 'socios', 'crear', NULL, 136, NULL, '{\"id\":136,\"numero_socio\":\"000119\",\"rut\":null,\"nombres\":\"Danilo Ivan\",\"apellidos\":\"Urbina Aravena\",\"nombre_completo\":\"Danilo Ivan Urbina Aravena\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:36:14\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:36:14', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:36:14'),
(156, 1, 'socios', 'crear', NULL, 137, NULL, '{\"id\":137,\"numero_socio\":\"000120\",\"rut\":null,\"nombres\":\"Juan\",\"apellidos\":\"Veliz Castro\",\"nombre_completo\":\"Juan Veliz Castro\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:36:25\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:36:25', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:36:25'),
(157, 1, 'socios', 'crear', NULL, 138, NULL, '{\"id\":138,\"numero_socio\":\"000121\",\"rut\":null,\"nombres\":\"Hector\",\"apellidos\":\"Vera Fuentes\",\"nombre_completo\":\"Hector Vera Fuentes\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:36:37\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:36:37', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:36:37'),
(158, 1, 'socios', 'crear', NULL, 139, NULL, '{\"id\":139,\"numero_socio\":\"000122\",\"rut\":null,\"nombres\":\"Luis\",\"apellidos\":\"Vergara Sepulveda\",\"nombre_completo\":\"Luis Vergara Sepulveda\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:36:49\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:36:49', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:36:49'),
(159, 1, 'socios', 'crear', NULL, 140, NULL, '{\"id\":140,\"numero_socio\":\"000123\",\"rut\":null,\"nombres\":\"Luis Hernan\",\"apellidos\":\"Videla Perez\",\"nombre_completo\":\"Luis Hernan Videla Perez\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:36:58\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:36:58', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:36:58'),
(160, 1, 'socios', 'crear', NULL, 141, NULL, '{\"id\":141,\"numero_socio\":\"000124\",\"rut\":null,\"nombres\":\"Jorge\",\"apellidos\":\"Villarroel\",\"nombre_completo\":\"Jorge Villarroel\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:08\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:37:08', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:37:08'),
(161, 1, 'socios', 'crear', NULL, 142, NULL, '{\"id\":142,\"numero_socio\":\"000125\",\"rut\":null,\"nombres\":\"Jose\",\"apellidos\":\"Vilugron Vilugron\",\"nombre_completo\":\"Jose Vilugron Vilugron\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:24\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:37:24', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:37:24'),
(162, 1, 'socios', 'crear', NULL, 143, NULL, '{\"id\":143,\"numero_socio\":\"000126\",\"rut\":null,\"nombres\":\"Leonarda\",\"apellidos\":\"Zurita Puebla\",\"nombre_completo\":\"Leonarda Zurita Puebla\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:34\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:37:34', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:37:34'),
(163, 1, 'socios', 'crear', NULL, 144, NULL, '{\"id\":144,\"numero_socio\":\"000127\",\"rut\":null,\"nombres\":\"Juan Carlos\",\"apellidos\":\"Zarate Villarroel\",\"nombre_completo\":\"Juan Carlos Zarate Villarroel\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:44\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:37:44', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:37:44');
INSERT INTO `auditoria` (`id`, `usuario_id`, `modulo`, `accion`, `registro_id`, `id_registro`, `datos_anteriores`, `datos_nuevos`, `fecha`, `ip`, `user_agent`, `created_at`) VALUES
(164, 1, 'socios', 'crear', NULL, 145, NULL, '{\"id\":145,\"numero_socio\":\"000128\",\"rut\":null,\"nombres\":\"Ricardo\",\"apellidos\":\"Zuñiga Peralta\",\"nombre_completo\":\"Ricardo Zuñiga Peralta\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:56\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:37:56', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:37:56'),
(165, 1, 'auth', 'login', NULL, 1, NULL, '{\"usuario\":\"admin\",\"correo\":\"admin@demo.local\"}', '2026-05-03 23:38:29', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:38:29'),
(166, 1, 'periodos', 'crear', NULL, 3, NULL, '{\"id\":3,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $6.000\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"6000.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:39:19\",\"updated_at\":null}', '2026-05-03 23:39:19', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:39:19'),
(167, 1, 'periodos', 'crear', NULL, 4, NULL, '{\"id\":4,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $3.000\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"3000.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:39:36\",\"updated_at\":null}', '2026-05-03 23:39:36', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:39:36'),
(168, 1, 'periodos', 'crear', NULL, 5, NULL, '{\"id\":5,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $3500\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"3500.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:40:22\",\"updated_at\":null}', '2026-05-03 23:40:22', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:40:22'),
(169, 1, 'periodos', 'crear', NULL, 6, NULL, '{\"id\":6,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $4000\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"4000.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:40:35\",\"updated_at\":null}', '2026-05-03 23:40:35', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:40:35'),
(170, 1, 'periodos', 'crear', NULL, 7, NULL, '{\"id\":7,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $5000\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"5000.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:40:53\",\"updated_at\":null}', '2026-05-03 23:40:53', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:40:53'),
(171, 1, 'periodos', 'crear', NULL, 8, NULL, '{\"id\":8,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $8000\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"8000.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:41:02\",\"updated_at\":null}', '2026-05-03 23:41:02', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:41:02'),
(172, 1, 'periodos', 'crear', NULL, 9, NULL, '{\"id\":9,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $10000\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"10000.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:41:23\",\"updated_at\":null}', '2026-05-03 23:41:23', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:41:23'),
(173, 1, 'periodos', 'crear', NULL, 10, NULL, '{\"id\":10,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $20000\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"20000.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:42:19\",\"updated_at\":null}', '2026-05-03 23:42:19', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:42:19'),
(174, 1, 'periodos', 'crear', NULL, 11, NULL, '{\"id\":11,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $1500\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"1500.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:42:27\",\"updated_at\":null}', '2026-05-03 23:42:27', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:42:27'),
(175, 1, 'periodos', 'crear', NULL, 12, NULL, '{\"id\":12,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $30000\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"30000.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:42:35\",\"updated_at\":null}', '2026-05-03 23:42:35', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:42:35'),
(176, 1, 'periodos', 'crear', NULL, 13, NULL, '{\"id\":13,\"anio\":2026,\"mes\":5,\"nombre_periodo\":\"Plan mensual $7000\",\"tipo_periodo\":\"mensual\",\"monto_a_pagar\":\"7000.00\",\"fecha_inicio\":null,\"fecha_fin\":null,\"fecha_vencimiento\":null,\"cerrado\":0,\"observacion\":null,\"created_at\":\"2026-05-03 19:43:26\",\"updated_at\":null}', '2026-05-03 23:43:26', '10.24.146.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:43:26'),
(177, 1, 'socios', 'actualizar', NULL, 145, '{\"id\":145,\"numero_socio\":\"000128\",\"rut\":null,\"nombres\":\"Ricardo\",\"apellidos\":\"Zuñiga Peralta\",\"nombre_completo\":\"Ricardo Zuñiga Peralta\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:56\",\"updated_at\":null,\"deleted_at\":null}', '{\"id\":145,\"numero_socio\":\"000128\",\"rut\":null,\"nombres\":\"Ricardo\",\"apellidos\":\"Zuñiga Peralta\",\"nombre_completo\":\"Ricardo Zuñiga Peralta\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:56\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:43:53', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:43:53'),
(178, 1, 'socios', 'actualizar', NULL, 144, '{\"id\":144,\"numero_socio\":\"000127\",\"rut\":null,\"nombres\":\"Juan Carlos\",\"apellidos\":\"Zarate Villarroel\",\"nombre_completo\":\"Juan Carlos Zarate Villarroel\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:44\",\"updated_at\":null,\"deleted_at\":null}', '{\"id\":144,\"numero_socio\":\"000127\",\"rut\":null,\"nombres\":\"Juan Carlos\",\"apellidos\":\"Zarate Villarroel\",\"nombre_completo\":\"Juan Carlos Zarate Villarroel\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:44\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:48:08', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:48:08'),
(179, 1, 'socios', 'actualizar', NULL, 143, '{\"id\":143,\"numero_socio\":\"000126\",\"rut\":null,\"nombres\":\"Leonarda\",\"apellidos\":\"Zurita Puebla\",\"nombre_completo\":\"Leonarda Zurita Puebla\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:34\",\"updated_at\":null,\"deleted_at\":null}', '{\"id\":143,\"numero_socio\":\"000126\",\"rut\":null,\"nombres\":\"Leonarda\",\"apellidos\":\"Zurita Puebla\",\"nombre_completo\":\"Leonarda Zurita Puebla\",\"fecha_nacimiento\":null,\"telefono\":null,\"correo\":null,\"direccion\":null,\"comuna\":null,\"ciudad\":null,\"tipo_socio_id\":null,\"estado_socio_id\":null,\"fecha_ingreso\":null,\"observaciones\":null,\"activo\":1,\"created_at\":\"2026-05-03 19:37:34\",\"updated_at\":null,\"deleted_at\":null}', '2026-05-03 23:48:18', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 23:48:18'),
(180, 1, 'auth', 'login', NULL, 1, NULL, '{\"usuario\":\"admin\",\"correo\":\"admin@demo.local\"}', '2026-05-04 00:35:21', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 00:35:21'),
(181, 1, 'auth', 'login', NULL, 1, NULL, '{\"usuario\":\"admin\",\"correo\":\"admin@demo.local\"}', '2026-05-04 00:36:31', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 00:36:31'),
(182, 1, 'auth', 'login', NULL, 1, NULL, '{\"usuario\":\"admin\",\"correo\":\"admin@demo.local\"}', '2026-05-04 02:56:26', '10.85.253.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 02:56:26'),
(183, 1, 'auth', 'login', NULL, 1, NULL, '{\"usuario\":\"admin\",\"correo\":\"admin@demo.local\"}', '2026-05-04 02:57:03', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 02:57:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conceptos_cobro`
--

CREATE TABLE `conceptos_cobro` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `monto_sugerido` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_organizacion` varchar(140) DEFAULT NULL,
  `nombre_sistema` varchar(140) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `rut_organizacion` varchar(30) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(40) DEFAULT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `sitio_web` varchar(120) DEFAULT NULL,
  `flow_checkout_activo` tinyint(1) NOT NULL DEFAULT 0,
  `flow_api_key` varchar(120) DEFAULT NULL,
  `flow_secret_key` varchar(140) DEFAULT NULL,
  `flow_modo_sandbox` tinyint(1) NOT NULL DEFAULT 1,
  `flow_url_confirmacion` varchar(255) DEFAULT NULL,
  `flow_url_retorno` varchar(255) DEFAULT NULL,
  `cuota_por_defecto` decimal(12,2) DEFAULT 0.00,
  `moneda` varchar(20) DEFAULT 'CLP',
  `simbolo_moneda` varchar(5) DEFAULT '$',
  `texto_comprobante` text DEFAULT NULL,
  `observaciones_generales` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nombre_organizacion`, `nombre_sistema`, `logo`, `rut_organizacion`, `direccion`, `telefono`, `correo`, `sitio_web`, `flow_checkout_activo`, `flow_api_key`, `flow_secret_key`, `flow_modo_sandbox`, `flow_url_confirmacion`, `flow_url_retorno`, `cuota_por_defecto`, `moneda`, `simbolo_moneda`, `texto_comprobante`, `observaciones_generales`, `updated_at`) VALUES
(1, 'Organización Demo', 'GO Expreso', NULL, '999988888-9', 'Dirección referencial 123', '+56 9 0000 0000', 'info@organizacion.test', 'https://tastisense', 0, '484DFD4D-0A41-424D-A573-95BDAF374LD4', '444a7bf7b3ba4c3a8708ce9d7be241223604ee96', 0, NULL, NULL, 15000.00, 'CLP', '$', 'Gracias por su pago.', NULL, '2026-04-26 17:19:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_bancarias`
--

CREATE TABLE `cuentas_bancarias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `banco` varchar(120) DEFAULT NULL,
  `tipo_cuenta` varchar(60) DEFAULT NULL,
  `numero_cuenta` varchar(60) DEFAULT NULL,
  `titular` varchar(120) DEFAULT NULL,
  `correo_asociado` varchar(120) DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1,
  `saldo_inicial` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuotas`
--

CREATE TABLE `cuotas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `socio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `periodo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `concepto_cobro_id` bigint(20) UNSIGNED DEFAULT NULL,
  `monto_base` decimal(12,2) DEFAULT NULL,
  `saldo_arrastre` decimal(12,2) DEFAULT 0.00,
  `monto_total` decimal(12,2) DEFAULT NULL,
  `monto_pagado` decimal(12,2) DEFAULT 0.00,
  `saldo_pendiente` decimal(12,2) DEFAULT NULL,
  `estado_cuota` enum('pendiente','abonada_parcial','pagada','vencida','exenta','anulada') DEFAULT 'pendiente',
  `fecha_vencimiento` date DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `generada_automaticamente` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuotas`
--

INSERT INTO `cuotas` (`id`, `socio_id`, `periodo_id`, `concepto_cobro_id`, `monto_base`, `saldo_arrastre`, `monto_total`, `monto_pagado`, `saldo_pendiente`, `estado_cuota`, `fecha_vencimiento`, `observacion`, `generada_automaticamente`, `created_at`, `updated_at`, `deleted_at`) VALUES
(17, 32, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:46:40', '2026-05-04 01:46:40', NULL),
(18, 33, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:47:18', '2026-05-04 01:47:18', NULL),
(19, 33, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:47:28', '2026-05-04 01:47:28', NULL),
(20, 18, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:51:16', '2026-05-04 01:51:16', NULL),
(21, 18, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:51:25', '2026-05-04 01:51:25', NULL),
(22, 18, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:51:35', '2026-05-04 01:51:35', NULL),
(23, 18, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:51:55', '2026-05-04 01:51:55', NULL),
(24, 18, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:52:37', '2026-05-04 01:52:37', NULL),
(25, 18, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:52:48', '2026-05-04 01:52:48', NULL),
(26, 20, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:54:04', '2026-05-04 01:54:04', NULL),
(27, 20, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:54:12', '2026-05-04 01:54:12', NULL),
(28, 20, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:54:18', '2026-05-04 01:54:18', NULL),
(29, 20, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:54:25', '2026-05-04 01:54:25', NULL),
(30, 20, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:54:58', '2026-05-04 01:54:58', NULL),
(31, 20, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:55:06', '2026-05-04 01:55:06', NULL),
(32, 20, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:55:13', '2026-05-04 01:55:13', NULL),
(33, 20, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:55:19', '2026-05-04 01:55:19', NULL),
(34, 20, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-09-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:55:26', '2026-05-04 01:55:26', NULL),
(35, 20, 8, NULL, 8000.00, 0.00, 8000.00, 8000.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:55:33', '2026-05-04 01:55:33', NULL),
(36, 22, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:57:32', '2026-05-04 01:57:32', NULL),
(37, 22, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:57:38', '2026-05-04 01:57:38', NULL),
(38, 22, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:57:46', '2026-05-04 01:57:46', NULL),
(39, 22, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:57:52', '2026-05-04 01:57:52', NULL),
(40, 23, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:58:14', '2026-05-04 01:58:14', NULL),
(41, 23, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:58:22', '2026-05-04 01:58:22', NULL),
(42, 23, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:58:27', '2026-05-04 01:58:27', NULL),
(43, 23, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:58:33', '2026-05-04 01:58:33', NULL),
(44, 23, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:58:40', '2026-05-04 01:58:40', NULL),
(45, 23, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:58:47', '2026-05-04 01:58:47', NULL),
(46, 28, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:59:23', '2026-05-04 01:59:23', NULL),
(47, 28, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:59:30', '2026-05-04 01:59:30', NULL),
(48, 28, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:59:35', '2026-05-04 01:59:35', NULL),
(49, 28, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:59:41', '2026-05-04 01:59:41', NULL),
(50, 28, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:59:47', '2026-05-04 01:59:47', NULL),
(51, 28, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 01:59:57', '2026-05-04 01:59:57', NULL),
(52, 28, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:00:06', '2026-05-04 02:00:06', NULL),
(57, 31, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:01:02', '2026-05-04 02:01:02', NULL),
(58, 31, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:01:08', '2026-05-04 02:01:08', NULL),
(59, 31, 4, NULL, 3000.00, 0.00, 3000.00, 3000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:01:14', '2026-05-04 02:01:14', NULL),
(60, 35, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:01:49', '2026-05-04 02:01:49', NULL),
(61, 35, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:01:55', '2026-05-04 02:01:55', NULL),
(62, 35, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:02:01', '2026-05-04 02:02:01', NULL),
(63, 35, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:02:07', '2026-05-04 02:02:07', NULL),
(64, 35, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:02:20', '2026-05-04 02:02:20', NULL),
(65, 35, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:02:26', '2026-05-04 02:02:26', NULL),
(66, 36, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:03:36', '2026-05-04 02:03:36', NULL),
(67, 36, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:03:42', '2026-05-04 02:03:42', NULL),
(68, 36, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:03:48', '2026-05-04 02:03:48', NULL),
(69, 36, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:03:53', '2026-05-04 02:03:53', NULL),
(70, 36, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:04:03', '2026-05-04 02:04:03', NULL),
(71, 36, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:04:09', '2026-05-04 02:04:09', NULL),
(72, 37, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:04:37', '2026-05-04 02:04:37', NULL),
(73, 37, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:04:47', '2026-05-04 02:04:47', NULL),
(74, 37, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:04:53', '2026-05-04 02:04:53', NULL),
(75, 37, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:04:58', '2026-05-04 02:04:58', NULL),
(76, 37, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:05:04', '2026-05-04 02:05:04', NULL),
(77, 37, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:05:11', '2026-05-04 02:05:11', NULL),
(78, 37, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:05:21', '2026-05-04 02:05:21', NULL),
(79, 38, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:06:06', '2026-05-04 02:06:06', NULL),
(80, 38, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:06:11', '2026-05-04 02:06:11', NULL),
(81, 39, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:06:38', '2026-05-04 02:06:38', NULL),
(82, 39, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:06:43', '2026-05-04 02:06:43', NULL),
(83, 39, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:06:49', '2026-05-04 02:06:49', NULL),
(84, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:07:19', '2026-05-04 02:07:19', NULL),
(85, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:07:24', '2026-05-04 02:07:25', NULL),
(86, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:07:30', '2026-05-04 02:07:30', NULL),
(87, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:07:35', '2026-05-04 02:07:35', NULL),
(88, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:07:42', '2026-05-04 02:07:42', NULL),
(89, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:07:47', '2026-05-04 02:07:47', NULL),
(90, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:07:52', '2026-05-04 02:07:52', NULL),
(91, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:07:57', '2026-05-04 02:07:57', NULL),
(92, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-09-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:08:03', '2026-05-04 02:08:03', NULL),
(93, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:08:07', '2026-05-04 02:08:07', NULL),
(94, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-11-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:08:12', '2026-05-04 02:08:12', NULL),
(95, 40, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:08:17', '2026-05-04 02:08:17', NULL),
(96, 41, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:08:32', '2026-05-04 02:08:32', NULL),
(97, 41, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:08:37', '2026-05-04 02:08:37', NULL),
(98, 41, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:08:48', '2026-05-04 02:08:48', NULL),
(99, 41, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:09:01', '2026-05-04 02:09:01', NULL),
(100, 43, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:10:28', '2026-05-04 02:10:28', NULL),
(101, 43, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:10:33', '2026-05-04 02:10:33', NULL),
(102, 43, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:10:39', '2026-05-04 02:10:39', NULL),
(103, 43, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:10:44', '2026-05-04 02:10:44', NULL),
(104, 43, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:10:50', '2026-05-04 02:10:50', NULL),
(105, 43, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:10:55', '2026-05-04 02:10:55', NULL),
(106, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:11:49', '2026-05-04 02:11:49', NULL),
(107, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:11:54', '2026-05-04 02:11:54', NULL),
(108, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:12:00', '2026-05-04 02:12:00', NULL),
(109, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:12:05', '2026-05-04 02:12:05', NULL),
(110, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:12:10', '2026-05-04 02:12:10', NULL),
(111, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-06-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:12:17', '2026-05-04 02:12:17', NULL),
(112, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:12:25', '2026-05-04 02:12:25', NULL),
(113, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:12:29', '2026-05-04 02:12:29', NULL),
(114, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-09-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:12:42', '2026-05-04 02:12:42', NULL),
(115, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:13:12', '2026-05-04 02:13:12', NULL),
(116, 44, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-11-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:13:20', '2026-05-04 02:13:20', NULL),
(117, 44, 12, NULL, 30000.00, 0.00, 30000.00, 30000.00, 0.00, 'pagada', '2026-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:13:33', '2026-05-04 02:13:33', NULL),
(122, 29, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:29:24', '2026-05-04 02:29:24', NULL),
(123, 29, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-02-28', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:29:29', '2026-05-04 02:29:29', NULL),
(124, 29, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:29:35', '2026-05-04 02:29:35', NULL),
(125, 29, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-04-30', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:29:42', '2026-05-04 02:29:42', NULL),
(126, 47, 12, NULL, 30000.00, 0.00, 30000.00, 30000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:45:46', '2026-05-04 02:45:46', NULL),
(127, 47, 12, NULL, 30000.00, 0.00, 30000.00, 0.00, 30000.00, 'pendiente', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:46:13', NULL, NULL),
(128, 50, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:48:52', '2026-05-04 02:48:52', NULL),
(129, 50, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:49:14', '2026-05-04 02:49:14', NULL),
(130, 50, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:49:22', '2026-05-04 02:49:22', NULL),
(131, 50, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:49:35', '2026-05-04 02:49:35', NULL),
(132, 51, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:50:10', '2026-05-04 02:50:10', NULL),
(133, 51, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:50:18', '2026-05-04 02:50:18', NULL),
(134, 51, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:50:27', '2026-05-04 02:50:27', NULL),
(135, 51, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:50:37', '2026-05-04 02:50:37', NULL),
(136, 51, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:50:46', '2026-05-04 02:50:46', NULL),
(137, 51, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2027-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:51:34', '2026-05-04 02:51:34', NULL),
(138, 54, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:56:57', '2026-05-04 02:56:57', NULL),
(139, 54, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:57:04', '2026-05-04 02:57:04', NULL),
(140, 54, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:57:12', '2026-05-04 02:57:12', NULL),
(141, 56, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:57:33', '2026-05-04 02:57:33', NULL),
(142, 56, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:57:42', '2026-05-04 02:57:42', NULL),
(143, 56, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:57:49', '2026-05-04 02:57:49', NULL),
(144, 56, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:58:00', '2026-05-04 02:58:00', NULL),
(145, 56, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:58:11', '2026-05-04 02:58:11', NULL),
(146, 57, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:58:37', '2026-05-04 02:58:37', NULL),
(147, 57, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:58:44', '2026-05-04 02:58:44', NULL),
(148, 57, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:58:51', '2026-05-04 02:58:51', NULL),
(149, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:59:22', '2026-05-04 02:59:22', NULL),
(150, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:59:29', '2026-05-04 02:59:29', NULL),
(151, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:59:36', '2026-05-04 02:59:36', NULL),
(152, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:59:45', '2026-05-04 02:59:45', NULL),
(153, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 02:59:52', '2026-05-04 02:59:52', NULL),
(154, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:00:01', '2026-05-04 03:00:01', NULL),
(155, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:00:08', '2026-05-04 03:00:08', NULL),
(156, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:00:18', '2026-05-04 03:00:18', NULL),
(157, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:00:26', '2026-05-04 03:00:26', NULL),
(158, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:00:33', '2026-05-04 03:00:33', NULL),
(159, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:00:40', '2026-05-04 03:00:40', NULL),
(160, 59, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:00:49', '2026-05-04 03:00:49', NULL),
(161, 60, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:01:20', '2026-05-04 03:01:20', NULL),
(162, 60, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:01:26', '2026-05-04 03:01:26', NULL),
(163, 60, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:01:32', '2026-05-04 03:01:32', NULL),
(164, 60, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:01:40', '2026-05-04 03:01:40', NULL),
(165, 60, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2026-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:01:53', '2026-05-04 03:01:53', NULL),
(166, 60, 5, NULL, 3500.00, 0.00, 3500.00, 3500.00, 0.00, 'pagada', '2027-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:02:02', '2026-05-04 03:02:02', NULL),
(167, 61, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:02:52', '2026-05-04 03:02:52', NULL),
(168, 61, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:03:01', '2026-05-04 03:03:01', NULL),
(169, 61, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:03:10', '2026-05-04 03:03:10', NULL),
(170, 61, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:03:18', '2026-05-04 03:03:18', NULL),
(171, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:06:52', '2026-05-04 03:06:52', NULL),
(172, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:06:58', '2026-05-04 03:06:58', NULL),
(173, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:07:06', '2026-05-04 03:07:06', NULL),
(174, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:07:15', '2026-05-04 03:07:15', NULL),
(175, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2026-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:07:20', '2026-05-04 03:07:20', NULL),
(176, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2027-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:07:27', '2026-05-04 03:07:27', NULL),
(177, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2027-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:07:53', '2026-05-04 03:07:53', NULL),
(178, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2027-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:08:00', '2026-05-04 03:08:00', NULL),
(179, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2027-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:08:07', '2026-05-04 03:08:07', NULL),
(180, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2027-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:08:17', '2026-05-04 03:08:17', NULL),
(181, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2027-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:08:25', '2026-05-04 03:08:25', NULL),
(182, 62, 6, NULL, 4000.00, 0.00, 4000.00, 4000.00, 0.00, 'pagada', '2027-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:08:34', '2026-05-04 03:08:34', NULL),
(183, 63, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:09:02', '2026-05-04 03:09:02', NULL),
(184, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:09:39', '2026-05-04 03:09:39', NULL),
(185, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:09:45', '2026-05-04 03:09:45', NULL),
(186, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:09:53', '2026-05-04 03:09:53', NULL),
(187, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:09:59', '2026-05-04 03:09:59', NULL),
(188, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2026-12-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:11:05', '2026-05-04 03:11:05', NULL),
(189, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-01-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:11:13', '2026-05-04 03:11:13', NULL),
(190, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-03-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:11:20', '2026-05-04 03:11:20', NULL),
(191, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:11:28', '2026-05-04 03:11:28', NULL),
(192, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-07-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:14:19', '2026-05-04 03:14:19', NULL),
(193, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-08-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:14:27', '2026-05-04 03:14:27', NULL),
(194, 64, 7, NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'pagada', '2027-10-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:14:35', '2026-05-04 03:14:35', NULL),
(195, 64, 10, NULL, 20000.00, 0.00, 20000.00, 20000.00, 0.00, 'pagada', '2026-05-31', 'Generada automáticamente desde Registro de cuotas', 1, '2026-05-04 03:14:45', '2026-05-04 03:14:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresos`
--

CREATE TABLE `egresos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date DEFAULT NULL,
  `tipo_egreso_id` bigint(20) UNSIGNED DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `monto` decimal(12,2) DEFAULT NULL,
  `numero_documento` varchar(80) DEFAULT NULL,
  `proveedor_destinatario` varchar(120) DEFAULT NULL,
  `cuenta_bancaria_id` bigint(20) UNSIGNED DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('aplicado','anulado','pendiente_revision') DEFAULT 'aplicado',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_socio`
--

CREATE TABLE `estados_socio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `color_badge` varchar(30) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados_socio`
--

INSERT INTO `estados_socio` (`id`, `nombre`, `descripcion`, `color_badge`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Activo', NULL, NULL, 1, '2026-04-18 05:18:31', NULL),
(2, 'Moroso', NULL, NULL, 1, '2026-04-18 05:18:31', NULL),
(3, 'Suspendido', NULL, NULL, 1, '2026-04-18 05:18:31', NULL),
(4, 'Retirado', NULL, NULL, 1, '2026-04-18 05:18:31', NULL),
(5, 'Honorario', NULL, NULL, 1, '2026-04-18 05:18:31', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medios_pago`
--

CREATE TABLE `medios_pago` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(60) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medios_pago`
--

INSERT INTO `medios_pago` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Transferencia', NULL, 1, '2026-04-18 05:18:31', NULL),
(2, 'Depósito', NULL, 1, '2026-04-18 05:18:31', NULL),
(3, 'Efectivo', NULL, 1, '2026-04-18 05:18:31', NULL),
(4, 'Caja', NULL, 1, '2026-04-18 05:18:31', NULL),
(5, 'Cheque', NULL, 1, '2026-04-18 05:18:31', NULL),
(6, 'Otro', NULL, 1, '2026-04-18 05:18:31', NULL),
(7, 'Tarjeta bancaria', NULL, 1, '2026-04-26 05:41:33', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(20) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `fecha_ejecucion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ejecutado_por` varchar(100) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_tesoreria`
--

CREATE TABLE `movimientos_tesoreria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cuenta_bancaria_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tipo_movimiento` enum('ingreso','egreso') DEFAULT NULL,
  `origen_modulo` varchar(80) DEFAULT NULL,
  `referencia_id` bigint(20) UNSIGNED DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `ingreso` decimal(12,2) DEFAULT 0.00,
  `egreso` decimal(12,2) DEFAULT 0.00,
  `saldo_referencial` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `socio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `monto_total` decimal(12,2) DEFAULT NULL,
  `medio_pago_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cuenta_bancaria_id` bigint(20) UNSIGNED DEFAULT NULL,
  `numero_comprobante` varchar(60) DEFAULT NULL,
  `referencia_externa` varchar(100) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `periodo_a_pagar` varchar(140) DEFAULT NULL,
  `observacion_interna` varchar(255) DEFAULT NULL,
  `estado_pago` enum('aplicado','anulado','pendiente_revision') DEFAULT 'aplicado',
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `socio_id`, `fecha_pago`, `monto_total`, `medio_pago_id`, `cuenta_bancaria_id`, `numero_comprobante`, `referencia_externa`, `observacion`, `periodo_a_pagar`, `observacion_interna`, `estado_pago`, `usuario_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(13, 99, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-204309-539', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 00:43:09', NULL, NULL),
(14, 99, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-204408-911', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 00:44:08', NULL, NULL),
(15, 32, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-214640-267', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 01:46:40', NULL, NULL),
(16, 33, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-214718-260', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 01:47:18', NULL, NULL),
(17, 33, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-214728-114', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 01:47:28', NULL, NULL),
(18, 18, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215116-889', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 01:51:16', NULL, NULL),
(19, 18, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215125-366', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 01:51:25', NULL, NULL),
(20, 18, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215135-579', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 01:51:35', NULL, NULL),
(21, 18, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215155-496', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 01:51:55', NULL, NULL),
(22, 18, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215237-739', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 01:52:37', NULL, NULL),
(23, 18, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215248-264', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 01:52:48', NULL, NULL),
(24, 20, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-215404-501', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 01:54:04', NULL, NULL),
(25, 20, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-215412-937', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 01:54:12', NULL, NULL),
(26, 20, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-215418-275', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 01:54:18', NULL, NULL),
(27, 20, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-215425-798', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 01:54:25', NULL, NULL),
(28, 20, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-215458-945', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 01:54:58', NULL, NULL),
(29, 20, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-215506-690', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 01:55:06', NULL, NULL),
(30, 20, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-215513-405', NULL, 'Pago registrado desde Registro de cuotas', 'Mes julio 2026', NULL, 'aplicado', 1, '2026-05-04 01:55:13', NULL, NULL),
(31, 20, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-215519-723', NULL, 'Pago registrado desde Registro de cuotas', 'Mes agosto 2026', NULL, 'aplicado', 1, '2026-05-04 01:55:19', NULL, NULL),
(32, 20, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-215526-837', NULL, 'Pago registrado desde Registro de cuotas', 'Mes septiembre 2026', NULL, 'aplicado', 1, '2026-05-04 01:55:26', NULL, NULL),
(33, 20, '2026-05-03', 8000.00, 1, NULL, 'CUO-20260503-215533-130', NULL, 'Pago registrado desde Registro de cuotas', 'Mes octubre 2026', NULL, 'aplicado', 1, '2026-05-04 01:55:33', NULL, NULL),
(34, 22, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215732-979', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 01:57:32', NULL, NULL),
(35, 22, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215738-957', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 01:57:38', NULL, NULL),
(36, 22, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215746-587', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 01:57:46', NULL, NULL),
(37, 22, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215752-540', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 01:57:52', NULL, NULL),
(38, 23, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215814-509', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 01:58:14', NULL, NULL),
(39, 23, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215822-937', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 01:58:22', NULL, NULL),
(40, 23, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215827-987', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 01:58:27', NULL, NULL),
(41, 23, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215833-605', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 01:58:33', NULL, NULL),
(42, 23, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215840-216', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 01:58:40', NULL, NULL),
(43, 23, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-215847-135', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 01:58:47', NULL, NULL),
(44, 28, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215923-957', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 01:59:23', NULL, NULL),
(45, 28, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215930-251', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 01:59:30', NULL, NULL),
(46, 28, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215935-724', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 01:59:35', NULL, NULL),
(47, 28, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215941-293', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 01:59:41', NULL, NULL),
(48, 28, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215947-395', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 01:59:47', NULL, NULL),
(49, 28, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-215957-342', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 01:59:57', NULL, NULL),
(50, 28, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220006-626', NULL, 'Pago registrado desde Registro de cuotas', 'Mes julio 2026', NULL, 'aplicado', 1, '2026-05-04 02:00:06', NULL, NULL),
(55, 31, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-220102-403', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:01:02', NULL, NULL),
(56, 31, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-220108-208', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:01:08', NULL, NULL),
(57, 31, '2026-05-03', 3000.00, 1, NULL, 'CUO-20260503-220114-490', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:01:14', NULL, NULL),
(58, 35, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-220149-944', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:01:49', NULL, NULL),
(59, 35, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-220155-881', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:01:55', NULL, NULL),
(60, 35, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-220201-500', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:02:01', NULL, NULL),
(61, 35, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-220207-890', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:02:07', NULL, NULL),
(62, 35, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-220220-296', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:02:20', NULL, NULL),
(63, 35, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-220226-351', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 02:02:26', NULL, NULL),
(64, 36, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220336-563', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:03:36', NULL, NULL),
(65, 36, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220342-179', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:03:42', NULL, NULL),
(66, 36, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220348-164', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:03:48', NULL, NULL),
(67, 36, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220353-221', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:03:53', NULL, NULL),
(68, 36, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220403-606', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:04:03', NULL, NULL),
(69, 36, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220409-581', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 02:04:09', NULL, NULL),
(70, 37, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220437-580', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:04:37', NULL, NULL),
(71, 37, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220447-212', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:04:47', NULL, NULL),
(72, 37, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220453-664', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:04:53', NULL, NULL),
(73, 37, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220458-467', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:04:58', NULL, NULL),
(74, 37, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220504-940', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:05:04', NULL, NULL),
(75, 37, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220511-930', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 02:05:11', NULL, NULL),
(76, 37, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-220521-599', NULL, 'Pago registrado desde Registro de cuotas', 'Mes julio 2026', NULL, 'aplicado', 1, '2026-05-04 02:05:21', NULL, NULL),
(77, 38, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220606-789', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:06:06', NULL, NULL),
(78, 38, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220611-277', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:06:11', NULL, NULL),
(79, 39, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220638-102', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:06:38', NULL, NULL),
(80, 39, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220643-346', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:06:43', NULL, NULL),
(81, 39, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220649-496', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:06:49', NULL, NULL),
(82, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220719-831', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:07:19', NULL, NULL),
(83, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220725-842', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:07:25', NULL, NULL),
(84, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220730-680', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:07:30', NULL, NULL),
(85, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220735-506', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:07:35', NULL, NULL),
(86, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220742-715', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:07:42', NULL, NULL),
(87, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220747-166', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 02:07:47', NULL, NULL),
(88, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220752-628', NULL, 'Pago registrado desde Registro de cuotas', 'Mes julio 2026', NULL, 'aplicado', 1, '2026-05-04 02:07:52', NULL, NULL),
(89, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220757-636', NULL, 'Pago registrado desde Registro de cuotas', 'Mes agosto 2026', NULL, 'aplicado', 1, '2026-05-04 02:07:57', NULL, NULL),
(90, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220803-310', NULL, 'Pago registrado desde Registro de cuotas', 'Mes septiembre 2026', NULL, 'aplicado', 1, '2026-05-04 02:08:03', NULL, NULL),
(91, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220807-873', NULL, 'Pago registrado desde Registro de cuotas', 'Mes octubre 2026', NULL, 'aplicado', 1, '2026-05-04 02:08:07', NULL, NULL),
(92, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220812-808', NULL, 'Pago registrado desde Registro de cuotas', 'Mes noviembre 2026', NULL, 'aplicado', 1, '2026-05-04 02:08:12', NULL, NULL),
(93, 40, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220817-530', NULL, 'Pago registrado desde Registro de cuotas', 'Mes diciembre 2026', NULL, 'aplicado', 1, '2026-05-04 02:08:17', NULL, NULL),
(94, 41, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220832-150', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:08:32', NULL, NULL),
(95, 41, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220837-630', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:08:37', NULL, NULL),
(96, 41, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220848-161', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:08:48', NULL, NULL),
(97, 41, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-220901-590', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:09:01', NULL, NULL),
(98, 43, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221028-143', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:10:28', NULL, NULL),
(99, 43, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221033-749', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:10:33', NULL, NULL),
(100, 43, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221039-592', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:10:39', NULL, NULL),
(101, 43, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221044-587', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:10:44', NULL, NULL),
(102, 43, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221050-392', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:10:50', NULL, NULL),
(103, 43, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221055-636', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 02:10:55', NULL, NULL),
(104, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221149-316', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:11:49', NULL, NULL),
(105, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221154-708', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:11:54', NULL, NULL),
(106, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221200-312', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:12:00', NULL, NULL),
(107, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221205-235', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:12:05', NULL, NULL),
(108, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221210-968', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:12:10', NULL, NULL),
(109, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221217-507', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 02:12:17', NULL, NULL),
(110, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221225-183', NULL, 'Pago registrado desde Registro de cuotas', 'Mes julio 2026', NULL, 'aplicado', 1, '2026-05-04 02:12:25', NULL, NULL),
(111, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221229-540', NULL, 'Pago registrado desde Registro de cuotas', 'Mes agosto 2026', NULL, 'aplicado', 1, '2026-05-04 02:12:29', NULL, NULL),
(112, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221242-374', NULL, 'Pago registrado desde Registro de cuotas', 'Mes septiembre 2026', NULL, 'aplicado', 1, '2026-05-04 02:12:42', NULL, NULL),
(113, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221312-555', NULL, 'Pago registrado desde Registro de cuotas', 'Mes octubre 2026', NULL, 'aplicado', 1, '2026-05-04 02:13:12', NULL, NULL),
(114, 44, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-221320-308', NULL, 'Pago registrado desde Registro de cuotas', 'Mes noviembre 2026', NULL, 'aplicado', 1, '2026-05-04 02:13:20', NULL, NULL),
(115, 44, '2026-05-03', 30000.00, 1, NULL, 'CUO-20260503-221333-709', NULL, 'Pago registrado desde Registro de cuotas', 'Mes diciembre 2026', NULL, 'aplicado', 1, '2026-05-04 02:13:33', NULL, NULL),
(120, 29, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-222924-132', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:29:24', NULL, NULL),
(121, 29, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-222929-201', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:29:29', NULL, NULL),
(122, 29, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-222935-182', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:29:35', NULL, NULL),
(123, 29, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-222942-949', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:29:42', NULL, NULL),
(124, 47, '2026-05-03', 30000.00, 1, NULL, 'CUO-20260503-224546-119', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:45:46', NULL, NULL),
(125, 50, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-224852-385', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:48:52', NULL, NULL),
(126, 50, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-224914-648', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:49:14', NULL, NULL),
(127, 50, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-224922-659', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:49:22', NULL, NULL),
(128, 50, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-224935-610', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 02:49:35', NULL, NULL),
(129, 51, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225010-157', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:50:10', NULL, NULL),
(130, 51, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225018-964', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:50:18', NULL, NULL),
(131, 51, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225027-410', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:50:27', NULL, NULL),
(132, 51, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225037-791', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:50:37', NULL, NULL),
(133, 51, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225046-702', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 02:50:46', NULL, NULL),
(134, 51, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225134-614', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:51:34', NULL, NULL),
(135, 54, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225657-730', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:56:57', NULL, NULL),
(136, 54, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225704-832', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:57:04', NULL, NULL),
(137, 54, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225712-843', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:57:12', NULL, NULL),
(138, 56, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225733-794', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:57:33', NULL, NULL),
(139, 56, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225742-625', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:57:42', NULL, NULL),
(140, 56, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225749-372', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:57:49', NULL, NULL),
(141, 56, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225800-748', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:58:00', NULL, NULL),
(142, 56, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225811-524', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:58:11', NULL, NULL),
(143, 57, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225837-951', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:58:37', NULL, NULL),
(144, 57, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225844-924', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:58:44', NULL, NULL),
(145, 57, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-225851-752', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:58:51', NULL, NULL),
(146, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225922-718', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 02:59:22', NULL, NULL),
(147, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225929-862', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 02:59:29', NULL, NULL),
(148, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225936-918', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 02:59:36', NULL, NULL),
(149, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225945-376', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 02:59:45', NULL, NULL),
(150, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-225952-103', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 02:59:52', NULL, NULL),
(151, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230001-508', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 03:00:01', NULL, NULL),
(152, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230008-388', NULL, 'Pago registrado desde Registro de cuotas', 'Mes julio 2026', NULL, 'aplicado', 1, '2026-05-04 03:00:08', NULL, NULL),
(153, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230018-835', NULL, 'Pago registrado desde Registro de cuotas', 'Mes agosto 2026', NULL, 'aplicado', 1, '2026-05-04 03:00:18', NULL, NULL),
(154, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230026-562', NULL, 'Pago registrado desde Registro de cuotas', 'Mes septiembre 2026', NULL, 'aplicado', 1, '2026-05-04 03:00:26', NULL, NULL),
(155, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230033-396', NULL, 'Pago registrado desde Registro de cuotas', 'Mes octubre 2026', NULL, 'aplicado', 1, '2026-05-04 03:00:33', NULL, NULL),
(156, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230040-439', NULL, 'Pago registrado desde Registro de cuotas', 'Mes noviembre 2026', NULL, 'aplicado', 1, '2026-05-04 03:00:40', NULL, NULL),
(157, 59, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230049-695', NULL, 'Pago registrado desde Registro de cuotas', 'Mes diciembre 2026', NULL, 'aplicado', 1, '2026-05-04 03:00:49', NULL, NULL),
(158, 60, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-230120-452', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 03:01:20', NULL, NULL),
(159, 60, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-230126-922', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 03:01:26', NULL, NULL),
(160, 60, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-230132-228', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 03:01:32', NULL, NULL),
(161, 60, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-230140-550', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 03:01:40', NULL, NULL),
(162, 60, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-230153-874', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 03:01:53', NULL, NULL),
(163, 60, '2026-05-03', 3500.00, 1, NULL, 'CUO-20260503-230202-237', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 03:02:02', NULL, NULL),
(164, 61, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230252-782', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 03:02:52', NULL, NULL),
(165, 61, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230301-320', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 03:03:01', NULL, NULL),
(166, 61, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230310-836', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 03:03:10', NULL, NULL),
(167, 61, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230318-150', NULL, 'Pago registrado desde Registro de cuotas', 'Mes julio 2026', NULL, 'aplicado', 1, '2026-05-04 03:03:18', NULL, NULL),
(168, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230652-626', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 03:06:52', NULL, NULL),
(169, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230658-897', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 03:06:58', NULL, NULL),
(170, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230706-393', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 03:07:06', NULL, NULL),
(171, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230715-963', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 03:07:15', NULL, NULL),
(172, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230720-425', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 03:07:20', NULL, NULL),
(173, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230727-130', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 03:07:27', NULL, NULL),
(174, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230753-110', NULL, 'Pago registrado desde Registro de cuotas', 'Mes julio 2026', NULL, 'aplicado', 1, '2026-05-04 03:07:53', NULL, NULL),
(175, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230800-249', NULL, 'Pago registrado desde Registro de cuotas', 'Mes agosto 2026', NULL, 'aplicado', 1, '2026-05-04 03:08:00', NULL, NULL),
(176, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230807-900', NULL, 'Pago registrado desde Registro de cuotas', 'Mes septiembre 2026', NULL, 'aplicado', 1, '2026-05-04 03:08:07', NULL, NULL),
(177, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230817-655', NULL, 'Pago registrado desde Registro de cuotas', 'Mes octubre 2026', NULL, 'aplicado', 1, '2026-05-04 03:08:17', NULL, NULL),
(178, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230825-614', NULL, 'Pago registrado desde Registro de cuotas', 'Mes noviembre 2026', NULL, 'aplicado', 1, '2026-05-04 03:08:25', NULL, NULL),
(179, 62, '2026-05-03', 4000.00, 1, NULL, 'CUO-20260503-230834-927', NULL, 'Pago registrado desde Registro de cuotas', 'Mes diciembre 2026', NULL, 'aplicado', 1, '2026-05-04 03:08:34', NULL, NULL),
(180, 63, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230902-856', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 03:09:02', NULL, NULL),
(181, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230939-356', NULL, 'Pago registrado desde Registro de cuotas', 'Mes enero 2026', NULL, 'aplicado', 1, '2026-05-04 03:09:39', NULL, NULL),
(182, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230945-410', NULL, 'Pago registrado desde Registro de cuotas', 'Mes febrero 2026', NULL, 'aplicado', 1, '2026-05-04 03:09:45', NULL, NULL),
(183, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230953-379', NULL, 'Pago registrado desde Registro de cuotas', 'Mes marzo 2026', NULL, 'aplicado', 1, '2026-05-04 03:09:53', NULL, NULL),
(184, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-230959-501', NULL, 'Pago registrado desde Registro de cuotas', 'Mes abril 2026', NULL, 'aplicado', 1, '2026-05-04 03:09:59', NULL, NULL),
(185, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-231105-343', NULL, 'Pago registrado desde Registro de cuotas', 'Mes mayo 2026', NULL, 'aplicado', 1, '2026-05-04 03:11:05', NULL, NULL),
(186, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-231113-372', NULL, 'Pago registrado desde Registro de cuotas', 'Mes junio 2026', NULL, 'aplicado', 1, '2026-05-04 03:11:13', NULL, NULL),
(187, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-231120-429', NULL, 'Pago registrado desde Registro de cuotas', 'Mes julio 2026', NULL, 'aplicado', 1, '2026-05-04 03:11:20', NULL, NULL),
(188, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-231128-832', NULL, 'Pago registrado desde Registro de cuotas', 'Mes agosto 2026', NULL, 'aplicado', 1, '2026-05-04 03:11:28', NULL, NULL),
(189, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-231419-950', NULL, 'Pago registrado desde Registro de cuotas', 'Mes septiembre 2026', NULL, 'aplicado', 1, '2026-05-04 03:14:19', NULL, NULL),
(190, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-231427-423', NULL, 'Pago registrado desde Registro de cuotas', 'Mes octubre 2026', NULL, 'aplicado', 1, '2026-05-04 03:14:27', NULL, NULL),
(191, 64, '2026-05-03', 5000.00, 1, NULL, 'CUO-20260503-231435-346', NULL, 'Pago registrado desde Registro de cuotas', 'Mes noviembre 2026', NULL, 'aplicado', 1, '2026-05-04 03:14:35', NULL, NULL),
(192, 64, '2026-05-03', 20000.00, 1, NULL, 'CUO-20260503-231445-221', NULL, 'Pago registrado desde Registro de cuotas', 'Mes diciembre 2026', NULL, 'aplicado', 1, '2026-05-04 03:14:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_detalle`
--

CREATE TABLE `pago_detalle` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pago_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cuota_id` bigint(20) UNSIGNED DEFAULT NULL,
  `monto_aplicado` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pago_detalle`
--

INSERT INTO `pago_detalle` (`id`, `pago_id`, `cuota_id`, `monto_aplicado`, `created_at`, `updated_at`) VALUES
(12, 13, 15, 5000.00, '2026-05-04 00:43:09', NULL),
(13, 14, 16, 5000.00, '2026-05-04 00:44:08', NULL),
(14, 15, 17, 4000.00, '2026-05-04 01:46:40', NULL),
(15, 16, 18, 4000.00, '2026-05-04 01:47:18', NULL),
(16, 17, 19, 4000.00, '2026-05-04 01:47:28', NULL),
(17, 18, 20, 4000.00, '2026-05-04 01:51:16', NULL),
(18, 19, 21, 4000.00, '2026-05-04 01:51:25', NULL),
(19, 20, 22, 4000.00, '2026-05-04 01:51:35', NULL),
(20, 21, 23, 4000.00, '2026-05-04 01:51:55', NULL),
(21, 22, 24, 4000.00, '2026-05-04 01:52:37', NULL),
(22, 23, 25, 4000.00, '2026-05-04 01:52:48', NULL),
(23, 24, 26, 3000.00, '2026-05-04 01:54:04', NULL),
(24, 25, 27, 3000.00, '2026-05-04 01:54:12', NULL),
(25, 26, 28, 3500.00, '2026-05-04 01:54:18', NULL),
(26, 27, 29, 3000.00, '2026-05-04 01:54:25', NULL),
(27, 28, 30, 3000.00, '2026-05-04 01:54:58', NULL),
(28, 29, 31, 3000.00, '2026-05-04 01:55:06', NULL),
(29, 30, 32, 3000.00, '2026-05-04 01:55:13', NULL),
(30, 31, 33, 3000.00, '2026-05-04 01:55:19', NULL),
(31, 32, 34, 3000.00, '2026-05-04 01:55:26', NULL),
(32, 33, 35, 8000.00, '2026-05-04 01:55:33', NULL),
(33, 34, 36, 5000.00, '2026-05-04 01:57:32', NULL),
(34, 35, 37, 5000.00, '2026-05-04 01:57:38', NULL),
(35, 36, 38, 5000.00, '2026-05-04 01:57:46', NULL),
(36, 37, 39, 5000.00, '2026-05-04 01:57:52', NULL),
(37, 38, 40, 5000.00, '2026-05-04 01:58:14', NULL),
(38, 39, 41, 5000.00, '2026-05-04 01:58:22', NULL),
(39, 40, 42, 5000.00, '2026-05-04 01:58:27', NULL),
(40, 41, 43, 5000.00, '2026-05-04 01:58:33', NULL),
(41, 42, 44, 5000.00, '2026-05-04 01:58:40', NULL),
(42, 43, 45, 5000.00, '2026-05-04 01:58:47', NULL),
(43, 44, 46, 4000.00, '2026-05-04 01:59:23', NULL),
(44, 45, 47, 4000.00, '2026-05-04 01:59:30', NULL),
(45, 46, 48, 4000.00, '2026-05-04 01:59:35', NULL),
(46, 47, 49, 4000.00, '2026-05-04 01:59:41', NULL),
(47, 48, 50, 4000.00, '2026-05-04 01:59:47', NULL),
(48, 49, 51, 4000.00, '2026-05-04 01:59:57', NULL),
(49, 50, 52, 4000.00, '2026-05-04 02:00:06', NULL),
(50, 51, 53, 4000.00, '2026-05-04 02:00:23', NULL),
(51, 52, 54, 4000.00, '2026-05-04 02:00:28', NULL),
(52, 53, 55, 4000.00, '2026-05-04 02:00:39', NULL),
(53, 54, 56, 4000.00, '2026-05-04 02:00:46', NULL),
(54, 55, 57, 3000.00, '2026-05-04 02:01:02', NULL),
(55, 56, 58, 3000.00, '2026-05-04 02:01:08', NULL),
(56, 57, 59, 3000.00, '2026-05-04 02:01:14', NULL),
(57, 58, 60, 3500.00, '2026-05-04 02:01:49', NULL),
(58, 59, 61, 3500.00, '2026-05-04 02:01:55', NULL),
(59, 60, 62, 3500.00, '2026-05-04 02:02:01', NULL),
(60, 61, 63, 3500.00, '2026-05-04 02:02:07', NULL),
(61, 62, 64, 3500.00, '2026-05-04 02:02:20', NULL),
(62, 63, 65, 3500.00, '2026-05-04 02:02:26', NULL),
(63, 64, 66, 4000.00, '2026-05-04 02:03:36', NULL),
(64, 65, 67, 4000.00, '2026-05-04 02:03:42', NULL),
(65, 66, 68, 4000.00, '2026-05-04 02:03:48', NULL),
(66, 67, 69, 4000.00, '2026-05-04 02:03:53', NULL),
(67, 68, 70, 4000.00, '2026-05-04 02:04:03', NULL),
(68, 69, 71, 4000.00, '2026-05-04 02:04:09', NULL),
(69, 70, 72, 4000.00, '2026-05-04 02:04:37', NULL),
(70, 71, 73, 4000.00, '2026-05-04 02:04:47', NULL),
(71, 72, 74, 4000.00, '2026-05-04 02:04:53', NULL),
(72, 73, 75, 4000.00, '2026-05-04 02:04:58', NULL),
(73, 74, 76, 4000.00, '2026-05-04 02:05:04', NULL),
(74, 75, 77, 4000.00, '2026-05-04 02:05:11', NULL),
(75, 76, 78, 4000.00, '2026-05-04 02:05:21', NULL),
(76, 77, 79, 5000.00, '2026-05-04 02:06:06', NULL),
(77, 78, 80, 5000.00, '2026-05-04 02:06:11', NULL),
(78, 79, 81, 5000.00, '2026-05-04 02:06:38', NULL),
(79, 80, 82, 5000.00, '2026-05-04 02:06:43', NULL),
(80, 81, 83, 5000.00, '2026-05-04 02:06:49', NULL),
(81, 82, 84, 5000.00, '2026-05-04 02:07:19', NULL),
(82, 83, 85, 5000.00, '2026-05-04 02:07:25', NULL),
(83, 84, 86, 5000.00, '2026-05-04 02:07:30', NULL),
(84, 85, 87, 5000.00, '2026-05-04 02:07:35', NULL),
(85, 86, 88, 5000.00, '2026-05-04 02:07:42', NULL),
(86, 87, 89, 5000.00, '2026-05-04 02:07:47', NULL),
(87, 88, 90, 5000.00, '2026-05-04 02:07:52', NULL),
(88, 89, 91, 5000.00, '2026-05-04 02:07:57', NULL),
(89, 90, 92, 5000.00, '2026-05-04 02:08:03', NULL),
(90, 91, 93, 5000.00, '2026-05-04 02:08:07', NULL),
(91, 92, 94, 5000.00, '2026-05-04 02:08:12', NULL),
(92, 93, 95, 5000.00, '2026-05-04 02:08:17', NULL),
(93, 94, 96, 5000.00, '2026-05-04 02:08:32', NULL),
(94, 95, 97, 5000.00, '2026-05-04 02:08:37', NULL),
(95, 96, 98, 5000.00, '2026-05-04 02:08:48', NULL),
(96, 97, 99, 5000.00, '2026-05-04 02:09:01', NULL),
(97, 98, 100, 5000.00, '2026-05-04 02:10:28', NULL),
(98, 99, 101, 5000.00, '2026-05-04 02:10:33', NULL),
(99, 100, 102, 5000.00, '2026-05-04 02:10:39', NULL),
(100, 101, 103, 5000.00, '2026-05-04 02:10:44', NULL),
(101, 102, 104, 5000.00, '2026-05-04 02:10:50', NULL),
(102, 103, 105, 5000.00, '2026-05-04 02:10:55', NULL),
(103, 104, 106, 5000.00, '2026-05-04 02:11:49', NULL),
(104, 105, 107, 5000.00, '2026-05-04 02:11:54', NULL),
(105, 106, 108, 5000.00, '2026-05-04 02:12:00', NULL),
(106, 107, 109, 5000.00, '2026-05-04 02:12:05', NULL),
(107, 108, 110, 5000.00, '2026-05-04 02:12:10', NULL),
(108, 109, 111, 5000.00, '2026-05-04 02:12:17', NULL),
(109, 110, 112, 5000.00, '2026-05-04 02:12:25', NULL),
(110, 111, 113, 5000.00, '2026-05-04 02:12:29', NULL),
(111, 112, 114, 5000.00, '2026-05-04 02:12:42', NULL),
(112, 113, 115, 5000.00, '2026-05-04 02:13:12', NULL),
(113, 114, 116, 5000.00, '2026-05-04 02:13:20', NULL),
(114, 115, 117, 30000.00, '2026-05-04 02:13:33', NULL),
(115, 116, 118, 4000.00, '2026-05-04 02:14:56', NULL),
(116, 117, 119, 4000.00, '2026-05-04 02:15:02', NULL),
(117, 118, 120, 4000.00, '2026-05-04 02:15:09', NULL),
(118, 119, 121, 4000.00, '2026-05-04 02:15:16', NULL),
(119, 120, 122, 4000.00, '2026-05-04 02:29:24', NULL),
(120, 121, 123, 4000.00, '2026-05-04 02:29:29', NULL),
(121, 122, 124, 4000.00, '2026-05-04 02:29:35', NULL),
(122, 123, 125, 4000.00, '2026-05-04 02:29:42', NULL),
(123, 124, 126, 30000.00, '2026-05-04 02:45:46', NULL),
(124, 125, 128, 5000.00, '2026-05-04 02:48:52', NULL),
(125, 126, 129, 4000.00, '2026-05-04 02:49:14', NULL),
(126, 127, 130, 4000.00, '2026-05-04 02:49:22', NULL),
(127, 128, 131, 4000.00, '2026-05-04 02:49:35', NULL),
(128, 129, 132, 4000.00, '2026-05-04 02:50:10', NULL),
(129, 130, 133, 4000.00, '2026-05-04 02:50:18', NULL),
(130, 131, 134, 4000.00, '2026-05-04 02:50:27', NULL),
(131, 132, 135, 4000.00, '2026-05-04 02:50:37', NULL),
(132, 133, 136, 4000.00, '2026-05-04 02:50:46', NULL),
(133, 134, 137, 4000.00, '2026-05-04 02:51:34', NULL),
(134, 135, 138, 4000.00, '2026-05-04 02:56:57', NULL),
(135, 136, 139, 4000.00, '2026-05-04 02:57:04', NULL),
(136, 137, 140, 4000.00, '2026-05-04 02:57:12', NULL),
(137, 138, 141, 5000.00, '2026-05-04 02:57:33', NULL),
(138, 139, 142, 5000.00, '2026-05-04 02:57:42', NULL),
(139, 140, 143, 5000.00, '2026-05-04 02:57:49', NULL),
(140, 141, 144, 5000.00, '2026-05-04 02:58:00', NULL),
(141, 142, 145, 5000.00, '2026-05-04 02:58:11', NULL),
(142, 143, 146, 4000.00, '2026-05-04 02:58:37', NULL),
(143, 144, 147, 4000.00, '2026-05-04 02:58:44', NULL),
(144, 145, 148, 4000.00, '2026-05-04 02:58:51', NULL),
(145, 146, 149, 5000.00, '2026-05-04 02:59:22', NULL),
(146, 147, 150, 5000.00, '2026-05-04 02:59:29', NULL),
(147, 148, 151, 5000.00, '2026-05-04 02:59:36', NULL),
(148, 149, 152, 5000.00, '2026-05-04 02:59:45', NULL),
(149, 150, 153, 5000.00, '2026-05-04 02:59:52', NULL),
(150, 151, 154, 5000.00, '2026-05-04 03:00:01', NULL),
(151, 152, 155, 5000.00, '2026-05-04 03:00:08', NULL),
(152, 153, 156, 5000.00, '2026-05-04 03:00:18', NULL),
(153, 154, 157, 5000.00, '2026-05-04 03:00:26', NULL),
(154, 155, 158, 5000.00, '2026-05-04 03:00:33', NULL),
(155, 156, 159, 5000.00, '2026-05-04 03:00:40', NULL),
(156, 157, 160, 5000.00, '2026-05-04 03:00:49', NULL),
(157, 158, 161, 3500.00, '2026-05-04 03:01:20', NULL),
(158, 159, 162, 3500.00, '2026-05-04 03:01:26', NULL),
(159, 160, 163, 3500.00, '2026-05-04 03:01:32', NULL),
(160, 161, 164, 3500.00, '2026-05-04 03:01:40', NULL),
(161, 162, 165, 3500.00, '2026-05-04 03:01:53', NULL),
(162, 163, 166, 3500.00, '2026-05-04 03:02:02', NULL),
(163, 164, 167, 5000.00, '2026-05-04 03:02:52', NULL),
(164, 165, 168, 5000.00, '2026-05-04 03:03:01', NULL),
(165, 166, 169, 5000.00, '2026-05-04 03:03:10', NULL),
(166, 167, 170, 5000.00, '2026-05-04 03:03:18', NULL),
(167, 168, 171, 4000.00, '2026-05-04 03:06:52', NULL),
(168, 169, 172, 4000.00, '2026-05-04 03:06:58', NULL),
(169, 170, 173, 4000.00, '2026-05-04 03:07:06', NULL),
(170, 171, 174, 4000.00, '2026-05-04 03:07:15', NULL),
(171, 172, 175, 4000.00, '2026-05-04 03:07:20', NULL),
(172, 173, 176, 4000.00, '2026-05-04 03:07:27', NULL),
(173, 174, 177, 4000.00, '2026-05-04 03:07:53', NULL),
(174, 175, 178, 4000.00, '2026-05-04 03:08:00', NULL),
(175, 176, 179, 4000.00, '2026-05-04 03:08:07', NULL),
(176, 177, 180, 4000.00, '2026-05-04 03:08:17', NULL),
(177, 178, 181, 4000.00, '2026-05-04 03:08:25', NULL),
(178, 179, 182, 4000.00, '2026-05-04 03:08:34', NULL),
(179, 180, 183, 5000.00, '2026-05-04 03:09:02', NULL),
(180, 181, 184, 5000.00, '2026-05-04 03:09:39', NULL),
(181, 182, 185, 5000.00, '2026-05-04 03:09:45', NULL),
(182, 183, 186, 5000.00, '2026-05-04 03:09:53', NULL),
(183, 184, 187, 5000.00, '2026-05-04 03:09:59', NULL),
(184, 185, 188, 5000.00, '2026-05-04 03:11:05', NULL),
(185, 186, 189, 5000.00, '2026-05-04 03:11:13', NULL),
(186, 187, 190, 5000.00, '2026-05-04 03:11:20', NULL),
(187, 188, 191, 5000.00, '2026-05-04 03:11:28', NULL),
(188, 189, 192, 5000.00, '2026-05-04 03:14:19', NULL),
(189, 190, 193, 5000.00, '2026-05-04 03:14:27', NULL),
(190, 191, 194, 5000.00, '2026-05-04 03:14:35', NULL),
(191, 192, 195, 20000.00, '2026-05-04 03:14:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos`
--

CREATE TABLE `periodos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anio` smallint(6) DEFAULT NULL,
  `mes` tinyint(4) DEFAULT NULL,
  `nombre_periodo` varchar(100) NOT NULL,
  `tipo_periodo` enum('mensual','trimestral','semestral','anual') NOT NULL,
  `monto_a_pagar` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `cerrado` tinyint(1) DEFAULT 0,
  `observacion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `periodos`
--

INSERT INTO `periodos` (`id`, `anio`, `mes`, `nombre_periodo`, `tipo_periodo`, `monto_a_pagar`, `fecha_inicio`, `fecha_fin`, `fecha_vencimiento`, `cerrado`, `observacion`, `created_at`, `updated_at`) VALUES
(3, 2026, 5, 'Plan mensual $6.000', 'mensual', 6000.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:39:19', NULL),
(4, 2026, 5, 'Plan mensual $3.000', 'mensual', 3000.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:39:36', NULL),
(5, 2026, 5, 'Plan mensual $3500', 'mensual', 3500.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:40:22', NULL),
(6, 2026, 5, 'Plan mensual $4000', 'mensual', 4000.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:40:35', NULL),
(7, 2026, 5, 'Plan mensual $5000', 'mensual', 5000.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:40:53', NULL),
(8, 2026, 5, 'Plan mensual $8000', 'mensual', 8000.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:41:02', NULL),
(9, 2026, 5, 'Plan mensual $10000', 'mensual', 10000.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:41:23', NULL),
(10, 2026, 5, 'Plan mensual $20000', 'mensual', 20000.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:42:19', NULL),
(11, 2026, 5, 'Plan mensual $1500', 'mensual', 1500.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:42:27', NULL),
(12, 2026, 5, 'Plan mensual $30000', 'mensual', 30000.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:42:35', NULL),
(13, 2026, 5, 'Plan mensual $7000', 'mensual', 7000.00, NULL, NULL, NULL, 0, NULL, '2026-05-03 23:43:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rendiciones`
--

CREATE TABLE `rendiciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_rendicion` varchar(60) DEFAULT NULL,
  `fecha_desde` date DEFAULT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `monto_total` decimal(12,2) DEFAULT NULL,
  `estado` enum('abierta','cerrada') DEFAULT 'abierta',
  `observacion` varchar(255) DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rendicion_detalle`
--

CREATE TABLE `rendicion_detalle` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rendicion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `egreso_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(60) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'Acceso total', 1, '2026-04-18 04:43:06', NULL),
(2, 'Tesorero', 'Operación financiera', 1, '2026-04-18 04:43:06', NULL),
(3, 'Consulta', 'Solo lectura', 1, '2026-04-18 04:43:06', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socios`
--

CREATE TABLE `socios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_socio` varchar(30) DEFAULT NULL,
  `rut` varchar(30) DEFAULT NULL,
  `nombres` varchar(120) DEFAULT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `nombre_completo` varchar(240) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `comuna` varchar(120) DEFAULT NULL,
  `ciudad` varchar(120) DEFAULT NULL,
  `tipo_socio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado_socio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `socios`
--

INSERT INTO `socios` (`id`, `numero_socio`, `rut`, `nombres`, `apellidos`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `direccion`, `comuna`, `ciudad`, `tipo_socio_id`, `estado_socio_id`, `fecha_ingreso`, `observaciones`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(18, '000001', NULL, 'Irma', 'Diaz De Alcayaga', 'Irma Diaz De Alcayaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:04:38', NULL, NULL),
(19, '000002', NULL, 'Sisy', 'Alcayaga Diaz', 'Sisy Alcayaga Diaz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:04:49', NULL, NULL),
(20, '000003', NULL, 'Pierina', 'Gomez Retamal', 'Pierina Gomez Retamal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:05:05', NULL, NULL),
(21, '000004', NULL, 'Barbara', 'Canales', 'Barbara Canales', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:05:15', NULL, NULL),
(22, '000005', NULL, 'Jose Mauricio', 'Acuña Perez', 'Jose Mauricio Acuña Perez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:05:29', NULL, NULL),
(23, '000006', NULL, 'Jose Carmen', 'Acuña Nuñez', 'Jose Carmen Acuña Nuñez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:05:49', NULL, NULL),
(24, '000007', NULL, 'Luis', 'Ahumada Martinez', 'Luis Ahumada Martinez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:06:00', NULL, NULL),
(25, '000008', NULL, 'Jacinto', 'Ahumada Parada', 'Jacinto Ahumada Parada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:06:16', NULL, NULL),
(26, '000009', NULL, 'Gabriel', 'Allendes Villarroel', 'Gabriel Allendes Villarroel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:06:28', NULL, NULL),
(27, '000010', NULL, 'Patricio', 'Osvaldo Alvarado', 'Patricio Osvaldo Alvarado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:06:40', NULL, NULL),
(28, '000011', NULL, 'Jose', 'Alegria Hernandez', 'Jose Alegria Hernandez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:06:49', NULL, NULL),
(29, '000012', NULL, 'Hernan', 'Aquiles Novoa', 'Hernan Aquiles Novoa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:07:01', NULL, NULL),
(30, '000013', NULL, 'Welingthone', 'Araya', 'Welingthone Araya', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:07:29', NULL, NULL),
(31, '000014', NULL, 'Luis', 'Albornoz Castillo', 'Luis Albornoz Castillo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:07:40', NULL, NULL),
(32, '000015', NULL, 'Luis', 'Astete Insunza', 'Luis Astete Insunza', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:07:51', NULL, NULL),
(33, '000016', NULL, 'Jose Arnold', 'Ayamante Alvarado', 'Jose Arnold Ayamante Alvarado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:08:07', NULL, NULL),
(34, '000017', NULL, 'Luis Armando', 'Bañados Carcamo', 'Luis Armando Bañados Carcamo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:08:18', NULL, NULL),
(35, '000018', NULL, 'Juan Bautista', 'Barra Alarcon', 'Juan Bautista Barra Alarcon', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:08:29', NULL, NULL),
(36, '000019', NULL, 'Pablo Guillermo', 'Bascour Nuñez', 'Pablo Guillermo Bascour Nuñez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:08:40', NULL, NULL),
(37, '000020', NULL, 'Froilan', 'Basoalto', 'Froilan Basoalto', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:08:51', NULL, NULL),
(38, '000021', NULL, 'Mario Patricio', 'Brante Ramirez', 'Mario Patricio Brante Ramirez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:09:03', NULL, NULL),
(39, '000022', NULL, 'Flavio', 'Becerra', 'Flavio Becerra', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:09:15', NULL, NULL),
(40, '000023', NULL, 'Humberto', 'Rene Belmar', 'Humberto Rene Belmar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:09:26', NULL, NULL),
(41, '000024', NULL, 'Carlos Raul', 'Belmar Fuentes', 'Carlos Raul Belmar Fuentes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:09:45', NULL, NULL),
(42, '000025', NULL, 'Ernesto', 'Calquin Lopez', 'Ernesto Calquin Lopez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:10:00', NULL, NULL),
(43, '000026', NULL, 'Jose Raul', 'Canales Millanao', 'Jose Raul Canales Millanao', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:10:12', NULL, NULL),
(44, '000027', NULL, 'Reynaldo', 'Carvajal Caceres', 'Reynaldo Carvajal Caceres', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:10:24', NULL, NULL),
(45, '000028', NULL, 'Ricardo Ivan', 'Castro Flores', 'Ricardo Ivan Castro Flores', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:10:35', NULL, NULL),
(46, '000029', NULL, 'Luis', 'Castro Martinez', 'Luis Castro Martinez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:10:47', NULL, NULL),
(47, '000030', NULL, 'Sergio', 'Catriao Lagos', 'Sergio Catriao Lagos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:10:58', NULL, NULL),
(48, '000031', NULL, 'Mario', 'Cerda', 'Mario Cerda', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:16:42', '2026-05-03 23:16:51', NULL),
(49, '000032', NULL, 'Luis', 'Cervando Lizama', 'Luis Cervando Lizama', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:17:20', NULL, NULL),
(50, '000033', NULL, 'Ruben', 'Ceron Hermosilla', 'Ruben Ceron Hermosilla', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:17:30', NULL, NULL),
(51, '000034', NULL, 'Manuel Ramon', 'Cofre Leiva', 'Manuel Ramon Cofre Leiva', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:17:42', NULL, NULL),
(52, '000035', NULL, 'Raul Ernesto', 'Collantes Bravo', 'Raul Ernesto Collantes Bravo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:17:53', NULL, NULL),
(53, '000036', NULL, 'Hector', 'Contreras Baez', 'Hector Contreras Baez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:18:02', NULL, NULL),
(54, '000037', NULL, 'Raul Enrique', 'Contreras Nuñez', 'Raul Enrique Contreras Nuñez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:18:14', NULL, NULL),
(55, '000038', NULL, 'Heraldo', 'Correa Trigo', 'Heraldo Correa Trigo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:18:23', NULL, NULL),
(56, '000039', NULL, 'Jorge', 'Devia', 'Jorge Devia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:18:32', NULL, NULL),
(57, '000040', NULL, 'Jorge Washington', 'Diaz Herrera', 'Jorge Washington Diaz Herrera', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:18:47', NULL, NULL),
(58, '000041', NULL, 'Jose Daniel', 'Diaz Vega', 'Jose Daniel Diaz Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:18:58', NULL, NULL),
(59, '000042', NULL, 'Eulogio', 'Diaz Hurtado', 'Eulogio Diaz Hurtado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:19:10', NULL, NULL),
(60, '000043', NULL, 'Lupercio', 'Espinoza Mujica', 'Lupercio Espinoza Mujica', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:19:22', NULL, NULL),
(61, '000044', NULL, 'Gloria Ines', 'Eriza Due', 'Gloria Ines Eriza Due', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:19:34', NULL, NULL),
(62, '000045', NULL, 'Luis', 'Fernandez Pinto', 'Luis Fernandez Pinto', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:19:48', NULL, NULL),
(63, '000046', NULL, 'Sylvia', 'Fuentes Jara', 'Sylvia Fuentes Jara', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:19:59', NULL, NULL),
(64, '000047', NULL, 'Luis', 'Flores Veli', 'Luis Flores Veli', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:20:13', NULL, NULL),
(65, '000048', NULL, 'Juan Manuel', 'Gallardo Moya', 'Juan Manuel Gallardo Moya', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:20:22', NULL, NULL),
(66, '000049', NULL, 'Justo Wlad', 'Garrido Caceres', 'Justo Wlad Garrido Caceres', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:20:42', NULL, NULL),
(67, '000050', NULL, 'Pedro', 'Godoy Rebolledo', 'Pedro Godoy Rebolledo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:20:54', NULL, NULL),
(68, '000051', NULL, 'Juan Carlos', 'Gonzalez Castro', 'Juan Carlos Gonzalez Castro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:21:04', NULL, NULL),
(69, '000052', NULL, 'Roberto', 'Gonzalez Marinao', 'Roberto Gonzalez Marinao', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:21:16', NULL, NULL),
(70, '000053', NULL, 'Carlos', 'Grandon Portilla', 'Carlos Grandon Portilla', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:21:31', NULL, NULL),
(71, '000054', NULL, 'Raul', 'Gutierrez Nilo', 'Raul Gutierrez Nilo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:22:02', NULL, NULL),
(72, '000055', NULL, 'Ramon', 'Gutierrez Boilet', 'Ramon Gutierrez Boilet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:22:16', NULL, NULL),
(73, '000056', NULL, 'Angel', 'Holstin Zelaya', 'Angel Holstin Zelaya', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:22:29', NULL, NULL),
(74, '000057', NULL, 'Julio Patricio', 'Lagos Gonzalez', 'Julio Patricio Lagos Gonzalez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:22:41', NULL, NULL),
(75, '000058', NULL, 'Claudio', 'Larreta Fonseca', 'Claudio Larreta Fonseca', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:22:53', NULL, NULL),
(76, '000059', NULL, 'Maria Sonia', 'Lefno', 'Maria Sonia Lefno', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:23:06', NULL, NULL),
(77, '000060', NULL, 'Roberto', 'Lizama Valenzuela', 'Roberto Lizama Valenzuela', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:23:18', NULL, NULL),
(78, '000061', NULL, 'Nibaldo', 'Mackenna Velarde', 'Nibaldo Mackenna Velarde', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:23:30', NULL, NULL),
(79, '000062', NULL, 'Juan', 'Matus Jara', 'Juan Matus Jara', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:23:42', NULL, NULL),
(80, '000063', NULL, 'Tristan', 'Mesias Meza', 'Tristan Mesias Meza', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:23:57', NULL, NULL),
(81, '000064', NULL, 'Oscar', 'Mendez Franco', 'Oscar Mendez Franco', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:24:07', NULL, NULL),
(82, '000065', NULL, 'Luis', 'Mettig', 'Luis Mettig', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:24:18', NULL, NULL),
(83, '000066', NULL, 'Samuel Fermin', 'Miranda Muñoz', 'Samuel Fermin Miranda Muñoz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:24:30', NULL, NULL),
(84, '000067', NULL, 'Jose', 'Moya Rodriguez', 'Jose Moya Rodriguez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:24:40', NULL, NULL),
(85, '000068', NULL, 'Luis', 'Morales Orellana', 'Luis Morales Orellana', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:24:51', NULL, NULL),
(86, '000069', NULL, 'Pedro Ismael', 'Muñoz Castillo', 'Pedro Ismael Muñoz Castillo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:25:02', NULL, NULL),
(87, '000070', NULL, 'Jorge Luis', 'Muñoz Lizama', 'Jorge Luis Muñoz Lizama', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:25:13', NULL, NULL),
(88, '000071', NULL, 'Victor Enrique', 'Muñoz Montiel', 'Victor Enrique Muñoz Montiel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:25:25', NULL, NULL),
(89, '000072', NULL, 'Jaime', 'Muñoz Neira', 'Jaime Muñoz Neira', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:25:38', NULL, NULL),
(90, '000073', NULL, 'Jimmy', 'Muñoz Owen', 'Jimmy Muñoz Owen', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:26:12', NULL, NULL),
(91, '000074', NULL, 'Oscar', 'Navas Ulloa', 'Oscar Navas Ulloa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:26:24', NULL, NULL),
(92, '000075', NULL, 'Victor', 'Negron Gallardo', 'Victor Negron Gallardo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:26:34', NULL, NULL),
(93, '000076', NULL, 'Emma', 'Norambuena Villagran', 'Emma Norambuena Villagran', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:26:45', NULL, NULL),
(94, '000077', NULL, 'Hugo', 'Nuñez Seguel', 'Hugo Nuñez Seguel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:27:17', NULL, NULL),
(95, '000078', NULL, 'Victor', 'Ojeda Diaz', 'Victor Ojeda Diaz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:27:27', NULL, NULL),
(96, '000079', NULL, 'Hector Manuel', 'Orellana Salgado', 'Hector Manuel Orellana Salgado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:27:49', NULL, NULL),
(97, '000080', NULL, 'Luis Alberto', 'Ortiz Rivas', 'Luis Alberto Ortiz Rivas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:28:02', NULL, NULL),
(98, '000081', NULL, 'David', 'Osorio Valdebenito', 'David Osorio Valdebenito', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:28:18', NULL, NULL),
(99, '000082', NULL, 'Abraham Roberto', 'Otarola Jerez', 'Abraham Roberto Otarola Jerez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:28:29', NULL, NULL),
(100, '000083', NULL, 'Luis Claudio', 'Oyarzun Ojeda', 'Luis Claudio Oyarzun Ojeda', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:28:44', NULL, NULL),
(101, '000084', NULL, 'Oscar', 'Paredes Villar', 'Oscar Paredes Villar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:28:54', NULL, NULL),
(102, '000085', NULL, 'Pilar', 'Perez', 'Pilar Perez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:29:06', NULL, NULL),
(103, '000086', NULL, 'Raul Daniel', 'Perez Hilliger', 'Raul Daniel Perez Hilliger', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:29:18', NULL, NULL),
(104, '000087', NULL, 'Victor', 'Pastene Varas', 'Victor Pastene Varas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:29:34', NULL, NULL),
(105, '000088', NULL, 'Luis Hernan', 'Perez Nuñez', 'Luis Hernan Perez Nuñez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:29:44', NULL, NULL),
(106, '000089', NULL, 'Sergio Eduardo', 'Pineda Ramirez', 'Sergio Eduardo Pineda Ramirez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:29:55', NULL, NULL),
(107, '000090', NULL, 'Patricio', 'Pino Willenbrick', 'Patricio Pino Willenbrick', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:30:05', NULL, NULL),
(108, '000091', NULL, 'Manuel', 'Porras Pizarro', 'Manuel Porras Pizarro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:30:16', NULL, NULL),
(109, '000092', NULL, 'Jaime', 'Quilodran Reyes', 'Jaime Quilodran Reyes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:30:29', NULL, NULL),
(110, '000093', NULL, 'Roque', 'Quijada Muñoz', 'Roque Quijada Muñoz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:30:42', NULL, NULL),
(111, '000094', NULL, 'Javier', 'Quiroz Rodriguez', 'Javier Quiroz Rodriguez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:30:51', NULL, NULL),
(112, '000095', NULL, 'Oscar', 'Rapiman', 'Oscar Rapiman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:31:05', NULL, NULL),
(113, '000096', NULL, 'Jose', 'Rebolledo Palma', 'Jose Rebolledo Palma', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:31:19', NULL, NULL),
(114, '000097', NULL, 'Juan Alberto', 'Reyes Muñoz', 'Juan Alberto Reyes Muñoz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:31:30', NULL, NULL),
(115, '000098', NULL, 'Luis', 'Rioseco Santander', 'Luis Rioseco Santander', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:31:41', NULL, NULL),
(116, '000099', NULL, 'Orlando', 'Rios Guerrero', 'Orlando Rios Guerrero', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:31:52', NULL, NULL),
(117, '000100', NULL, 'Nector', 'Riquelme Delgado', 'Nector Riquelme Delgado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:32:05', NULL, NULL),
(118, '000101', NULL, 'Pablo', 'Robles Benitez', 'Pablo Robles Benitez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:32:17', NULL, NULL),
(119, '000102', NULL, 'Rene Hipolito', 'Rodriguez Ojeda', 'Rene Hipolito Rodriguez Ojeda', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:32:30', NULL, NULL),
(120, '000103', NULL, 'Javier Jesus', 'Rodriguez Quiroz', 'Javier Jesus Rodriguez Quiroz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:32:44', NULL, NULL),
(121, '000104', NULL, 'Fernando Luis', 'Roman Gomez', 'Fernando Luis Roman Gomez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:32:54', NULL, NULL),
(122, '000105', NULL, 'Victor Manuel', 'Ross', 'Victor Manuel Ross', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:33:05', NULL, NULL),
(123, '000106', NULL, 'Alfonso', 'Roussel Santos', 'Alfonso Roussel Santos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:33:17', NULL, NULL),
(124, '000107', NULL, 'Enrique', 'Ruiz Hernandez', 'Enrique Ruiz Hernandez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:33:43', NULL, NULL),
(125, '000108', NULL, 'Pascuala', 'Saez Navarrete', 'Pascuala Saez Navarrete', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:33:52', NULL, NULL),
(126, '000109', NULL, 'Marcelo', 'Salazar Inostroza', 'Marcelo Salazar Inostroza', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:34:01', NULL, NULL),
(127, '000110', NULL, 'Juan Erminio', 'Sanhueza Pino', 'Juan Erminio Sanhueza Pino', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:34:12', NULL, NULL),
(128, '000111', NULL, 'Manuel', 'Santelices Elgueta', 'Manuel Santelices Elgueta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:34:24', NULL, NULL),
(129, '000112', NULL, 'Egoberto', 'Sepulveda Peña', 'Egoberto Sepulveda Peña', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:34:43', NULL, NULL),
(130, '000113', NULL, 'Manuel Alfredo', 'Tejada Tamayo', 'Manuel Alfredo Tejada Tamayo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:34:57', NULL, NULL),
(131, '000114', NULL, 'Rodolfo', 'Tilleria Tilleria', 'Rodolfo Tilleria Tilleria', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:35:11', NULL, NULL),
(132, '000115', NULL, 'Leonardo', 'Trigo Almuna', 'Leonardo Trigo Almuna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:35:22', NULL, NULL),
(133, '000116', NULL, 'Oscar', 'Toro Calderon', 'Oscar Toro Calderon', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:35:34', NULL, NULL),
(134, '000117', NULL, 'Manuel', 'Troncoso Madariaga', 'Manuel Troncoso Madariaga', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:35:51', NULL, NULL),
(135, '000118', NULL, 'Alejandro', 'Torres Sanhueza', 'Alejandro Torres Sanhueza', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:36:02', NULL, NULL),
(136, '000119', NULL, 'Danilo Ivan', 'Urbina Aravena', 'Danilo Ivan Urbina Aravena', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:36:14', NULL, NULL),
(137, '000120', NULL, 'Juan', 'Veliz Castro', 'Juan Veliz Castro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:36:25', NULL, NULL),
(138, '000121', NULL, 'Hector', 'Vera Fuentes', 'Hector Vera Fuentes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:36:37', NULL, NULL),
(139, '000122', NULL, 'Luis', 'Vergara Sepulveda', 'Luis Vergara Sepulveda', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:36:49', NULL, NULL),
(140, '000123', NULL, 'Luis Hernan', 'Videla Perez', 'Luis Hernan Videla Perez', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:36:58', NULL, NULL),
(141, '000124', NULL, 'Jorge', 'Villarroel', 'Jorge Villarroel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:37:08', NULL, NULL),
(142, '000125', NULL, 'Jose', 'Vilugron Vilugron', 'Jose Vilugron Vilugron', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:37:24', NULL, NULL),
(143, '000126', NULL, 'Leonarda', 'Zurita Puebla', 'Leonarda Zurita Puebla', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:37:34', NULL, NULL),
(144, '000127', NULL, 'Juan Carlos', 'Zarate Villarroel', 'Juan Carlos Zarate Villarroel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:37:44', NULL, NULL),
(145, '000128', NULL, 'Ricardo', 'Zuñiga Peralta', 'Ricardo Zuñiga Peralta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 23:37:56', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socio_planes`
--

CREATE TABLE `socio_planes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `socio_id` bigint(20) UNSIGNED NOT NULL,
  `periodo_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `socio_planes`
--

INSERT INTO `socio_planes` (`id`, `socio_id`, `periodo_id`, `created_at`) VALUES
(73, 18, 3, '2026-05-04 00:14:13'),
(74, 18, 4, '2026-05-04 00:14:13'),
(75, 18, 5, '2026-05-04 00:14:13'),
(76, 18, 6, '2026-05-04 00:14:13'),
(77, 18, 7, '2026-05-04 00:14:13'),
(78, 18, 8, '2026-05-04 00:14:13'),
(79, 18, 9, '2026-05-04 00:14:13'),
(80, 18, 10, '2026-05-04 00:14:13'),
(81, 18, 11, '2026-05-04 00:14:13'),
(82, 18, 12, '2026-05-04 00:14:13'),
(83, 18, 13, '2026-05-04 00:14:13'),
(84, 19, 3, '2026-05-04 00:14:13'),
(85, 19, 4, '2026-05-04 00:14:13'),
(86, 19, 5, '2026-05-04 00:14:13'),
(87, 19, 6, '2026-05-04 00:14:13'),
(88, 19, 7, '2026-05-04 00:14:13'),
(89, 19, 8, '2026-05-04 00:14:13'),
(90, 19, 9, '2026-05-04 00:14:13'),
(91, 19, 10, '2026-05-04 00:14:13'),
(92, 19, 11, '2026-05-04 00:14:13'),
(93, 19, 12, '2026-05-04 00:14:13'),
(94, 19, 13, '2026-05-04 00:14:13'),
(95, 20, 3, '2026-05-04 00:14:13'),
(96, 20, 4, '2026-05-04 00:14:13'),
(97, 20, 5, '2026-05-04 00:14:13'),
(98, 20, 6, '2026-05-04 00:14:13'),
(99, 20, 7, '2026-05-04 00:14:13'),
(100, 20, 8, '2026-05-04 00:14:13'),
(101, 20, 9, '2026-05-04 00:14:13'),
(102, 20, 10, '2026-05-04 00:14:13'),
(103, 20, 11, '2026-05-04 00:14:13'),
(104, 20, 12, '2026-05-04 00:14:13'),
(105, 20, 13, '2026-05-04 00:14:13'),
(106, 21, 3, '2026-05-04 00:14:13'),
(107, 21, 4, '2026-05-04 00:14:13'),
(108, 21, 5, '2026-05-04 00:14:13'),
(109, 21, 6, '2026-05-04 00:14:13'),
(110, 21, 7, '2026-05-04 00:14:13'),
(111, 21, 8, '2026-05-04 00:14:13'),
(112, 21, 9, '2026-05-04 00:14:13'),
(113, 21, 10, '2026-05-04 00:14:13'),
(114, 21, 11, '2026-05-04 00:14:13'),
(115, 21, 12, '2026-05-04 00:14:13'),
(116, 21, 13, '2026-05-04 00:14:13'),
(117, 22, 3, '2026-05-04 00:14:13'),
(118, 22, 4, '2026-05-04 00:14:13'),
(119, 22, 5, '2026-05-04 00:14:13'),
(120, 22, 6, '2026-05-04 00:14:13'),
(121, 22, 7, '2026-05-04 00:14:13'),
(122, 22, 8, '2026-05-04 00:14:13'),
(123, 22, 9, '2026-05-04 00:14:13'),
(124, 22, 10, '2026-05-04 00:14:13'),
(125, 22, 11, '2026-05-04 00:14:13'),
(126, 22, 12, '2026-05-04 00:14:13'),
(127, 22, 13, '2026-05-04 00:14:13'),
(128, 23, 3, '2026-05-04 00:14:13'),
(129, 23, 4, '2026-05-04 00:14:13'),
(130, 23, 5, '2026-05-04 00:14:13'),
(131, 23, 6, '2026-05-04 00:14:13'),
(132, 23, 7, '2026-05-04 00:14:13'),
(133, 23, 8, '2026-05-04 00:14:13'),
(134, 23, 9, '2026-05-04 00:14:13'),
(135, 23, 10, '2026-05-04 00:14:13'),
(136, 23, 11, '2026-05-04 00:14:13'),
(137, 23, 12, '2026-05-04 00:14:13'),
(138, 23, 13, '2026-05-04 00:14:13'),
(139, 24, 3, '2026-05-04 00:14:13'),
(140, 24, 4, '2026-05-04 00:14:13'),
(141, 24, 5, '2026-05-04 00:14:13'),
(142, 24, 6, '2026-05-04 00:14:13'),
(143, 24, 7, '2026-05-04 00:14:13'),
(144, 24, 8, '2026-05-04 00:14:13'),
(145, 24, 9, '2026-05-04 00:14:13'),
(146, 24, 10, '2026-05-04 00:14:13'),
(147, 24, 11, '2026-05-04 00:14:13'),
(148, 24, 12, '2026-05-04 00:14:13'),
(149, 24, 13, '2026-05-04 00:14:13'),
(150, 25, 3, '2026-05-04 00:14:13'),
(151, 25, 4, '2026-05-04 00:14:13'),
(152, 25, 5, '2026-05-04 00:14:13'),
(153, 25, 6, '2026-05-04 00:14:13'),
(154, 25, 7, '2026-05-04 00:14:13'),
(155, 25, 8, '2026-05-04 00:14:13'),
(156, 25, 9, '2026-05-04 00:14:13'),
(157, 25, 10, '2026-05-04 00:14:13'),
(158, 25, 11, '2026-05-04 00:14:13'),
(159, 25, 12, '2026-05-04 00:14:13'),
(160, 25, 13, '2026-05-04 00:14:13'),
(161, 26, 3, '2026-05-04 00:14:13'),
(162, 26, 4, '2026-05-04 00:14:13'),
(163, 26, 5, '2026-05-04 00:14:13'),
(164, 26, 6, '2026-05-04 00:14:13'),
(165, 26, 7, '2026-05-04 00:14:13'),
(166, 26, 8, '2026-05-04 00:14:13'),
(167, 26, 9, '2026-05-04 00:14:13'),
(168, 26, 10, '2026-05-04 00:14:13'),
(169, 26, 11, '2026-05-04 00:14:13'),
(170, 26, 12, '2026-05-04 00:14:13'),
(171, 26, 13, '2026-05-04 00:14:13'),
(172, 27, 3, '2026-05-04 00:14:13'),
(173, 27, 4, '2026-05-04 00:14:13'),
(174, 27, 5, '2026-05-04 00:14:13'),
(175, 27, 6, '2026-05-04 00:14:13'),
(176, 27, 7, '2026-05-04 00:14:13'),
(177, 27, 8, '2026-05-04 00:14:13'),
(178, 27, 9, '2026-05-04 00:14:13'),
(179, 27, 10, '2026-05-04 00:14:13'),
(180, 27, 11, '2026-05-04 00:14:13'),
(181, 27, 12, '2026-05-04 00:14:13'),
(182, 27, 13, '2026-05-04 00:14:13'),
(183, 28, 3, '2026-05-04 00:14:13'),
(184, 28, 4, '2026-05-04 00:14:13'),
(185, 28, 5, '2026-05-04 00:14:13'),
(186, 28, 6, '2026-05-04 00:14:13'),
(187, 28, 7, '2026-05-04 00:14:13'),
(188, 28, 8, '2026-05-04 00:14:13'),
(189, 28, 9, '2026-05-04 00:14:13'),
(190, 28, 10, '2026-05-04 00:14:13'),
(191, 28, 11, '2026-05-04 00:14:13'),
(192, 28, 12, '2026-05-04 00:14:13'),
(193, 28, 13, '2026-05-04 00:14:13'),
(194, 29, 3, '2026-05-04 00:14:13'),
(195, 29, 4, '2026-05-04 00:14:13'),
(196, 29, 5, '2026-05-04 00:14:13'),
(197, 29, 6, '2026-05-04 00:14:13'),
(198, 29, 7, '2026-05-04 00:14:13'),
(199, 29, 8, '2026-05-04 00:14:13'),
(200, 29, 9, '2026-05-04 00:14:13'),
(201, 29, 10, '2026-05-04 00:14:13'),
(202, 29, 11, '2026-05-04 00:14:13'),
(203, 29, 12, '2026-05-04 00:14:13'),
(204, 29, 13, '2026-05-04 00:14:13'),
(205, 30, 3, '2026-05-04 00:14:13'),
(206, 30, 4, '2026-05-04 00:14:13'),
(207, 30, 5, '2026-05-04 00:14:13'),
(208, 30, 6, '2026-05-04 00:14:13'),
(209, 30, 7, '2026-05-04 00:14:13'),
(210, 30, 8, '2026-05-04 00:14:13'),
(211, 30, 9, '2026-05-04 00:14:13'),
(212, 30, 10, '2026-05-04 00:14:13'),
(213, 30, 11, '2026-05-04 00:14:13'),
(214, 30, 12, '2026-05-04 00:14:13'),
(215, 30, 13, '2026-05-04 00:14:13'),
(216, 31, 3, '2026-05-04 00:14:13'),
(217, 31, 4, '2026-05-04 00:14:13'),
(218, 31, 5, '2026-05-04 00:14:13'),
(219, 31, 6, '2026-05-04 00:14:13'),
(220, 31, 7, '2026-05-04 00:14:13'),
(221, 31, 8, '2026-05-04 00:14:13'),
(222, 31, 9, '2026-05-04 00:14:13'),
(223, 31, 10, '2026-05-04 00:14:13'),
(224, 31, 11, '2026-05-04 00:14:13'),
(225, 31, 12, '2026-05-04 00:14:13'),
(226, 31, 13, '2026-05-04 00:14:13'),
(227, 32, 3, '2026-05-04 00:14:13'),
(228, 32, 4, '2026-05-04 00:14:13'),
(229, 32, 5, '2026-05-04 00:14:13'),
(230, 32, 6, '2026-05-04 00:14:13'),
(231, 32, 7, '2026-05-04 00:14:13'),
(232, 32, 8, '2026-05-04 00:14:13'),
(233, 32, 9, '2026-05-04 00:14:13'),
(234, 32, 10, '2026-05-04 00:14:13'),
(235, 32, 11, '2026-05-04 00:14:13'),
(236, 32, 12, '2026-05-04 00:14:13'),
(237, 32, 13, '2026-05-04 00:14:13'),
(238, 33, 3, '2026-05-04 00:14:13'),
(239, 33, 4, '2026-05-04 00:14:13'),
(240, 33, 5, '2026-05-04 00:14:13'),
(241, 33, 6, '2026-05-04 00:14:13'),
(242, 33, 7, '2026-05-04 00:14:13'),
(243, 33, 8, '2026-05-04 00:14:13'),
(244, 33, 9, '2026-05-04 00:14:13'),
(245, 33, 10, '2026-05-04 00:14:13'),
(246, 33, 11, '2026-05-04 00:14:13'),
(247, 33, 12, '2026-05-04 00:14:13'),
(248, 33, 13, '2026-05-04 00:14:13'),
(249, 34, 3, '2026-05-04 00:14:13'),
(250, 34, 4, '2026-05-04 00:14:13'),
(251, 34, 5, '2026-05-04 00:14:13'),
(252, 34, 6, '2026-05-04 00:14:13'),
(253, 34, 7, '2026-05-04 00:14:13'),
(254, 34, 8, '2026-05-04 00:14:13'),
(255, 34, 9, '2026-05-04 00:14:13'),
(256, 34, 10, '2026-05-04 00:14:13'),
(257, 34, 11, '2026-05-04 00:14:13'),
(258, 34, 12, '2026-05-04 00:14:13'),
(259, 34, 13, '2026-05-04 00:14:13'),
(260, 35, 3, '2026-05-04 00:14:13'),
(261, 35, 4, '2026-05-04 00:14:13'),
(262, 35, 5, '2026-05-04 00:14:13'),
(263, 35, 6, '2026-05-04 00:14:13'),
(264, 35, 7, '2026-05-04 00:14:13'),
(265, 35, 8, '2026-05-04 00:14:13'),
(266, 35, 9, '2026-05-04 00:14:13'),
(267, 35, 10, '2026-05-04 00:14:13'),
(268, 35, 11, '2026-05-04 00:14:13'),
(269, 35, 12, '2026-05-04 00:14:13'),
(270, 35, 13, '2026-05-04 00:14:13'),
(271, 36, 3, '2026-05-04 00:14:13'),
(272, 36, 4, '2026-05-04 00:14:13'),
(273, 36, 5, '2026-05-04 00:14:13'),
(274, 36, 6, '2026-05-04 00:14:13'),
(275, 36, 7, '2026-05-04 00:14:13'),
(276, 36, 8, '2026-05-04 00:14:13'),
(277, 36, 9, '2026-05-04 00:14:13'),
(278, 36, 10, '2026-05-04 00:14:13'),
(279, 36, 11, '2026-05-04 00:14:13'),
(280, 36, 12, '2026-05-04 00:14:13'),
(281, 36, 13, '2026-05-04 00:14:13'),
(282, 37, 3, '2026-05-04 00:14:13'),
(283, 37, 4, '2026-05-04 00:14:13'),
(284, 37, 5, '2026-05-04 00:14:13'),
(285, 37, 6, '2026-05-04 00:14:13'),
(286, 37, 7, '2026-05-04 00:14:13'),
(287, 37, 8, '2026-05-04 00:14:13'),
(288, 37, 9, '2026-05-04 00:14:13'),
(289, 37, 10, '2026-05-04 00:14:13'),
(290, 37, 11, '2026-05-04 00:14:13'),
(291, 37, 12, '2026-05-04 00:14:13'),
(292, 37, 13, '2026-05-04 00:14:13'),
(293, 38, 3, '2026-05-04 00:14:13'),
(294, 38, 4, '2026-05-04 00:14:13'),
(295, 38, 5, '2026-05-04 00:14:13'),
(296, 38, 6, '2026-05-04 00:14:13'),
(297, 38, 7, '2026-05-04 00:14:13'),
(298, 38, 8, '2026-05-04 00:14:13'),
(299, 38, 9, '2026-05-04 00:14:13'),
(300, 38, 10, '2026-05-04 00:14:13'),
(301, 38, 11, '2026-05-04 00:14:13'),
(302, 38, 12, '2026-05-04 00:14:13'),
(303, 38, 13, '2026-05-04 00:14:13'),
(304, 39, 3, '2026-05-04 00:14:13'),
(305, 39, 4, '2026-05-04 00:14:13'),
(306, 39, 5, '2026-05-04 00:14:13'),
(307, 39, 6, '2026-05-04 00:14:13'),
(308, 39, 7, '2026-05-04 00:14:13'),
(309, 39, 8, '2026-05-04 00:14:13'),
(310, 39, 9, '2026-05-04 00:14:13'),
(311, 39, 10, '2026-05-04 00:14:13'),
(312, 39, 11, '2026-05-04 00:14:13'),
(313, 39, 12, '2026-05-04 00:14:13'),
(314, 39, 13, '2026-05-04 00:14:13'),
(315, 40, 3, '2026-05-04 00:14:13'),
(316, 40, 4, '2026-05-04 00:14:13'),
(317, 40, 5, '2026-05-04 00:14:13'),
(318, 40, 6, '2026-05-04 00:14:13'),
(319, 40, 7, '2026-05-04 00:14:13'),
(320, 40, 8, '2026-05-04 00:14:13'),
(321, 40, 9, '2026-05-04 00:14:13'),
(322, 40, 10, '2026-05-04 00:14:13'),
(323, 40, 11, '2026-05-04 00:14:13'),
(324, 40, 12, '2026-05-04 00:14:13'),
(325, 40, 13, '2026-05-04 00:14:13'),
(326, 41, 3, '2026-05-04 00:14:13'),
(327, 41, 4, '2026-05-04 00:14:13'),
(328, 41, 5, '2026-05-04 00:14:13'),
(329, 41, 6, '2026-05-04 00:14:13'),
(330, 41, 7, '2026-05-04 00:14:13'),
(331, 41, 8, '2026-05-04 00:14:13'),
(332, 41, 9, '2026-05-04 00:14:13'),
(333, 41, 10, '2026-05-04 00:14:13'),
(334, 41, 11, '2026-05-04 00:14:13'),
(335, 41, 12, '2026-05-04 00:14:13'),
(336, 41, 13, '2026-05-04 00:14:13'),
(337, 42, 3, '2026-05-04 00:14:13'),
(338, 42, 4, '2026-05-04 00:14:13'),
(339, 42, 5, '2026-05-04 00:14:13'),
(340, 42, 6, '2026-05-04 00:14:13'),
(341, 42, 7, '2026-05-04 00:14:13'),
(342, 42, 8, '2026-05-04 00:14:13'),
(343, 42, 9, '2026-05-04 00:14:13'),
(344, 42, 10, '2026-05-04 00:14:13'),
(345, 42, 11, '2026-05-04 00:14:13'),
(346, 42, 12, '2026-05-04 00:14:13'),
(347, 42, 13, '2026-05-04 00:14:13'),
(348, 43, 3, '2026-05-04 00:14:13'),
(349, 43, 4, '2026-05-04 00:14:13'),
(350, 43, 5, '2026-05-04 00:14:13'),
(351, 43, 6, '2026-05-04 00:14:13'),
(352, 43, 7, '2026-05-04 00:14:13'),
(353, 43, 8, '2026-05-04 00:14:13'),
(354, 43, 9, '2026-05-04 00:14:13'),
(355, 43, 10, '2026-05-04 00:14:13'),
(356, 43, 11, '2026-05-04 00:14:13'),
(357, 43, 12, '2026-05-04 00:14:13'),
(358, 43, 13, '2026-05-04 00:14:13'),
(359, 44, 3, '2026-05-04 00:14:13'),
(360, 44, 4, '2026-05-04 00:14:13'),
(361, 44, 5, '2026-05-04 00:14:13'),
(362, 44, 6, '2026-05-04 00:14:13'),
(363, 44, 7, '2026-05-04 00:14:13'),
(364, 44, 8, '2026-05-04 00:14:13'),
(365, 44, 9, '2026-05-04 00:14:13'),
(366, 44, 10, '2026-05-04 00:14:13'),
(367, 44, 11, '2026-05-04 00:14:13'),
(368, 44, 12, '2026-05-04 00:14:13'),
(369, 44, 13, '2026-05-04 00:14:13'),
(370, 45, 3, '2026-05-04 00:14:13'),
(371, 45, 4, '2026-05-04 00:14:13'),
(372, 45, 5, '2026-05-04 00:14:13'),
(373, 45, 6, '2026-05-04 00:14:13'),
(374, 45, 7, '2026-05-04 00:14:13'),
(375, 45, 8, '2026-05-04 00:14:13'),
(376, 45, 9, '2026-05-04 00:14:13'),
(377, 45, 10, '2026-05-04 00:14:13'),
(378, 45, 11, '2026-05-04 00:14:13'),
(379, 45, 12, '2026-05-04 00:14:13'),
(380, 45, 13, '2026-05-04 00:14:13'),
(381, 46, 3, '2026-05-04 00:14:13'),
(382, 46, 4, '2026-05-04 00:14:13'),
(383, 46, 5, '2026-05-04 00:14:13'),
(384, 46, 6, '2026-05-04 00:14:13'),
(385, 46, 7, '2026-05-04 00:14:13'),
(386, 46, 8, '2026-05-04 00:14:13'),
(387, 46, 9, '2026-05-04 00:14:13'),
(388, 46, 10, '2026-05-04 00:14:13'),
(389, 46, 11, '2026-05-04 00:14:13'),
(390, 46, 12, '2026-05-04 00:14:13'),
(391, 46, 13, '2026-05-04 00:14:13'),
(392, 47, 3, '2026-05-04 00:14:13'),
(393, 47, 4, '2026-05-04 00:14:13'),
(394, 47, 5, '2026-05-04 00:14:13'),
(395, 47, 6, '2026-05-04 00:14:13'),
(396, 47, 7, '2026-05-04 00:14:13'),
(397, 47, 8, '2026-05-04 00:14:13'),
(398, 47, 9, '2026-05-04 00:14:13'),
(399, 47, 10, '2026-05-04 00:14:13'),
(400, 47, 11, '2026-05-04 00:14:13'),
(401, 47, 12, '2026-05-04 00:14:13'),
(402, 47, 13, '2026-05-04 00:14:13'),
(403, 48, 3, '2026-05-04 00:14:13'),
(404, 48, 4, '2026-05-04 00:14:13'),
(405, 48, 5, '2026-05-04 00:14:13'),
(406, 48, 6, '2026-05-04 00:14:13'),
(407, 48, 7, '2026-05-04 00:14:13'),
(408, 48, 8, '2026-05-04 00:14:13'),
(409, 48, 9, '2026-05-04 00:14:13'),
(410, 48, 10, '2026-05-04 00:14:13'),
(411, 48, 11, '2026-05-04 00:14:13'),
(412, 48, 12, '2026-05-04 00:14:13'),
(413, 48, 13, '2026-05-04 00:14:13'),
(414, 49, 3, '2026-05-04 00:14:13'),
(415, 49, 4, '2026-05-04 00:14:13'),
(416, 49, 5, '2026-05-04 00:14:13'),
(417, 49, 6, '2026-05-04 00:14:13'),
(418, 49, 7, '2026-05-04 00:14:13'),
(419, 49, 8, '2026-05-04 00:14:13'),
(420, 49, 9, '2026-05-04 00:14:13'),
(421, 49, 10, '2026-05-04 00:14:13'),
(422, 49, 11, '2026-05-04 00:14:13'),
(423, 49, 12, '2026-05-04 00:14:13'),
(424, 49, 13, '2026-05-04 00:14:13'),
(425, 50, 3, '2026-05-04 00:14:13'),
(426, 50, 4, '2026-05-04 00:14:13'),
(427, 50, 5, '2026-05-04 00:14:13'),
(428, 50, 6, '2026-05-04 00:14:13'),
(429, 50, 7, '2026-05-04 00:14:13'),
(430, 50, 8, '2026-05-04 00:14:13'),
(431, 50, 9, '2026-05-04 00:14:13'),
(432, 50, 10, '2026-05-04 00:14:13'),
(433, 50, 11, '2026-05-04 00:14:13'),
(434, 50, 12, '2026-05-04 00:14:13'),
(435, 50, 13, '2026-05-04 00:14:13'),
(436, 51, 3, '2026-05-04 00:14:13'),
(437, 51, 4, '2026-05-04 00:14:13'),
(438, 51, 5, '2026-05-04 00:14:13'),
(439, 51, 6, '2026-05-04 00:14:13'),
(440, 51, 7, '2026-05-04 00:14:13'),
(441, 51, 8, '2026-05-04 00:14:13'),
(442, 51, 9, '2026-05-04 00:14:13'),
(443, 51, 10, '2026-05-04 00:14:13'),
(444, 51, 11, '2026-05-04 00:14:13'),
(445, 51, 12, '2026-05-04 00:14:13'),
(446, 51, 13, '2026-05-04 00:14:13'),
(447, 52, 3, '2026-05-04 00:14:13'),
(448, 52, 4, '2026-05-04 00:14:13'),
(449, 52, 5, '2026-05-04 00:14:13'),
(450, 52, 6, '2026-05-04 00:14:13'),
(451, 52, 7, '2026-05-04 00:14:13'),
(452, 52, 8, '2026-05-04 00:14:13'),
(453, 52, 9, '2026-05-04 00:14:13'),
(454, 52, 10, '2026-05-04 00:14:13'),
(455, 52, 11, '2026-05-04 00:14:13'),
(456, 52, 12, '2026-05-04 00:14:13'),
(457, 52, 13, '2026-05-04 00:14:13'),
(458, 53, 3, '2026-05-04 00:14:13'),
(459, 53, 4, '2026-05-04 00:14:13'),
(460, 53, 5, '2026-05-04 00:14:13'),
(461, 53, 6, '2026-05-04 00:14:13'),
(462, 53, 7, '2026-05-04 00:14:13'),
(463, 53, 8, '2026-05-04 00:14:13'),
(464, 53, 9, '2026-05-04 00:14:13'),
(465, 53, 10, '2026-05-04 00:14:13'),
(466, 53, 11, '2026-05-04 00:14:13'),
(467, 53, 12, '2026-05-04 00:14:13'),
(468, 53, 13, '2026-05-04 00:14:13'),
(469, 54, 3, '2026-05-04 00:14:13'),
(470, 54, 4, '2026-05-04 00:14:13'),
(471, 54, 5, '2026-05-04 00:14:13'),
(472, 54, 6, '2026-05-04 00:14:13'),
(473, 54, 7, '2026-05-04 00:14:13'),
(474, 54, 8, '2026-05-04 00:14:13'),
(475, 54, 9, '2026-05-04 00:14:13'),
(476, 54, 10, '2026-05-04 00:14:13'),
(477, 54, 11, '2026-05-04 00:14:13'),
(478, 54, 12, '2026-05-04 00:14:13'),
(479, 54, 13, '2026-05-04 00:14:13'),
(480, 55, 3, '2026-05-04 00:14:13'),
(481, 55, 4, '2026-05-04 00:14:13'),
(482, 55, 5, '2026-05-04 00:14:13'),
(483, 55, 6, '2026-05-04 00:14:13'),
(484, 55, 7, '2026-05-04 00:14:13'),
(485, 55, 8, '2026-05-04 00:14:13'),
(486, 55, 9, '2026-05-04 00:14:13'),
(487, 55, 10, '2026-05-04 00:14:13'),
(488, 55, 11, '2026-05-04 00:14:13'),
(489, 55, 12, '2026-05-04 00:14:13'),
(490, 55, 13, '2026-05-04 00:14:13'),
(491, 56, 3, '2026-05-04 00:14:13'),
(492, 56, 4, '2026-05-04 00:14:13'),
(493, 56, 5, '2026-05-04 00:14:13'),
(494, 56, 6, '2026-05-04 00:14:13'),
(495, 56, 7, '2026-05-04 00:14:13'),
(496, 56, 8, '2026-05-04 00:14:13'),
(497, 56, 9, '2026-05-04 00:14:13'),
(498, 56, 10, '2026-05-04 00:14:13'),
(499, 56, 11, '2026-05-04 00:14:13'),
(500, 56, 12, '2026-05-04 00:14:13'),
(501, 56, 13, '2026-05-04 00:14:13'),
(502, 57, 3, '2026-05-04 00:14:13'),
(503, 57, 4, '2026-05-04 00:14:13'),
(504, 57, 5, '2026-05-04 00:14:13'),
(505, 57, 6, '2026-05-04 00:14:13'),
(506, 57, 7, '2026-05-04 00:14:13'),
(507, 57, 8, '2026-05-04 00:14:13'),
(508, 57, 9, '2026-05-04 00:14:13'),
(509, 57, 10, '2026-05-04 00:14:13'),
(510, 57, 11, '2026-05-04 00:14:13'),
(511, 57, 12, '2026-05-04 00:14:13'),
(512, 57, 13, '2026-05-04 00:14:13'),
(513, 58, 3, '2026-05-04 00:14:13'),
(514, 58, 4, '2026-05-04 00:14:13'),
(515, 58, 5, '2026-05-04 00:14:13'),
(516, 58, 6, '2026-05-04 00:14:13'),
(517, 58, 7, '2026-05-04 00:14:13'),
(518, 58, 8, '2026-05-04 00:14:13'),
(519, 58, 9, '2026-05-04 00:14:13'),
(520, 58, 10, '2026-05-04 00:14:13'),
(521, 58, 11, '2026-05-04 00:14:13'),
(522, 58, 12, '2026-05-04 00:14:13'),
(523, 58, 13, '2026-05-04 00:14:13'),
(524, 59, 3, '2026-05-04 00:14:13'),
(525, 59, 4, '2026-05-04 00:14:13'),
(526, 59, 5, '2026-05-04 00:14:13'),
(527, 59, 6, '2026-05-04 00:14:13'),
(528, 59, 7, '2026-05-04 00:14:13'),
(529, 59, 8, '2026-05-04 00:14:13'),
(530, 59, 9, '2026-05-04 00:14:13'),
(531, 59, 10, '2026-05-04 00:14:13'),
(532, 59, 11, '2026-05-04 00:14:13'),
(533, 59, 12, '2026-05-04 00:14:13'),
(534, 59, 13, '2026-05-04 00:14:13'),
(535, 60, 3, '2026-05-04 00:14:13'),
(536, 60, 4, '2026-05-04 00:14:13'),
(537, 60, 5, '2026-05-04 00:14:13'),
(538, 60, 6, '2026-05-04 00:14:13'),
(539, 60, 7, '2026-05-04 00:14:13'),
(540, 60, 8, '2026-05-04 00:14:13'),
(541, 60, 9, '2026-05-04 00:14:13'),
(542, 60, 10, '2026-05-04 00:14:13'),
(543, 60, 11, '2026-05-04 00:14:13'),
(544, 60, 12, '2026-05-04 00:14:13'),
(545, 60, 13, '2026-05-04 00:14:13'),
(546, 61, 3, '2026-05-04 00:14:13'),
(547, 61, 4, '2026-05-04 00:14:13'),
(548, 61, 5, '2026-05-04 00:14:13'),
(549, 61, 6, '2026-05-04 00:14:13'),
(550, 61, 7, '2026-05-04 00:14:13'),
(551, 61, 8, '2026-05-04 00:14:13'),
(552, 61, 9, '2026-05-04 00:14:13'),
(553, 61, 10, '2026-05-04 00:14:13'),
(554, 61, 11, '2026-05-04 00:14:13'),
(555, 61, 12, '2026-05-04 00:14:13'),
(556, 61, 13, '2026-05-04 00:14:13'),
(557, 62, 3, '2026-05-04 00:14:13'),
(558, 62, 4, '2026-05-04 00:14:13'),
(559, 62, 5, '2026-05-04 00:14:13'),
(560, 62, 6, '2026-05-04 00:14:13'),
(561, 62, 7, '2026-05-04 00:14:13'),
(562, 62, 8, '2026-05-04 00:14:13'),
(563, 62, 9, '2026-05-04 00:14:13'),
(564, 62, 10, '2026-05-04 00:14:13'),
(565, 62, 11, '2026-05-04 00:14:13'),
(566, 62, 12, '2026-05-04 00:14:13'),
(567, 62, 13, '2026-05-04 00:14:13'),
(568, 63, 3, '2026-05-04 00:14:13'),
(569, 63, 4, '2026-05-04 00:14:13'),
(570, 63, 5, '2026-05-04 00:14:13'),
(571, 63, 6, '2026-05-04 00:14:13'),
(572, 63, 7, '2026-05-04 00:14:13'),
(573, 63, 8, '2026-05-04 00:14:13'),
(574, 63, 9, '2026-05-04 00:14:13'),
(575, 63, 10, '2026-05-04 00:14:13'),
(576, 63, 11, '2026-05-04 00:14:13'),
(577, 63, 12, '2026-05-04 00:14:13'),
(578, 63, 13, '2026-05-04 00:14:13'),
(579, 64, 3, '2026-05-04 00:14:13'),
(580, 64, 4, '2026-05-04 00:14:13'),
(581, 64, 5, '2026-05-04 00:14:13'),
(582, 64, 6, '2026-05-04 00:14:13'),
(583, 64, 7, '2026-05-04 00:14:13'),
(584, 64, 8, '2026-05-04 00:14:13'),
(585, 64, 9, '2026-05-04 00:14:13'),
(586, 64, 10, '2026-05-04 00:14:13'),
(587, 64, 11, '2026-05-04 00:14:13'),
(588, 64, 12, '2026-05-04 00:14:13'),
(589, 64, 13, '2026-05-04 00:14:13'),
(590, 65, 3, '2026-05-04 00:14:13'),
(591, 65, 4, '2026-05-04 00:14:13'),
(592, 65, 5, '2026-05-04 00:14:13'),
(593, 65, 6, '2026-05-04 00:14:13'),
(594, 65, 7, '2026-05-04 00:14:13'),
(595, 65, 8, '2026-05-04 00:14:13'),
(596, 65, 9, '2026-05-04 00:14:13'),
(597, 65, 10, '2026-05-04 00:14:13'),
(598, 65, 11, '2026-05-04 00:14:13'),
(599, 65, 12, '2026-05-04 00:14:13'),
(600, 65, 13, '2026-05-04 00:14:13'),
(601, 66, 3, '2026-05-04 00:14:13'),
(602, 66, 4, '2026-05-04 00:14:13'),
(603, 66, 5, '2026-05-04 00:14:13'),
(604, 66, 6, '2026-05-04 00:14:13'),
(605, 66, 7, '2026-05-04 00:14:13'),
(606, 66, 8, '2026-05-04 00:14:13'),
(607, 66, 9, '2026-05-04 00:14:13'),
(608, 66, 10, '2026-05-04 00:14:13'),
(609, 66, 11, '2026-05-04 00:14:13'),
(610, 66, 12, '2026-05-04 00:14:13'),
(611, 66, 13, '2026-05-04 00:14:13'),
(612, 67, 3, '2026-05-04 00:14:13'),
(613, 67, 4, '2026-05-04 00:14:13'),
(614, 67, 5, '2026-05-04 00:14:13'),
(615, 67, 6, '2026-05-04 00:14:13'),
(616, 67, 7, '2026-05-04 00:14:13'),
(617, 67, 8, '2026-05-04 00:14:13'),
(618, 67, 9, '2026-05-04 00:14:13'),
(619, 67, 10, '2026-05-04 00:14:13'),
(620, 67, 11, '2026-05-04 00:14:13'),
(621, 67, 12, '2026-05-04 00:14:13'),
(622, 67, 13, '2026-05-04 00:14:13'),
(623, 68, 3, '2026-05-04 00:14:13'),
(624, 68, 4, '2026-05-04 00:14:13'),
(625, 68, 5, '2026-05-04 00:14:13'),
(626, 68, 6, '2026-05-04 00:14:13'),
(627, 68, 7, '2026-05-04 00:14:13'),
(628, 68, 8, '2026-05-04 00:14:13'),
(629, 68, 9, '2026-05-04 00:14:13'),
(630, 68, 10, '2026-05-04 00:14:13'),
(631, 68, 11, '2026-05-04 00:14:13'),
(632, 68, 12, '2026-05-04 00:14:13'),
(633, 68, 13, '2026-05-04 00:14:13'),
(634, 69, 3, '2026-05-04 00:14:13'),
(635, 69, 4, '2026-05-04 00:14:13'),
(636, 69, 5, '2026-05-04 00:14:13'),
(637, 69, 6, '2026-05-04 00:14:13'),
(638, 69, 7, '2026-05-04 00:14:13'),
(639, 69, 8, '2026-05-04 00:14:13'),
(640, 69, 9, '2026-05-04 00:14:13'),
(641, 69, 10, '2026-05-04 00:14:13'),
(642, 69, 11, '2026-05-04 00:14:13'),
(643, 69, 12, '2026-05-04 00:14:13'),
(644, 69, 13, '2026-05-04 00:14:13'),
(645, 70, 3, '2026-05-04 00:14:13'),
(646, 70, 4, '2026-05-04 00:14:13'),
(647, 70, 5, '2026-05-04 00:14:13'),
(648, 70, 6, '2026-05-04 00:14:13'),
(649, 70, 7, '2026-05-04 00:14:13'),
(650, 70, 8, '2026-05-04 00:14:13'),
(651, 70, 9, '2026-05-04 00:14:13'),
(652, 70, 10, '2026-05-04 00:14:13'),
(653, 70, 11, '2026-05-04 00:14:13'),
(654, 70, 12, '2026-05-04 00:14:13'),
(655, 70, 13, '2026-05-04 00:14:13'),
(656, 71, 3, '2026-05-04 00:14:13'),
(657, 71, 4, '2026-05-04 00:14:13'),
(658, 71, 5, '2026-05-04 00:14:13'),
(659, 71, 6, '2026-05-04 00:14:13'),
(660, 71, 7, '2026-05-04 00:14:13'),
(661, 71, 8, '2026-05-04 00:14:13'),
(662, 71, 9, '2026-05-04 00:14:13'),
(663, 71, 10, '2026-05-04 00:14:13'),
(664, 71, 11, '2026-05-04 00:14:13'),
(665, 71, 12, '2026-05-04 00:14:13'),
(666, 71, 13, '2026-05-04 00:14:13'),
(667, 72, 3, '2026-05-04 00:14:13'),
(668, 72, 4, '2026-05-04 00:14:13'),
(669, 72, 5, '2026-05-04 00:14:14'),
(670, 72, 6, '2026-05-04 00:14:14'),
(671, 72, 7, '2026-05-04 00:14:14'),
(672, 72, 8, '2026-05-04 00:14:14'),
(673, 72, 9, '2026-05-04 00:14:14'),
(674, 72, 10, '2026-05-04 00:14:14'),
(675, 72, 11, '2026-05-04 00:14:14'),
(676, 72, 12, '2026-05-04 00:14:14'),
(677, 72, 13, '2026-05-04 00:14:14'),
(678, 73, 3, '2026-05-04 00:14:14'),
(679, 73, 4, '2026-05-04 00:14:14'),
(680, 73, 5, '2026-05-04 00:14:14'),
(681, 73, 6, '2026-05-04 00:14:14'),
(682, 73, 7, '2026-05-04 00:14:14'),
(683, 73, 8, '2026-05-04 00:14:14'),
(684, 73, 9, '2026-05-04 00:14:14'),
(685, 73, 10, '2026-05-04 00:14:14'),
(686, 73, 11, '2026-05-04 00:14:14'),
(687, 73, 12, '2026-05-04 00:14:14'),
(688, 73, 13, '2026-05-04 00:14:14'),
(689, 74, 3, '2026-05-04 00:14:14'),
(690, 74, 4, '2026-05-04 00:14:14'),
(691, 74, 5, '2026-05-04 00:14:14'),
(692, 74, 6, '2026-05-04 00:14:14'),
(693, 74, 7, '2026-05-04 00:14:14'),
(694, 74, 8, '2026-05-04 00:14:14'),
(695, 74, 9, '2026-05-04 00:14:14'),
(696, 74, 10, '2026-05-04 00:14:14'),
(697, 74, 11, '2026-05-04 00:14:14'),
(698, 74, 12, '2026-05-04 00:14:14'),
(699, 74, 13, '2026-05-04 00:14:14'),
(700, 75, 3, '2026-05-04 00:14:14'),
(701, 75, 4, '2026-05-04 00:14:14'),
(702, 75, 5, '2026-05-04 00:14:14'),
(703, 75, 6, '2026-05-04 00:14:14'),
(704, 75, 7, '2026-05-04 00:14:14'),
(705, 75, 8, '2026-05-04 00:14:14'),
(706, 75, 9, '2026-05-04 00:14:14'),
(707, 75, 10, '2026-05-04 00:14:14'),
(708, 75, 11, '2026-05-04 00:14:14'),
(709, 75, 12, '2026-05-04 00:14:14'),
(710, 75, 13, '2026-05-04 00:14:14'),
(711, 76, 3, '2026-05-04 00:14:14'),
(712, 76, 4, '2026-05-04 00:14:14'),
(713, 76, 5, '2026-05-04 00:14:14'),
(714, 76, 6, '2026-05-04 00:14:14'),
(715, 76, 7, '2026-05-04 00:14:14'),
(716, 76, 8, '2026-05-04 00:14:14'),
(717, 76, 9, '2026-05-04 00:14:14'),
(718, 76, 10, '2026-05-04 00:14:14'),
(719, 76, 11, '2026-05-04 00:14:14'),
(720, 76, 12, '2026-05-04 00:14:14'),
(721, 76, 13, '2026-05-04 00:14:14'),
(722, 77, 3, '2026-05-04 00:14:14'),
(723, 77, 4, '2026-05-04 00:14:14'),
(724, 77, 5, '2026-05-04 00:14:14'),
(725, 77, 6, '2026-05-04 00:14:14'),
(726, 77, 7, '2026-05-04 00:14:14'),
(727, 77, 8, '2026-05-04 00:14:14'),
(728, 77, 9, '2026-05-04 00:14:14'),
(729, 77, 10, '2026-05-04 00:14:14'),
(730, 77, 11, '2026-05-04 00:14:14'),
(731, 77, 12, '2026-05-04 00:14:14'),
(732, 77, 13, '2026-05-04 00:14:14'),
(733, 78, 3, '2026-05-04 00:14:14'),
(734, 78, 4, '2026-05-04 00:14:14'),
(735, 78, 5, '2026-05-04 00:14:14'),
(736, 78, 6, '2026-05-04 00:14:14'),
(737, 78, 7, '2026-05-04 00:14:14'),
(738, 78, 8, '2026-05-04 00:14:14'),
(739, 78, 9, '2026-05-04 00:14:14'),
(740, 78, 10, '2026-05-04 00:14:14'),
(741, 78, 11, '2026-05-04 00:14:14'),
(742, 78, 12, '2026-05-04 00:14:14'),
(743, 78, 13, '2026-05-04 00:14:14'),
(744, 79, 3, '2026-05-04 00:14:14'),
(745, 79, 4, '2026-05-04 00:14:14'),
(746, 79, 5, '2026-05-04 00:14:14'),
(747, 79, 6, '2026-05-04 00:14:14'),
(748, 79, 7, '2026-05-04 00:14:14'),
(749, 79, 8, '2026-05-04 00:14:14'),
(750, 79, 9, '2026-05-04 00:14:14'),
(751, 79, 10, '2026-05-04 00:14:14'),
(752, 79, 11, '2026-05-04 00:14:14'),
(753, 79, 12, '2026-05-04 00:14:14'),
(754, 79, 13, '2026-05-04 00:14:14'),
(755, 80, 3, '2026-05-04 00:14:14'),
(756, 80, 4, '2026-05-04 00:14:14'),
(757, 80, 5, '2026-05-04 00:14:14'),
(758, 80, 6, '2026-05-04 00:14:14'),
(759, 80, 7, '2026-05-04 00:14:14'),
(760, 80, 8, '2026-05-04 00:14:14'),
(761, 80, 9, '2026-05-04 00:14:14'),
(762, 80, 10, '2026-05-04 00:14:14'),
(763, 80, 11, '2026-05-04 00:14:14'),
(764, 80, 12, '2026-05-04 00:14:14'),
(765, 80, 13, '2026-05-04 00:14:14'),
(766, 81, 3, '2026-05-04 00:14:14'),
(767, 81, 4, '2026-05-04 00:14:14'),
(768, 81, 5, '2026-05-04 00:14:14'),
(769, 81, 6, '2026-05-04 00:14:14'),
(770, 81, 7, '2026-05-04 00:14:14'),
(771, 81, 8, '2026-05-04 00:14:14'),
(772, 81, 9, '2026-05-04 00:14:14'),
(773, 81, 10, '2026-05-04 00:14:14'),
(774, 81, 11, '2026-05-04 00:14:14'),
(775, 81, 12, '2026-05-04 00:14:14'),
(776, 81, 13, '2026-05-04 00:14:14'),
(777, 82, 3, '2026-05-04 00:14:14'),
(778, 82, 4, '2026-05-04 00:14:14'),
(779, 82, 5, '2026-05-04 00:14:14'),
(780, 82, 6, '2026-05-04 00:14:14'),
(781, 82, 7, '2026-05-04 00:14:14'),
(782, 82, 8, '2026-05-04 00:14:14'),
(783, 82, 9, '2026-05-04 00:14:14'),
(784, 82, 10, '2026-05-04 00:14:14'),
(785, 82, 11, '2026-05-04 00:14:14'),
(786, 82, 12, '2026-05-04 00:14:14'),
(787, 82, 13, '2026-05-04 00:14:14'),
(788, 83, 3, '2026-05-04 00:14:14'),
(789, 83, 4, '2026-05-04 00:14:14'),
(790, 83, 5, '2026-05-04 00:14:14'),
(791, 83, 6, '2026-05-04 00:14:14'),
(792, 83, 7, '2026-05-04 00:14:14'),
(793, 83, 8, '2026-05-04 00:14:14'),
(794, 83, 9, '2026-05-04 00:14:14'),
(795, 83, 10, '2026-05-04 00:14:14'),
(796, 83, 11, '2026-05-04 00:14:14'),
(797, 83, 12, '2026-05-04 00:14:14'),
(798, 83, 13, '2026-05-04 00:14:14'),
(799, 84, 3, '2026-05-04 00:14:14'),
(800, 84, 4, '2026-05-04 00:14:14'),
(801, 84, 5, '2026-05-04 00:14:14'),
(802, 84, 6, '2026-05-04 00:14:14'),
(803, 84, 7, '2026-05-04 00:14:14'),
(804, 84, 8, '2026-05-04 00:14:14'),
(805, 84, 9, '2026-05-04 00:14:14'),
(806, 84, 10, '2026-05-04 00:14:14'),
(807, 84, 11, '2026-05-04 00:14:14'),
(808, 84, 12, '2026-05-04 00:14:14'),
(809, 84, 13, '2026-05-04 00:14:14'),
(810, 85, 3, '2026-05-04 00:14:14'),
(811, 85, 4, '2026-05-04 00:14:14'),
(812, 85, 5, '2026-05-04 00:14:14'),
(813, 85, 6, '2026-05-04 00:14:14'),
(814, 85, 7, '2026-05-04 00:14:14'),
(815, 85, 8, '2026-05-04 00:14:14'),
(816, 85, 9, '2026-05-04 00:14:14'),
(817, 85, 10, '2026-05-04 00:14:14'),
(818, 85, 11, '2026-05-04 00:14:14'),
(819, 85, 12, '2026-05-04 00:14:14'),
(820, 85, 13, '2026-05-04 00:14:14'),
(821, 86, 3, '2026-05-04 00:14:14'),
(822, 86, 4, '2026-05-04 00:14:14'),
(823, 86, 5, '2026-05-04 00:14:14'),
(824, 86, 6, '2026-05-04 00:14:14'),
(825, 86, 7, '2026-05-04 00:14:14'),
(826, 86, 8, '2026-05-04 00:14:14'),
(827, 86, 9, '2026-05-04 00:14:14'),
(828, 86, 10, '2026-05-04 00:14:14'),
(829, 86, 11, '2026-05-04 00:14:14'),
(830, 86, 12, '2026-05-04 00:14:14'),
(831, 86, 13, '2026-05-04 00:14:14'),
(832, 87, 3, '2026-05-04 00:14:14'),
(833, 87, 4, '2026-05-04 00:14:14'),
(834, 87, 5, '2026-05-04 00:14:14'),
(835, 87, 6, '2026-05-04 00:14:14'),
(836, 87, 7, '2026-05-04 00:14:14'),
(837, 87, 8, '2026-05-04 00:14:14'),
(838, 87, 9, '2026-05-04 00:14:14'),
(839, 87, 10, '2026-05-04 00:14:14'),
(840, 87, 11, '2026-05-04 00:14:14'),
(841, 87, 12, '2026-05-04 00:14:14'),
(842, 87, 13, '2026-05-04 00:14:14'),
(843, 88, 3, '2026-05-04 00:14:14'),
(844, 88, 4, '2026-05-04 00:14:14'),
(845, 88, 5, '2026-05-04 00:14:14'),
(846, 88, 6, '2026-05-04 00:14:14'),
(847, 88, 7, '2026-05-04 00:14:14'),
(848, 88, 8, '2026-05-04 00:14:14'),
(849, 88, 9, '2026-05-04 00:14:14'),
(850, 88, 10, '2026-05-04 00:14:14'),
(851, 88, 11, '2026-05-04 00:14:14'),
(852, 88, 12, '2026-05-04 00:14:14'),
(853, 88, 13, '2026-05-04 00:14:14'),
(854, 89, 3, '2026-05-04 00:14:14'),
(855, 89, 4, '2026-05-04 00:14:14'),
(856, 89, 5, '2026-05-04 00:14:14'),
(857, 89, 6, '2026-05-04 00:14:14'),
(858, 89, 7, '2026-05-04 00:14:14'),
(859, 89, 8, '2026-05-04 00:14:14'),
(860, 89, 9, '2026-05-04 00:14:14'),
(861, 89, 10, '2026-05-04 00:14:14'),
(862, 89, 11, '2026-05-04 00:14:14'),
(863, 89, 12, '2026-05-04 00:14:14'),
(864, 89, 13, '2026-05-04 00:14:14'),
(865, 90, 3, '2026-05-04 00:14:14'),
(866, 90, 4, '2026-05-04 00:14:14'),
(867, 90, 5, '2026-05-04 00:14:14'),
(868, 90, 6, '2026-05-04 00:14:14'),
(869, 90, 7, '2026-05-04 00:14:14'),
(870, 90, 8, '2026-05-04 00:14:14'),
(871, 90, 9, '2026-05-04 00:14:14'),
(872, 90, 10, '2026-05-04 00:14:14'),
(873, 90, 11, '2026-05-04 00:14:14'),
(874, 90, 12, '2026-05-04 00:14:14'),
(875, 90, 13, '2026-05-04 00:14:14'),
(876, 91, 3, '2026-05-04 00:14:14'),
(877, 91, 4, '2026-05-04 00:14:14'),
(878, 91, 5, '2026-05-04 00:14:14'),
(879, 91, 6, '2026-05-04 00:14:14'),
(880, 91, 7, '2026-05-04 00:14:14'),
(881, 91, 8, '2026-05-04 00:14:14'),
(882, 91, 9, '2026-05-04 00:14:14'),
(883, 91, 10, '2026-05-04 00:14:14'),
(884, 91, 11, '2026-05-04 00:14:14'),
(885, 91, 12, '2026-05-04 00:14:14'),
(886, 91, 13, '2026-05-04 00:14:14'),
(887, 92, 3, '2026-05-04 00:14:14'),
(888, 92, 4, '2026-05-04 00:14:14'),
(889, 92, 5, '2026-05-04 00:14:14'),
(890, 92, 6, '2026-05-04 00:14:14'),
(891, 92, 7, '2026-05-04 00:14:14'),
(892, 92, 8, '2026-05-04 00:14:14'),
(893, 92, 9, '2026-05-04 00:14:14'),
(894, 92, 10, '2026-05-04 00:14:14'),
(895, 92, 11, '2026-05-04 00:14:14'),
(896, 92, 12, '2026-05-04 00:14:14'),
(897, 92, 13, '2026-05-04 00:14:14'),
(898, 93, 3, '2026-05-04 00:14:14'),
(899, 93, 4, '2026-05-04 00:14:14'),
(900, 93, 5, '2026-05-04 00:14:14'),
(901, 93, 6, '2026-05-04 00:14:14'),
(902, 93, 7, '2026-05-04 00:14:14'),
(903, 93, 8, '2026-05-04 00:14:14'),
(904, 93, 9, '2026-05-04 00:14:14'),
(905, 93, 10, '2026-05-04 00:14:14'),
(906, 93, 11, '2026-05-04 00:14:14'),
(907, 93, 12, '2026-05-04 00:14:14'),
(908, 93, 13, '2026-05-04 00:14:14'),
(909, 94, 3, '2026-05-04 00:14:14'),
(910, 94, 4, '2026-05-04 00:14:14'),
(911, 94, 5, '2026-05-04 00:14:14'),
(912, 94, 6, '2026-05-04 00:14:14'),
(913, 94, 7, '2026-05-04 00:14:14'),
(914, 94, 8, '2026-05-04 00:14:14'),
(915, 94, 9, '2026-05-04 00:14:14'),
(916, 94, 10, '2026-05-04 00:14:14'),
(917, 94, 11, '2026-05-04 00:14:14'),
(918, 94, 12, '2026-05-04 00:14:14'),
(919, 94, 13, '2026-05-04 00:14:14'),
(920, 95, 3, '2026-05-04 00:14:14'),
(921, 95, 4, '2026-05-04 00:14:14'),
(922, 95, 5, '2026-05-04 00:14:14'),
(923, 95, 6, '2026-05-04 00:14:14'),
(924, 95, 7, '2026-05-04 00:14:14'),
(925, 95, 8, '2026-05-04 00:14:14'),
(926, 95, 9, '2026-05-04 00:14:14'),
(927, 95, 10, '2026-05-04 00:14:14'),
(928, 95, 11, '2026-05-04 00:14:14'),
(929, 95, 12, '2026-05-04 00:14:14'),
(930, 95, 13, '2026-05-04 00:14:14'),
(931, 96, 3, '2026-05-04 00:14:14'),
(932, 96, 4, '2026-05-04 00:14:14'),
(933, 96, 5, '2026-05-04 00:14:14'),
(934, 96, 6, '2026-05-04 00:14:14'),
(935, 96, 7, '2026-05-04 00:14:14'),
(936, 96, 8, '2026-05-04 00:14:14'),
(937, 96, 9, '2026-05-04 00:14:14'),
(938, 96, 10, '2026-05-04 00:14:14'),
(939, 96, 11, '2026-05-04 00:14:14'),
(940, 96, 12, '2026-05-04 00:14:14'),
(941, 96, 13, '2026-05-04 00:14:14'),
(942, 97, 3, '2026-05-04 00:14:14'),
(943, 97, 4, '2026-05-04 00:14:14'),
(944, 97, 5, '2026-05-04 00:14:14'),
(945, 97, 6, '2026-05-04 00:14:14'),
(946, 97, 7, '2026-05-04 00:14:14'),
(947, 97, 8, '2026-05-04 00:14:14'),
(948, 97, 9, '2026-05-04 00:14:14'),
(949, 97, 10, '2026-05-04 00:14:14'),
(950, 97, 11, '2026-05-04 00:14:14'),
(951, 97, 12, '2026-05-04 00:14:14'),
(952, 97, 13, '2026-05-04 00:14:14'),
(953, 98, 3, '2026-05-04 00:14:14'),
(954, 98, 4, '2026-05-04 00:14:14'),
(955, 98, 5, '2026-05-04 00:14:14'),
(956, 98, 6, '2026-05-04 00:14:14'),
(957, 98, 7, '2026-05-04 00:14:14'),
(958, 98, 8, '2026-05-04 00:14:14'),
(959, 98, 9, '2026-05-04 00:14:14'),
(960, 98, 10, '2026-05-04 00:14:14'),
(961, 98, 11, '2026-05-04 00:14:14'),
(962, 98, 12, '2026-05-04 00:14:14'),
(963, 98, 13, '2026-05-04 00:14:14'),
(964, 99, 3, '2026-05-04 00:14:14'),
(965, 99, 4, '2026-05-04 00:14:14'),
(966, 99, 5, '2026-05-04 00:14:14'),
(967, 99, 6, '2026-05-04 00:14:14'),
(968, 99, 7, '2026-05-04 00:14:14'),
(969, 99, 8, '2026-05-04 00:14:14'),
(970, 99, 9, '2026-05-04 00:14:14'),
(971, 99, 10, '2026-05-04 00:14:14'),
(972, 99, 11, '2026-05-04 00:14:14'),
(973, 99, 12, '2026-05-04 00:14:14'),
(974, 99, 13, '2026-05-04 00:14:14'),
(975, 100, 3, '2026-05-04 00:14:14'),
(976, 100, 4, '2026-05-04 00:14:14'),
(977, 100, 5, '2026-05-04 00:14:14'),
(978, 100, 6, '2026-05-04 00:14:14'),
(979, 100, 7, '2026-05-04 00:14:14'),
(980, 100, 8, '2026-05-04 00:14:14'),
(981, 100, 9, '2026-05-04 00:14:14'),
(982, 100, 10, '2026-05-04 00:14:14'),
(983, 100, 11, '2026-05-04 00:14:14'),
(984, 100, 12, '2026-05-04 00:14:14'),
(985, 100, 13, '2026-05-04 00:14:14'),
(986, 101, 3, '2026-05-04 00:14:14'),
(987, 101, 4, '2026-05-04 00:14:14'),
(988, 101, 5, '2026-05-04 00:14:14'),
(989, 101, 6, '2026-05-04 00:14:14'),
(990, 101, 7, '2026-05-04 00:14:14'),
(991, 101, 8, '2026-05-04 00:14:14'),
(992, 101, 9, '2026-05-04 00:14:14'),
(993, 101, 10, '2026-05-04 00:14:14'),
(994, 101, 11, '2026-05-04 00:14:14'),
(995, 101, 12, '2026-05-04 00:14:14'),
(996, 101, 13, '2026-05-04 00:14:14'),
(997, 102, 3, '2026-05-04 00:14:14'),
(998, 102, 4, '2026-05-04 00:14:14'),
(999, 102, 5, '2026-05-04 00:14:14'),
(1000, 102, 6, '2026-05-04 00:14:14'),
(1001, 102, 7, '2026-05-04 00:14:14'),
(1002, 102, 8, '2026-05-04 00:14:14'),
(1003, 102, 9, '2026-05-04 00:14:14'),
(1004, 102, 10, '2026-05-04 00:14:14'),
(1005, 102, 11, '2026-05-04 00:14:14'),
(1006, 102, 12, '2026-05-04 00:14:14'),
(1007, 102, 13, '2026-05-04 00:14:14'),
(1008, 103, 3, '2026-05-04 00:14:14'),
(1009, 103, 4, '2026-05-04 00:14:14'),
(1010, 103, 5, '2026-05-04 00:14:14'),
(1011, 103, 6, '2026-05-04 00:14:14'),
(1012, 103, 7, '2026-05-04 00:14:14'),
(1013, 103, 8, '2026-05-04 00:14:14'),
(1014, 103, 9, '2026-05-04 00:14:14'),
(1015, 103, 10, '2026-05-04 00:14:14'),
(1016, 103, 11, '2026-05-04 00:14:14'),
(1017, 103, 12, '2026-05-04 00:14:14'),
(1018, 103, 13, '2026-05-04 00:14:14'),
(1019, 104, 3, '2026-05-04 00:14:14'),
(1020, 104, 4, '2026-05-04 00:14:14'),
(1021, 104, 5, '2026-05-04 00:14:14'),
(1022, 104, 6, '2026-05-04 00:14:14'),
(1023, 104, 7, '2026-05-04 00:14:14'),
(1024, 104, 8, '2026-05-04 00:14:14'),
(1025, 104, 9, '2026-05-04 00:14:14'),
(1026, 104, 10, '2026-05-04 00:14:14'),
(1027, 104, 11, '2026-05-04 00:14:14'),
(1028, 104, 12, '2026-05-04 00:14:14'),
(1029, 104, 13, '2026-05-04 00:14:14'),
(1030, 105, 3, '2026-05-04 00:14:14'),
(1031, 105, 4, '2026-05-04 00:14:14'),
(1032, 105, 5, '2026-05-04 00:14:14'),
(1033, 105, 6, '2026-05-04 00:14:14'),
(1034, 105, 7, '2026-05-04 00:14:14'),
(1035, 105, 8, '2026-05-04 00:14:14'),
(1036, 105, 9, '2026-05-04 00:14:14'),
(1037, 105, 10, '2026-05-04 00:14:14'),
(1038, 105, 11, '2026-05-04 00:14:14'),
(1039, 105, 12, '2026-05-04 00:14:14'),
(1040, 105, 13, '2026-05-04 00:14:14'),
(1041, 106, 3, '2026-05-04 00:14:14'),
(1042, 106, 4, '2026-05-04 00:14:14'),
(1043, 106, 5, '2026-05-04 00:14:14'),
(1044, 106, 6, '2026-05-04 00:14:14'),
(1045, 106, 7, '2026-05-04 00:14:14'),
(1046, 106, 8, '2026-05-04 00:14:14'),
(1047, 106, 9, '2026-05-04 00:14:14'),
(1048, 106, 10, '2026-05-04 00:14:14'),
(1049, 106, 11, '2026-05-04 00:14:14'),
(1050, 106, 12, '2026-05-04 00:14:14'),
(1051, 106, 13, '2026-05-04 00:14:14'),
(1052, 107, 3, '2026-05-04 00:14:14'),
(1053, 107, 4, '2026-05-04 00:14:14'),
(1054, 107, 5, '2026-05-04 00:14:14'),
(1055, 107, 6, '2026-05-04 00:14:14'),
(1056, 107, 7, '2026-05-04 00:14:14'),
(1057, 107, 8, '2026-05-04 00:14:14'),
(1058, 107, 9, '2026-05-04 00:14:14'),
(1059, 107, 10, '2026-05-04 00:14:14'),
(1060, 107, 11, '2026-05-04 00:14:14'),
(1061, 107, 12, '2026-05-04 00:14:14'),
(1062, 107, 13, '2026-05-04 00:14:14'),
(1063, 108, 3, '2026-05-04 00:14:14'),
(1064, 108, 4, '2026-05-04 00:14:14'),
(1065, 108, 5, '2026-05-04 00:14:14'),
(1066, 108, 6, '2026-05-04 00:14:14'),
(1067, 108, 7, '2026-05-04 00:14:14'),
(1068, 108, 8, '2026-05-04 00:14:14'),
(1069, 108, 9, '2026-05-04 00:14:14'),
(1070, 108, 10, '2026-05-04 00:14:14'),
(1071, 108, 11, '2026-05-04 00:14:14'),
(1072, 108, 12, '2026-05-04 00:14:14'),
(1073, 108, 13, '2026-05-04 00:14:14'),
(1074, 109, 3, '2026-05-04 00:14:14'),
(1075, 109, 4, '2026-05-04 00:14:14'),
(1076, 109, 5, '2026-05-04 00:14:14'),
(1077, 109, 6, '2026-05-04 00:14:14'),
(1078, 109, 7, '2026-05-04 00:14:14'),
(1079, 109, 8, '2026-05-04 00:14:14'),
(1080, 109, 9, '2026-05-04 00:14:14'),
(1081, 109, 10, '2026-05-04 00:14:14'),
(1082, 109, 11, '2026-05-04 00:14:14'),
(1083, 109, 12, '2026-05-04 00:14:14'),
(1084, 109, 13, '2026-05-04 00:14:14'),
(1085, 110, 3, '2026-05-04 00:14:14'),
(1086, 110, 4, '2026-05-04 00:14:14'),
(1087, 110, 5, '2026-05-04 00:14:14'),
(1088, 110, 6, '2026-05-04 00:14:14'),
(1089, 110, 7, '2026-05-04 00:14:14'),
(1090, 110, 8, '2026-05-04 00:14:14'),
(1091, 110, 9, '2026-05-04 00:14:14'),
(1092, 110, 10, '2026-05-04 00:14:14'),
(1093, 110, 11, '2026-05-04 00:14:14'),
(1094, 110, 12, '2026-05-04 00:14:14'),
(1095, 110, 13, '2026-05-04 00:14:14'),
(1096, 111, 3, '2026-05-04 00:14:14'),
(1097, 111, 4, '2026-05-04 00:14:14'),
(1098, 111, 5, '2026-05-04 00:14:14'),
(1099, 111, 6, '2026-05-04 00:14:14'),
(1100, 111, 7, '2026-05-04 00:14:14'),
(1101, 111, 8, '2026-05-04 00:14:14'),
(1102, 111, 9, '2026-05-04 00:14:14'),
(1103, 111, 10, '2026-05-04 00:14:14'),
(1104, 111, 11, '2026-05-04 00:14:14'),
(1105, 111, 12, '2026-05-04 00:14:14'),
(1106, 111, 13, '2026-05-04 00:14:14'),
(1107, 112, 3, '2026-05-04 00:14:14'),
(1108, 112, 4, '2026-05-04 00:14:14'),
(1109, 112, 5, '2026-05-04 00:14:14'),
(1110, 112, 6, '2026-05-04 00:14:14'),
(1111, 112, 7, '2026-05-04 00:14:14'),
(1112, 112, 8, '2026-05-04 00:14:14'),
(1113, 112, 9, '2026-05-04 00:14:14'),
(1114, 112, 10, '2026-05-04 00:14:14'),
(1115, 112, 11, '2026-05-04 00:14:14'),
(1116, 112, 12, '2026-05-04 00:14:14'),
(1117, 112, 13, '2026-05-04 00:14:14'),
(1118, 113, 3, '2026-05-04 00:14:14'),
(1119, 113, 4, '2026-05-04 00:14:14'),
(1120, 113, 5, '2026-05-04 00:14:14'),
(1121, 113, 6, '2026-05-04 00:14:14'),
(1122, 113, 7, '2026-05-04 00:14:14'),
(1123, 113, 8, '2026-05-04 00:14:14'),
(1124, 113, 9, '2026-05-04 00:14:14'),
(1125, 113, 10, '2026-05-04 00:14:14'),
(1126, 113, 11, '2026-05-04 00:14:14'),
(1127, 113, 12, '2026-05-04 00:14:14'),
(1128, 113, 13, '2026-05-04 00:14:14'),
(1129, 114, 3, '2026-05-04 00:14:14'),
(1130, 114, 4, '2026-05-04 00:14:14'),
(1131, 114, 5, '2026-05-04 00:14:14'),
(1132, 114, 6, '2026-05-04 00:14:14'),
(1133, 114, 7, '2026-05-04 00:14:14'),
(1134, 114, 8, '2026-05-04 00:14:14'),
(1135, 114, 9, '2026-05-04 00:14:14'),
(1136, 114, 10, '2026-05-04 00:14:14'),
(1137, 114, 11, '2026-05-04 00:14:14'),
(1138, 114, 12, '2026-05-04 00:14:14'),
(1139, 114, 13, '2026-05-04 00:14:14'),
(1140, 115, 3, '2026-05-04 00:14:14'),
(1141, 115, 4, '2026-05-04 00:14:14'),
(1142, 115, 5, '2026-05-04 00:14:14'),
(1143, 115, 6, '2026-05-04 00:14:14'),
(1144, 115, 7, '2026-05-04 00:14:14'),
(1145, 115, 8, '2026-05-04 00:14:14'),
(1146, 115, 9, '2026-05-04 00:14:14'),
(1147, 115, 10, '2026-05-04 00:14:14'),
(1148, 115, 11, '2026-05-04 00:14:14'),
(1149, 115, 12, '2026-05-04 00:14:14'),
(1150, 115, 13, '2026-05-04 00:14:14'),
(1151, 116, 3, '2026-05-04 00:14:14'),
(1152, 116, 4, '2026-05-04 00:14:14'),
(1153, 116, 5, '2026-05-04 00:14:14'),
(1154, 116, 6, '2026-05-04 00:14:14'),
(1155, 116, 7, '2026-05-04 00:14:14'),
(1156, 116, 8, '2026-05-04 00:14:14'),
(1157, 116, 9, '2026-05-04 00:14:14'),
(1158, 116, 10, '2026-05-04 00:14:14'),
(1159, 116, 11, '2026-05-04 00:14:14'),
(1160, 116, 12, '2026-05-04 00:14:14'),
(1161, 116, 13, '2026-05-04 00:14:14'),
(1162, 117, 3, '2026-05-04 00:14:14'),
(1163, 117, 4, '2026-05-04 00:14:14'),
(1164, 117, 5, '2026-05-04 00:14:14'),
(1165, 117, 6, '2026-05-04 00:14:14'),
(1166, 117, 7, '2026-05-04 00:14:14'),
(1167, 117, 8, '2026-05-04 00:14:14'),
(1168, 117, 9, '2026-05-04 00:14:14'),
(1169, 117, 10, '2026-05-04 00:14:14'),
(1170, 117, 11, '2026-05-04 00:14:14'),
(1171, 117, 12, '2026-05-04 00:14:14'),
(1172, 117, 13, '2026-05-04 00:14:14'),
(1173, 118, 3, '2026-05-04 00:14:14'),
(1174, 118, 4, '2026-05-04 00:14:14'),
(1175, 118, 5, '2026-05-04 00:14:14'),
(1176, 118, 6, '2026-05-04 00:14:14'),
(1177, 118, 7, '2026-05-04 00:14:14'),
(1178, 118, 8, '2026-05-04 00:14:14'),
(1179, 118, 9, '2026-05-04 00:14:14'),
(1180, 118, 10, '2026-05-04 00:14:14'),
(1181, 118, 11, '2026-05-04 00:14:14'),
(1182, 118, 12, '2026-05-04 00:14:14'),
(1183, 118, 13, '2026-05-04 00:14:14'),
(1184, 119, 3, '2026-05-04 00:14:14'),
(1185, 119, 4, '2026-05-04 00:14:14'),
(1186, 119, 5, '2026-05-04 00:14:14'),
(1187, 119, 6, '2026-05-04 00:14:14'),
(1188, 119, 7, '2026-05-04 00:14:14'),
(1189, 119, 8, '2026-05-04 00:14:14'),
(1190, 119, 9, '2026-05-04 00:14:14'),
(1191, 119, 10, '2026-05-04 00:14:14'),
(1192, 119, 11, '2026-05-04 00:14:14'),
(1193, 119, 12, '2026-05-04 00:14:14'),
(1194, 119, 13, '2026-05-04 00:14:14'),
(1195, 120, 3, '2026-05-04 00:14:14'),
(1196, 120, 4, '2026-05-04 00:14:14'),
(1197, 120, 5, '2026-05-04 00:14:14'),
(1198, 120, 6, '2026-05-04 00:14:14'),
(1199, 120, 7, '2026-05-04 00:14:14'),
(1200, 120, 8, '2026-05-04 00:14:14'),
(1201, 120, 9, '2026-05-04 00:14:14'),
(1202, 120, 10, '2026-05-04 00:14:14'),
(1203, 120, 11, '2026-05-04 00:14:14'),
(1204, 120, 12, '2026-05-04 00:14:14'),
(1205, 120, 13, '2026-05-04 00:14:14'),
(1206, 121, 3, '2026-05-04 00:14:14'),
(1207, 121, 4, '2026-05-04 00:14:14'),
(1208, 121, 5, '2026-05-04 00:14:14'),
(1209, 121, 6, '2026-05-04 00:14:14'),
(1210, 121, 7, '2026-05-04 00:14:14'),
(1211, 121, 8, '2026-05-04 00:14:14'),
(1212, 121, 9, '2026-05-04 00:14:14'),
(1213, 121, 10, '2026-05-04 00:14:14'),
(1214, 121, 11, '2026-05-04 00:14:14'),
(1215, 121, 12, '2026-05-04 00:14:14'),
(1216, 121, 13, '2026-05-04 00:14:14'),
(1217, 122, 3, '2026-05-04 00:14:14'),
(1218, 122, 4, '2026-05-04 00:14:14'),
(1219, 122, 5, '2026-05-04 00:14:14'),
(1220, 122, 6, '2026-05-04 00:14:14'),
(1221, 122, 7, '2026-05-04 00:14:14'),
(1222, 122, 8, '2026-05-04 00:14:14'),
(1223, 122, 9, '2026-05-04 00:14:14'),
(1224, 122, 10, '2026-05-04 00:14:14'),
(1225, 122, 11, '2026-05-04 00:14:14'),
(1226, 122, 12, '2026-05-04 00:14:14'),
(1227, 122, 13, '2026-05-04 00:14:14'),
(1228, 123, 3, '2026-05-04 00:14:14'),
(1229, 123, 4, '2026-05-04 00:14:14'),
(1230, 123, 5, '2026-05-04 00:14:14'),
(1231, 123, 6, '2026-05-04 00:14:14'),
(1232, 123, 7, '2026-05-04 00:14:14'),
(1233, 123, 8, '2026-05-04 00:14:14'),
(1234, 123, 9, '2026-05-04 00:14:14'),
(1235, 123, 10, '2026-05-04 00:14:14'),
(1236, 123, 11, '2026-05-04 00:14:14'),
(1237, 123, 12, '2026-05-04 00:14:14'),
(1238, 123, 13, '2026-05-04 00:14:14'),
(1239, 124, 3, '2026-05-04 00:14:14'),
(1240, 124, 4, '2026-05-04 00:14:14'),
(1241, 124, 5, '2026-05-04 00:14:14'),
(1242, 124, 6, '2026-05-04 00:14:14'),
(1243, 124, 7, '2026-05-04 00:14:14'),
(1244, 124, 8, '2026-05-04 00:14:14'),
(1245, 124, 9, '2026-05-04 00:14:14'),
(1246, 124, 10, '2026-05-04 00:14:14'),
(1247, 124, 11, '2026-05-04 00:14:14'),
(1248, 124, 12, '2026-05-04 00:14:14'),
(1249, 124, 13, '2026-05-04 00:14:14'),
(1250, 125, 3, '2026-05-04 00:14:14'),
(1251, 125, 4, '2026-05-04 00:14:14'),
(1252, 125, 5, '2026-05-04 00:14:14'),
(1253, 125, 6, '2026-05-04 00:14:14'),
(1254, 125, 7, '2026-05-04 00:14:14'),
(1255, 125, 8, '2026-05-04 00:14:14'),
(1256, 125, 9, '2026-05-04 00:14:14'),
(1257, 125, 10, '2026-05-04 00:14:14'),
(1258, 125, 11, '2026-05-04 00:14:14'),
(1259, 125, 12, '2026-05-04 00:14:14'),
(1260, 125, 13, '2026-05-04 00:14:14'),
(1261, 126, 3, '2026-05-04 00:14:14'),
(1262, 126, 4, '2026-05-04 00:14:14'),
(1263, 126, 5, '2026-05-04 00:14:14'),
(1264, 126, 6, '2026-05-04 00:14:14'),
(1265, 126, 7, '2026-05-04 00:14:14'),
(1266, 126, 8, '2026-05-04 00:14:14'),
(1267, 126, 9, '2026-05-04 00:14:14'),
(1268, 126, 10, '2026-05-04 00:14:14'),
(1269, 126, 11, '2026-05-04 00:14:14'),
(1270, 126, 12, '2026-05-04 00:14:14'),
(1271, 126, 13, '2026-05-04 00:14:14'),
(1272, 127, 3, '2026-05-04 00:14:14'),
(1273, 127, 4, '2026-05-04 00:14:14'),
(1274, 127, 5, '2026-05-04 00:14:14'),
(1275, 127, 6, '2026-05-04 00:14:14'),
(1276, 127, 7, '2026-05-04 00:14:14'),
(1277, 127, 8, '2026-05-04 00:14:14'),
(1278, 127, 9, '2026-05-04 00:14:14'),
(1279, 127, 10, '2026-05-04 00:14:14'),
(1280, 127, 11, '2026-05-04 00:14:14'),
(1281, 127, 12, '2026-05-04 00:14:14'),
(1282, 127, 13, '2026-05-04 00:14:14'),
(1283, 128, 3, '2026-05-04 00:14:14'),
(1284, 128, 4, '2026-05-04 00:14:14'),
(1285, 128, 5, '2026-05-04 00:14:14'),
(1286, 128, 6, '2026-05-04 00:14:14'),
(1287, 128, 7, '2026-05-04 00:14:14'),
(1288, 128, 8, '2026-05-04 00:14:14'),
(1289, 128, 9, '2026-05-04 00:14:14'),
(1290, 128, 10, '2026-05-04 00:14:14'),
(1291, 128, 11, '2026-05-04 00:14:14'),
(1292, 128, 12, '2026-05-04 00:14:14'),
(1293, 128, 13, '2026-05-04 00:14:14'),
(1294, 129, 3, '2026-05-04 00:14:14'),
(1295, 129, 4, '2026-05-04 00:14:14'),
(1296, 129, 5, '2026-05-04 00:14:14'),
(1297, 129, 6, '2026-05-04 00:14:14'),
(1298, 129, 7, '2026-05-04 00:14:14'),
(1299, 129, 8, '2026-05-04 00:14:14'),
(1300, 129, 9, '2026-05-04 00:14:14'),
(1301, 129, 10, '2026-05-04 00:14:14'),
(1302, 129, 11, '2026-05-04 00:14:14'),
(1303, 129, 12, '2026-05-04 00:14:14'),
(1304, 129, 13, '2026-05-04 00:14:14'),
(1305, 130, 3, '2026-05-04 00:14:14'),
(1306, 130, 4, '2026-05-04 00:14:14'),
(1307, 130, 5, '2026-05-04 00:14:14'),
(1308, 130, 6, '2026-05-04 00:14:14'),
(1309, 130, 7, '2026-05-04 00:14:14'),
(1310, 130, 8, '2026-05-04 00:14:14'),
(1311, 130, 9, '2026-05-04 00:14:14'),
(1312, 130, 10, '2026-05-04 00:14:14'),
(1313, 130, 11, '2026-05-04 00:14:14'),
(1314, 130, 12, '2026-05-04 00:14:14'),
(1315, 130, 13, '2026-05-04 00:14:14'),
(1316, 131, 3, '2026-05-04 00:14:14'),
(1317, 131, 4, '2026-05-04 00:14:14'),
(1318, 131, 5, '2026-05-04 00:14:14'),
(1319, 131, 6, '2026-05-04 00:14:14'),
(1320, 131, 7, '2026-05-04 00:14:14'),
(1321, 131, 8, '2026-05-04 00:14:14'),
(1322, 131, 9, '2026-05-04 00:14:14'),
(1323, 131, 10, '2026-05-04 00:14:14'),
(1324, 131, 11, '2026-05-04 00:14:14'),
(1325, 131, 12, '2026-05-04 00:14:14'),
(1326, 131, 13, '2026-05-04 00:14:14'),
(1327, 132, 3, '2026-05-04 00:14:14'),
(1328, 132, 4, '2026-05-04 00:14:14'),
(1329, 132, 5, '2026-05-04 00:14:14'),
(1330, 132, 6, '2026-05-04 00:14:14'),
(1331, 132, 7, '2026-05-04 00:14:14'),
(1332, 132, 8, '2026-05-04 00:14:14'),
(1333, 132, 9, '2026-05-04 00:14:14'),
(1334, 132, 10, '2026-05-04 00:14:14'),
(1335, 132, 11, '2026-05-04 00:14:14'),
(1336, 132, 12, '2026-05-04 00:14:14'),
(1337, 132, 13, '2026-05-04 00:14:14'),
(1338, 133, 3, '2026-05-04 00:14:14'),
(1339, 133, 4, '2026-05-04 00:14:14'),
(1340, 133, 5, '2026-05-04 00:14:14'),
(1341, 133, 6, '2026-05-04 00:14:14'),
(1342, 133, 7, '2026-05-04 00:14:14'),
(1343, 133, 8, '2026-05-04 00:14:14'),
(1344, 133, 9, '2026-05-04 00:14:14'),
(1345, 133, 10, '2026-05-04 00:14:14'),
(1346, 133, 11, '2026-05-04 00:14:14'),
(1347, 133, 12, '2026-05-04 00:14:14'),
(1348, 133, 13, '2026-05-04 00:14:14'),
(1349, 134, 3, '2026-05-04 00:14:14'),
(1350, 134, 4, '2026-05-04 00:14:14'),
(1351, 134, 5, '2026-05-04 00:14:14'),
(1352, 134, 6, '2026-05-04 00:14:14'),
(1353, 134, 7, '2026-05-04 00:14:14'),
(1354, 134, 8, '2026-05-04 00:14:14'),
(1355, 134, 9, '2026-05-04 00:14:14'),
(1356, 134, 10, '2026-05-04 00:14:14'),
(1357, 134, 11, '2026-05-04 00:14:14'),
(1358, 134, 12, '2026-05-04 00:14:14'),
(1359, 134, 13, '2026-05-04 00:14:14'),
(1360, 135, 3, '2026-05-04 00:14:14'),
(1361, 135, 4, '2026-05-04 00:14:14'),
(1362, 135, 5, '2026-05-04 00:14:14'),
(1363, 135, 6, '2026-05-04 00:14:14'),
(1364, 135, 7, '2026-05-04 00:14:14'),
(1365, 135, 8, '2026-05-04 00:14:14'),
(1366, 135, 9, '2026-05-04 00:14:14'),
(1367, 135, 10, '2026-05-04 00:14:14'),
(1368, 135, 11, '2026-05-04 00:14:14'),
(1369, 135, 12, '2026-05-04 00:14:14'),
(1370, 135, 13, '2026-05-04 00:14:14'),
(1371, 136, 3, '2026-05-04 00:14:14'),
(1372, 136, 4, '2026-05-04 00:14:14'),
(1373, 136, 5, '2026-05-04 00:14:14'),
(1374, 136, 6, '2026-05-04 00:14:14'),
(1375, 136, 7, '2026-05-04 00:14:14'),
(1376, 136, 8, '2026-05-04 00:14:14'),
(1377, 136, 9, '2026-05-04 00:14:14'),
(1378, 136, 10, '2026-05-04 00:14:14'),
(1379, 136, 11, '2026-05-04 00:14:14'),
(1380, 136, 12, '2026-05-04 00:14:14'),
(1381, 136, 13, '2026-05-04 00:14:14'),
(1382, 137, 3, '2026-05-04 00:14:14'),
(1383, 137, 4, '2026-05-04 00:14:14'),
(1384, 137, 5, '2026-05-04 00:14:14'),
(1385, 137, 6, '2026-05-04 00:14:14'),
(1386, 137, 7, '2026-05-04 00:14:14'),
(1387, 137, 8, '2026-05-04 00:14:14'),
(1388, 137, 9, '2026-05-04 00:14:14'),
(1389, 137, 10, '2026-05-04 00:14:14'),
(1390, 137, 11, '2026-05-04 00:14:14'),
(1391, 137, 12, '2026-05-04 00:14:14'),
(1392, 137, 13, '2026-05-04 00:14:14'),
(1393, 138, 3, '2026-05-04 00:14:14'),
(1394, 138, 4, '2026-05-04 00:14:14'),
(1395, 138, 5, '2026-05-04 00:14:14'),
(1396, 138, 6, '2026-05-04 00:14:14'),
(1397, 138, 7, '2026-05-04 00:14:14'),
(1398, 138, 8, '2026-05-04 00:14:14'),
(1399, 138, 9, '2026-05-04 00:14:14'),
(1400, 138, 10, '2026-05-04 00:14:14'),
(1401, 138, 11, '2026-05-04 00:14:14'),
(1402, 138, 12, '2026-05-04 00:14:14'),
(1403, 138, 13, '2026-05-04 00:14:14'),
(1404, 139, 3, '2026-05-04 00:14:14'),
(1405, 139, 4, '2026-05-04 00:14:14'),
(1406, 139, 5, '2026-05-04 00:14:14'),
(1407, 139, 6, '2026-05-04 00:14:14'),
(1408, 139, 7, '2026-05-04 00:14:14'),
(1409, 139, 8, '2026-05-04 00:14:14'),
(1410, 139, 9, '2026-05-04 00:14:14'),
(1411, 139, 10, '2026-05-04 00:14:14'),
(1412, 139, 11, '2026-05-04 00:14:14'),
(1413, 139, 12, '2026-05-04 00:14:14'),
(1414, 139, 13, '2026-05-04 00:14:14'),
(1415, 140, 3, '2026-05-04 00:14:14'),
(1416, 140, 4, '2026-05-04 00:14:14'),
(1417, 140, 5, '2026-05-04 00:14:14'),
(1418, 140, 6, '2026-05-04 00:14:14'),
(1419, 140, 7, '2026-05-04 00:14:14'),
(1420, 140, 8, '2026-05-04 00:14:14'),
(1421, 140, 9, '2026-05-04 00:14:14'),
(1422, 140, 10, '2026-05-04 00:14:14'),
(1423, 140, 11, '2026-05-04 00:14:14'),
(1424, 140, 12, '2026-05-04 00:14:14'),
(1425, 140, 13, '2026-05-04 00:14:14'),
(1426, 141, 3, '2026-05-04 00:14:14'),
(1427, 141, 4, '2026-05-04 00:14:14'),
(1428, 141, 5, '2026-05-04 00:14:14'),
(1429, 141, 6, '2026-05-04 00:14:14'),
(1430, 141, 7, '2026-05-04 00:14:14'),
(1431, 141, 8, '2026-05-04 00:14:14'),
(1432, 141, 9, '2026-05-04 00:14:14'),
(1433, 141, 10, '2026-05-04 00:14:14'),
(1434, 141, 11, '2026-05-04 00:14:14'),
(1435, 141, 12, '2026-05-04 00:14:14'),
(1436, 141, 13, '2026-05-04 00:14:14'),
(1437, 142, 3, '2026-05-04 00:14:14'),
(1438, 142, 4, '2026-05-04 00:14:14'),
(1439, 142, 5, '2026-05-04 00:14:14'),
(1440, 142, 6, '2026-05-04 00:14:14'),
(1441, 142, 7, '2026-05-04 00:14:14'),
(1442, 142, 8, '2026-05-04 00:14:14'),
(1443, 142, 9, '2026-05-04 00:14:14'),
(1444, 142, 10, '2026-05-04 00:14:14'),
(1445, 142, 11, '2026-05-04 00:14:14'),
(1446, 142, 12, '2026-05-04 00:14:14'),
(1447, 142, 13, '2026-05-04 00:14:14'),
(1448, 143, 3, '2026-05-04 00:14:14'),
(1449, 143, 4, '2026-05-04 00:14:14'),
(1450, 143, 5, '2026-05-04 00:14:14'),
(1451, 143, 6, '2026-05-04 00:14:14'),
(1452, 143, 7, '2026-05-04 00:14:14'),
(1453, 143, 8, '2026-05-04 00:14:14'),
(1454, 143, 9, '2026-05-04 00:14:14'),
(1455, 143, 10, '2026-05-04 00:14:14'),
(1456, 143, 11, '2026-05-04 00:14:14'),
(1457, 143, 12, '2026-05-04 00:14:14');
INSERT INTO `socio_planes` (`id`, `socio_id`, `periodo_id`, `created_at`) VALUES
(1458, 143, 13, '2026-05-04 00:14:14'),
(1459, 144, 3, '2026-05-04 00:14:14'),
(1460, 144, 4, '2026-05-04 00:14:14'),
(1461, 144, 5, '2026-05-04 00:14:14'),
(1462, 144, 6, '2026-05-04 00:14:14'),
(1463, 144, 7, '2026-05-04 00:14:14'),
(1464, 144, 8, '2026-05-04 00:14:14'),
(1465, 144, 9, '2026-05-04 00:14:14'),
(1466, 144, 10, '2026-05-04 00:14:14'),
(1467, 144, 11, '2026-05-04 00:14:14'),
(1468, 144, 12, '2026-05-04 00:14:14'),
(1469, 144, 13, '2026-05-04 00:14:14'),
(1470, 145, 3, '2026-05-04 00:14:14'),
(1471, 145, 4, '2026-05-04 00:14:14'),
(1472, 145, 5, '2026-05-04 00:14:14'),
(1473, 145, 6, '2026-05-04 00:14:14'),
(1474, 145, 7, '2026-05-04 00:14:14'),
(1475, 145, 8, '2026-05-04 00:14:14'),
(1476, 145, 9, '2026-05-04 00:14:14'),
(1477, 145, 10, '2026-05-04 00:14:14'),
(1478, 145, 11, '2026-05-04 00:14:14'),
(1479, 145, 12, '2026-05-04 00:14:14'),
(1480, 145, 13, '2026-05-04 00:14:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_aporte`
--

CREATE TABLE `tipos_aporte` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_aporte`
--

INSERT INTO `tipos_aporte` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Honorario', NULL, 1, '2026-04-18 05:18:31', NULL),
(2, 'Donación', NULL, 1, '2026-04-18 05:18:31', NULL),
(3, 'Cooperación', NULL, 1, '2026-04-18 05:18:31', NULL),
(4, 'Extraordinario', NULL, 1, '2026-04-18 05:18:31', NULL),
(5, 'Apoyo especial', NULL, 1, '2026-04-18 05:18:31', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_egreso`
--

CREATE TABLE `tipos_egreso` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_egreso`
--

INSERT INTO `tipos_egreso` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Rendición', NULL, 1, '2026-04-18 05:18:31', NULL),
(2, 'Gasto operativo', NULL, 1, '2026-04-18 05:18:31', NULL),
(3, 'Mantención', NULL, 1, '2026-04-18 05:18:31', NULL),
(4, 'Compra', NULL, 1, '2026-04-18 05:18:31', NULL),
(5, 'Apoyo social', NULL, 1, '2026-04-18 05:18:31', NULL),
(6, 'Administración', NULL, 1, '2026-04-18 05:18:31', NULL),
(7, 'Otro', NULL, 1, '2026-04-18 05:18:31', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_socio`
--

CREATE TABLE `tipos_socio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_socio`
--

INSERT INTO `tipos_socio` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Regular', NULL, 1, '2026-04-18 05:18:31', NULL),
(2, 'Honorario', NULL, 1, '2026-04-18 05:18:31', NULL),
(3, 'Cooperador', NULL, 1, '2026-04-18 05:18:31', NULL),
(4, 'Exento', NULL, 1, '2026-04-18 05:18:31', NULL),
(5, 'Otro', NULL, 1, '2026-04-18 05:18:31', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(120) DEFAULT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `usuario` varchar(60) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `usuario`, `password`, `rol_id`, `activo`, `ultimo_acceso`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin General', 'admin@local.test', 'admin', '$2y$10$2QSYU4kkt9NhjzP9MryZz.g1GC2wkjI93G8xw6ZV9w6fPzE6lDPL2', 1, 1, NULL, '2026-04-18 04:43:06', NULL, NULL),
(2, 'Tesorero Demo', 'tesorero@local.test', 'tesorero', '$2y$10$2QSYU4kkt9NhjzP9MryZz.g1GC2wkjI93G8xw6ZV9w6fPzE6lDPL2', 2, 1, NULL, '2026-04-18 04:43:06', NULL, NULL),
(3, 'Consulta Demo', 'consulta@local.test', 'consulta', '$2y$10$2QSYU4kkt9NhjzP9MryZz.g1GC2wkjI93G8xw6ZV9w6fPzE6lDPL2', 3, 1, NULL, '2026-04-18 04:43:06', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aportes`
--
ALTER TABLE `aportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `socio_id` (`socio_id`),
  ADD KEY `tipo_aporte_id` (`tipo_aporte_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `conceptos_cobro`
--
ALTER TABLE `conceptos_cobro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuotas`
--
ALTER TABLE `cuotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cuotas_estado` (`estado_cuota`),
  ADD KEY `socio_id` (`socio_id`),
  ADD KEY `periodo_id` (`periodo_id`),
  ADD KEY `concepto_cobro_id` (`concepto_cobro_id`);

--
-- Indices de la tabla `egresos`
--
ALTER TABLE `egresos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_egreso_id` (`tipo_egreso_id`),
  ADD KEY `cuenta_bancaria_id` (`cuenta_bancaria_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `estados_socio`
--
ALTER TABLE `estados_socio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `medios_pago`
--
ALTER TABLE `medios_pago`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `version` (`version`);

--
-- Indices de la tabla `movimientos_tesoreria`
--
ALTER TABLE `movimientos_tesoreria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuenta_bancaria_id` (`cuenta_bancaria_id`),
  ADD KEY `idx_movimientos_fecha` (`fecha`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pago_fecha` (`fecha_pago`),
  ADD KEY `socio_id` (`socio_id`),
  ADD KEY `medio_pago_id` (`medio_pago_id`),
  ADD KEY `cuenta_bancaria_id` (`cuenta_bancaria_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `pago_detalle`
--
ALTER TABLE `pago_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pago_id` (`pago_id`),
  ADD KEY `cuota_id` (`cuota_id`);

--
-- Indices de la tabla `periodos`
--
ALTER TABLE `periodos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rendiciones`
--
ALTER TABLE `rendiciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_rendicion` (`numero_rendicion`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `rendicion_detalle`
--
ALTER TABLE `rendicion_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rendicion_id` (`rendicion_id`),
  ADD KEY `egreso_id` (`egreso_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `socios`
--
ALTER TABLE `socios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_socio` (`numero_socio`),
  ADD UNIQUE KEY `rut` (`rut`),
  ADD KEY `idx_socios_nombre` (`nombre_completo`),
  ADD KEY `idx_socios_rut` (`rut`),
  ADD KEY `tipo_socio_id` (`tipo_socio_id`),
  ADD KEY `estado_socio_id` (`estado_socio_id`);

--
-- Indices de la tabla `socio_planes`
--
ALTER TABLE `socio_planes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_socio_plan` (`socio_id`,`periodo_id`),
  ADD KEY `fk_socio_planes_periodo` (`periodo_id`);

--
-- Indices de la tabla `tipos_aporte`
--
ALTER TABLE `tipos_aporte`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipos_egreso`
--
ALTER TABLE `tipos_egreso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipos_socio`
--
ALTER TABLE `tipos_socio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aportes`
--
ALTER TABLE `aportes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT de la tabla `conceptos_cobro`
--
ALTER TABLE `conceptos_cobro`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuotas`
--
ALTER TABLE `cuotas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT de la tabla `egresos`
--
ALTER TABLE `egresos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estados_socio`
--
ALTER TABLE `estados_socio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `medios_pago`
--
ALTER TABLE `medios_pago`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_tesoreria`
--
ALTER TABLE `movimientos_tesoreria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT de la tabla `pago_detalle`
--
ALTER TABLE `pago_detalle`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT de la tabla `periodos`
--
ALTER TABLE `periodos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `rendiciones`
--
ALTER TABLE `rendiciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rendicion_detalle`
--
ALTER TABLE `rendicion_detalle`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `socios`
--
ALTER TABLE `socios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT de la tabla `socio_planes`
--
ALTER TABLE `socio_planes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1481;

--
-- AUTO_INCREMENT de la tabla `tipos_aporte`
--
ALTER TABLE `tipos_aporte`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipos_egreso`
--
ALTER TABLE `tipos_egreso`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipos_socio`
--
ALTER TABLE `tipos_socio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aportes`
--
ALTER TABLE `aportes`
  ADD CONSTRAINT `aportes_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  ADD CONSTRAINT `aportes_ibfk_2` FOREIGN KEY (`tipo_aporte_id`) REFERENCES `tipos_aporte` (`id`),
  ADD CONSTRAINT `aportes_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `cuotas`
--
ALTER TABLE `cuotas`
  ADD CONSTRAINT `cuotas_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  ADD CONSTRAINT `cuotas_ibfk_2` FOREIGN KEY (`periodo_id`) REFERENCES `periodos` (`id`),
  ADD CONSTRAINT `cuotas_ibfk_3` FOREIGN KEY (`concepto_cobro_id`) REFERENCES `conceptos_cobro` (`id`);

--
-- Filtros para la tabla `egresos`
--
ALTER TABLE `egresos`
  ADD CONSTRAINT `egresos_ibfk_1` FOREIGN KEY (`tipo_egreso_id`) REFERENCES `tipos_egreso` (`id`),
  ADD CONSTRAINT `egresos_ibfk_2` FOREIGN KEY (`cuenta_bancaria_id`) REFERENCES `cuentas_bancarias` (`id`),
  ADD CONSTRAINT `egresos_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `movimientos_tesoreria`
--
ALTER TABLE `movimientos_tesoreria`
  ADD CONSTRAINT `movimientos_tesoreria_ibfk_1` FOREIGN KEY (`cuenta_bancaria_id`) REFERENCES `cuentas_bancarias` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`medio_pago_id`) REFERENCES `medios_pago` (`id`),
  ADD CONSTRAINT `pagos_ibfk_3` FOREIGN KEY (`cuenta_bancaria_id`) REFERENCES `cuentas_bancarias` (`id`),
  ADD CONSTRAINT `pagos_ibfk_4` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pago_detalle`
--
ALTER TABLE `pago_detalle`
  ADD CONSTRAINT `pago_detalle_ibfk_1` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`),
  ADD CONSTRAINT `pago_detalle_ibfk_2` FOREIGN KEY (`cuota_id`) REFERENCES `cuotas` (`id`);

--
-- Filtros para la tabla `rendiciones`
--
ALTER TABLE `rendiciones`
  ADD CONSTRAINT `rendiciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `rendicion_detalle`
--
ALTER TABLE `rendicion_detalle`
  ADD CONSTRAINT `rendicion_detalle_ibfk_1` FOREIGN KEY (`rendicion_id`) REFERENCES `rendiciones` (`id`),
  ADD CONSTRAINT `rendicion_detalle_ibfk_2` FOREIGN KEY (`egreso_id`) REFERENCES `egresos` (`id`);

--
-- Filtros para la tabla `socios`
--
ALTER TABLE `socios`
  ADD CONSTRAINT `socios_ibfk_1` FOREIGN KEY (`tipo_socio_id`) REFERENCES `tipos_socio` (`id`),
  ADD CONSTRAINT `socios_ibfk_2` FOREIGN KEY (`estado_socio_id`) REFERENCES `estados_socio` (`id`);

--
-- Filtros para la tabla `socio_planes`
--
ALTER TABLE `socio_planes`
  ADD CONSTRAINT `fk_socio_planes_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos` (`id`),
  ADD CONSTRAINT `fk_socio_planes_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
