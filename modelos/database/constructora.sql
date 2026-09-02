-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-09-2026 a las 14:16:35
-- Versión del servidor: 10.4.11-MariaDB
-- Versión de PHP: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `constructora`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `registrar_devolucion_herramienta` (IN `p_id_herramienta_obra` INT, IN `p_cantidad` INT, IN `p_id_usuario` INT, IN `p_observaciones` TEXT)  BEGIN

    DECLARE v_cantidad_asignada INT DEFAULT 0;
    DECLARE v_cantidad_devuelta INT DEFAULT 0;
    DECLARE v_pendiente INT DEFAULT 0;

    -- Buscar la asignación
    SELECT
        cantidad_asignada,
        cantidad_devuelta
    INTO
        v_cantidad_asignada,
        v_cantidad_devuelta
    FROM herramienta_obra
    WHERE id_herramienta_obra = p_id_herramienta_obra
    LIMIT 1;

    -- Verificar existencia
    IF v_cantidad_asignada = 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La asignación de herramienta no existe.';

    END IF;


    -- Calcular cantidad pendiente
    SET v_pendiente =
        v_cantidad_asignada - v_cantidad_devuelta;


    -- Validar cantidad
    IF p_cantidad IS NULL OR p_cantidad <= 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad a devolver debe ser mayor a cero.';

    END IF;


    -- Evitar devolver más herramientas de las asignadas
    IF p_cantidad > v_pendiente THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
            'La cantidad devuelta supera la cantidad pendiente.';

    END IF;


    -- Registrar devolución
    INSERT INTO devolucion_herramienta (
        id_herramienta_obra,
        cantidad,
        fecha_devolucion,
        observaciones,
        id_usuario
    )
    VALUES (
        p_id_herramienta_obra,
        p_cantidad,
        CURRENT_TIMESTAMP,
        p_observaciones,
        p_id_usuario
    );


    -- Actualizar cantidad acumulada
    UPDATE herramienta_obra
    SET cantidad_devuelta =
        cantidad_devuelta + p_cantidad
    WHERE id_herramienta_obra = p_id_herramienta_obra;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso_usuario`
--

CREATE TABLE `acceso_usuario` (
  `id_acceso` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_hora_ingreso` datetime NOT NULL,
  `fecha_hora_salida` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `acceso_usuario`
--

INSERT INTO `acceso_usuario` (`id_acceso`, `id_usuario`, `fecha_hora_ingreso`, `fecha_hora_salida`) VALUES
(1, 13, '2026-07-08 23:03:02', NULL),
(2, 15, '2026-07-08 23:05:15', '2026-07-08 23:05:21'),
(3, 15, '2026-07-08 23:06:00', '2026-07-08 23:06:06'),
(4, 13, '2026-07-08 23:11:16', '2026-07-08 23:11:22'),
(5, 8, '2026-07-09 00:38:29', '2026-07-09 00:38:52'),
(6, 15, '2026-07-09 00:38:59', '2026-07-09 00:39:11'),
(7, 24, '2026-07-09 00:39:30', '2026-07-09 00:39:37'),
(8, 9, '2026-07-09 00:41:55', NULL),
(10, 12, '2026-07-09 21:30:42', '2026-07-09 21:31:18'),
(11, 8, '2026-07-09 21:37:39', '2026-07-09 23:25:51'),
(13, 27, '2026-07-09 23:58:39', '2026-07-09 23:59:18'),
(14, 27, '2026-07-09 23:59:31', '2026-07-10 00:00:54'),
(15, 27, '2026-07-10 00:11:24', '2026-07-10 00:15:27'),
(16, 10, '2026-07-10 00:15:49', '2026-07-10 00:20:46'),
(17, 27, '2026-07-10 00:21:04', '2026-07-10 00:34:02'),
(18, 27, '2026-07-10 00:34:08', NULL),
(19, 27, '2026-07-10 15:39:57', '2026-07-10 15:47:32'),
(20, 27, '2026-07-10 15:49:30', '2026-07-10 16:19:11'),
(21, 27, '2026-07-10 16:19:15', '2026-07-10 19:03:53'),
(22, 27, '2026-07-10 19:07:07', '2026-07-10 19:34:21'),
(26, 27, '2026-07-10 19:46:30', '2026-07-10 19:47:01'),
(27, 27, '2026-07-10 19:48:03', '2026-07-10 20:20:16'),
(28, 27, '2026-07-10 20:20:28', NULL),
(29, 27, '2026-07-11 09:25:56', NULL),
(30, 27, '2026-07-13 11:58:06', '2026-07-13 13:14:28'),
(31, 27, '2026-07-13 13:15:20', '2026-07-13 14:17:10'),
(32, 27, '2026-07-13 14:18:14', '2026-07-13 16:20:24'),
(33, 27, '2026-07-13 21:03:05', NULL),
(34, 27, '2026-07-14 11:56:29', NULL),
(35, 27, '2026-07-16 11:28:21', '2026-07-16 11:40:52'),
(36, 28, '2026-07-16 11:41:06', '2026-07-16 13:44:47'),
(37, 28, '2026-07-16 15:02:02', NULL),
(38, 27, '2026-07-16 20:51:53', NULL),
(39, 27, '2026-07-17 11:39:43', '2026-07-17 13:22:08'),
(40, 27, '2026-07-17 21:08:07', NULL),
(41, 27, '2026-07-18 19:41:13', NULL),
(42, 27, '2026-07-18 19:43:14', '2026-07-19 12:36:08'),
(43, 41, '2026-07-19 12:36:26', '2026-07-19 12:36:37'),
(44, 27, '2026-07-19 12:36:43', NULL),
(45, 27, '2026-07-20 22:10:08', NULL),
(46, 27, '2026-07-21 11:22:43', '2026-07-21 18:04:32'),
(47, 20, '2026-07-21 18:06:10', '2026-07-21 18:06:44'),
(48, 16, '2026-07-21 20:25:47', NULL),
(49, 27, '2026-07-22 14:14:11', NULL),
(50, 27, '2026-07-22 16:06:04', NULL),
(51, 27, '2026-07-22 18:27:17', NULL),
(52, 27, '2026-07-22 18:36:49', NULL),
(53, 27, '2026-07-24 11:55:24', NULL),
(54, 27, '2026-07-26 20:07:00', '2026-07-26 20:38:33'),
(55, 27, '2026-07-26 20:48:13', '2026-07-26 21:44:36'),
(56, 27, '2026-07-26 21:44:48', '2026-07-26 21:51:49'),
(57, 27, '2026-07-26 21:52:43', NULL),
(58, 27, '2026-07-26 22:13:53', '2026-07-26 23:12:35'),
(59, 27, '2026-07-27 07:53:48', '2026-07-27 08:06:22'),
(60, 16, '2026-07-27 08:08:13', '2026-07-27 08:14:01'),
(61, 27, '2026-07-27 08:12:10', '2026-07-27 08:13:41'),
(62, 27, '2026-07-27 08:34:36', NULL),
(63, 27, '2026-07-27 08:50:52', '2026-07-27 08:51:03'),
(64, 17, '2026-07-27 08:52:23', '2026-07-27 09:01:09'),
(65, 27, '2026-07-27 08:53:56', '2026-07-27 08:55:40'),
(66, 27, '2026-07-27 10:31:35', '2026-07-27 10:57:39'),
(67, 27, '2026-07-28 12:42:12', NULL),
(68, 27, '2026-07-28 18:37:33', '2026-07-28 23:13:42'),
(69, 41, '2026-07-28 23:13:46', '2026-07-28 23:14:18'),
(70, 27, '2026-07-28 23:14:22', '2026-07-28 23:35:05'),
(71, 27, '2026-07-29 08:03:08', NULL),
(72, 27, '2026-07-29 10:17:19', NULL),
(73, 27, '2026-07-29 13:55:06', '2026-07-29 14:05:09'),
(74, 27, '2026-07-30 10:22:38', '2026-07-30 12:06:10'),
(75, 27, '2026-07-30 12:06:21', '2026-07-30 12:06:39'),
(76, 27, '2026-07-30 20:58:35', '2026-07-30 20:59:15'),
(77, 27, '2026-07-31 07:51:35', '2026-07-31 07:55:17'),
(78, 27, '2026-07-31 07:58:51', '2026-07-31 08:12:27'),
(79, 27, '2026-07-31 08:16:52', '2026-07-31 08:16:55'),
(80, 27, '2026-07-31 08:30:50', '2026-07-31 08:59:20'),
(81, 27, '2026-07-31 08:59:42', '2026-07-31 09:04:20'),
(82, 27, '2026-07-31 09:05:55', '2026-07-31 09:06:29'),
(83, 27, '2026-07-31 09:08:56', '2026-07-31 09:19:10'),
(84, 27, '2026-07-31 10:01:59', '2026-07-31 10:02:32'),
(85, 27, '2026-07-31 10:05:32', '2026-07-31 10:16:39'),
(86, 27, '2026-08-09 20:22:23', NULL),
(87, 27, '2026-08-10 07:43:52', '2026-08-10 10:23:58'),
(88, 27, '2026-08-10 10:26:14', '2026-08-10 10:34:17'),
(89, 27, '2026-08-10 10:39:08', NULL),
(90, 27, '2026-08-10 21:48:00', NULL),
(91, 27, '2026-08-11 15:31:33', NULL),
(92, 27, '2026-08-11 20:06:15', NULL),
(93, 27, '2026-08-12 07:43:51', '2026-08-12 09:11:29'),
(94, 27, '2026-08-12 10:04:41', NULL),
(95, 27, '2026-08-12 11:16:19', NULL),
(96, 27, '2026-08-12 19:24:34', '2026-08-12 20:01:59'),
(97, 47, '2026-08-12 20:02:14', '2026-08-12 20:04:13'),
(98, 27, '2026-08-12 20:04:28', '2026-08-12 20:05:32'),
(99, 9, '2026-08-12 20:06:03', '2026-08-12 20:52:44'),
(100, 38, '2026-08-12 20:53:18', NULL),
(101, 27, '2026-08-13 15:16:46', '2026-08-13 15:35:20'),
(102, 27, '2026-08-13 15:35:46', '2026-08-13 15:36:35'),
(103, 27, '2026-08-14 08:08:29', '2026-08-14 08:41:16'),
(104, 27, '2026-08-14 10:10:06', '2026-08-14 10:14:59'),
(105, 27, '2026-08-14 10:15:14', '2026-08-14 10:23:00'),
(106, 8, '2026-08-14 10:23:08', '2026-08-14 10:24:16'),
(107, 27, '2026-08-14 20:20:47', '2026-08-14 23:08:53'),
(108, 27, '2026-08-14 23:09:35', '2026-08-14 23:10:54'),
(109, 27, '2026-08-15 09:18:05', NULL),
(110, 27, '2026-08-16 13:23:42', '2026-08-16 13:34:55'),
(111, 27, '2026-08-18 08:27:47', '2026-08-18 09:18:05'),
(112, 47, '2026-08-18 09:18:22', '2026-08-18 09:19:53'),
(113, 27, '2026-08-24 07:50:09', NULL),
(114, 27, '2026-08-24 08:10:40', NULL),
(115, 27, '2026-08-24 09:39:13', '2026-08-24 10:58:44'),
(116, 27, '2026-08-25 08:39:12', NULL),
(117, 27, '2026-08-30 14:39:02', NULL),
(118, 27, '2026-08-30 20:08:58', '2026-08-30 22:02:20'),
(119, 27, '2026-08-30 22:03:00', '2026-08-30 22:14:59'),
(120, 27, '2026-08-30 22:23:10', '2026-08-30 22:23:56'),
(121, 27, '2026-09-02 07:48:22', NULL),
(122, 27, '2026-09-02 08:16:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id_auditoria` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla_afectada` varchar(100) DEFAULT NULL,
  `id_registro` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id_auditoria`, `id_usuario`, `accion`, `tabla_afectada`, `id_registro`, `fecha`, `descripcion`) VALUES
(351, 27, 'EDITAR', 'empleado_obra', 10, '2026-08-14 22:04:59', 'Editó la asignación de un empleado en una obra'),
(352, 27, 'EDITAR', 'empleado_obra', 12, '2026-08-14 22:09:53', 'Retiró un empleado de una obra'),
(353, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-14 22:11:06', 'Asignó un empleado a una obra'),
(354, 27, 'EDITAR', 'empleado_obra', 19, '2026-08-14 22:11:16', 'Editó la asignación de un empleado en una obra'),
(355, 27, 'EDITAR', 'empleado_obra', 19, '2026-08-14 22:11:42', 'Retiró un empleado de una obra'),
(356, 27, 'INSERTAR', 'empleado_obra', 47, '2026-08-14 22:16:56', 'Asignó un empleado a una obra'),
(357, 27, 'EDITAR', 'empleado_obra', 20, '2026-08-14 22:17:24', 'Editó la asignación de un empleado en una obra'),
(358, 27, 'EDITAR', 'empleado_obra', 18, '2026-08-14 22:17:57', 'Retiró un empleado de una obra'),
(359, 27, 'INSERTAR', 'empleado_obra', 47, '2026-08-14 22:19:51', 'Asignó un empleado a una obra'),
(360, 27, 'EDITAR', 'empleado_obra', 21, '2026-08-14 22:31:31', 'Retiró al empleado de todas sus obras activas. Motivo: Despido'),
(361, 27, 'EDITAR', 'empleado_obra', 10, '2026-08-14 22:33:33', 'Retiró al empleado de todas sus obras activas. Motivo: Despido'),
(362, 27, 'EDITAR', 'empleado_obra', 17, '2026-08-14 22:35:11', 'Retiró al empleado de la obra actual. Motivo: Despido'),
(363, 27, 'EDITAR', 'empleado_obra', 16, '2026-08-14 22:35:40', 'Retiró al empleado de la obra actual. Motivo: Despido'),
(364, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-14 22:40:34', 'Asignó un empleado a una obra'),
(365, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-14 22:41:06', 'Asignó un empleado a una obra'),
(366, 27, 'EDITAR', 'empleado_obra', 23, '2026-08-14 22:41:12', 'Retiró al empleado de la obra actual. Motivo: Finalización de contrato'),
(367, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-14 22:42:13', 'Asignó un empleado a una obra'),
(368, 27, 'ACTIVAR', 'empleado_obra', 23, '2026-08-14 22:53:51', 'Reactivó al empleado Karina Coronel en una obra'),
(369, 27, 'ACTIVAR', 'empleado_obra', 10, '2026-08-14 22:58:24', 'Reactivó al empleado Karina Coronel en una obra'),
(370, 27, 'ACTIVAR', 'empleado_obra', 12, '2026-08-14 22:58:27', 'Reactivó al empleado Patricia Morales en una obra'),
(371, 27, 'ACTIVAR', 'empleado_obra', 18, '2026-08-14 22:58:31', 'Reactivó al empleado Pedro Martinez en una obra'),
(372, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-14 23:06:16', 'Asignó un empleado a una obra'),
(373, 27, 'EDITAR', 'empleado_obra', 25, '2026-08-14 23:06:27', 'Editó una asignación de empleado'),
(374, 27, 'EDITAR', 'empleado_obra', 25, '2026-08-14 23:07:17', 'Retiró al empleado de las obras seleccionadas. Motivo: Despido'),
(375, 27, 'EDITAR', 'empleado_obra', 22, '2026-08-14 23:07:48', 'Retiró al empleado de la obra actual. Motivo: Accidente / licencia'),
(376, 27, 'ACTIVAR', 'empleado_obra', 22, '2026-08-14 23:07:56', 'Reactivó al empleado Karina Coronel en una obra'),
(377, 27, 'EDITAR', 'empleado_obra', 10, '2026-08-14 23:10:27', 'Retiró al empleado de las obras seleccionadas. Motivo: Accidente / licencia'),
(378, 27, 'ACTIVAR', 'empleado_obra', 10, '2026-08-15 09:18:21', 'Reactivó al empleado Karina Coronel en una obra'),
(379, 27, 'EDITAR', 'empleado_obra', 10, '2026-08-15 09:19:08', 'Retiró al empleado de todas sus obras activas. Motivo: Despido'),
(380, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-16 13:31:28', 'Asignó un empleado a una obra'),
(381, 27, 'EDITAR', 'empleado_obra', 26, '2026-08-16 13:31:43', 'Retiró al empleado de la obra actual. Motivo: Despido'),
(382, 27, 'ACTIVAR', 'empleado_obra', 26, '2026-08-16 13:32:54', 'Reactivó al empleado Karina Coronel en una obra'),
(383, 27, 'EDITAR', 'empleado_obra', 26, '2026-08-16 13:33:45', 'Retiró al empleado de la obra actual. Motivo: Despido'),
(384, 27, 'ACTIVAR', 'empleado_obra', 26, '2026-08-16 13:34:27', 'Reactivó al empleado Karina Coronel en una obra'),
(385, 27, 'INSERTAR', 'empleado_obra', 49, '2026-08-18 08:30:13', 'Asignó un empleado a una obra'),
(386, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-18 08:30:32', 'Asignó un empleado a una obra'),
(387, 27, 'INSERTAR', 'empleado_obra', 48, '2026-08-18 09:10:29', 'Asignó un empleado a una obra'),
(388, 27, 'EDITAR', 'empleado_obra', 26, '2026-08-18 09:11:05', 'Retiró al empleado de todas sus obras activas. Motivo: Despido'),
(389, 27, 'ACTIVAR', 'empleado_obra', 26, '2026-08-18 09:11:45', 'Reactivó al empleado Karina Coronel en una obra'),
(390, 27, 'INSERTAR', 'usuario', 51, '2026-08-18 09:13:20', 'Registró un nuevo usuario'),
(391, 27, 'INSERTAR', 'obra', 19, '2026-08-18 09:13:54', 'Registró la obra: Departamento de roberto'),
(392, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-18 09:14:35', 'Asignó un empleado a una obra'),
(393, 27, 'EDITAR', 'usuario', 27, '2026-08-18 09:15:25', 'Modificó sus datos personales desde el perfil'),
(394, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-24 07:54:14', 'Asignó un empleado a una obra'),
(395, 27, 'ACTIVAR', 'usuario', 10, '2026-08-24 09:50:04', 'Activó nuevamente un usuario'),
(396, 27, 'EDITAR', 'usuario', 9, '2026-08-24 09:50:36', 'Modificó el usuario'),
(397, 27, 'BAJA', 'usuario', 9, '2026-08-24 09:51:08', 'Desactivó un usuario'),
(398, 27, 'INSERTAR', 'obra', 20, '2026-08-24 09:52:01', 'Registró la obra: EPET 7'),
(399, 27, 'EDITAR', 'obra', 20, '2026-08-24 09:52:20', 'Modificó la obra EPET 7'),
(400, 27, 'EDITAR', 'usuario', 10, '2026-08-24 10:02:39', 'Modificó el usuario'),
(401, 27, 'EDITAR', 'usuario', 38, '2026-08-24 10:06:36', 'Modificó el usuario'),
(402, 27, 'EDITAR', 'usuario', 38, '2026-08-24 10:06:50', 'Modificó el usuario'),
(403, 27, 'EDITAR', 'usuario', 10, '2026-08-24 10:37:52', 'Modificó el usuario'),
(404, 27, 'EDITAR', 'usuario', 40, '2026-08-24 10:48:35', 'Modificó el usuario'),
(405, 27, 'EDITAR', 'usuario', 40, '2026-08-24 10:50:10', 'Modificó el usuario'),
(406, 27, 'EDITAR', 'usuario', 40, '2026-08-24 10:51:11', 'Modificó el usuario'),
(407, 27, 'EDITAR', 'usuario', 40, '2026-08-24 10:51:18', 'Modificó el usuario'),
(408, 27, 'EDITAR', 'usuario', 38, '2026-08-25 08:39:49', 'Modificó el usuario'),
(409, 27, 'EDITAR', 'usuario', 40, '2026-08-25 08:40:08', 'Modificó el usuario'),
(410, 27, 'EDITAR', 'usuario', 45, '2026-08-25 08:41:01', 'Modificó el usuario'),
(411, 27, 'ACTIVAR', 'usuario', 13, '2026-08-25 08:49:14', 'Activó nuevamente un usuario'),
(412, 27, 'BAJA', 'usuario', 13, '2026-08-25 08:51:58', 'Desactivó un usuario'),
(413, 27, 'EDITAR', 'obra', 15, '2026-08-25 09:05:53', 'Modificó la obra Refacción de hogar'),
(414, 27, 'BAJA', 'usuario', 22, '2026-08-25 09:06:12', 'Desactivó un usuario'),
(415, 27, 'ACTIVAR', 'usuario', 22, '2026-08-25 09:06:17', 'Activó nuevamente un usuario'),
(416, 27, 'BAJA', 'usuario', 22, '2026-08-30 14:40:24', 'Desactivó un usuario'),
(417, 27, 'ACTIVAR', 'usuario', 22, '2026-08-30 14:40:55', 'Activó nuevamente un usuario'),
(418, 27, 'ACTIVAR', 'usuario', 13, '2026-08-30 14:41:01', 'Activó nuevamente un usuario'),
(419, 27, 'ACTIVAR', 'usuario', 14, '2026-08-30 14:41:07', 'Activó nuevamente un usuario'),
(420, 27, 'ACTIVAR', 'usuario', 9, '2026-08-30 14:41:12', 'Activó nuevamente un usuario'),
(421, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-08-30 15:49:04', 'Asignación de herramienta a obra'),
(422, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-08-30 15:55:50', 'Asignación de herramienta a obra'),
(423, 27, 'INSERTAR', 'herramienta_obra', 38, '2026-08-30 20:38:55', 'Se asignaron 6 unidad(es) de la herramienta ID 36 a la obra ID 9'),
(424, 27, 'INSERTAR', 'devolucion_herramienta', 38, '2026-08-30 20:39:06', 'Registro de devolución de 6 herramienta(s)'),
(425, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-08-30 20:42:23', 'Asignación de herramienta a obra'),
(426, 27, 'INSERTAR', 'devolucion_herramienta', 39, '2026-08-30 20:42:27', 'Registro de devolución de 1 herramienta(s)'),
(427, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-08-30 20:42:38', 'Asignación de herramienta a obra'),
(428, 27, 'INSERTAR', 'devolucion_herramienta', 40, '2026-08-30 20:42:45', 'Registro de devolución de 7 herramienta(s)'),
(429, 27, 'INSERTAR', 'devolucion_herramienta', 40, '2026-08-30 20:46:11', 'Registro de devolución de 2 herramienta(s)'),
(430, 27, 'EDITAR', 'herramienta', 35, '2026-08-30 20:46:38', 'Se actualizaron los datos de la herramienta CINTA'),
(431, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-08-30 20:47:34', 'Asignación de herramienta a obra'),
(432, 27, 'INSERTAR', 'devolucion_herramienta', 41, '2026-08-30 20:48:11', 'Registro de devolución de 3 herramienta(s)'),
(433, 27, 'INSERTAR', 'devolucion_herramienta', 41, '2026-08-30 20:48:49', 'Registro de devolución de 1 herramienta(s)'),
(434, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-08-30 20:55:36', 'Asignación de herramienta a obra'),
(435, 27, 'INSERTAR', 'devolucion_herramienta', 42, '2026-08-30 20:55:56', 'Registro de devolución de 6 herramienta(s)'),
(436, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-08-30 20:58:21', 'Asignación de herramienta a obra'),
(437, 27, 'INSERTAR', 'devolucion_herramienta', 43, '2026-08-30 20:58:28', 'Registro de devolución de 6 herramienta(s)'),
(438, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-08-30 20:59:04', 'Asignación de herramienta a obra'),
(439, 27, 'INSERTAR', 'devolucion_herramienta', 44, '2026-08-30 20:59:12', 'Registro de devolución de 3 herramienta(s)'),
(440, 27, 'INSERTAR', 'herramienta', 38, '2026-08-30 21:06:57', 'Se registró la herramienta CINTA con 6 unidades.'),
(441, 27, 'BAJA', 'usuario', 43, '2026-08-30 21:33:21', 'Desactivó un usuario'),
(442, 27, 'EDITAR', 'usuario', 44, '2026-08-30 21:34:42', 'Modificó el usuario'),
(443, 27, 'ACTIVAR', 'usuario', 43, '2026-08-30 21:37:03', 'Activó nuevamente un usuario'),
(444, 27, 'BAJA', 'usuario', 47, '2026-08-30 22:00:29', 'Desactivó un usuario'),
(445, 27, 'INSERTAR', 'herramienta', 39, '2026-08-30 22:05:21', 'Se registró la herramienta MARTILLO PATA DE CABRA con 19 unidades.'),
(446, 27, 'INSERTAR', 'herramienta', 40, '2026-08-30 22:06:08', 'Se registró la herramienta MAZA 2K con 12 unidades.'),
(447, 27, 'INSERTAR', 'herramienta', 41, '2026-08-30 22:06:49', 'Se registró la herramienta MAZA 5KG con 16 unidades.'),
(448, 27, 'INSERTAR', 'herramienta', 42, '2026-08-30 22:07:42', 'Se registró la herramienta JUEGO DE DESTORNILLADORES con 9 unidades.'),
(449, 27, 'INSERTAR', 'herramienta', 43, '2026-08-30 22:08:37', 'Se registró la herramienta LLAVE FRANCESA con 13 unidades.'),
(450, 27, 'INSERTAR', 'herramienta', 44, '2026-08-30 22:09:18', 'Se registró la herramienta SERRUCHO con 6 unidades.'),
(451, 27, 'INSERTAR', 'herramienta', 45, '2026-08-30 22:10:00', 'Se registró la herramienta FRATACHO con 20 unidades.'),
(452, 27, 'INSERTAR', 'herramienta', 46, '2026-08-30 22:10:53', 'Se registró la herramienta TENAZA con 30 unidades.'),
(453, 27, 'EDITAR', 'herramienta', 46, '2026-08-30 22:11:12', 'Se actualizaron los datos de la herramienta TENAZA'),
(454, 27, 'INSERTAR', 'herramienta', 47, '2026-08-30 22:12:18', 'Se registró la herramienta TALADRO PERCUTOR con 6 unidades.'),
(455, 27, 'INSERTAR', 'herramienta', 48, '2026-08-30 22:13:01', 'Se registró la herramienta TALADRO PERCUTOR con 5 unidades.'),
(456, 27, 'INSERTAR', 'herramienta', 49, '2026-08-30 22:13:52', 'Se registró la herramienta AMOLADORA ANGULAR con 10 unidades.'),
(457, 27, 'INSERTAR', 'herramienta', 50, '2026-08-30 22:14:55', 'Se registró la herramienta AMOLADORA 230MM con 5 unidades.'),
(458, 27, 'BAJA', 'usuario', 43, '2026-09-02 07:57:34', 'Desactivó un usuario'),
(459, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-09-02 07:59:12', 'Asignación de herramienta a obra');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avance_diario`
--

CREATE TABLE `avance_diario` (
  `id_avance_diario` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `avance_diario`
--

INSERT INTO `avance_diario` (`id_avance_diario`, `id_obra`, `fecha`, `descripcion`) VALUES
(22, 9, '2026-07-16', '4 m de contrapiso\r\n'),
(23, 9, '2026-07-16', 'Revoque'),
(27, 9, '2026-07-24', '5 m2 de contrapiso'),
(30, 9, '2026-07-27', 'Contrapiso'),
(31, 9, '2026-07-27', '3m2 de pared'),
(32, 9, '2026-07-31', '3m de contrapiso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo`
--

CREATE TABLE `cargo` (
  `id_cargo` int(11) NOT NULL,
  `nombre_cargo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `cargo`
--

INSERT INTO `cargo` (`id_cargo`, `nombre_cargo`, `descripcion`) VALUES
(1, 'Albañil', 'Ejecuta trabajos generales de construcción.'),
(2, 'Ayudante de Obra', 'Asiste en las tareas de construcción y apoyo al personal.'),
(3, 'Carpintero', 'Realiza trabajos de carpintería en obras.'),
(4, 'Electricista', 'Instala y mantiene sistemas eléctricos.'),
(5, 'Plomero', 'Instala y repara sistemas de agua y desagües.'),
(6, 'Pintor', 'Realiza trabajos de pintura y terminaciones.'),
(7, 'Soldador', 'Realiza trabajos de soldadura en estructuras metálicas.'),
(8, 'Herrero', 'Fabrica e instala estructuras metálicas y herrería.'),
(9, 'Operador de Maquinaria', 'Opera maquinaria pesada utilizada en la construcción.'),
(10, 'Maestro Mayor de Obras', 'Coordina y supervisa aspectos técnicos de la ejecución de la obra.'),
(11, 'Oficial especializado ', 'Tiene conocimiento de todas las actividades (electricidad, plomería, herreria, etc)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobro`
--

CREATE TABLE `cobro` (
  `id_cobro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_obra` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `id_metodo_pago` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_cobrar`
--

CREATE TABLE `cuenta_cobrar` (
  `id_cuenta_cobrar` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('Pendiente','Pagada','Vencida') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_pagar`
--

CREATE TABLE `cuenta_pagar` (
  `id_cuenta_pagar` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('Pendiente','Pagada','Vencida') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_orden`
--

CREATE TABLE `detalle_orden` (
  `id_detalle_orden` int(11) NOT NULL,
  `id_orden` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_presupuesto`
--

CREATE TABLE `detalle_presupuesto` (
  `id_detalle` int(11) NOT NULL,
  `id_presupuesto` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucion_herramienta`
--

CREATE TABLE `devolucion_herramienta` (
  `id_devolucion` int(11) NOT NULL,
  `id_herramienta_obra` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_devolucion` datetime NOT NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `devolucion_herramienta`
--

INSERT INTO `devolucion_herramienta` (`id_devolucion`, `id_herramienta_obra`, `cantidad`, `fecha_devolucion`, `observaciones`, `id_usuario`) VALUES
(11, 38, 6, '2026-08-31 01:39:06', '', 27),
(12, 39, 1, '2026-08-31 01:42:27', '', 27),
(13, 40, 7, '2026-08-31 01:42:45', '', 27),
(14, 40, 2, '2026-08-31 01:46:11', '', 27),
(15, 41, 3, '2026-08-31 01:48:11', '', 27),
(16, 41, 1, '2026-08-31 01:48:49', '', 27),
(17, 42, 6, '2026-08-31 01:55:56', '', 27),
(18, 43, 6, '2026-08-31 01:58:28', '', 27),
(19, 44, 3, '2026-08-31 01:59:12', '', 27);

--
-- Disparadores `devolucion_herramienta`
--
DELIMITER $$
CREATE TRIGGER `after_insert_devolucion_herramienta` AFTER INSERT ON `devolucion_herramienta` FOR EACH ROW BEGIN

    UPDATE herramienta_obra
    SET cantidad_devuelta =
        cantidad_devuelta + NEW.cantidad
    WHERE id_herramienta_obra = NEW.id_herramienta_obra;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_obra`
--

CREATE TABLE `documento_obra` (
  `id_documento` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `tipo_documento` varchar(50) NOT NULL COMMENT 'Plano, Contrato, Permiso, Otro',
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) NOT NULL COMMENT 'Quien subió el archivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_cargo`
--

CREATE TABLE `empleado_cargo` (
  `id_empleado_cargo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleado_cargo`
--

INSERT INTO `empleado_cargo` (`id_empleado_cargo`, `id_usuario`, `id_cargo`) VALUES
(13, 29, 1),
(14, 29, 2),
(26, 47, 8),
(4, 48, 6),
(9, 49, 5),
(8, 49, 6),
(7, 49, 8),
(25, 50, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_obra`
--

CREATE TABLE `empleado_obra` (
  `id_empleado_obra` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_egreso` date DEFAULT NULL,
  `motivo_egreso` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `id_cargo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleado_obra`
--

INSERT INTO `empleado_obra` (`id_empleado_obra`, `id_usuario`, `id_obra`, `fecha_ingreso`, `fecha_egreso`, `motivo_egreso`, `observaciones`, `estado`, `id_cargo`) VALUES
(26, 29, 9, '2026-08-16', NULL, NULL, '', 1, 1),
(27, 49, 9, '2026-08-18', NULL, NULL, '', 1, 6),
(28, 29, 15, '2026-08-18', '2026-08-18', 'Despido', '', 0, 2),
(29, 48, 9, '2026-08-18', NULL, NULL, '', 1, 6),
(30, 29, 19, '2026-08-18', NULL, NULL, '', 1, 2),
(31, 29, 18, '2026-08-24', NULL, NULL, '', 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_herramienta`
--

CREATE TABLE `estado_herramienta` (
  `id_estado_herramienta` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado_herramienta`
--

INSERT INTO `estado_herramienta` (`id_estado_herramienta`, `nombre`) VALUES
(2, 'Asignada'),
(5, 'Devuelta'),
(1, 'Disponible'),
(3, 'En reparación'),
(4, 'Fuera de servicio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etapa_obra`
--

CREATE TABLE `etapa_obra` (
  `id_etapa` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `nombre_etapa` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Pendiente','En Proceso','Finalizada','Cancelada') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `etapa_obra`
--

INSERT INTO `etapa_obra` (`id_etapa`, `id_obra`, `nombre_etapa`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(25, 9, 'Preparación del terreno', 'Limpieza y nivelación del terreno', '2026-07-01', '2026-07-10', 'Finalizada'),
(26, 9, 'Fundaciones', 'Construcción de bases', '2026-07-11', '0000-00-00', 'En Proceso'),
(27, 9, 'Estructura', 'Levantamiento de estructura', '2026-07-17', '2026-07-31', 'Finalizada'),
(28, 9, 'dfghjk', '6u5yrtg', '0000-00-00', '0000-00-00', 'Finalizada'),
(29, 9, 'fggg', 'm', '0000-00-00', '0000-00-00', 'Finalizada'),
(30, 9, 'Fundaciones', '', '0000-00-00', '0000-00-00', 'Finalizada'),
(31, 15, 'Planificación', 'Diseño, planos, permisos y presupuesto.', '2026-08-13', '2026-08-20', 'Finalizada'),
(32, 9, 'Estructura', '', '0000-00-00', '2026-07-31', 'Finalizada'),
(33, 9, 'Contrapisos', '', '2026-07-27', '2026-07-31', 'Finalizada'),
(34, 9, 'ttt', '', '2026-07-31', '0000-00-00', 'Finalizada'),
(35, 9, 'Estructura', '', '2026-08-22', '2026-08-10', 'Finalizada'),
(36, 15, 'Preparación del terreno', 'Limpieza, nivelación y replanteo.', '2026-08-21', '2026-08-28', 'Finalizada'),
(37, 15, 'Cimentación', 'Excavaciones, bases y fundaciones.', '2026-08-31', '2026-09-11', 'Finalizada'),
(38, 15, 'Estructura', 'Columnas, vigas, losas y muros estructurales.', '2026-08-13', '2026-08-27', 'Finalizada'),
(39, 15, 'Mampostería', 'Levantamiento de paredes y divisiones.', '0000-00-00', '0000-00-00', 'Pendiente'),
(40, 15, 'Techado', 'Colocación de techos y cubiertas.', '0000-00-00', '0000-00-00', 'Pendiente'),
(41, 15, 'Instalación eléctrica', 'Cableado, tableros y conexiones eléctricas.', '0000-00-00', '0000-00-00', 'Pendiente'),
(42, 15, 'Instalación sanitaria', 'Agua, desagües y cloacas.', '0000-00-00', '0000-00-00', 'Pendiente'),
(43, 16, 'Estructura', '', '2026-08-20', '2026-08-28', 'Finalizada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id_factura` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `numero_factura` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `total` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foto_obra`
--

CREATE TABLE `foto_obra` (
  `id_foto` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gasto_general`
--

CREATE TABLE `gasto_general` (
  `id_gasto` int(11) NOT NULL,
  `id_presupuesto` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramienta`
--

CREATE TABLE `herramienta` (
  `id_herramienta` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `cantidad_total` int(11) DEFAULT 1,
  `fecha_adquisicion` date DEFAULT NULL,
  `costo` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `herramienta`
--

INSERT INTO `herramienta` (`id_herramienta`, `nombre`, `tipo`, `marca`, `modelo`, `cantidad_total`, `fecha_adquisicion`, `costo`) VALUES
(36, 'Martillo', 'Manual', 'Algo', '22ooj3', 6, '2026-07-18', '3000.00'),
(37, 'Martillo', 'Manual', 'Stanley', 'm13', 10, '2026-07-27', '20000.00'),
(38, 'Cinta', 'Medición', 'Ns', 't43', 6, '2026-08-28', '13000.00'),
(39, 'Martillo pata de cabra', 'Manual', 'Stanley', 'STHT25637', 19, '2026-08-30', '35000.00'),
(40, 'Maza 2k', 'Manual', 'Tramontina', '40567', 12, '2026-08-30', '38000.00'),
(41, 'Maza 5kg', 'Manual', 'Tramontina', '40569', 16, '2026-08-30', '65000.00'),
(42, 'Juego de destornilladores', 'Manual', 'Stanley', 'STHT600377', 9, '2026-08-30', '45000.00'),
(43, 'Llave francesa', 'Manual', 'Bahco', '8071', 13, '2026-08-30', '45000.00'),
(44, 'Serrucho', 'Manual', 'Stanley', '15-166', 6, '2026-08-30', '25000.00'),
(45, 'Fratacho', 'Manual', 'Tramontina', '77380', 20, '2026-08-30', '16000.00'),
(46, 'Tenaza', 'Manual', 'Bahco', '2171G', 30, '2026-08-30', '26000.00'),
(47, 'Taladro percutor', 'Eléntrica', 'Bosch', 'GSB 550 RE', 6, '2026-08-30', '112000.00'),
(48, 'Taladro percutor', 'Eléntrica', 'Bosch', 'GSB 535 RE', 5, '2026-08-30', '158000.00'),
(49, 'Amoladora angular', 'Eléntrica', 'Bosch', 'GWS 740', 10, '2026-08-30', '120000.00'),
(50, 'Amoladora 230mm', 'Eléntrica', 'Bosch', 'GWS 626', 5, '2026-08-30', '80000.00'),
(51, 'Nivel de burbuja', 'Medición', 'Stanley', '42-287', 8, '2026-08-30', '28000.00'),
(52, 'Nivel láser', 'Medición', 'Bosch', 'GLL 2-10', 3, '2026-08-30', '185000.00'),
(53, 'Flexómetro 5m', 'Medición', 'Stanley', 'STHT36115', 15, '2026-08-30', '12000.00'),
(54, 'Flexómetro 8m', 'Medición', 'Stanley', 'STHT30828', 10, '2026-08-30', '18000.00'),
(55, 'Escuadra metálica', 'Medición', 'Stanley', '46-536', 8, '2026-08-30', '15000.00'),
(56, 'Plomada', 'Medición', 'Tramontina', '43120', 10, '2026-08-30', '11000.00'),
(57, 'Alicate universal', 'Manual', 'Stanley', '84-056', 12, '2026-08-30', '22000.00'),
(58, 'Pinza pico de loro', 'Manual', 'Bahco', '8224', 8, '2026-08-30', '42000.00'),
(59, 'Cincel para mampostería', 'Manual', 'Tramontina', '40520', 15, '2026-08-30', '14000.00'),
(60, 'Cortafierro', 'Manual', 'Tramontina', '40518', 10, '2026-08-30', '16000.00'),
(61, 'Pala de punta', 'Manual', 'Tramontina', '77400', 12, '2026-08-30', '28000.00'),
(62, 'Pala ancha', 'Manual', 'Tramontina', '77410', 10, '2026-08-30', '30000.00'),
(63, 'Pico de obra', 'Manual', 'Tramontina', '77450', 8, '2026-08-30', '42000.00'),
(64, 'Azada', 'Manual', 'Tramontina', '77420', 8, '2026-08-30', '26000.00'),
(65, 'Carretilla de obra', 'Transporte', 'Tramontina', '77700', 6, '2026-08-30', '95000.00'),
(66, 'Carretilla reforzada', 'Transporte', 'Tramontina', '77701', 4, '2026-08-30', '125000.00'),
(67, 'Mezcladora de cemento', 'Maquinaria', 'Lusqtoff', 'MC-130', 2, '2026-08-30', '850000.00'),
(68, 'Hormigonera', 'Maquinaria', 'Gamma', 'G2800', 2, '2026-08-30', '980000.00'),
(69, 'Compactador tipo canguro', 'Maquinaria', 'Wacker Neuson', 'BS 60-2', 2, '2026-08-30', '2500000.00'),
(70, 'Vibrador de hormigón', 'Maquinaria', 'Lusqtoff', 'VIB-1500', 3, '2026-08-30', '450000.00'),
(71, 'Generador eléctrico', 'Maquinaria', 'Gamma', 'G6500', 2, '2026-08-30', '1200000.00'),
(72, 'Hidrolavadora', 'Eléntrica', 'Karcher', 'K3 Power', 3, '2026-08-30', '320000.00'),
(73, 'Rotomartillo', 'Eléntrica', 'Bosch', 'GBH 2-26 DRE', 4, '2026-08-30', '350000.00'),
(74, 'Sierra circular', 'Eléntrica', 'Bosch', 'GKS 130', 4, '2026-08-30', '210000.00'),
(75, 'Sierra caladora', 'Eléntrica', 'Bosch', 'GST 700', 4, '2026-08-30', '180000.00'),
(76, 'Lijadora orbital', 'Eléntrica', 'Bosch', 'GEX 125-1 AE', 3, '2026-08-30', '190000.00'),
(77, 'Atornillador eléctrico', 'Eléntrica', 'Makita', 'DF0300', 5, '2026-08-30', '220000.00'),
(78, 'Llave de impacto', 'Eléntrica', 'Makita', 'TW1000', 2, '2026-08-30', '680000.00'),
(79, 'Compresor de aire', 'Maquinaria', 'Gamma', 'G2800', 2, '2026-08-30', '750000.00'),
(80, 'Escalera de aluminio', 'Altura', 'Werner', '7408', 5, '2026-08-30', '180000.00'),
(81, 'Escalera extensible', 'Altura', 'Werner', 'D6224-2', 3, '2026-08-30', '350000.00'),
(82, 'Andamio tubular', 'Altura', 'Layher', 'Allround', 10, '2026-08-30', '280000.00'),
(83, 'Plataforma de trabajo', 'Altura', 'Werner', 'AP-25', 4, '2026-08-30', '420000.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramienta_obra`
--

CREATE TABLE `herramienta_obra` (
  `id_herramienta_obra` int(11) NOT NULL,
  `id_herramienta` int(11) NOT NULL,
  `cantidad_asignada` int(11) NOT NULL DEFAULT 1,
  `cantidad_devuelta` int(11) NOT NULL DEFAULT 0,
  `id_obra` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `id_estado_herramienta` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `herramienta_obra`
--

INSERT INTO `herramienta_obra` (`id_herramienta_obra`, `id_herramienta`, `cantidad_asignada`, `cantidad_devuelta`, `id_obra`, `fecha_asignacion`, `fecha_devolucion`, `observaciones`, `id_estado_herramienta`) VALUES
(38, 36, 6, 12, 9, '2026-08-30', NULL, '', 1),
(39, 36, 1, 1, 9, '2026-08-30', NULL, '', 5),
(40, 37, 9, 9, 9, '2026-08-30', NULL, '', 5),
(41, 36, 4, 4, 9, '2026-08-30', NULL, '', 5),
(42, 36, 6, 6, 9, '2026-08-30', NULL, '', 5),
(43, 36, 6, 6, 9, '2026-08-30', NULL, '', 5),
(44, 37, 7, 3, 9, '2026-08-30', NULL, '', 2),
(45, 50, 1, 0, 9, '2026-09-02', NULL, '', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_estado_obra`
--

CREATE TABLE `historial_estado_obra` (
  `id_historial` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horas_trabajadas`
--

CREATE TABLE `horas_trabajadas` (
  `id_hora` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `cantidad_horas` decimal(5,2) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencia`
--

CREATE TABLE `incidencia` (
  `id_incidencia` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tipo_incidencia` enum('Material','Seguridad','Clima','Herramientas','Personal','Cliente','Diseño/Planos','Retraso') DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Pendiente','En revisión','Resuelta') DEFAULT 'Pendiente',
  `solucion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso`
--

CREATE TABLE `ingreso` (
  `id_ingreso` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_obra` int(11) DEFAULT NULL,
  `id_cobro` int(11) DEFAULT NULL COMMENT 'Si el ingreso viene de un cobro registrado',
  `descripcion` varchar(255) NOT NULL,
  `fecha` date NOT NULL,
  `monto` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mano_obra_presupuesto`
--

CREATE TABLE `mano_obra_presupuesto` (
  `id_mano_obra_pres` int(11) NOT NULL,
  `id_presupuesto` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad_horas` decimal(8,2) NOT NULL,
  `costo_hora` decimal(10,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimiento`
--

CREATE TABLE `mantenimiento` (
  `id_mantenimiento` int(11) NOT NULL,
  `id_herramienta` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo` decimal(12,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `estado` enum('Pendiente','En proceso','Finalizado') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material`
--

CREATE TABLE `material` (
  `id_material` int(11) NOT NULL,
  `nombre_material` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `stock` decimal(10,2) DEFAULT 0.00,
  `stock_minimo` decimal(10,2) DEFAULT 0.00,
  `unidad_medida` varchar(20) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `material`
--

INSERT INTO `material` (`id_material`, `nombre_material`, `descripcion`, `stock`, `stock_minimo`, `unidad_medida`, `estado`) VALUES
(1, 'Cemento Portland 50 kg', 'Bolsa de cemento Portland de 50 kg.', '250.00', '300.00', 'Bolsa', 1),
(2, 'Arena fina', 'Arena fina para revoques y terminaciones.', '80.00', '0.00', 'm³', 0),
(3, 'Arena gruesa', 'Arena gruesa para hormigón.', '120.00', '0.00', 'm³', 0),
(4, 'Piedra partida', 'Piedra para elaboración de hormigón.', '90.00', '0.00', 'm³', 1),
(5, 'Cal hidratada', 'Cal para mezclas de albañilería.', '120.00', '200.00', 'Bolsa', 1),
(6, 'Ladrillo común', 'Ladrillo macizo de arcilla.', '8000.00', '0.00', 'Unidad', 1),
(7, 'Ladrillo hueco 18x18x33', 'Ladrillo cerámico hueco.', '4500.00', '0.00', 'Unidad', 0),
(8, 'Hierro 6 mm', 'Varilla de acero de 6 mm.', '350.00', '300.00', 'Unidad', 1),
(9, 'Hierro 8 mm', 'Varilla de acero de 8 mm.', '300.00', '340.00', 'Unidad', 1),
(10, 'Hierro 10 mm', 'Varilla de acero de 10 mm.', '250.00', '0.00', 'Unidad', 1),
(11, 'Hierro 12 mm', 'Varilla de acero de 12 mm.', '180.00', '150.00', 'Unidad', 0),
(12, 'Malla electrosoldada', 'Malla para refuerzo de losas.', '70.00', '0.00', 'Unidad', 1),
(13, 'Alambre recocido', 'Alambre para atado de armaduras.', '80.16', '50.00', 'Rollo', 0),
(14, 'Clavo 2\"', 'Clavo de acero de 2 pulgadas.', '50.00', '0.00', 'Kg', 1),
(15, 'Clavo 3\"', 'Clavo de acero de 3 pulgadas.', '40.00', '0.00', 'Kg', 0),
(16, 'Tornillo autoperforante', 'Tornillo para chapa galvanizada.', '5000.00', '0.00', 'Unidad', 1),
(17, 'Caño PVC 50 mm', 'Caño sanitario de PVC.', '120.00', '0.00', 'Unidad', 0),
(18, 'Caño PVC 110 MM', 'Caño sanitario de PVC.', '80.00', '100.00', 'Unidad', 1),
(19, 'Codo PVC 90°', 'Accesorio para instalaciones sanitarias.', '150.00', '0.00', 'Unidad', 1),
(20, 'Cable unipolar 2,5 mm²', 'Cable para instalación eléctrica.', '1000.00', '0.00', 'Metro', 0),
(21, 'Cable unipolar 4 mm²', 'Cable eléctrico de mayor sección.', '700.00', '0.00', 'Metro', 0),
(22, 'Interruptor térmico', 'Protección para circuitos eléctricos.', '45.00', '0.00', 'Unidad', 1),
(23, 'Llave de luz', 'Interruptor simple de embutir.', '120.00', '0.00', 'Unidad', 0),
(24, 'Pintura látex interior', 'Pintura para interiores.', '80.00', '0.00', 'Balde', 1),
(25, 'Pintura látex exterior', 'Pintura para exteriores.', '60.00', '80.00', 'Balde', 1),
(26, 'Membrana asfáltica', 'Membrana impermeabilizante.', '45.00', '0.00', 'Rollo', 1),
(27, 'Cerámica 45x45 cm', 'Piso cerámico.', '900.00', '0.00', 'm²', 1),
(28, 'Adhesivo para cerámicos', 'Pegamento para revestimientos.', '160.00', '200.00', 'Bolsa', 1),
(29, 'Pastina', 'Material para juntas de cerámicos.', '90.00', '0.00', 'Bolsa', 1),
(30, 'Chapa galvanizada', 'Chapa para cubiertas.', '130.00', '0.00', 'Unidad', 1),
(31, 'Cinta aislante', 'Cinta aislante', '300.00', '100.00', 'Unidad', 1),
(33, 'Hierro torcionado 8 mm', 'Hierro para armar cimientos', '200.00', '100.00', 'Metro', 1),
(34, 'fghj', 'jhgf', '440.00', '300.00', 'Unidad', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material_obra`
--

CREATE TABLE `material_obra` (
  `id_material_obra` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad_medida` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id_metodo_pago` int(11) NOT NULL,
  `nombre_metodo` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_caja`
--

CREATE TABLE `movimiento_caja` (
  `id_movimiento_caja` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que registró el movimiento',
  `id_obra` int(11) DEFAULT NULL COMMENT 'NULL si es movimiento general de empresa',
  `fecha` datetime DEFAULT current_timestamp(),
  `tipo_movimiento` varchar(50) NOT NULL COMMENT 'Ingreso, Egreso',
  `descripcion` varchar(255) DEFAULT NULL,
  `monto` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_inventario`
--

CREATE TABLE `movimiento_inventario` (
  `id_movimiento` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `tipo_movimiento` varchar(50) NOT NULL COMMENT 'Entrada, Salida, Ajuste',
  `cantidad` decimal(10,2) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_material`
--

CREATE TABLE `movimiento_material` (
  `id_movimiento` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo` enum('INGRESO','EGRESO') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `movimiento_material`
--

INSERT INTO `movimiento_material` (`id_movimiento`, `id_material`, `id_usuario`, `tipo`, `cantidad`, `fecha`, `observacion`) VALUES
(1, 28, 27, 'INGRESO', '40.00', '2026-07-21 17:10:03', ''),
(2, 28, 27, 'INGRESO', '100.00', '2026-07-21 17:11:00', ''),
(3, 28, 27, 'EGRESO', '100.00', '2026-07-21 17:11:31', ''),
(4, 13, 27, 'EGRESO', '19.84', '2026-08-14 08:39:25', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `obra`
--

CREATE TABLE `obra` (
  `id_obra` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_jefe_obra` int(11) DEFAULT NULL,
  `nombre_obra` varchar(150) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Planificacion','En Proceso','Finalizada','Cancelada','Suspendida') DEFAULT 'Planificacion',
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `obra`
--

INSERT INTO `obra` (`id_obra`, `id_usuario`, `id_jefe_obra`, `nombre_obra`, `direccion`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `activo`) VALUES
(9, 34, 22, 'Quincho Amyra', 'Senador Emilio Tomás Barrio Eva Perón', 'Casa tipo quinta.', '2026-07-24', '0000-00-00', 'En Proceso', 1),
(15, 26, 12, 'Refacción de hogar', 'Av. Senador Emelio Tomas, Barrio Eva Peron, Mz 10 Cs 22', '', '2026-08-12', '0000-00-00', 'En Proceso', 1),
(16, 26, NULL, 'Casa de Dylan', 'Barrio 8 de octubre', '', '0000-00-00', '0000-00-00', 'Finalizada', 1),
(17, 40, NULL, 'iliukyjhre', 'iuytgfred', 'kjhgfds', '0000-00-00', '0000-00-00', 'Planificacion', 1),
(18, 17, NULL, 'Refacción de la E.P.E.S N° 5', 'Senador Emilio Tomas', 'Refacción de las instalaciones.', '2026-07-27', '0000-00-00', 'En Proceso', 1),
(19, 51, NULL, 'Departamento de roberto', 'Av italia', '', '2026-08-19', '0000-00-00', 'En Proceso', 1),
(20, 10, 12, 'EPET 7', 'RRH', '', '0000-00-00', '0000-00-00', 'En Proceso', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_compra`
--

CREATE TABLE `orden_compra` (
  `id_orden` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total` decimal(15,2) DEFAULT 0.00,
  `estado` enum('Pendiente','Aprobada','Recibida','Cancelada') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `id_metodo_pago` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre_permiso` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_permiso`) VALUES
(10, 'usuarios'),
(11, 'roles'),
(12, 'obras'),
(13, 'clientes'),
(14, 'empleados'),
(15, 'materiales'),
(16, 'herramientas'),
(17, 'inventario'),
(18, 'presupuestos'),
(19, 'costos'),
(20, 'documentos'),
(21, 'avances'),
(22, 'tareas'),
(23, 'asistencia'),
(24, 'incidencias'),
(25, 'reportes'),
(26, 'caja'),
(27, 'cuentas cobrar'),
(28, 'cuentas pagar'),
(29, 'proveedores'),
(30, 'pagos'),
(31, 'perfil');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precio_material`
--

CREATE TABLE `precio_material` (
  `id_precio` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `fecha_actualizacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `precio_material`
--

INSERT INTO `precio_material` (`id_precio`, `id_material`, `id_proveedor`, `precio`, `fecha_actualizacion`) VALUES
(55, 1, 1, '9800.00', '2026-01-01 00:00:00'),
(56, 2, 1, '45000.00', '2026-01-01 00:00:00'),
(57, 3, 1, '52000.00', '2026-01-01 00:00:00'),
(58, 4, 1, '980.00', '2026-01-01 00:00:00'),
(59, 5, 1, '13800.00', '2026-01-01 00:00:00'),
(60, 6, 1, '16900.00', '2026-01-01 00:00:00'),
(61, 5, 2, '13650.00', '2026-01-01 00:00:00'),
(62, 6, 2, '16750.00', '2026-01-01 00:00:00'),
(63, 11, 2, '1480.00', '2026-01-01 00:00:00'),
(64, 12, 2, '3950.00', '2026-01-01 00:00:00'),
(65, 13, 2, '19750.00', '2026-01-01 00:00:00'),
(66, 8, 3, '8700.00', '2026-01-01 00:00:00'),
(67, 9, 3, '28500.00', '2026-01-01 00:00:00'),
(68, 10, 4, '18500.00', '2026-01-01 00:00:00'),
(69, 15, 5, '32000.00', '2026-01-01 00:00:00'),
(70, 1, 6, '9600.00', '2026-01-01 00:00:00'),
(71, 2, 6, '44000.00', '2026-01-01 00:00:00'),
(72, 3, 6, '51000.00', '2026-01-01 00:00:00'),
(73, 4, 6, '950.00', '2026-01-01 00:00:00'),
(74, 7, 6, '7100.00', '2026-01-01 00:00:00'),
(75, 10, 6, '18200.00', '2026-01-01 00:00:00'),
(76, 11, 7, '1430.00', '2026-01-01 00:00:00'),
(77, 12, 7, '3850.00', '2026-01-01 00:00:00'),
(78, 5, 8, '13450.00', '2026-01-01 00:00:00'),
(79, 6, 8, '16600.00', '2026-01-01 00:00:00'),
(80, 13, 8, '19500.00', '2026-01-01 00:00:00'),
(81, 14, 8, '49000.00', '2026-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto`
--

CREATE TABLE `presupuesto` (
  `id_presupuesto` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `version` int(11) DEFAULT 1,
  `costo_total` decimal(15,2) DEFAULT 0.00,
  `estado` enum('Activo','Inactivo','Aprobado','Rechazado') DEFAULT 'Activo',
  `detalle_general` text DEFAULT NULL COMMENT 'RF extra: observaciones',
  `fecha_aprobacion` date DEFAULT NULL,
  `id_usuario_aprobacion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_proveedor` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nombre_proveedor`, `telefono`, `direccion`, `correo`) VALUES
(1, 'Corralón San Miguel', '3704123456', 'Av. Gendarmería Nacional 1450, Formosa', 'ventas@corralonsanmiguel.com'),
(2, 'Corralón El Constructor', '3704234567', 'Av. Italia 980, Formosa', 'contacto@elconstructor.com'),
(3, 'Ferretería Industrial Norte', '3704345678', 'Av. 9 de Julio 2130, Formosa', 'ventas@industrialnorte.com'),
(4, 'Materiales Formosa S.R.L.', '3704456789', 'Av. Pantaleón Gómez 1675, Formosa', 'info@materialesformosa.com'),
(5, 'Electricidad Norte', '3704567890', 'Av. González Lelong 1040, Formosa', 'ventas@electricidadnorte.com'),
(6, 'Sanitarios del Litoral', '3704678901', 'Av. Independencia 870, Formosa', 'contacto@sanitarioslitoral.com'),
(7, 'Pinturería Color Hogar', '3704789012', 'Av. Kirchner 650, Formosa', 'ventas@colorhogar.com'),
(8, 'Aceros del Norte', '3704890123', 'Ruta Nacional 11 Km 1188, Formosa', 'info@acerosdelnorte.com'),
(9, 'Hormigones Formosa', '3704901234', 'Parque Industrial, Formosa', 'ventas@hormigonesformosa.com'),
(10, 'Distribuidora ConstruMax', '3704012345', 'Av. Néstor Kirchner 2150, Formosa', 'administracion@construmax.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor_material`
--

CREATE TABLE `proveedor_material` (
  `id_proveedor_material` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_material` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `proveedor_material`
--

INSERT INTO `proveedor_material` (`id_proveedor_material`, `id_proveedor`, `id_material`) VALUES
(28, 1, 1),
(29, 1, 2),
(30, 1, 3),
(31, 1, 4),
(32, 1, 5),
(33, 1, 6),
(34, 2, 5),
(35, 2, 6),
(36, 2, 11),
(37, 2, 12),
(38, 2, 13),
(39, 3, 9),
(40, 3, 8),
(41, 4, 10),
(42, 5, 15),
(43, 6, 1),
(44, 6, 2),
(45, 6, 3),
(46, 6, 4),
(47, 6, 7),
(48, 6, 10),
(49, 7, 11),
(50, 7, 12),
(51, 8, 5),
(52, 8, 6),
(53, 8, 13),
(54, 8, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte`
--

CREATE TABLE `reporte` (
  `id_reporte` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que generó el reporte',
  `id_obra` int(11) DEFAULT NULL COMMENT 'NULL si es reporte general de la empresa',
  `tipo_reporte` varchar(100) NOT NULL,
  `fecha_generacion` datetime DEFAULT current_timestamp(),
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Empleado', 'Realiza las tareas asignadas dentro de las obras de construcción.'),
(2, 'Gerente', 'Administra obras, clientes, empleados, presupuestos y reportes.'),
(3, 'Administrativo', 'Gestiona clientes, documentos, presupuestos, cobros y pagos.'),
(4, 'Jefe de Obra', 'Supervisa el avance de las obras y coordina empleados.'),
(5, 'Encargado de Depósito', 'Administra materiales, herramientas e inventario.'),
(6, 'Cliente', 'Consulta el estado de sus obras, documentos y presupuestos.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permiso`
--

CREATE TABLE `rol_permiso` (
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `rol_permiso`
--

INSERT INTO `rol_permiso` (`id_rol`, `id_permiso`) VALUES
(1, 22),
(1, 23),
(1, 24),
(1, 31),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31),
(3, 10),
(3, 13),
(3, 14),
(3, 18),
(3, 19),
(3, 20),
(3, 25),
(3, 26),
(3, 27),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(4, 12),
(4, 14),
(4, 15),
(4, 16),
(4, 17),
(4, 20),
(4, 21),
(4, 22),
(4, 23),
(4, 24),
(4, 25),
(4, 31),
(5, 15),
(5, 16),
(5, 17),
(5, 29),
(5, 31),
(6, 12),
(6, 18),
(6, 20),
(6, 21),
(6, 25),
(6, 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_material`
--

CREATE TABLE `solicitud_material` (
  `id_solicitud` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('Pendiente','Aprobada','Rechazada','Entregada') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea`
--

CREATE TABLE `tarea` (
  `id_tarea` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_herramienta`
--

CREATE TABLE `unidad_herramienta` (
  `id_unidad` int(11) NOT NULL,
  `id_herramienta` int(11) NOT NULL,
  `id_herramienta_obra` int(11) DEFAULT NULL,
  `numero_unidad` int(11) NOT NULL,
  `id_estado_herramienta` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `unidad_herramienta`
--

INSERT INTO `unidad_herramienta` (`id_unidad`, `id_herramienta`, `id_herramienta_obra`, `numero_unidad`, `id_estado_herramienta`) VALUES
(8, 36, NULL, 1, 1),
(9, 36, NULL, 2, 1),
(10, 36, NULL, 3, 1),
(11, 36, NULL, 4, 1),
(12, 36, NULL, 5, 1),
(13, 36, NULL, 6, 1),
(14, 37, NULL, 1, 1),
(15, 37, NULL, 2, 1),
(16, 37, NULL, 3, 1),
(17, 37, NULL, 4, 2),
(18, 37, NULL, 5, 2),
(19, 37, NULL, 6, 2),
(20, 37, NULL, 7, 2),
(21, 37, NULL, 8, 1),
(22, 37, NULL, 9, 1),
(23, 37, NULL, 10, 1),
(24, 38, NULL, 1, 1),
(25, 38, NULL, 2, 1),
(26, 38, NULL, 3, 1),
(27, 38, NULL, 4, 1),
(28, 38, NULL, 5, 1),
(29, 38, NULL, 6, 1),
(30, 39, NULL, 1, 1),
(31, 39, NULL, 2, 1),
(32, 39, NULL, 3, 1),
(33, 39, NULL, 4, 1),
(34, 39, NULL, 5, 1),
(35, 39, NULL, 6, 1),
(36, 39, NULL, 7, 1),
(37, 39, NULL, 8, 1),
(38, 39, NULL, 9, 1),
(39, 39, NULL, 10, 1),
(40, 39, NULL, 11, 1),
(41, 39, NULL, 12, 1),
(42, 39, NULL, 13, 1),
(43, 39, NULL, 14, 1),
(44, 39, NULL, 15, 1),
(45, 39, NULL, 16, 1),
(46, 39, NULL, 17, 1),
(47, 39, NULL, 18, 1),
(48, 39, NULL, 19, 1),
(49, 40, NULL, 1, 1),
(50, 40, NULL, 2, 1),
(51, 40, NULL, 3, 1),
(52, 40, NULL, 4, 1),
(53, 40, NULL, 5, 1),
(54, 40, NULL, 6, 1),
(55, 40, NULL, 7, 1),
(56, 40, NULL, 8, 1),
(57, 40, NULL, 9, 1),
(58, 40, NULL, 10, 1),
(59, 40, NULL, 11, 1),
(60, 40, NULL, 12, 1),
(61, 41, NULL, 1, 1),
(62, 41, NULL, 2, 1),
(63, 41, NULL, 3, 1),
(64, 41, NULL, 4, 1),
(65, 41, NULL, 5, 1),
(66, 41, NULL, 6, 1),
(67, 41, NULL, 7, 1),
(68, 41, NULL, 8, 1),
(69, 41, NULL, 9, 1),
(70, 41, NULL, 10, 1),
(71, 41, NULL, 11, 1),
(72, 41, NULL, 12, 1),
(73, 41, NULL, 13, 1),
(74, 41, NULL, 14, 1),
(75, 41, NULL, 15, 1),
(76, 41, NULL, 16, 1),
(77, 42, NULL, 1, 1),
(78, 42, NULL, 2, 1),
(79, 42, NULL, 3, 1),
(80, 42, NULL, 4, 1),
(81, 42, NULL, 5, 1),
(82, 42, NULL, 6, 1),
(83, 42, NULL, 7, 1),
(84, 42, NULL, 8, 1),
(85, 42, NULL, 9, 1),
(86, 43, NULL, 1, 1),
(87, 43, NULL, 2, 1),
(88, 43, NULL, 3, 1),
(89, 43, NULL, 4, 1),
(90, 43, NULL, 5, 1),
(91, 43, NULL, 6, 1),
(92, 43, NULL, 7, 1),
(93, 43, NULL, 8, 1),
(94, 43, NULL, 9, 1),
(95, 43, NULL, 10, 1),
(96, 43, NULL, 11, 1),
(97, 43, NULL, 12, 1),
(98, 43, NULL, 13, 1),
(99, 44, NULL, 1, 1),
(100, 44, NULL, 2, 1),
(101, 44, NULL, 3, 1),
(102, 44, NULL, 4, 1),
(103, 44, NULL, 5, 1),
(104, 44, NULL, 6, 1),
(105, 45, NULL, 1, 1),
(106, 45, NULL, 2, 1),
(107, 45, NULL, 3, 1),
(108, 45, NULL, 4, 1),
(109, 45, NULL, 5, 1),
(110, 45, NULL, 6, 1),
(111, 45, NULL, 7, 1),
(112, 45, NULL, 8, 1),
(113, 45, NULL, 9, 1),
(114, 45, NULL, 10, 1),
(115, 45, NULL, 11, 1),
(116, 45, NULL, 12, 1),
(117, 45, NULL, 13, 1),
(118, 45, NULL, 14, 1),
(119, 45, NULL, 15, 1),
(120, 45, NULL, 16, 1),
(121, 45, NULL, 17, 1),
(122, 45, NULL, 18, 1),
(123, 45, NULL, 19, 1),
(124, 45, NULL, 20, 1),
(125, 46, NULL, 1, 1),
(126, 46, NULL, 2, 1),
(127, 46, NULL, 3, 1),
(128, 46, NULL, 4, 1),
(129, 46, NULL, 5, 1),
(130, 46, NULL, 6, 1),
(131, 46, NULL, 7, 1),
(132, 46, NULL, 8, 1),
(133, 46, NULL, 9, 1),
(134, 46, NULL, 10, 1),
(135, 46, NULL, 11, 1),
(136, 46, NULL, 12, 1),
(137, 46, NULL, 13, 1),
(138, 46, NULL, 14, 1),
(139, 46, NULL, 15, 1),
(140, 46, NULL, 16, 1),
(141, 46, NULL, 17, 1),
(142, 46, NULL, 18, 1),
(143, 46, NULL, 19, 1),
(144, 46, NULL, 20, 1),
(145, 46, NULL, 21, 1),
(146, 46, NULL, 22, 1),
(147, 46, NULL, 23, 1),
(148, 46, NULL, 24, 1),
(149, 46, NULL, 25, 1),
(150, 46, NULL, 26, 1),
(151, 46, NULL, 27, 1),
(152, 46, NULL, 28, 1),
(153, 46, NULL, 29, 1),
(154, 46, NULL, 30, 1),
(155, 47, NULL, 1, 1),
(156, 47, NULL, 2, 1),
(157, 47, NULL, 3, 1),
(158, 47, NULL, 4, 1),
(159, 47, NULL, 5, 1),
(160, 47, NULL, 6, 1),
(161, 48, NULL, 1, 1),
(162, 48, NULL, 2, 1),
(163, 48, NULL, 3, 1),
(164, 48, NULL, 4, 1),
(165, 48, NULL, 5, 1),
(166, 49, NULL, 1, 1),
(167, 49, NULL, 2, 1),
(168, 49, NULL, 3, 1),
(169, 49, NULL, 4, 1),
(170, 49, NULL, 5, 1),
(171, 49, NULL, 6, 1),
(172, 49, NULL, 7, 1),
(173, 49, NULL, 8, 1),
(174, 49, NULL, 9, 1),
(175, 49, NULL, 10, 1),
(176, 50, NULL, 1, 2),
(177, 50, NULL, 2, 1),
(178, 50, NULL, 3, 1),
(179, 50, NULL, 4, 1),
(180, 50, NULL, 5, 1),
(181, 51, NULL, 1, 1),
(182, 51, NULL, 2, 1),
(183, 51, NULL, 3, 1),
(184, 51, NULL, 4, 1),
(185, 51, NULL, 5, 1),
(186, 51, NULL, 6, 1),
(187, 51, NULL, 7, 1),
(188, 51, NULL, 8, 1),
(189, 52, NULL, 1, 1),
(190, 52, NULL, 2, 1),
(191, 52, NULL, 3, 1),
(192, 53, NULL, 1, 1),
(193, 53, NULL, 2, 1),
(194, 53, NULL, 3, 1),
(195, 53, NULL, 4, 1),
(196, 53, NULL, 5, 1),
(197, 53, NULL, 6, 1),
(198, 53, NULL, 7, 1),
(199, 53, NULL, 8, 1),
(200, 53, NULL, 9, 1),
(201, 53, NULL, 10, 1),
(202, 53, NULL, 11, 1),
(203, 53, NULL, 12, 1),
(204, 53, NULL, 13, 1),
(205, 53, NULL, 14, 1),
(206, 53, NULL, 15, 1),
(207, 54, NULL, 1, 1),
(208, 54, NULL, 2, 1),
(209, 54, NULL, 3, 1),
(210, 54, NULL, 4, 1),
(211, 54, NULL, 5, 1),
(212, 54, NULL, 6, 1),
(213, 54, NULL, 7, 1),
(214, 54, NULL, 8, 1),
(215, 54, NULL, 9, 1),
(216, 54, NULL, 10, 1),
(217, 55, NULL, 1, 1),
(218, 55, NULL, 2, 1),
(219, 55, NULL, 3, 1),
(220, 55, NULL, 4, 1),
(221, 55, NULL, 5, 1),
(222, 55, NULL, 6, 1),
(223, 55, NULL, 7, 1),
(224, 55, NULL, 8, 1),
(225, 56, NULL, 1, 1),
(226, 56, NULL, 2, 1),
(227, 56, NULL, 3, 1),
(228, 56, NULL, 4, 1),
(229, 56, NULL, 5, 1),
(230, 56, NULL, 6, 1),
(231, 56, NULL, 7, 1),
(232, 56, NULL, 8, 1),
(233, 56, NULL, 9, 1),
(234, 56, NULL, 10, 1),
(235, 57, NULL, 1, 1),
(236, 57, NULL, 2, 1),
(237, 57, NULL, 3, 1),
(238, 57, NULL, 4, 1),
(239, 57, NULL, 5, 1),
(240, 57, NULL, 6, 1),
(241, 57, NULL, 7, 1),
(242, 57, NULL, 8, 1),
(243, 57, NULL, 9, 1),
(244, 57, NULL, 10, 1),
(245, 57, NULL, 11, 1),
(246, 57, NULL, 12, 1),
(247, 58, NULL, 1, 1),
(248, 58, NULL, 2, 1),
(249, 58, NULL, 3, 1),
(250, 58, NULL, 4, 1),
(251, 58, NULL, 5, 1),
(252, 58, NULL, 6, 1),
(253, 58, NULL, 7, 1),
(254, 58, NULL, 8, 1),
(255, 59, NULL, 1, 1),
(256, 59, NULL, 2, 1),
(257, 59, NULL, 3, 1),
(258, 59, NULL, 4, 1),
(259, 59, NULL, 5, 1),
(260, 59, NULL, 6, 1),
(261, 59, NULL, 7, 1),
(262, 59, NULL, 8, 1),
(263, 59, NULL, 9, 1),
(264, 59, NULL, 10, 1),
(265, 59, NULL, 11, 1),
(266, 59, NULL, 12, 1),
(267, 59, NULL, 13, 1),
(268, 59, NULL, 14, 1),
(269, 59, NULL, 15, 1),
(270, 60, NULL, 1, 1),
(271, 60, NULL, 2, 1),
(272, 60, NULL, 3, 1),
(273, 60, NULL, 4, 1),
(274, 60, NULL, 5, 1),
(275, 60, NULL, 6, 1),
(276, 60, NULL, 7, 1),
(277, 60, NULL, 8, 1),
(278, 60, NULL, 9, 1),
(279, 60, NULL, 10, 1),
(280, 61, NULL, 1, 1),
(281, 61, NULL, 2, 1),
(282, 61, NULL, 3, 1),
(283, 61, NULL, 4, 1),
(284, 61, NULL, 5, 1),
(285, 61, NULL, 6, 1),
(286, 61, NULL, 7, 1),
(287, 61, NULL, 8, 1),
(288, 61, NULL, 9, 1),
(289, 61, NULL, 10, 1),
(290, 61, NULL, 11, 1),
(291, 61, NULL, 12, 1),
(292, 62, NULL, 1, 1),
(293, 62, NULL, 2, 1),
(294, 62, NULL, 3, 1),
(295, 62, NULL, 4, 1),
(296, 62, NULL, 5, 1),
(297, 62, NULL, 6, 1),
(298, 62, NULL, 7, 1),
(299, 62, NULL, 8, 1),
(300, 62, NULL, 9, 1),
(301, 62, NULL, 10, 1),
(302, 63, NULL, 1, 1),
(303, 63, NULL, 2, 1),
(304, 63, NULL, 3, 1),
(305, 63, NULL, 4, 1),
(306, 63, NULL, 5, 1),
(307, 63, NULL, 6, 1),
(308, 63, NULL, 7, 1),
(309, 63, NULL, 8, 1),
(310, 64, NULL, 1, 1),
(311, 64, NULL, 2, 1),
(312, 64, NULL, 3, 1),
(313, 64, NULL, 4, 1),
(314, 64, NULL, 5, 1),
(315, 64, NULL, 6, 1),
(316, 64, NULL, 7, 1),
(317, 64, NULL, 8, 1),
(318, 65, NULL, 1, 1),
(319, 65, NULL, 2, 1),
(320, 65, NULL, 3, 1),
(321, 65, NULL, 4, 1),
(322, 65, NULL, 5, 1),
(323, 65, NULL, 6, 1),
(324, 66, NULL, 1, 1),
(325, 66, NULL, 2, 1),
(326, 66, NULL, 3, 1),
(327, 66, NULL, 4, 1),
(328, 67, NULL, 1, 1),
(329, 67, NULL, 2, 1),
(330, 68, NULL, 1, 1),
(331, 68, NULL, 2, 1),
(332, 69, NULL, 1, 1),
(333, 69, NULL, 2, 1),
(334, 70, NULL, 1, 1),
(335, 70, NULL, 2, 1),
(336, 70, NULL, 3, 1),
(337, 71, NULL, 1, 1),
(338, 71, NULL, 2, 1),
(339, 72, NULL, 1, 1),
(340, 72, NULL, 2, 1),
(341, 72, NULL, 3, 1),
(342, 73, NULL, 1, 1),
(343, 73, NULL, 2, 1),
(344, 73, NULL, 3, 1),
(345, 73, NULL, 4, 1),
(346, 74, NULL, 1, 1),
(347, 74, NULL, 2, 1),
(348, 74, NULL, 3, 1),
(349, 74, NULL, 4, 1),
(350, 75, NULL, 1, 1),
(351, 75, NULL, 2, 1),
(352, 75, NULL, 3, 1),
(353, 75, NULL, 4, 1),
(354, 76, NULL, 1, 1),
(355, 76, NULL, 2, 1),
(356, 76, NULL, 3, 1),
(357, 77, NULL, 1, 1),
(358, 77, NULL, 2, 1),
(359, 77, NULL, 3, 1),
(360, 77, NULL, 4, 1),
(361, 77, NULL, 5, 1),
(362, 78, NULL, 1, 1),
(363, 78, NULL, 2, 1),
(364, 79, NULL, 1, 1),
(365, 79, NULL, 2, 1),
(366, 80, NULL, 1, 1),
(367, 80, NULL, 2, 1),
(368, 80, NULL, 3, 1),
(369, 80, NULL, 4, 1),
(370, 80, NULL, 5, 1),
(371, 81, NULL, 1, 1),
(372, 81, NULL, 2, 1),
(373, 81, NULL, 3, 1),
(374, 82, NULL, 1, 1),
(375, 82, NULL, 2, 1),
(376, 82, NULL, 3, 1),
(377, 82, NULL, 4, 1),
(378, 82, NULL, 5, 1),
(379, 82, NULL, 6, 1),
(380, 82, NULL, 7, 1),
(381, 82, NULL, 8, 1),
(382, 82, NULL, 9, 1),
(383, 82, NULL, 10, 1),
(384, 83, NULL, 1, 1),
(385, 83, NULL, 2, 1),
(386, 83, NULL, 3, 1),
(387, 83, NULL, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `correo` varchar(150) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `salario` decimal(12,2) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(1) DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `id_rol`, `nombre`, `apellido`, `documento`, `correo`, `contraseña`, `telefono`, `direccion`, `salario`, `fecha_registro`, `estado`) VALUES
(8, 1, 'Mariana', 'Lopez', '', 'mariana.lopez@constructora.com', 'gerente123', '', 'San Martin 2 Mz 3 Cs 5', '22000.00', '2026-07-01 08:10:00', 1),
(9, 4, 'Laura', 'Gómez', '', 'laura.gomez@constructora.com', 'admin123', '', '', NULL, '2026-07-01 08:20:00', 1),
(10, 6, 'Diego', 'Benítez', '12345673', 'diego.benitez@constructora.com', 'admin123', '3705009988', 'hgfds', NULL, '2026-07-01 08:30:00', 1),
(11, 3, 'Valeria', 'Romero', NULL, 'valeria.romero@constructora.com', 'admin123', NULL, NULL, NULL, '2026-07-01 08:40:00', 1),
(12, 4, 'Miguel', 'Fernández', NULL, 'miguel.fernandez@constructora.com', 'jefe123', NULL, NULL, NULL, '2026-07-01 08:50:00', 1),
(13, 4, 'Ricardo', 'Acosta', NULL, 'ricardo.acosta@constructora.com', 'jefe123', NULL, NULL, NULL, '2026-07-01 09:00:00', 1),
(14, 4, 'Sergio', 'Vera', NULL, 'sergio.vera@constructora.com', 'jefe123', NULL, NULL, NULL, '2026-07-01 09:10:00', 1),
(15, 2, 'Jorge', 'Ramirez', '32859437', 'jorge.ramirez@constructora.com', 'deposito123', '3704560091', '', NULL, '2026-07-01 09:20:00', 1),
(16, 5, 'Ramón', 'Ortiz', NULL, 'ramon.ortiz@constructora.com', 'deposito123', NULL, NULL, NULL, '2026-07-01 09:30:00', 1),
(17, 6, 'Juan', 'Pérez', NULL, 'juan.perez@constructora.com', 'empleado123', NULL, NULL, NULL, '2026-07-01 09:40:00', 1),
(18, 2, 'Pedro', 'Sosa', NULL, 'pedro.sosa@constructora.com', 'empleado123', NULL, NULL, NULL, '2026-07-01 09:50:00', 0),
(19, 6, 'Lucas', 'Giménez', NULL, 'lucas.gimenez@constructora.com', 'empleado123', NULL, NULL, NULL, '2026-07-01 10:00:00', 1),
(20, 5, 'Gabriel', 'Rojas', '27987654', 'gabriel.rojas@constructora.com', 'empleado123', '3704525167', '', NULL, '2026-07-01 10:10:00', 1),
(21, 2, 'Matías', 'Silva', '23499879', 'matias.silva@constructora.com', 'empleado123', '3704012988', 'Barrio Antenor Gauna Mz 10 Cs 21', '250000.00', '2026-07-01 10:20:00', 1),
(22, 4, 'Joel', 'Mendoza', NULL, 'jose.mendoza@constructora.com', 'empleado123', NULL, NULL, NULL, '2026-07-01 10:30:00', 1),
(24, 2, 'Roberto', 'Suárez', NULL, 'roberto.suarez@gmail.com', 'cliente123', NULL, NULL, NULL, '2026-07-01 10:50:00', 0),
(25, 1, 'Patricia', 'Morales', '', 'patricia.morales@gmail.com', 'cliente123', '', 'gvcx', '765432.00', '2026-07-01 11:00:00', 1),
(26, 6, 'Fernando', 'Altamirano', '37287390', 'fernando.altamirano@gmail.com', 'cliente123', '3705778822', NULL, NULL, '2026-07-01 11:10:00', 1),
(27, 2, 'Thiago', 'Rohaly', '421245667', 'rohaly1310thiago@gmail.com', 'Thiago', '3704565656', 'Eva Perón, Mz 7 Cs 11', NULL, '2026-07-09 23:58:12', 1),
(28, 2, 'Tatiana', 'Aguirre', NULL, 'aguirreTatiana@gmail.com', 'tati123', NULL, NULL, NULL, '2026-07-10 00:12:08', 1),
(29, 1, 'Karina', 'Coronel', '48576489', 'karinaCoronel@gmail.com', 'hola12', '87654', 'fghj', '23456789.00', '2026-07-10 17:10:44', 1),
(30, 6, 'Mateo', 'Guerra', NULL, 'mateo@gmail.com', '12345', NULL, NULL, NULL, '2026-07-10 17:48:10', 0),
(33, 5, 'Lucas', 'Gomez', NULL, 'gomez@gmail', '123', NULL, NULL, NULL, '2026-07-10 19:51:16', 0),
(34, 6, 'Amyra', 'Rohaly', '37456783', 'amy@gmail', 'amy', '3704576879', 'Barrio República Argentina', NULL, '2026-07-10 21:44:39', 1),
(35, 6, 'Marlene ', 'Fernandez', NULL, 'marfer@gmail.com', 'Mar123', NULL, NULL, NULL, '2026-07-13 12:43:29', 0),
(36, 5, 'Mariano', 'Altamirano', NULL, 'altamirano20Roberto@gmail.com', 'roberto', NULL, NULL, NULL, '2026-07-16 21:08:31', 1),
(37, 6, 'Ricardo', 'Perez', '', 'ricardo@gmail.com', '12345', NULL, NULL, NULL, '2026-07-16 23:33:15', 0),
(38, 6, 'Pedro', ' Benítez', '', 'pedrobenitez@gmail.com', 'Pedro1234', '', NULL, NULL, '2026-07-17 11:41:41', 1),
(39, 2, 'Pablo', 'Gutierres', '12345678', 'pedrogutierres@gmail.com', '1234', '3704564738', 'ugfuyhf', '3333.00', '2026-07-17 12:05:07', 1),
(40, 6, 'Cristian', 'Duré', '76543', 'crisdure@gmail.com', '12345', '76543', 'hgfdgf', NULL, '2026-07-18 19:48:30', 1),
(41, 3, 'Paola', 'Gutierres', '26456367', 'pao@gmail.com', '12345', '3704555522', '', NULL, '2026-07-19 12:36:04', 1),
(42, 2, 'Ricardo', 'Lopez', '23345567', 'lopezricardo@gmail.com', 'ricardo', NULL, NULL, '60000.00', '2026-07-20 23:59:45', 1),
(43, 2, 'Dylan', 'Rohaly', '29388488', 'dylanRohalyy@gmail.com', 'Dylan123', '3704576879', 'Barrio República Argentina', '40000.00', '2026-07-24 13:23:51', 0),
(44, 6, 'Manuel', 'Aguirre', '27888999', 'manuAguirr@gmail.com', 'Manu12', '3705670092', '', NULL, '2026-07-26 20:10:18', 1),
(45, 2, 'Fidelina', 'González ', '246864675', 'fide@gmail.com', '1234', '3704566778', 'Senador Emilio Tomas Mz 7 Cs 11', NULL, '2026-07-27 08:54:52', 0),
(46, 6, 'Matias', 'Martinez', '27888999', 'matimar@gmail.com', '123', '3704049484', NULL, NULL, '2026-07-30 10:23:03', 1),
(47, 1, 'Juan', 'Fernandez', '19234234', 'juanfer@gmail.com', 'juan123', '3704556646', 'B° Independencia Mz 3 Cs 2', '400000.00', '2026-08-09 21:15:03', 0),
(48, 1, 'Santiago', 'Ramirez', '19000999', 'sanntiagoramirez20@gmail.com', 'santi12', '3705666655', 'San Agustin Mz 12 Cs 29', '20000.00', '2026-08-11 17:05:59', 1),
(49, 1, 'Pedro', 'Martinez', '12223333', 'martinezpedro@gmail.com', '2323', '3704314144', 'El Porvenir Mz i Cs 34', '43999.97', '2026-08-11 17:31:35', 1),
(50, 1, 'Mariano', 'Rodriguez', '12222333', 'marianorodriguez@gmail.com', '111', '3704887733', 'San Agustin Mz 18 Cs 20', '12345678.31', '2026-08-11 21:02:49', 1),
(51, 6, 'Roberto', 'Bordon ', '123456789', 'hh@gmail', '1234', '3704314178', NULL, NULL, '2026-08-18 09:13:20', 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_herramientas_asignadas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_herramientas_asignadas` (
`id_herramienta_obra` int(11)
,`id_herramienta` int(11)
,`id_obra` int(11)
,`cantidad_asignada` int(11)
,`cantidad_devuelta` int(11)
,`cantidad_pendiente` bigint(12)
,`estado_devolucion` varchar(18)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_herramientas_asignadas`
--
DROP TABLE IF EXISTS `vista_herramientas_asignadas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_herramientas_asignadas`  AS  select `ho`.`id_herramienta_obra` AS `id_herramienta_obra`,`ho`.`id_herramienta` AS `id_herramienta`,`ho`.`id_obra` AS `id_obra`,`ho`.`cantidad_asignada` AS `cantidad_asignada`,`ho`.`cantidad_devuelta` AS `cantidad_devuelta`,`ho`.`cantidad_asignada` - `ho`.`cantidad_devuelta` AS `cantidad_pendiente`,case when `ho`.`cantidad_devuelta` = 0 then 'Pendiente' when `ho`.`cantidad_devuelta` < `ho`.`cantidad_asignada` then 'Devolución parcial' when `ho`.`cantidad_devuelta` >= `ho`.`cantidad_asignada` then 'Devuelta' else 'Pendiente' end AS `estado_devolucion` from `herramienta_obra` `ho` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acceso_usuario`
--
ALTER TABLE `acceso_usuario`
  ADD PRIMARY KEY (`id_acceso`),
  ADD KEY `idx_acceso_usuario` (`id_usuario`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `idx_asistencia_empleado` (`id_usuario`),
  ADD KEY `idx_asistencia_fecha` (`fecha`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `avance_diario`
--
ALTER TABLE `avance_diario`
  ADD PRIMARY KEY (`id_avance_diario`),
  ADD KEY `idx_avancediario_obra` (`id_obra`);

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Indices de la tabla `cobro`
--
ALTER TABLE `cobro`
  ADD PRIMARY KEY (`id_cobro`),
  ADD KEY `idx_cobro_cliente` (`id_usuario`),
  ADD KEY `idx_cobro_obra` (`id_obra`),
  ADD KEY `fk_cobro_metodo` (`id_metodo_pago`);

--
-- Indices de la tabla `cuenta_cobrar`
--
ALTER TABLE `cuenta_cobrar`
  ADD PRIMARY KEY (`id_cuenta_cobrar`),
  ADD KEY `idx_cuentacobrar_cliente` (`id_usuario`);

--
-- Indices de la tabla `cuenta_pagar`
--
ALTER TABLE `cuenta_pagar`
  ADD PRIMARY KEY (`id_cuenta_pagar`),
  ADD KEY `idx_cuentapagar_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  ADD PRIMARY KEY (`id_detalle_orden`),
  ADD KEY `idx_detorden_orden` (`id_orden`),
  ADD KEY `idx_detorden_material` (`id_material`);

--
-- Indices de la tabla `detalle_presupuesto`
--
ALTER TABLE `detalle_presupuesto`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `idx_detpresupuesto_presupuesto` (`id_presupuesto`);

--
-- Indices de la tabla `devolucion_herramienta`
--
ALTER TABLE `devolucion_herramienta`
  ADD PRIMARY KEY (`id_devolucion`),
  ADD KEY `idx_devolucion_herramienta_obra` (`id_herramienta_obra`),
  ADD KEY `idx_devolucion_fecha` (`fecha_devolucion`),
  ADD KEY `idx_devolucion_usuario` (`id_usuario`);

--
-- Indices de la tabla `documento_obra`
--
ALTER TABLE `documento_obra`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `fk_documento_usuario` (`id_usuario`),
  ADD KEY `idx_documento_obra` (`id_obra`);

--
-- Indices de la tabla `empleado_cargo`
--
ALTER TABLE `empleado_cargo`
  ADD PRIMARY KEY (`id_empleado_cargo`),
  ADD UNIQUE KEY `uk_empleado_cargo` (`id_usuario`,`id_cargo`),
  ADD KEY `fk_empleado_cargo_cargo` (`id_cargo`);

--
-- Indices de la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  ADD PRIMARY KEY (`id_empleado_obra`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_obra` (`id_obra`),
  ADD KEY `fk_empleado_obra_cargo` (`id_cargo`);

--
-- Indices de la tabla `estado_herramienta`
--
ALTER TABLE `estado_herramienta`
  ADD PRIMARY KEY (`id_estado_herramienta`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `etapa_obra`
--
ALTER TABLE `etapa_obra`
  ADD PRIMARY KEY (`id_etapa`),
  ADD KEY `idx_etapa_obra` (`id_obra`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `idx_factura_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `foto_obra`
--
ALTER TABLE `foto_obra`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `idx_foto_obra` (`id_obra`);

--
-- Indices de la tabla `gasto_general`
--
ALTER TABLE `gasto_general`
  ADD PRIMARY KEY (`id_gasto`),
  ADD KEY `idx_gasto_presupuesto` (`id_presupuesto`);

--
-- Indices de la tabla `herramienta`
--
ALTER TABLE `herramienta`
  ADD PRIMARY KEY (`id_herramienta`);

--
-- Indices de la tabla `herramienta_obra`
--
ALTER TABLE `herramienta_obra`
  ADD PRIMARY KEY (`id_herramienta_obra`),
  ADD KEY `fk_herramienta_obra_herramienta` (`id_herramienta`),
  ADD KEY `fk_herramienta_obra_obra` (`id_obra`),
  ADD KEY `fk_herramienta_obra_estado` (`id_estado_herramienta`);

--
-- Indices de la tabla `historial_estado_obra`
--
ALTER TABLE `historial_estado_obra`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `id_obra` (`id_obra`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `horas_trabajadas`
--
ALTER TABLE `horas_trabajadas`
  ADD PRIMARY KEY (`id_hora`),
  ADD KEY `idx_horas_empleado` (`id_usuario`),
  ADD KEY `idx_horas_obra` (`id_obra`);

--
-- Indices de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD PRIMARY KEY (`id_incidencia`),
  ADD KEY `idx_incidencia_obra` (`id_obra`);

--
-- Indices de la tabla `ingreso`
--
ALTER TABLE `ingreso`
  ADD PRIMARY KEY (`id_ingreso`),
  ADD KEY `idx_ingreso_cliente` (`id_usuario`),
  ADD KEY `idx_ingreso_obra` (`id_obra`),
  ADD KEY `idx_ingreso_cobro` (`id_cobro`);

--
-- Indices de la tabla `mano_obra_presupuesto`
--
ALTER TABLE `mano_obra_presupuesto`
  ADD PRIMARY KEY (`id_mano_obra_pres`),
  ADD KEY `idx_manobra_presupuesto` (`id_presupuesto`);

--
-- Indices de la tabla `mantenimiento`
--
ALTER TABLE `mantenimiento`
  ADD PRIMARY KEY (`id_mantenimiento`),
  ADD KEY `idx_mant_herramienta` (`id_herramienta`),
  ADD KEY `fk_mantenimiento_usuario` (`id_usuario`);

--
-- Indices de la tabla `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`);

--
-- Indices de la tabla `material_obra`
--
ALTER TABLE `material_obra`
  ADD PRIMARY KEY (`id_material_obra`),
  ADD KEY `idx_matobra_material` (`id_material`),
  ADD KEY `idx_matobra_obra` (`id_obra`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id_metodo_pago`),
  ADD UNIQUE KEY `nombre_metodo` (`nombre_metodo`);

--
-- Indices de la tabla `movimiento_caja`
--
ALTER TABLE `movimiento_caja`
  ADD PRIMARY KEY (`id_movimiento_caja`),
  ADD KEY `idx_movcaja_usuario` (`id_usuario`),
  ADD KEY `idx_movcaja_obra` (`id_obra`);

--
-- Indices de la tabla `movimiento_inventario`
--
ALTER TABLE `movimiento_inventario`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `idx_movinventario_material` (`id_material`),
  ADD KEY `fk_movinventario_usuario` (`id_usuario`);

--
-- Indices de la tabla `movimiento_material`
--
ALTER TABLE `movimiento_material`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `obra`
--
ALTER TABLE `obra`
  ADD PRIMARY KEY (`id_obra`),
  ADD KEY `idx_obra_cliente` (`id_usuario`),
  ADD KEY `fk_obra_jefe_obra` (`id_jefe_obra`);

--
-- Indices de la tabla `orden_compra`
--
ALTER TABLE `orden_compra`
  ADD PRIMARY KEY (`id_orden`),
  ADD KEY `idx_orden_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `idx_pago_obra` (`id_obra`),
  ADD KEY `fk_pago_metodo` (`id_metodo_pago`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `precio_material`
--
ALTER TABLE `precio_material`
  ADD PRIMARY KEY (`id_precio`),
  ADD KEY `idx_preciomat_material` (`id_material`),
  ADD KEY `idx_preciomat_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD PRIMARY KEY (`id_presupuesto`),
  ADD KEY `idx_presupuesto_obra` (`id_obra`),
  ADD KEY `fk_presupuesto_aprobacion` (`id_usuario_aprobacion`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `proveedor_material`
--
ALTER TABLE `proveedor_material`
  ADD PRIMARY KEY (`id_proveedor_material`),
  ADD KEY `idx_provmat_proveedor` (`id_proveedor`),
  ADD KEY `idx_provmat_material` (`id_material`);

--
-- Indices de la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD PRIMARY KEY (`id_reporte`),
  ADD KEY `idx_reporte_usuario` (`id_usuario`),
  ADD KEY `idx_reporte_obra` (`id_obra`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD PRIMARY KEY (`id_rol`,`id_permiso`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `solicitud_material`
--
ALTER TABLE `solicitud_material`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `idx_solicitud_obra` (`id_obra`);

--
-- Indices de la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `idx_tarea_empleado` (`id_usuario`),
  ADD KEY `idx_tarea_obra` (`id_obra`);

--
-- Indices de la tabla `unidad_herramienta`
--
ALTER TABLE `unidad_herramienta`
  ADD PRIMARY KEY (`id_unidad`),
  ADD KEY `id_herramienta` (`id_herramienta`),
  ADD KEY `id_estado_herramienta` (`id_estado_herramienta`),
  ADD KEY `fk_unidad_herramienta_obra` (`id_herramienta_obra`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `idx_usuario_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acceso_usuario`
--
ALTER TABLE `acceso_usuario`
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=460;

--
-- AUTO_INCREMENT de la tabla `avance_diario`
--
ALTER TABLE `avance_diario`
  MODIFY `id_avance_diario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `cobro`
--
ALTER TABLE `cobro`
  MODIFY `id_cobro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuenta_cobrar`
--
ALTER TABLE `cuenta_cobrar`
  MODIFY `id_cuenta_cobrar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuenta_pagar`
--
ALTER TABLE `cuenta_pagar`
  MODIFY `id_cuenta_pagar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  MODIFY `id_detalle_orden` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_presupuesto`
--
ALTER TABLE `detalle_presupuesto`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devolucion_herramienta`
--
ALTER TABLE `devolucion_herramienta`
  MODIFY `id_devolucion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documento_obra`
--
ALTER TABLE `documento_obra`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleado_cargo`
--
ALTER TABLE `empleado_cargo`
  MODIFY `id_empleado_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  MODIFY `id_empleado_obra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `estado_herramienta`
--
ALTER TABLE `estado_herramienta`
  MODIFY `id_estado_herramienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `etapa_obra`
--
ALTER TABLE `etapa_obra`
  MODIFY `id_etapa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `foto_obra`
--
ALTER TABLE `foto_obra`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `gasto_general`
--
ALTER TABLE `gasto_general`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `herramienta`
--
ALTER TABLE `herramienta`
  MODIFY `id_herramienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `herramienta_obra`
--
ALTER TABLE `herramienta_obra`
  MODIFY `id_herramienta_obra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `historial_estado_obra`
--
ALTER TABLE `historial_estado_obra`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horas_trabajadas`
--
ALTER TABLE `horas_trabajadas`
  MODIFY `id_hora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  MODIFY `id_incidencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `ingreso`
--
ALTER TABLE `ingreso`
  MODIFY `id_ingreso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mano_obra_presupuesto`
--
ALTER TABLE `mano_obra_presupuesto`
  MODIFY `id_mano_obra_pres` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mantenimiento`
--
ALTER TABLE `mantenimiento`
  MODIFY `id_mantenimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `material_obra`
--
ALTER TABLE `material_obra`
  MODIFY `id_material_obra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimiento_caja`
--
ALTER TABLE `movimiento_caja`
  MODIFY `id_movimiento_caja` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimiento_inventario`
--
ALTER TABLE `movimiento_inventario`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimiento_material`
--
ALTER TABLE `movimiento_material`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `obra`
--
ALTER TABLE `obra`
  MODIFY `id_obra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `orden_compra`
--
ALTER TABLE `orden_compra`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `precio_material`
--
ALTER TABLE `precio_material`
  MODIFY `id_precio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  MODIFY `id_presupuesto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `proveedor_material`
--
ALTER TABLE `proveedor_material`
  MODIFY `id_proveedor_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `reporte`
--
ALTER TABLE `reporte`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `solicitud_material`
--
ALTER TABLE `solicitud_material`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tarea`
--
ALTER TABLE `tarea`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidad_herramienta`
--
ALTER TABLE `unidad_herramienta`
  MODIFY `id_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=388;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acceso_usuario`
--
ALTER TABLE `acceso_usuario`
  ADD CONSTRAINT `fk_acceso_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `fk_asistencia_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `avance_diario`
--
ALTER TABLE `avance_diario`
  ADD CONSTRAINT `fk_avancediario_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cobro`
--
ALTER TABLE `cobro`
  ADD CONSTRAINT `fk_cobro_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodo_pago` (`id_metodo_pago`),
  ADD CONSTRAINT `fk_cobro_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cobro_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `cuenta_cobrar`
--
ALTER TABLE `cuenta_cobrar`
  ADD CONSTRAINT `fk_cuentacobrar_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `cuenta_pagar`
--
ALTER TABLE `cuenta_pagar`
  ADD CONSTRAINT `fk_cuentapagar_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  ADD CONSTRAINT `fk_detorden_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `fk_detorden_orden` FOREIGN KEY (`id_orden`) REFERENCES `orden_compra` (`id_orden`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_presupuesto`
--
ALTER TABLE `detalle_presupuesto`
  ADD CONSTRAINT `fk_detpresupuesto_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `devolucion_herramienta`
--
ALTER TABLE `devolucion_herramienta`
  ADD CONSTRAINT `fk_devolucion_herramienta_obra` FOREIGN KEY (`id_herramienta_obra`) REFERENCES `herramienta_obra` (`id_herramienta_obra`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_devolucion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `documento_obra`
--
ALTER TABLE `documento_obra`
  ADD CONSTRAINT `fk_documento_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_documento_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `empleado_cargo`
--
ALTER TABLE `empleado_cargo`
  ADD CONSTRAINT `fk_empleado_cargo_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`),
  ADD CONSTRAINT `fk_empleado_cargo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  ADD CONSTRAINT `empleado_obra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `empleado_obra_ibfk_2` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`),
  ADD CONSTRAINT `fk_empleado_obra_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`);

--
-- Filtros para la tabla `etapa_obra`
--
ALTER TABLE `etapa_obra`
  ADD CONSTRAINT `fk_etapa_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `fk_factura_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `foto_obra`
--
ALTER TABLE `foto_obra`
  ADD CONSTRAINT `fk_foto_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `gasto_general`
--
ALTER TABLE `gasto_general`
  ADD CONSTRAINT `fk_gasto_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `herramienta_obra`
--
ALTER TABLE `herramienta_obra`
  ADD CONSTRAINT `fk_herramienta_obra_estado` FOREIGN KEY (`id_estado_herramienta`) REFERENCES `estado_herramienta` (`id_estado_herramienta`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_herramienta_obra_herramienta` FOREIGN KEY (`id_herramienta`) REFERENCES `herramienta` (`id_herramienta`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_herramienta_obra_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_estado_obra`
--
ALTER TABLE `historial_estado_obra`
  ADD CONSTRAINT `historial_estado_obra_ibfk_1` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_estado_obra_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `horas_trabajadas`
--
ALTER TABLE `horas_trabajadas`
  ADD CONSTRAINT `fk_horas_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_horas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD CONSTRAINT `fk_incidencia_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ingreso`
--
ALTER TABLE `ingreso`
  ADD CONSTRAINT `fk_ingreso_cobro` FOREIGN KEY (`id_cobro`) REFERENCES `cobro` (`id_cobro`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ingreso_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ingreso_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `mano_obra_presupuesto`
--
ALTER TABLE `mano_obra_presupuesto`
  ADD CONSTRAINT `fk_manobra_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mantenimiento`
--
ALTER TABLE `mantenimiento`
  ADD CONSTRAINT `fk_mant_herramienta` FOREIGN KEY (`id_herramienta`) REFERENCES `herramienta` (`id_herramienta`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mantenimiento_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `material_obra`
--
ALTER TABLE `material_obra`
  ADD CONSTRAINT `fk_matobra_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `fk_matobra_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimiento_caja`
--
ALTER TABLE `movimiento_caja`
  ADD CONSTRAINT `fk_movcaja_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_movcaja_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `movimiento_inventario`
--
ALTER TABLE `movimiento_inventario`
  ADD CONSTRAINT `fk_movinventario_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `fk_movinventario_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `movimiento_material`
--
ALTER TABLE `movimiento_material`
  ADD CONSTRAINT `movimiento_material_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `movimiento_material_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `obra`
--
ALTER TABLE `obra`
  ADD CONSTRAINT `fk_obra_jefe_obra` FOREIGN KEY (`id_jefe_obra`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_obra_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `orden_compra`
--
ALTER TABLE `orden_compra`
  ADD CONSTRAINT `fk_orden_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `fk_pago_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodo_pago` (`id_metodo_pago`),
  ADD CONSTRAINT `fk_pago_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `precio_material`
--
ALTER TABLE `precio_material`
  ADD CONSTRAINT `fk_preciomat_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_preciomat_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD CONSTRAINT `fk_presupuesto_aprobacion` FOREIGN KEY (`id_usuario_aprobacion`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `fk_presupuesto_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proveedor_material`
--
ALTER TABLE `proveedor_material`
  ADD CONSTRAINT `fk_provmat_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_provmat_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD CONSTRAINT `fk_reporte_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reporte_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD CONSTRAINT `rol_permiso_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
  ADD CONSTRAINT `rol_permiso_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`);

--
-- Filtros para la tabla `solicitud_material`
--
ALTER TABLE `solicitud_material`
  ADD CONSTRAINT `fk_solicitud_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD CONSTRAINT `fk_tarea_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tarea_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `unidad_herramienta`
--
ALTER TABLE `unidad_herramienta`
  ADD CONSTRAINT `fk_unidad_herramienta_obra` FOREIGN KEY (`id_herramienta_obra`) REFERENCES `herramienta_obra` (`id_herramienta_obra`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `unidad_herramienta_ibfk_1` FOREIGN KEY (`id_herramienta`) REFERENCES `herramienta` (`id_herramienta`),
  ADD CONSTRAINT `unidad_herramienta_ibfk_2` FOREIGN KEY (`id_estado_herramienta`) REFERENCES `estado_herramienta` (`id_estado_herramienta`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
