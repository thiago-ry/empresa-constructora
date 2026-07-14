-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-07-2026 a las 02:38:55
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
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
  `fecha` datetime DEFAULT current_timestamp(),
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(1, 1, '2026-01-12', 'Se realizó excavación del terreno y preparación para la construcción de los cimientos.'),
(2, 1, '2026-01-18', 'Se colocaron hierros estructurales y se preparó el hormigonado de la base.'),
(3, 1, '2026-01-25', 'Finalización de cimientos y preparación para iniciar la estructura.'),
(4, 1, '2026-02-10', 'Construcción de columnas y vigas principales de la vivienda.'),
(5, 1, '2026-03-05', 'Avance en levantamiento de paredes interiores y exteriores.'),
(6, 1, '2026-04-10', 'Instalación de cableado eléctrico y cañerías sanitarias.'),
(7, 1, '2026-05-05', 'Trabajos de pintura, colocación de pisos y terminaciones finales.'),
(8, 2, '2026-02-10', 'Inicio de demolición de sectores internos del local.'),
(9, 2, '2026-02-18', 'Retiro de materiales antiguos y limpieza del área de trabajo.'),
(10, 2, '2026-03-05', 'Modificación de espacios internos según el nuevo diseño.'),
(11, 2, '2026-04-01', 'Instalación de sistema eléctrico y adecuación de iluminación.'),
(12, 3, '2026-03-15', 'Preparación del terreno y nivelación del área de construcción.'),
(13, 3, '2026-03-25', 'Finalización del movimiento de suelo e inicio de excavaciones.'),
(14, 3, '2026-04-15', 'Construcción inicial de bases y cimientos del edificio.'),
(15, 4, '2026-04-10', 'Revisión del espacio disponible y planificación de la ampliación.'),
(16, 4, '2026-05-05', 'Preparación del terreno para comenzar la construcción.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avance_obra`
--

CREATE TABLE `avance_obra` (
  `id_avance` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `avance_obra`
--

INSERT INTO `avance_obra` (`id_avance`, `id_obra`, `fecha`, `porcentaje`, `observaciones`) VALUES
(1, 1, '2026-01-08', '15.00', 'Finalización de planificación y comienzo de preparación del terreno.'),
(2, 1, '2026-01-25', '30.00', 'Cimientos terminados correctamente.'),
(3, 1, '2026-02-20', '50.00', 'Estructura principal finalizada.'),
(4, 1, '2026-03-15', '65.00', 'Mampostería completada.'),
(5, 1, '2026-03-30', '75.00', 'Techado finalizado.'),
(6, 1, '2026-04-25', '90.00', 'Instalaciones terminadas.'),
(7, 1, '2026-05-10', '95.00', 'Obra en etapa de terminaciones finales.'),
(8, 2, '2026-02-07', '15.00', 'Planificación terminada.'),
(9, 2, '2026-02-20', '35.00', 'Demolición completada.'),
(10, 2, '2026-03-15', '60.00', 'Adecuaciones estructurales realizadas.'),
(11, 2, '2026-04-05', '75.00', 'Instalaciones en proceso.'),
(12, 3, '2026-03-10', '10.00', 'Planificación inicial completada.'),
(13, 3, '2026-03-25', '25.00', 'Movimiento de suelo finalizado.'),
(14, 3, '2026-04-20', '40.00', 'Construcción de cimientos en proceso.'),
(15, 4, '2026-04-10', '10.00', 'Inicio de planificación de ampliación.'),
(16, 4, '2026-05-01', '20.00', 'Preparación del área de construcción.');

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
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre`, `apellido`, `telefono`, `direccion`, `correo`, `id_usuario`) VALUES
(1, 'Ana', 'López', '3704123456', 'Av. Italia 1250, Formosa', 'ana.lopez@gmail.com', 17),
(2, 'Roberto', 'Suárez', '3704234567', 'B° San Martín 845, Formosa', 'roberto.suarez@gmail.com', 18),
(3, 'Patricia', 'Morales', '3704345678', 'Av. González Lelong 542, Formosa', 'patricia.morales@gmail.com', 19),
(4, 'Fernando', 'Almirón', '3704456789', 'B° Guadalupe 1130, Formosa', 'fernando.almiron@gmail.com', 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobro`
--

CREATE TABLE `cobro` (
  `id_cobro` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
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
  `id_cliente` int(11) NOT NULL,
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
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id_empleado` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `salario` decimal(12,2) DEFAULT NULL,
  `estado` enum('Activo','Inactivo','Suspendido') DEFAULT 'Activo',
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id_empleado`, `nombre`, `apellido`, `documento`, `telefono`, `direccion`, `salario`, `estado`, `id_usuario`) VALUES
(1, 'Juan', 'Pérez', '40123456', '3704123456', 'Barrio San Miguel, Formosa', '950000.00', 'Activo', 11),
(2, 'Pedro', 'Sosa', '39234567', '3704234567', 'Barrio Eva Perón, Formosa', '850000.00', 'Activo', 12),
(3, 'Lucas', 'Giménez', '41345678', '3704345678', 'Barrio Guadalupe, Formosa', '900000.00', 'Activo', 13),
(4, 'Gabriel', 'Rojas', '38765432', '3704456789', 'Barrio San Francisco, Formosa', '920000.00', 'Activo', 14),
(5, 'Matías', 'Silva', '39876543', '3704567890', 'Barrio Independencia, Formosa', '870000.00', 'Activo', 15),
(6, 'José', 'Mendoza', '40567891', '3704678901', 'Barrio Liborsi, Formosa', '980000.00', 'Activo', 16),
(7, 'Cristian', 'Benítez', '39654321', '3704789012', 'Barrio Colluccio, Formosa', '1100000.00', 'Activo', NULL),
(8, 'Diego', 'Ramírez', '38987654', '3704890123', 'Barrio Obrero, Formosa', '1250000.00', 'Activo', NULL),
(9, 'Ricardo', 'Fernández', '37654321', '3704901234', 'Barrio San Antonio, Formosa', '1400000.00', 'Activo', NULL),
(10, 'Carlos', 'Acosta', '38543210', '3704012345', 'Barrio Don Bosco, Formosa', '1500000.00', 'Activo', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_cargo`
--

CREATE TABLE `empleado_cargo` (
  `id_empleado_cargo` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleado_cargo`
--

INSERT INTO `empleado_cargo` (`id_empleado_cargo`, `id_empleado`, `id_cargo`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 2),
(4, 2, 6),
(5, 3, 3),
(6, 3, 8),
(7, 4, 4),
(8, 5, 5),
(9, 6, 6),
(10, 6, 1),
(11, 7, 7),
(12, 7, 8),
(13, 8, 8),
(14, 8, 9),
(15, 9, 9),
(16, 9, 2),
(17, 10, 10),
(18, 10, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_obra`
--

CREATE TABLE `empleado_obra` (
  `id_empleado_obra` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleado_obra`
--

INSERT INTO `empleado_obra` (`id_empleado_obra`, `id_empleado`, `id_obra`, `fecha_asignacion`) VALUES
(21, 1, 1, '2026-01-10'),
(22, 2, 1, '2026-01-10'),
(23, 10, 1, '2026-01-10'),
(24, 3, 2, '2026-02-05'),
(25, 4, 2, '2026-02-05'),
(26, 5, 2, '2026-02-05'),
(27, 10, 2, '2026-02-05'),
(28, 1, 3, '2026-03-12'),
(29, 6, 3, '2026-03-12'),
(30, 7, 3, '2026-03-12'),
(31, 10, 3, '2026-03-12'),
(32, 2, 4, '2026-04-08'),
(33, 8, 4, '2026-04-08'),
(34, 9, 4, '2026-04-08'),
(35, 10, 4, '2026-04-08');

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
(1, 1, 'Planificación', 'Planificación y organización inicial del proyecto.', '2026-01-02', '2026-01-08', 'Finalizada'),
(2, 1, 'Cimientos', 'Excavación y construcción de cimientos.', '2026-01-09', '2026-01-25', 'Finalizada'),
(3, 1, 'Estructura', 'Construcción de columnas, vigas y losas.', '2026-01-26', '2026-02-20', 'Finalizada'),
(4, 1, 'Mampostería', 'Levantamiento de paredes.', '2026-02-21', '2026-03-15', 'Finalizada'),
(5, 1, 'Techado', 'Construcción y colocación del techo.', '2026-03-16', '2026-03-30', 'Finalizada'),
(6, 1, 'Instalaciones', 'Instalaciones eléctricas y sanitarias.', '2026-04-01', '2026-04-25', 'Finalizada'),
(7, 1, 'Terminaciones', 'Pintura, pisos y detalles finales.', '2026-04-26', NULL, 'En Proceso'),
(8, 2, 'Planificación', 'Planificación de la remodelación.', '2026-02-01', '2026-02-07', 'Finalizada'),
(9, 2, 'Demolición', 'Retiro de estructuras existentes.', '2026-02-08', '2026-02-20', 'Finalizada'),
(10, 2, 'Estructura', 'Adecuación estructural del local.', '2026-02-21', '2026-03-15', 'Finalizada'),
(11, 2, 'Instalaciones', 'Instalaciones eléctricas y sanitarias.', '2026-03-16', NULL, 'En Proceso'),
(12, 2, 'Terminaciones', 'Pintura y acabados.', NULL, NULL, 'Pendiente'),
(13, 3, 'Planificación', 'Planificación del edificio.', '2026-03-01', '2026-03-10', 'Finalizada'),
(14, 3, 'Movimiento de Suelo', 'Preparación del terreno.', '2026-03-11', '2026-03-25', 'Finalizada'),
(15, 3, 'Cimientos', 'Construcción de bases del edificio.', '2026-03-26', NULL, 'En Proceso'),
(16, 3, 'Estructura', 'Construcción de la estructura principal.', NULL, NULL, 'Pendiente'),
(17, 3, 'Mampostería', 'Construcción de paredes.', NULL, NULL, 'Pendiente'),
(18, 4, 'Planificación', 'Planificación de la ampliación.', '2026-04-01', NULL, 'En Proceso'),
(19, 4, 'Cimientos', 'Construcción de cimientos.', NULL, NULL, 'Pendiente'),
(20, 4, 'Estructura', 'Construcción de estructura.', NULL, NULL, 'Pendiente'),
(21, 4, 'Terminaciones', 'Acabados finales.', NULL, NULL, 'Pendiente');

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

--
-- Volcado de datos para la tabla `foto_obra`
--

INSERT INTO `foto_obra` (`id_foto`, `id_obra`, `ruta_imagen`, `descripcion`, `fecha`) VALUES
(1, 1, 'assets/img/obras/obra1_planificacion.jpg', 'Inicio de planificación y preparación del terreno.', '2026-01-05'),
(2, 1, 'assets/img/obras/obra1_cimientos.jpg', 'Construcción de los cimientos de la vivienda.', '2026-01-20'),
(3, 1, 'assets/img/obras/obra1_estructura.jpg', 'Avance de columnas y estructura principal.', '2026-02-15'),
(4, 1, 'assets/img/obras/obra1_terminaciones.jpg', 'Trabajos finales de pintura y acabados.', '2026-05-05'),
(5, 2, 'assets/img/obras/obra2_inicio.jpg', 'Estado inicial del local antes de la remodelación.', '2026-02-05'),
(6, 2, 'assets/img/obras/obra2_demolicion.jpg', 'Retiro de estructuras antiguas.', '2026-02-15'),
(7, 2, 'assets/img/obras/obra2_instalaciones.jpg', 'Instalación eléctrica del nuevo espacio comercial.', '2026-04-01'),
(8, 3, 'assets/img/obras/obra3_terreno.jpg', 'Preparación del terreno para construcción.', '2026-03-15'),
(9, 3, 'assets/img/obras/obra3_cimientos.jpg', 'Construcción de bases del edificio.', '2026-04-15'),
(10, 4, 'assets/img/obras/obra4_inicio.jpg', 'Inicio del proyecto de ampliación.', '2026-04-10');

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
  `numero_inventario` varchar(50) DEFAULT NULL,
  `cantidad_total` int(11) DEFAULT 1,
  `fecha_adquisicion` date DEFAULT NULL,
  `costo` decimal(12,2) DEFAULT NULL,
  `estado` enum('Disponible','Asignada','En reparación','Fuera de servicio') NOT NULL DEFAULT 'Disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `herramienta`
--

INSERT INTO `herramienta` (`id_herramienta`, `nombre`, `tipo`, `marca`, `modelo`, `numero_inventario`, `cantidad_total`, `fecha_adquisicion`, `costo`, `estado`) VALUES
(16, 'Martillo de acero', 'Manual', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(17, 'Taladro eléctrico', 'Eléctrica', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(18, 'Amoladora angular', 'Eléctrica', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(19, 'Cinta métrica 5 metros', 'Medición', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(20, 'Nivel de burbuja', 'Medición', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(21, 'Pala de punta', 'Manual', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(22, 'Pico de construcción', 'Manual', NULL, NULL, NULL, 1, NULL, NULL, 'Asignada'),
(23, 'Carretilla metálica', 'Transporte', NULL, NULL, NULL, 1, NULL, NULL, 'Asignada'),
(24, 'Mezcladora de cemento', 'Maquinaria', NULL, NULL, NULL, 1, NULL, NULL, 'Asignada'),
(25, 'Escalera extensible de aluminio', 'Altura', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(26, 'Juego de destornilladores', 'Manual', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(27, 'Llave inglesa ajustable', 'Manual', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(28, 'Soldadora eléctrica', 'Eléctrica', NULL, NULL, NULL, 1, NULL, NULL, 'En reparación'),
(29, 'Compresor de aire', 'Maquinaria', NULL, NULL, NULL, 1, NULL, NULL, 'Disponible'),
(30, 'Generador eléctrico portátil', 'Maquinaria', NULL, NULL, NULL, 1, NULL, NULL, 'Fuera de servicio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramienta_obra`
--

CREATE TABLE `herramienta_obra` (
  `id_herramienta_obra` int(11) NOT NULL,
  `id_herramienta` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `id_obra` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `id_empleado` int(11) NOT NULL,
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

--
-- Volcado de datos para la tabla `incidencia`
--

INSERT INTO `incidencia` (`id_incidencia`, `id_obra`, `fecha`, `tipo_incidencia`, `descripcion`, `estado`, `solucion`) VALUES
(1, 1, '2026-02-03', 'Clima', 'Las lluvias intensas impidieron continuar con los trabajos exteriores de la obra.', 'Resuelta', 'Se reprogramaron las actividades y se retomaron los trabajos una vez mejoradas las condiciones climáticas.'),
(2, 1, '2026-04-12', 'Material', 'Se detectó falta de materiales eléctricos necesarios para finalizar las instalaciones.', 'Resuelta', 'Se realizó la compra de materiales faltantes al proveedor correspondiente.'),
(3, 1, '2026-05-02', 'Personal', 'Un empleado no pudo asistir durante varios días afectando el cronograma de terminaciones.', 'Resuelta', 'Se reorganizaron las tareas asignando personal disponible de la obra.'),
(4, 2, '2026-02-14', 'Diseño/Planos', 'Durante la demolición se encontraron diferencias entre los planos y la estructura existente.', 'Resuelta', 'Se actualizaron los planos y se adaptó el diseño según las condiciones encontradas.'),
(5, 2, '2026-03-25', 'Retraso', 'Los trabajos internos tuvieron una demora debido a problemas con la entrega de materiales.', 'Resuelta', 'Se coordinó una nueva fecha de entrega con el proveedor.'),
(6, 2, '2026-04-08', 'Herramientas', 'Una herramienta utilizada para trabajos eléctricos presentó fallas.', 'Resuelta', 'Se realizó mantenimiento y reemplazo temporal de la herramienta.'),
(7, 3, '2026-03-28', 'Clima', 'Las condiciones climáticas retrasaron la preparación inicial del terreno.', 'Resuelta', 'Se modificó el cronograma de trabajo para compensar los días perdidos.'),
(8, 3, '2026-04-18', 'Herramientas', 'La maquinaria necesaria para excavación no estuvo disponible en la fecha prevista.', 'En revisión', 'Se está coordinando disponibilidad de maquinaria con proveedores.'),
(9, 4, '2026-05-08', 'Cliente', 'El cliente solicitó modificaciones en la distribución del espacio construido.', 'En revisión', 'Pendiente de aprobación del nuevo diseño de ampliación.'),
(10, 4, '2026-05-15', 'Diseño/Planos', 'Se requirió modificar parte de los planos originales por cambios solicitados.', 'Pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso`
--

CREATE TABLE `ingreso` (
  `id_ingreso` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
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
(1, 'Cemento Portland 50 kg', 'Bolsa de cemento Portland de 50 kg.', '250.00', '0.00', 'Bolsa', 1),
(2, 'Arena fina', 'Arena fina para revoques y terminaciones.', '80.00', '0.00', 'm³', 1),
(3, 'Arena gruesa', 'Arena gruesa para hormigón.', '120.00', '0.00', 'm³', 1),
(4, 'Piedra partida', 'Piedra para elaboración de hormigón.', '90.00', '0.00', 'm³', 1),
(5, 'Cal hidratada', 'Cal para mezclas de albañilería.', '120.00', '0.00', 'Bolsa', 1),
(6, 'Ladrillo común', 'Ladrillo macizo de arcilla.', '8000.00', '0.00', 'Unidad', 1),
(7, 'Ladrillo hueco 18x18x33', 'Ladrillo cerámico hueco.', '4500.00', '0.00', 'Unidad', 1),
(8, 'Hierro Ø6 mm', 'Varilla de acero de 6 mm.', '350.00', '0.00', 'Unidad', 1),
(9, 'Hierro Ø8 mm', 'Varilla de acero de 8 mm.', '300.00', '0.00', 'Unidad', 1),
(10, 'Hierro Ø10 mm', 'Varilla de acero de 10 mm.', '250.00', '0.00', 'Unidad', 1),
(11, 'Hierro Ø12 mm', 'Varilla de acero de 12 mm.', '180.00', '0.00', 'Unidad', 1),
(12, 'Malla electrosoldada', 'Malla para refuerzo de losas.', '70.00', '0.00', 'Unidad', 1),
(13, 'Alambre recocido', 'Alambre para atado de armaduras.', '100.00', '0.00', 'Rollo', 1),
(14, 'Clavo 2\"', 'Clavo de acero de 2 pulgadas.', '50.00', '0.00', 'Kg', 1),
(15, 'Clavo 3\"', 'Clavo de acero de 3 pulgadas.', '40.00', '0.00', 'Kg', 1),
(16, 'Tornillo autoperforante', 'Tornillo para chapa galvanizada.', '5000.00', '0.00', 'Unidad', 1),
(17, 'Caño PVC 50 mm', 'Caño sanitario de PVC.', '120.00', '0.00', 'Unidad', 1),
(18, 'Caño PVC 110 mm', 'Caño sanitario de PVC.', '80.00', '0.00', 'Unidad', 1),
(19, 'Codo PVC 90°', 'Accesorio para instalaciones sanitarias.', '150.00', '0.00', 'Unidad', 1),
(20, 'Cable unipolar 2,5 mm²', 'Cable para instalación eléctrica.', '1000.00', '0.00', 'Metro', 1),
(21, 'Cable unipolar 4 mm²', 'Cable eléctrico de mayor sección.', '700.00', '0.00', 'Metro', 1),
(22, 'Interruptor térmico', 'Protección para circuitos eléctricos.', '45.00', '0.00', 'Unidad', 1),
(23, 'Llave de luz', 'Interruptor simple de embutir.', '120.00', '0.00', 'Unidad', 1),
(24, 'Pintura látex interior', 'Pintura para interiores.', '80.00', '0.00', 'Balde', 1),
(25, 'Pintura látex exterior', 'Pintura para exteriores.', '60.00', '0.00', 'Balde', 1),
(26, 'Membrana asfáltica', 'Membrana impermeabilizante.', '45.00', '0.00', 'Rollo', 1),
(27, 'Cerámica 45x45 cm', 'Piso cerámico.', '900.00', '0.00', 'm²', 1),
(28, 'Adhesivo para cerámicos', 'Pegamento para revestimientos.', '120.00', '0.00', 'Bolsa', 1),
(29, 'Pastina', 'Material para juntas de cerámicos.', '90.00', '0.00', 'Bolsa', 1),
(30, 'Chapa galvanizada', 'Chapa para cubiertas.', '130.00', '0.00', 'Unidad', 1);

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
-- Estructura de tabla para la tabla `obra`
--

CREATE TABLE `obra` (
  `id_obra` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `nombre_obra` varchar(150) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `porcentaje_avance` decimal(5,2) DEFAULT 0.00,
  `estado` enum('Planificacion','En Proceso','Finalizada','Cancelada','Suspendida') DEFAULT 'Planificacion'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `obra`
--

INSERT INTO `obra` (`id_obra`, `id_cliente`, `nombre_obra`, `direccion`, `descripcion`, `fecha_inicio`, `fecha_fin`, `porcentaje_avance`, `estado`) VALUES
(1, 1, 'Construcción Vivienda Familiar López', 'Av. Italia 1250, Formosa', 'Construcción de una vivienda unifamiliar de dos plantas.', '2026-01-15', '2026-10-30', '95.00', 'En Proceso'),
(2, 2, 'Remodelación Local Comercial Suárez', 'B° San Martín 845, Formosa', 'Remodelación completa de un local comercial, incluyendo instalaciones eléctricas y sanitarias.', '2026-02-10', '2026-06-20', '70.00', 'Finalizada'),
(3, 3, 'Edificio Residencial Morales', 'Av. González Lelong 542, Formosa', 'Construcción de un edificio residencial de cuatro departamentos.', '2026-03-05', '2027-02-28', '30.00', 'En Proceso'),
(4, 4, 'Ampliación Vivienda Almirón', 'B° Guadalupe 1130, Formosa', 'Ampliación de vivienda con construcción de cochera y dos habitaciones.', '2026-04-01', '2026-08-15', '10.00', 'En Proceso');

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
(5, 'Depósito', 'Administra materiales, herramientas e inventario.'),
(6, 'Cliente', 'Consulta el estado de sus obras, documentos y presupuestos.');

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
  `id_empleado` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(1) DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `id_rol`, `nombre`, `apellido`, `correo`, `contraseña`, `fecha_registro`, `estado`) VALUES
(7, 2, 'Carlos', 'Martínez', 'carlos.martinez@constructora.com', 'gerente123', '2026-07-01 08:00:00', 1),
(8, 2, 'Mariana', 'López', 'mariana.lopez@constructora.com', 'gerente123', '2026-07-01 08:10:00', 1),
(9, 3, 'Laura', 'Gómez', 'laura.gomez@constructora.com', 'admin123', '2026-07-01 08:20:00', 1),
(10, 3, 'Diego', 'Benítez', 'diego.benitez@constructora.com', 'admin123', '2026-07-01 08:30:00', 1),
(11, 3, 'Valeria', 'Romero', 'valeria.romero@constructora.com', 'admin123', '2026-07-01 08:40:00', 1),
(12, 4, 'Miguel', 'Fernández', 'miguel.fernandez@constructora.com', 'jefe123', '2026-07-01 08:50:00', 1),
(13, 4, 'Ricardo', 'Acosta', 'ricardo.acosta@constructora.com', 'jefe123', '2026-07-01 09:00:00', 1),
(14, 4, 'Sergio', 'Vera', 'sergio.vera@constructora.com', 'jefe123', '2026-07-01 09:10:00', 1),
(15, 5, 'Jorge', 'Ramírez', 'jorge.ramirez@constructora.com', 'deposito123', '2026-07-01 09:20:00', 1),
(16, 5, 'Ramón', 'Ortiz', 'ramon.ortiz@constructora.com', 'deposito123', '2026-07-01 09:30:00', 1),
(17, 1, 'Juan', 'Pérez', 'juan.perez@constructora.com', 'empleado123', '2026-07-01 09:40:00', 1),
(18, 1, 'Pedro', 'Sosa', 'pedro.sosa@constructora.com', 'empleado123', '2026-07-01 09:50:00', 1),
(19, 1, 'Lucas', 'Giménez', 'lucas.gimenez@constructora.com', 'empleado123', '2026-07-01 10:00:00', 1),
(20, 1, 'Gabriel', 'Rojas', 'gabriel.rojas@constructora.com', 'empleado123', '2026-07-01 10:10:00', 1),
(21, 1, 'Matías', 'Silva', 'matias.silva@constructora.com', 'empleado123', '2026-07-01 10:20:00', 1),
(22, 1, 'José', 'Mendoza', 'jose.mendoza@constructora.com', 'empleado123', '2026-07-01 10:30:00', 1),
(23, 6, 'Ana', 'López', 'ana.lopez@gmail.com', 'cliente123', '2026-07-01 10:40:00', 1),
(24, 6, 'Roberto', 'Suárez', 'roberto.suarez@gmail.com', 'cliente123', '2026-07-01 10:50:00', 1),
(25, 6, 'Patricia', 'Morales', 'patricia.morales@gmail.com', 'cliente123', '2026-07-01 11:00:00', 1),
(26, 6, 'Fernando', 'Almirón', 'fernando.almiron@gmail.com', 'cliente123', '2026-07-01 11:10:00', 1);

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
  ADD KEY `idx_asistencia_empleado` (`id_empleado`),
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
-- Indices de la tabla `avance_obra`
--
ALTER TABLE `avance_obra`
  ADD PRIMARY KEY (`id_avance`),
  ADD KEY `idx_avance_obra` (`id_obra`);

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `fk_cliente_usuario` (`id_usuario`);

--
-- Indices de la tabla `cobro`
--
ALTER TABLE `cobro`
  ADD PRIMARY KEY (`id_cobro`),
  ADD KEY `idx_cobro_cliente` (`id_cliente`),
  ADD KEY `idx_cobro_obra` (`id_obra`),
  ADD KEY `fk_cobro_metodo` (`id_metodo_pago`);

--
-- Indices de la tabla `cuenta_cobrar`
--
ALTER TABLE `cuenta_cobrar`
  ADD PRIMARY KEY (`id_cuenta_cobrar`),
  ADD KEY `idx_cuentacobrar_cliente` (`id_cliente`);

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
-- Indices de la tabla `documento_obra`
--
ALTER TABLE `documento_obra`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `fk_documento_usuario` (`id_usuario`),
  ADD KEY `idx_documento_obra` (`id_obra`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id_empleado`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD KEY `idx_empleado_documento` (`documento`),
  ADD KEY `fk_empleado_usuario` (`id_usuario`);

--
-- Indices de la tabla `empleado_cargo`
--
ALTER TABLE `empleado_cargo`
  ADD PRIMARY KEY (`id_empleado_cargo`),
  ADD KEY `idx_empcargo_empleado` (`id_empleado`),
  ADD KEY `idx_empcargo_cargo` (`id_cargo`);

--
-- Indices de la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  ADD PRIMARY KEY (`id_empleado_obra`),
  ADD KEY `idx_empobra_empleado` (`id_empleado`),
  ADD KEY `idx_empobra_obra` (`id_obra`);

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
  ADD KEY `idx_herrobra_herramienta` (`id_herramienta`),
  ADD KEY `idx_herrobra_obra` (`id_obra`);

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
  ADD KEY `idx_horas_empleado` (`id_empleado`),
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
  ADD KEY `idx_ingreso_cliente` (`id_cliente`),
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
-- Indices de la tabla `obra`
--
ALTER TABLE `obra`
  ADD PRIMARY KEY (`id_obra`),
  ADD KEY `idx_obra_cliente` (`id_cliente`);

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
  ADD KEY `idx_tarea_empleado` (`id_empleado`),
  ADD KEY `idx_tarea_obra` (`id_obra`);

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
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `avance_diario`
--
ALTER TABLE `avance_diario`
  MODIFY `id_avance_diario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `avance_obra`
--
ALTER TABLE `avance_obra`
  MODIFY `id_avance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT de la tabla `documento_obra`
--
ALTER TABLE `documento_obra`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `empleado_cargo`
--
ALTER TABLE `empleado_cargo`
  MODIFY `id_empleado_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  MODIFY `id_empleado_obra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `etapa_obra`
--
ALTER TABLE `etapa_obra`
  MODIFY `id_etapa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `id_herramienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `herramienta_obra`
--
ALTER TABLE `herramienta_obra`
  MODIFY `id_herramienta_obra` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
-- AUTO_INCREMENT de la tabla `obra`
--
ALTER TABLE `obra`
  MODIFY `id_obra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
  ADD CONSTRAINT `fk_asistencia_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE;

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
-- Filtros para la tabla `avance_obra`
--
ALTER TABLE `avance_obra`
  ADD CONSTRAINT `fk_avance_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `cobro`
--
ALTER TABLE `cobro`
  ADD CONSTRAINT `fk_cobro_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_cobro_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodo_pago` (`id_metodo_pago`),
  ADD CONSTRAINT `fk_cobro_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cuenta_cobrar`
--
ALTER TABLE `cuenta_cobrar`
  ADD CONSTRAINT `fk_cuentacobrar_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`);

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
-- Filtros para la tabla `documento_obra`
--
ALTER TABLE `documento_obra`
  ADD CONSTRAINT `fk_documento_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_documento_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `fk_empleado_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `empleado_cargo`
--
ALTER TABLE `empleado_cargo`
  ADD CONSTRAINT `fk_empcargo_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_empcargo_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  ADD CONSTRAINT `fk_empobra_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_empobra_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_herrobra_herramienta` FOREIGN KEY (`id_herramienta`) REFERENCES `herramienta` (`id_herramienta`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_herrobra_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_horas_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_horas_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD CONSTRAINT `fk_incidencia_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ingreso`
--
ALTER TABLE `ingreso`
  ADD CONSTRAINT `fk_ingreso_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ingreso_cobro` FOREIGN KEY (`id_cobro`) REFERENCES `cobro` (`id_cobro`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ingreso_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE SET NULL;

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
-- Filtros para la tabla `obra`
--
ALTER TABLE `obra`
  ADD CONSTRAINT `fk_obra_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`);

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
-- Filtros para la tabla `solicitud_material`
--
ALTER TABLE `solicitud_material`
  ADD CONSTRAINT `fk_solicitud_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD CONSTRAINT `fk_tarea_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tarea_obra` FOREIGN KEY (`id_obra`) REFERENCES `obra` (`id_obra`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

CREATE TABLE permisos (
    id_permiso INT AUTO_INCREMENT PRIMARY KEY,
    nombre_permiso VARCHAR(100) NOT NULL
);

INSERT INTO permisos (nombre_permiso) VALUES
('usuarios'),
('roles'),
('obras'),
('clientes'),
('empleados'),
('materiales'),
('herramientas'),
('inventario'),
('presupuestos'),
('costos'),
('documentos'),
('avances'),
('tareas'),
('asistencia'),
('incidencias'),
('reportes'),
('caja'),
('cuentas cobrar'),
('cuentas pagar'),
('proveedores'),
('pagos'),
('perfil');

CREATE TABLE rol_permiso (
    id_rol INT NOT NULL,
    id_permiso INT NOT NULL,
    PRIMARY KEY (id_rol, id_permiso),
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol),
    FOREIGN KEY (id_permiso) REFERENCES permisos(id_permiso)
);

ALTER TABLE auditoria
ADD COLUMN id_registro INT NULL AFTER tabla_afectada;

ALTER TABLE obra
ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1
AFTER estado;

ALTER TABLE avance_obra
ADD COLUMN id_etapa INT NOT NULL AFTER id_obra;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
