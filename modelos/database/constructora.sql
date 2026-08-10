-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-08-2026 a las 03:21:56
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
(86, 27, '2026-08-09 20:22:23', NULL);

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
(239, 27, 'INSERTAR', 'usuario', 44, '2026-07-26 20:10:18', 'Registró un nuevo usuario'),
(240, 27, 'EDITAR', 'usuario', 44, '2026-07-26 20:10:51', 'Modificó el usuario'),
(241, 27, 'BAJA', 'usuario', 44, '2026-07-26 20:11:13', 'Desactivó un usuario'),
(242, 27, 'ACTIVAR', 'usuario', 44, '2026-07-26 20:11:31', 'Activó nuevamente un usuario'),
(243, 27, 'INSERTAR', 'obra', 18, '2026-07-26 20:13:23', 'Registró la obra: Refacción de la E.P.E.S N° 5'),
(244, 27, 'EDITAR', 'obra', 9, '2026-07-26 20:15:25', 'Modificó la obra Quincho Amyra'),
(245, 27, 'INSERTAR', 'avance_diario', 28, '2026-07-26 20:17:06', 'Se registró un nuevo avance diario en la obra ID 9'),
(246, 27, 'EDITAR', 'avance_diario', 28, '2026-07-26 20:17:26', 'Se modificó un avance diario de la obra ID 9'),
(247, 27, 'INSERTAR', 'empleado_obra', 42, '2026-07-26 20:17:52', 'Asignó un empleado a una obra'),
(248, 27, 'INSERTAR', 'herramienta_obra', 15, '2026-07-26 20:32:26', 'Asignación de herramienta a obra'),
(249, 27, 'EDITAR', 'herramienta_obra', 23, '2026-07-26 20:33:06', 'Actualización de herramienta asignada'),
(250, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-07-26 21:48:06', 'Asignación de herramienta a obra'),
(251, 27, 'EDITAR', 'herramienta_obra', 25, '2026-07-26 21:48:37', 'Actualización de herramienta asignada'),
(252, 27, 'EDITAR', 'herramienta_obra', 24, '2026-07-26 21:49:07', 'Actualización de herramienta asignada'),
(253, 27, 'INSERTAR', 'avance_diario', 29, '2026-07-26 21:50:05', 'Se registró un nuevo avance diario en la obra ID 9'),
(254, 27, 'ELIMINAR', 'avance_diario', 24, '2026-07-26 21:50:20', 'Se eliminó un avance diario de la obra ID 9'),
(255, 27, 'ELIMINAR', 'avance_diario', 28, '2026-07-26 21:50:23', 'Se eliminó un avance diario de la obra ID 9'),
(256, 27, 'ELIMINAR', 'avance_diario', 29, '2026-07-26 21:50:26', 'Se eliminó un avance diario de la obra ID 9'),
(257, 27, 'BAJA', 'usuario', 10, '2026-07-27 07:58:10', 'Desactivó un usuario'),
(258, 27, 'BAJA', 'usuario', 18, '2026-07-27 07:58:33', 'Desactivó un usuario'),
(259, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-07-27 08:05:27', 'Asignación de herramienta a obra'),
(260, 27, 'INSERTAR', 'avance_diario', 30, '2026-07-27 08:13:09', 'Se registró un nuevo avance diario en la obra ID 9'),
(261, 27, 'INSERTAR', 'avance_diario', 31, '2026-07-27 08:44:08', 'Se registró un nuevo avance diario en la obra ID 9'),
(262, 27, 'INSERTAR', 'empleado_obra', 15, '2026-07-27 08:44:30', 'Asignó un empleado a una obra'),
(263, 27, 'INSERTAR', 'herramienta', 37, '2026-07-27 08:45:26', 'Se registró la herramienta MARTILLO con 10 unidades.'),
(264, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-07-27 08:48:13', 'Asignación de herramienta a obra'),
(265, 27, 'EDITAR', 'herramienta_obra', 27, '2026-07-27 08:48:45', 'Actualización de herramienta asignada'),
(266, 27, 'EDITAR', 'herramienta_obra', 27, '2026-07-27 08:48:48', 'Actualización de herramienta asignada'),
(267, 27, 'INSERTAR', 'usuario', 45, '2026-07-27 08:54:52', 'Registró un nuevo usuario'),
(268, 27, 'ACTIVAR', 'usuario', 10, '2026-07-28 12:43:33', 'Activó nuevamente un usuario'),
(269, 27, 'INSERTAR', 'empleado_obra', 39, '2026-07-28 12:45:05', 'Asignó un empleado a una obra'),
(270, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-07-28 12:45:22', 'Asignación de herramienta a obra'),
(271, 27, 'EDITAR', 'herramienta_obra', 28, '2026-07-28 12:45:31', 'Actualización de herramienta asignada'),
(272, 27, 'BAJA', 'MATERIAL', 28, '2026-07-28 12:46:12', 'Se dio de baja el material ID: 28'),
(273, 27, 'EDITAR', 'usuario', 20, '2026-07-28 18:44:58', 'Modificó el usuario'),
(274, 27, 'BAJA', 'usuario', 37, '2026-07-28 20:16:32', 'Desactivó un usuario'),
(275, 27, 'ACTIVAR', 'usuario', 19, '2026-07-28 22:21:08', 'Activó nuevamente un usuario'),
(276, 27, 'BAJA', 'usuario', 19, '2026-07-28 22:21:17', 'Desactivó un usuario'),
(277, 27, 'ACTIVAR', 'usuario', 35, '2026-07-28 23:09:31', 'Activó nuevamente un usuario'),
(278, 27, 'ACTIVAR', 'usuario', 19, '2026-07-28 23:09:33', 'Activó nuevamente un usuario'),
(279, 27, 'ACTIVAR', 'usuario', 30, '2026-07-28 23:09:35', 'Activó nuevamente un usuario'),
(280, 27, 'BAJA', 'usuario', 19, '2026-07-28 23:09:39', 'Desactivó un usuario'),
(281, 27, 'EDITAR', 'usuario', 41, '2026-07-28 23:12:22', 'Modificó el usuario'),
(282, 41, 'BAJA', 'usuario', 35, '2026-07-28 23:14:03', 'Desactivó un usuario'),
(283, 27, 'BAJA', 'usuario', 10, '2026-07-28 23:30:06', 'Desactivó un usuario'),
(284, 27, 'BAJA', 'usuario', 30, '2026-07-28 23:30:16', 'Desactivó un usuario'),
(285, 27, 'ACTIVAR', 'usuario', 19, '2026-07-29 08:03:23', 'Activó nuevamente un usuario'),
(286, 27, 'INSERTAR', 'usuario', 46, '2026-07-30 10:23:03', 'Registró un nuevo usuario'),
(287, 27, 'EDITAR', 'usuario', 26, '2026-07-30 11:59:43', 'Modificó el usuario'),
(288, 27, 'EDITAR', 'usuario', 26, '2026-07-30 11:59:57', 'Modificó el usuario'),
(289, 27, 'EDITAR', 'usuario', 26, '2026-07-30 12:00:46', 'Modificó el usuario'),
(290, 27, 'BAJA', 'usuario', 45, '2026-07-31 08:32:03', 'Desactivó un usuario'),
(291, 27, 'ACTIVAR', 'usuario', 35, '2026-07-31 08:34:29', 'Activó nuevamente un usuario'),
(292, 27, 'ACTIVAR', 'usuario', 18, '2026-07-31 08:41:49', 'Activó nuevamente un usuario'),
(293, 27, 'BAJA', 'usuario', 24, '2026-07-31 08:42:57', 'Desactivó un usuario'),
(294, 27, 'BAJA', 'usuario', 18, '2026-07-31 08:43:06', 'Desactivó un usuario'),
(295, 27, 'ACTIVAR', 'usuario', 25, '2026-07-31 08:46:55', 'Activó nuevamente un usuario'),
(296, 27, 'INSERTAR', 'herramienta_obra', 9, '2026-07-31 08:48:25', 'Asignación de herramienta a obra'),
(297, 27, 'EDITAR', 'herramienta_obra', 29, '2026-07-31 08:49:20', 'Actualización de herramienta asignada'),
(298, 27, 'ACTIVAR', 'MATERIAL', 28, '2026-07-31 08:57:22', 'Se activó el material ID: 28'),
(299, 27, 'INSERTAR', 'avance_diario', 32, '2026-07-31 09:03:52', 'Se registró un nuevo avance diario en la obra ID 9'),
(300, 27, 'EDITAR', 'usuario', 29, '2026-08-09 21:08:23', 'Modificó el usuario'),
(301, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-09 21:11:40', 'Asignó un empleado a una obra'),
(302, 27, 'INSERTAR', 'empleado_obra', 29, '2026-08-09 21:12:12', 'Asignó un empleado a una obra'),
(303, 27, 'EDITAR', 'usuario', 25, '2026-08-09 21:13:01', 'Modificó el usuario'),
(304, 27, 'INSERTAR', 'usuario', 47, '2026-08-09 21:15:03', 'Registró un nuevo usuario'),
(305, 27, 'BAJA', 'usuario', 47, '2026-08-09 21:26:05', 'Desactivó un usuario'),
(306, 27, 'BAJA', 'usuario', 35, '2026-08-09 21:26:16', 'Desactivó un usuario'),
(307, 27, 'EDITAR', 'obra', 9, '2026-08-09 21:27:23', 'Modificó la obra Quincho Amyra'),
(308, 27, 'INSERTAR', 'empleado_obra', 25, '2026-08-09 21:27:58', 'Asignó un empleado a una obra');

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
(10, 'Maestro Mayor de Obras', 'Coordina y supervisa aspectos técnicos de la ejecución de la obra.');

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
DELIMITER $$
CREATE TRIGGER `before_insert_devolucion_herramienta` BEFORE INSERT ON `devolucion_herramienta` FOR EACH ROW BEGIN

    DECLARE v_asignada INT DEFAULT 0;
    DECLARE v_devuelta INT DEFAULT 0;

    SELECT
        cantidad_asignada,
        cantidad_devuelta
    INTO
        v_asignada,
        v_devuelta
    FROM herramienta_obra
    WHERE id_herramienta_obra = NEW.id_herramienta_obra
    LIMIT 1;

    IF v_asignada = 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
            'La asignación de herramienta no existe.';

    END IF;

    IF NEW.cantidad <= 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
            'La cantidad devuelta debe ser mayor a cero.';

    END IF;

    IF NEW.cantidad > (v_asignada - v_devuelta) THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
            'La devolución supera la cantidad pendiente.';

    END IF;

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
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleado_obra`
--

INSERT INTO `empleado_obra` (`id_empleado_obra`, `id_usuario`, `id_obra`, `fecha_ingreso`, `fecha_egreso`, `motivo_egreso`, `observaciones`, `estado`) VALUES
(10, 29, 9, '2026-08-09', NULL, NULL, '', 1),
(11, 29, 15, '2026-08-09', NULL, NULL, '', 1),
(12, 25, 9, '2026-08-09', NULL, NULL, '', 1);

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
(26, 9, 'Fundaciones', 'Construcción de bases', '2026-07-11', '2026-07-22', 'Finalizada'),
(27, 9, 'Estructura', 'Levantamiento de estructura', '2026-07-17', '2026-07-31', 'Finalizada'),
(28, 9, 'dfghjk', '6u5yrtg', '0000-00-00', '0000-00-00', 'Finalizada'),
(29, 9, 'fggg', 'm', '0000-00-00', '0000-00-00', 'Finalizada'),
(30, 9, 'Fundaciones', '', '0000-00-00', '0000-00-00', 'Finalizada'),
(31, 15, 'rtthrth', '', '0000-00-00', '0000-00-00', 'Cancelada'),
(32, 9, 'Estructura', '', '0000-00-00', '2026-07-31', 'Finalizada'),
(33, 9, 'Contrapisos', '', '2026-07-27', '2026-07-31', 'Finalizada'),
(34, 9, 'ttt', '', '2026-07-31', '0000-00-00', 'Pendiente');

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
(35, 'Cinta', 'Medición', 'Hola', 'holaaa', 8, '2026-07-25', '30000.00'),
(36, 'Martillo', 'Manual', 'Algo', '22ooj3', 6, '2026-07-18', '3000.00'),
(37, 'Martillo', 'Manual', 'Stanley', 'm13', 10, '2026-07-27', '20000.00');

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
  `cantidad` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `id_estado_herramienta` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `herramienta_obra`
--

INSERT INTO `herramienta_obra` (`id_herramienta_obra`, `id_herramienta`, `cantidad_asignada`, `cantidad_devuelta`, `id_obra`, `cantidad`, `fecha_asignacion`, `fecha_devolucion`, `observaciones`, `id_estado_herramienta`) VALUES
(17, 35, 3, 3, 9, 3, '2026-07-24', '2026-07-30', '', 5),
(18, 36, 1, 1, 9, 1, '2026-07-24', '2026-07-24', '', 5),
(19, 35, 3, 3, 9, 3, '2026-07-24', '2026-07-25', '', 5),
(20, 35, 5, 5, 9, 5, '2026-07-24', '2026-07-25', '', 5),
(21, 35, 6, 6, 9, 6, '2026-07-24', '2026-07-18', '', 5),
(22, 35, 3, 3, 9, 3, '2026-07-24', '2026-07-25', '', 5),
(23, 35, 6, 6, 9, 6, '2026-07-24', '2026-07-26', '', 5),
(24, 35, 1, 1, 15, 1, '2026-07-26', '2026-07-26', '', 5),
(25, 35, 3, 3, 9, 3, '2026-07-26', '2026-07-28', '', 5),
(26, 35, 6, 0, 9, 6, '2026-07-27', NULL, '', 2),
(27, 37, 10, 10, 9, 10, '2026-07-27', '2026-07-27', '', 5),
(28, 36, 1, 1, 9, 1, '2026-07-28', '2026-07-30', '', 5),
(29, 36, 4, 4, 9, 4, '2026-07-31', '0000-00-00', '', 2);

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
(13, 'Alambre recocido', 'Alambre para atado de armaduras.', '100.00', '50.00', 'Rollo', 0),
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
(3, 28, 27, 'EGRESO', '100.00', '2026-07-21 17:11:31', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `obra`
--

CREATE TABLE `obra` (
  `id_obra` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
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

INSERT INTO `obra` (`id_obra`, `id_usuario`, `nombre_obra`, `direccion`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `activo`) VALUES
(9, 34, 'Quincho Amyra', 'Senador Emilio Tomás Barrio Eva Perón', 'Casa tipo quinta.', '2026-07-24', '0000-00-00', 'En Proceso', 1),
(15, 26, 'rtgeggr', 'wgwwg', 'erwer', '0000-00-00', '0000-00-00', 'Planificacion', 1),
(16, 26, 'Casa de Dylan', 'Barrio 8 de octubre', '', '0000-00-00', '0000-00-00', 'Planificacion', 1),
(17, 40, 'iliukyjhre', 'iuytgfred', 'kjhgfds', '0000-00-00', '0000-00-00', 'Planificacion', 1),
(18, 17, 'Refacción de la E.P.E.S N° 5', 'Senador Emilio Tomas', 'Refacción de las instalaciones.', '2026-07-27', '0000-00-00', 'En Proceso', 1);

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
  `numero_unidad` int(11) NOT NULL,
  `id_estado_herramienta` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `unidad_herramienta`
--

INSERT INTO `unidad_herramienta` (`id_unidad`, `id_herramienta`, `numero_unidad`, `id_estado_herramienta`) VALUES
(5, 35, 1, 1),
(6, 35, 2, 1),
(7, 35, 3, 1),
(8, 36, 1, 1),
(9, 36, 2, 1),
(10, 36, 3, 1),
(11, 36, 4, 1),
(12, 36, 5, 1),
(13, 36, 6, 1),
(14, 37, 1, 1),
(15, 37, 2, 1),
(16, 37, 3, 1),
(17, 37, 4, 1),
(18, 37, 5, 1),
(19, 37, 6, 1),
(20, 37, 7, 1),
(21, 37, 8, 1),
(22, 37, 9, 1),
(23, 37, 10, 1);

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
  `id_cargo` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(1) DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `id_rol`, `nombre`, `apellido`, `documento`, `correo`, `contraseña`, `telefono`, `direccion`, `salario`, `id_cargo`, `fecha_registro`, `estado`) VALUES
(8, 2, 'Mariana', 'López', NULL, 'mariana.lopez@constructora.com', 'gerente123', NULL, NULL, NULL, NULL, '2026-07-01 08:10:00', 0),
(9, 3, 'Laura', 'Gómez', NULL, 'laura.gomez@constructora.com', 'admin123', NULL, NULL, NULL, NULL, '2026-07-01 08:20:00', 0),
(10, 6, 'Diego', 'Benitez', '12345673', 'diego.benitez@constructora.com', 'admin123', '3705009988', 'hgfds', NULL, NULL, '2026-07-01 08:30:00', 0),
(11, 3, 'Valeria', 'Romero', NULL, 'valeria.romero@constructora.com', 'admin123', NULL, NULL, NULL, NULL, '2026-07-01 08:40:00', 1),
(12, 4, 'Miguel', 'Fernández', NULL, 'miguel.fernandez@constructora.com', 'jefe123', NULL, NULL, NULL, NULL, '2026-07-01 08:50:00', 1),
(13, 4, 'Ricardo', 'Acosta', NULL, 'ricardo.acosta@constructora.com', 'jefe123', NULL, NULL, NULL, NULL, '2026-07-01 09:00:00', 0),
(14, 4, 'Sergio', 'Vera', NULL, 'sergio.vera@constructora.com', 'jefe123', NULL, NULL, NULL, NULL, '2026-07-01 09:10:00', 0),
(15, 2, 'Jorge', 'Ramirez', '32859437', 'jorge.ramirez@constructora.com', 'deposito123', '3704560091', '', NULL, NULL, '2026-07-01 09:20:00', 1),
(16, 5, 'Ramón', 'Ortiz', NULL, 'ramon.ortiz@constructora.com', 'deposito123', NULL, NULL, NULL, NULL, '2026-07-01 09:30:00', 1),
(17, 6, 'Juan', 'Pérez', NULL, 'juan.perez@constructora.com', 'empleado123', NULL, NULL, NULL, NULL, '2026-07-01 09:40:00', 1),
(18, 2, 'Pedro', 'Sosa', NULL, 'pedro.sosa@constructora.com', 'empleado123', NULL, NULL, NULL, NULL, '2026-07-01 09:50:00', 0),
(19, 6, 'Lucas', 'Giménez', NULL, 'lucas.gimenez@constructora.com', 'empleado123', NULL, NULL, NULL, NULL, '2026-07-01 10:00:00', 1),
(20, 5, 'Gabriel', 'Rojas', '27987654', 'gabriel.rojas@constructora.com', 'empleado123', '3704525167', '', NULL, NULL, '2026-07-01 10:10:00', 1),
(21, 2, 'Mat??as', 'Silva', '23499879', 'matias.silva@constructora.com', 'empleado123', '3704012988', 'Barrio Antenor Gauna Mz 10 Cs 21', '250000.00', NULL, '2026-07-01 10:20:00', 1),
(22, 4, 'Joel', 'Mendoza', NULL, 'jose.mendoza@constructora.com', 'empleado123', NULL, NULL, NULL, NULL, '2026-07-01 10:30:00', 1),
(24, 2, 'Roberto', 'Suárez', NULL, 'roberto.suarez@gmail.com', 'cliente123', NULL, NULL, NULL, NULL, '2026-07-01 10:50:00', 0),
(25, 1, 'Patricia', 'Morales', '', 'patricia.morales@gmail.com', 'cliente123', '', 'gvcx', '765432.00', NULL, '2026-07-01 11:00:00', 1),
(26, 6, 'Fernando', 'Altamirano', '37287390', 'fernando.altamirano@gmail.com', 'cliente123', '3705778822', NULL, NULL, NULL, '2026-07-01 11:10:00', 1),
(27, 2, 'Thiago', 'Rohaly', NULL, 'rohaly1310thiago@gmail.com', '1234', NULL, NULL, NULL, NULL, '2026-07-09 23:58:12', 1),
(28, 2, 'Tatiana', 'Aguirre', NULL, 'aguirreTatiana@gmail.com', 'tati123', NULL, NULL, NULL, NULL, '2026-07-10 00:12:08', 1),
(29, 1, 'Karina', 'Coronel', '48576489', 'karinaCoronel@gmail.com', 'hola12', '87654', 'fghj', '23456789.00', NULL, '2026-07-10 17:10:44', 1),
(30, 6, 'Mateo', 'Guerra', NULL, 'mateo@gmail.com', '12345', NULL, NULL, NULL, NULL, '2026-07-10 17:48:10', 0),
(33, 5, 'Lucas', 'Gomez', NULL, 'gomez@gmail', '123', NULL, NULL, NULL, NULL, '2026-07-10 19:51:16', 0),
(34, 6, 'Amyra', 'Rohaly', '37456783', 'amy@gmail', 'amy', '3704576879', 'Barrio República Argentina', NULL, NULL, '2026-07-10 21:44:39', 1),
(35, 6, 'Marlene ', 'Fernandez', NULL, 'marfer@gmail.com', 'Mar123', NULL, NULL, NULL, NULL, '2026-07-13 12:43:29', 0),
(36, 5, 'Mariano', 'Altamirano', NULL, 'altamirano20Roberto@gmail.com', 'roberto', NULL, NULL, NULL, NULL, '2026-07-16 21:08:31', 1),
(37, 6, 'Ricardo', 'Perez', '', 'ricardo@gmail.com', '12345', NULL, NULL, NULL, NULL, '2026-07-16 23:33:15', 0),
(38, 6, 'Pedro', ' Benitez', NULL, 'pedrobenitez@gmail.com', 'Pedro1234', NULL, NULL, NULL, NULL, '2026-07-17 11:41:41', 1),
(39, 2, 'Pablo', 'Gutierres', '12345678', 'pedrogutierres@gmail.com', '1234', '3704564738', 'ugfuyhf', '3333.00', NULL, '2026-07-17 12:05:07', 1),
(40, 6, 'Cristian', 'Dure', '76543', 'crisdure@gmail.com', '12345', '76543', 'hgfdgf', NULL, NULL, '2026-07-18 19:48:30', 1),
(41, 3, 'Paola', 'Gutierres', '26456367', 'pao@gmail.com', '12345', '3704555522', '', NULL, NULL, '2026-07-19 12:36:04', 1),
(42, 2, 'Ricardo', 'Lopez', '23345567', 'lopezricardo@gmail.com', 'ricardo', NULL, NULL, '60000.00', NULL, '2026-07-20 23:59:45', 1),
(43, 2, 'Dylan', 'Rohaly', '29388488', 'dylanRohalyy@gmail.com', 'Dylan123', '3704576879', 'Barrio República Argentina', '40000.00', NULL, '2026-07-24 13:23:51', 1),
(44, 5, 'Manuel', 'Aguirre', '27888999', 'manuAguirr@gmail.com', 'Manu12', '3705670092', '', NULL, NULL, '2026-07-26 20:10:18', 1),
(45, 2, 'Fidelina', 'Gonz??lez ', '246864675', 'fide@gmail.com', '1234', '3704566778', 'Senador Emilio Tomas Mz 7 Cs 11', '2222.00', NULL, '2026-07-27 08:54:52', 0),
(46, 6, 'Matias', 'Martinez', '27888999', 'matimar@gmail.com', '123', '3704049484', NULL, NULL, NULL, '2026-07-30 10:23:03', 1),
(47, 1, 'Juan', 'Fernandez', '19234234', 'juanfer@gmail.com', 'juan123', '3704556646', 'B° Independencia Mz 3 Cs 2', '400000.00', NULL, '2026-08-09 21:15:03', 0);

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
-- Indices de la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  ADD PRIMARY KEY (`id_empleado_obra`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_obra` (`id_obra`);

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
  ADD KEY `idx_obra_cliente` (`id_usuario`);

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
  ADD KEY `id_estado_herramienta` (`id_estado_herramienta`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `idx_usuario_rol` (`id_rol`),
  ADD KEY `fk_usuario_cargo` (`id_cargo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acceso_usuario`
--
ALTER TABLE `acceso_usuario`
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- AUTO_INCREMENT de la tabla `avance_diario`
--
ALTER TABLE `avance_diario`
  MODIFY `id_avance_diario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- AUTO_INCREMENT de la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  MODIFY `id_empleado_obra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `estado_herramienta`
--
ALTER TABLE `estado_herramienta`
  MODIFY `id_estado_herramienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `etapa_obra`
--
ALTER TABLE `etapa_obra`
  MODIFY `id_etapa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
  MODIFY `id_herramienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `herramienta_obra`
--
ALTER TABLE `herramienta_obra`
  MODIFY `id_herramienta_obra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `obra`
--
ALTER TABLE `obra`
  MODIFY `id_obra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  MODIFY `id_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

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
-- Filtros para la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  ADD CONSTRAINT `empleado_obra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `empleado_obra_ibfk_2` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`);

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
  ADD CONSTRAINT `unidad_herramienta_ibfk_1` FOREIGN KEY (`id_herramienta`) REFERENCES `herramienta` (`id_herramienta`),
  ADD CONSTRAINT `unidad_herramienta_ibfk_2` FOREIGN KEY (`id_estado_herramienta`) REFERENCES `estado_herramienta` (`id_estado_herramienta`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`),
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
