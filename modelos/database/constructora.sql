-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-07-2026 a las 02:11:00
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
-- Estructura de tabla para la tabla `avance_diario`
--

CREATE TABLE `avance_diario` (
  `id_avance_diario` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo`
--

CREATE TABLE `cargo` (
  `id_cargo` int(11) NOT NULL,
  `nombre_cargo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `estado` tinyint(1) DEFAULT 1,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_cargo`
--

CREATE TABLE `empleado_cargo` (
  `id_empleado_cargo` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `estado` enum('Disponible','Asignada','En Reparacion','Fuera de Servicio') DEFAULT 'Disponible',
  `disponibilidad` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramienta_obra`
--

CREATE TABLE `herramienta_obra` (
  `id_herramienta_obra` int(11) NOT NULL,
  `id_herramienta` int(11) NOT NULL,
  `id_obra` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL
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
  `descripcion` text DEFAULT NULL,
  `problema_detectado` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `costo` decimal(12,2) DEFAULT NULL
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
  `unidad_medida` varchar(20) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `detalle_general` text DEFAULT NULL COMMENT 'RF extra: observaciones'
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor_material`
--

CREATE TABLE `proveedor_material` (
  `id_proveedor_material` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_material` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  ADD KEY `idx_mant_herramienta` (`id_herramienta`);

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
  ADD KEY `idx_movinventario_material` (`id_material`);

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
  ADD KEY `idx_presupuesto_obra` (`id_obra`);

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
-- AUTO_INCREMENT de la tabla `avance_diario`
--
ALTER TABLE `avance_diario`
  MODIFY `id_avance_diario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `avance_obra`
--
ALTER TABLE `avance_obra`
  MODIFY `id_avance` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleado_cargo`
--
ALTER TABLE `empleado_cargo`
  MODIFY `id_empleado_cargo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleado_obra`
--
ALTER TABLE `empleado_obra`
  MODIFY `id_empleado_obra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `etapa_obra`
--
ALTER TABLE `etapa_obra`
  MODIFY `id_etapa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `foto_obra`
--
ALTER TABLE `foto_obra`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gasto_general`
--
ALTER TABLE `gasto_general`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `herramienta`
--
ALTER TABLE `herramienta`
  MODIFY `id_herramienta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `herramienta_obra`
--
ALTER TABLE `herramienta_obra`
  MODIFY `id_herramienta_obra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horas_trabajadas`
--
ALTER TABLE `horas_trabajadas`
  MODIFY `id_hora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  MODIFY `id_incidencia` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_obra` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_precio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  MODIFY `id_presupuesto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor_material`
--
ALTER TABLE `proveedor_material`
  MODIFY `id_proveedor_material` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reporte`
--
ALTER TABLE `reporte`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `fk_mant_herramienta` FOREIGN KEY (`id_herramienta`) REFERENCES `herramienta` (`id_herramienta`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_movinventario_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`);

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

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

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario`
(`id_rol`, `nombre`, `apellido`, `correo`, `contraseña`, `fecha_registro`, `estado`)
VALUES
(2, 'Carlos', 'Martínez', 'carlos.martinez@constructora.com', 'gerente123', '2026-07-01 08:00:00', 1),
(2, 'Mariana', 'López', 'mariana.lopez@constructora.com', 'gerente123', '2026-07-01 08:10:00', 1),
(3, 'Laura', 'Gómez', 'laura.gomez@constructora.com', 'admin123', '2026-07-01 08:20:00', 1),
(3, 'Diego', 'Benítez', 'diego.benitez@constructora.com', 'admin123', '2026-07-01 08:30:00', 1),
(3, 'Valeria', 'Romero', 'valeria.romero@constructora.com', 'admin123', '2026-07-01 08:40:00', 1),
(4, 'Miguel', 'Fernández', 'miguel.fernandez@constructora.com', 'jefe123', '2026-07-01 08:50:00', 1),
(4, 'Ricardo', 'Acosta', 'ricardo.acosta@constructora.com', 'jefe123', '2026-07-01 09:00:00', 1),
(4, 'Sergio', 'Vera', 'sergio.vera@constructora.com', 'jefe123', '2026-07-01 09:10:00', 1),
(5, 'Jorge', 'Ramírez', 'jorge.ramirez@constructora.com', 'deposito123', '2026-07-01 09:20:00', 1),
(5, 'Ramón', 'Ortiz', 'ramon.ortiz@constructora.com', 'deposito123', '2026-07-01 09:30:00', 1),
(1, 'Juan', 'Pérez', 'juan.perez@constructora.com', 'empleado123', '2026-07-01 09:40:00', 1),
(1, 'Pedro', 'Sosa', 'pedro.sosa@constructora.com', 'empleado123', '2026-07-01 09:50:00', 1),
(1, 'Lucas', 'Giménez', 'lucas.gimenez@constructora.com', 'empleado123', '2026-07-01 10:00:00', 1),
(1, 'Gabriel', 'Rojas', 'gabriel.rojas@constructora.com', 'empleado123', '2026-07-01 10:10:00', 1),
(1, 'Matías', 'Silva', 'matias.silva@constructora.com', 'empleado123', '2026-07-01 10:20:00', 1),
(1, 'José', 'Mendoza', 'jose.mendoza@constructora.com', 'empleado123', '2026-07-01 10:30:00', 1),
(6, 'Ana', 'López', 'ana.lopez@gmail.com', 'cliente123', '2026-07-01 10:40:00', 1),
(6, 'Roberto', 'Suárez', 'roberto.suarez@gmail.com', 'cliente123', '2026-07-01 10:50:00', 1),
(6, 'Patricia', 'Morales', 'patricia.morales@gmail.com', 'cliente123', '2026-07-01 11:00:00', 1),
(6, 'Fernando', 'Almirón', 'fernando.almiron@gmail.com', 'cliente123', '2026-07-01 11:10:00', 1);

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente`
(`nombre`, `apellido`, `telefono`, `direccion`, `correo`, `id_usuario`)
VALUES
('Ana', 'López', '3704123456', 'Av. Italia 1250, Formosa', 'ana.lopez@gmail.com', 17),
('Roberto', 'Suárez', '3704234567', 'B° San Martín 845, Formosa', 'roberto.suarez@gmail.com', 18),
('Patricia', 'Morales', '3704345678', 'Av. González Lelong 542, Formosa', 'patricia.morales@gmail.com', 19),
('Fernando', 'Almirón', '3704456789', 'B° Guadalupe 1130, Formosa', 'fernando.almiron@gmail.com', 20);

--
-- Volcado de datos para la tabla `obra`
--

INSERT INTO `obra`
(`id_cliente`, `nombre_obra`, `direccion`, `descripcion`, `fecha_inicio`, `fecha_fin`, `porcentaje_avance`, `estado`)
VALUES
(1, 'Construcción Vivienda Familiar López', 'Av. Italia 1250, Formosa', 'Construcción de una vivienda unifamiliar de dos plantas.', '2026-01-15', '2026-10-30', 45.00, 'En Proceso'),

(2, 'Remodelación Local Comercial Suárez', 'B° San Martín 845, Formosa', 'Remodelación completa de un local comercial, incluyendo instalaciones eléctricas y sanitarias.', '2026-02-10', '2026-06-20', 100.00, 'Finalizada'),

(3, 'Edificio Residencial Morales', 'Av. González Lelong 542, Formosa', 'Construcción de un edificio residencial de cuatro departamentos.', '2026-03-05', '2027-02-28', 25.00, 'En Proceso'),

(4, 'Ampliación Vivienda Almirón', 'B° Guadalupe 1130, Formosa', 'Ampliación de vivienda con construcción de cochera y dos habitaciones.', '2026-04-01', '2026-08-15', 70.00, 'En Proceso');