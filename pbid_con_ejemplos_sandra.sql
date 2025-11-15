-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-11-2025 a las 00:22:34
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
-- Base de datos: `pbid`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anuncios`
--

CREATE TABLE `anuncios` (
  `IdAnuncio` int(11) NOT NULL,
  `TAnuncio` smallint(5) UNSIGNED DEFAULT NULL,
  `TVivienda` smallint(5) UNSIGNED DEFAULT NULL,
  `FPrincipal` varchar(255) DEFAULT NULL,
  `Alternativo` varchar(255) NOT NULL,
  `Titulo` varchar(255) DEFAULT NULL,
  `Precio` decimal(12,2) DEFAULT NULL,
  `Texto` text DEFAULT NULL,
  `Ciudad` varchar(255) DEFAULT NULL,
  `Pais` int(11) DEFAULT NULL,
  `Superficie` decimal(10,2) DEFAULT NULL,
  `NHabitaciones` int(11) DEFAULT NULL,
  `NBanyos` int(11) DEFAULT NULL,
  `Planta` int(11) DEFAULT NULL,
  `Anyo` int(11) DEFAULT NULL,
  `FRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `Usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `anuncios`
--

INSERT INTO `anuncios` (`IdAnuncio`, `TAnuncio`, `TVivienda`, `FPrincipal`, `Alternativo`, `Titulo`, `Precio`, `Texto`, `Ciudad`, `Pais`, `Superficie`, `NHabitaciones`, `NBanyos`, `Planta`, `Anyo`, `FRegistro`, `Usuario`) VALUES
(1, 2, 2, 'img/piso_alicante1.jpg', 'Piso amplio y luminoso', 'Piso en alquiler en Alicante', 550.00, '3 hab, 2 baños, céntrico', 'Alicante', 1, 90.00, 3, 2, 1, 2010, '2025-11-14 11:48:49', 1),
(2, 1, 2, 'img/piso_valencia1.jpg', 'Luminoso y reformado', 'Piso en venta en Valencia', 120000.00, '2 hab, buena iluminación', 'Valencia', 1, 80.00, 2, 1, 2, 2005, '2025-11-12 11:56:39', 2),
(3, 2, 3, 'img/oficina_alicante.jpg', 'Oficina luminosa en centro', 'Oficina en alquiler en Alicante', 700.00, 'Oficina de 80 m2, 2 despachos y recepción', 'Alicante', 1, 80.00, 0, 1, 0, 2012, '2025-11-13 12:19:14', 1),
(4, 2, 4, 'img/local_valencia.jpg', 'Local en zona comercial', 'Local en alquiler en Valencia', 1200.00, 'Local 120 m2 con escaparate grande', 'Valencia', 1, 120.00, 0, 1, 0, 2008, '2025-11-11 12:19:14', 2),
(5, 1, 5, 'img/garaje_valencia2.jpg', 'Plaza de garaje subterránea', 'Plaza de garaje en venta en Valencia', 15000.00, 'Plaza en garaje comunitario, acceso automático', 'Valencia', 1, 12.00, 0, 0, 0, 1997, '2025-11-09 12:19:14', 3),
(6, 2, 3, 'img/oficina_madrid.jpg', 'Oficina céntrica y luminosa', 'Oficina en alquiler en Madrid', 1400.00, 'Oficina 100 m2, varios despachos, buena comunicación', 'Madrid', 1, 100.00, 0, 1, 3, 2014, '2025-11-07 12:19:14', 1),
(7, 2, 4, 'img/local_barcelona.jpg', 'Local en barrio comercial', 'Local en alquiler en Barcelona', 2000.00, 'Local 90 m2, zona de mucho paso', 'Barcelona', 1, 90.00, 0, 1, 0, 2011, '2025-11-05 12:19:14', 3),
(8, 1, 5, 'img/garaje_madrid.jpg', 'Plaza de garaje céntrica', 'Plaza de garaje en venta en Madrid', 18000.00, 'Plaza amplia en garaje vigilado', 'Madrid', 1, 13.00, 0, 0, 0, 2002, '2025-11-03 12:19:14', 2),
(9, 2, 3, 'img/oficina_sevilla.jpg', 'Despacho con luz natural', 'Oficina en alquiler en Sevilla', 600.00, 'Despacho 60 m2 con dos puestos de trabajo', 'Sevilla', 1, 60.00, 0, 1, 1, 2010, '2025-11-01 12:19:14', 1),
(10, 2, 4, 'img/local_zaragoza.jpg', 'Local con excelente ubicación', 'Local en alquiler en Zaragoza', 800.00, 'Local 75 m2, zona comercial', 'Zaragoza', 1, 75.00, 0, 1, 0, 2007, '2025-10-30 12:19:14', 2),
(11, 1, 5, 'img/garaje_bilbao.jpg', 'Plaza cómoda y segura', 'Plaza de garaje en venta en Bilbao', 16000.00, 'Plaza amplia en garaje con vigilancia', 'Bilbao', 1, 12.50, 0, 0, 0, 2000, '2025-10-28 12:19:14', 3),
(12, 2, 3, 'img/oficina_granada.jpg', 'Despacho con vistas', 'Oficina en alquiler en Granada', 500.00, 'Despacho 55 m2, ideal para consultorio', 'Granada', 1, 55.00, 0, 1, 2, 2013, '2025-10-26 12:19:14', 2),
(13, 2, 4, 'img/local_murcia.jpg', 'Local comercial reformado', 'Local en alquiler en Murcia', 650.00, 'Local 50 m2, reformado hace 2 años', 'Murcia', 1, 50.00, 0, 1, 0, 2019, '2025-10-24 12:19:14', 1),
(14, 1, 5, 'img/garaje_castellon.jpg', 'Plaza en garaje privado', 'Plaza de garaje en venta en Castellón', 10000.00, 'Plaza en garaje privado, fácil acceso', 'Castellón', 1, 11.00, 0, 0, 0, 1995, '2025-10-22 12:19:14', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estilos`
--

CREATE TABLE `estilos` (
  `IdEstilo` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Fichero` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estilos`
--

INSERT INTO `estilos` (`IdEstilo`, `Nombre`, `Descripcion`, `Fichero`) VALUES
(1, 'Inmolink', 'Tema principal que importa main y variables', 'css/inmolink.css'),
(2, 'Alto contraste grande', 'Alto contraste con texto aumentado', 'css/alto_contraste_grande.css'),
(3, 'Alto contraste', 'Alto contraste para accesibilidad', 'css/alto_contraste.css'),
(4, 'Noche', 'Tema oscuro / modo noche', 'css/noche.css'),
(5, 'Texto grande', 'Texto aumentado para mejor legibilidad', 'css/texto_grande.css'),
(6, 'Texto dislexia', 'Texto grande con estilo pensado para dislexia', 'css/texto_grande_dislexia.css');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos`
--

CREATE TABLE `fotos` (
  `IdFoto` int(11) NOT NULL,
  `Titulo` varchar(255) DEFAULT NULL,
  `Foto` varchar(255) DEFAULT NULL,
  `Alternativo` varchar(255) NOT NULL,
  `Anuncio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `fotos`
--

INSERT INTO `fotos` (`IdFoto`, `Titulo`, `Foto`, `Alternativo`, `Anuncio`) VALUES
(1, 'Salón', 'img/piso_alicante1_1.jpg', 'Salón con mucha luz', 1),
(2, 'Cocina', 'img/piso_alicante1_2.jpg', 'Cocina equipada', 1),
(6, 'Salón', 'img/piso_valencia1_1.jpg', 'Salón muy acogedor', 2),
(7, 'Cocina', 'img/piso_valencia1_2.jpg', 'Cocina espaciosa y preparada', 2),
(8, 'Entrada', 'img/oficina1.jpg', 'Entrada y escaparate', 3),
(9, 'Despacho', 'img/oficina2.jpg', 'Despacho principal', 3),
(10, 'Interior', 'img/local1.jpg', 'Zona de atención al público', 4),
(11, 'Plaza', 'img/garaje2.jpg', 'Plaza numerada', 5),
(12, 'Recepción', 'img/oficina4.jpg', 'Recepción y zona espera', 6),
(13, 'Despacho principal', 'img/oficina2.jpg', 'Despacho con ventana', 6),
(14, 'Escaparate', 'img/local3.jpg', 'Escaparate grande', 7),
(15, 'Zona interior', 'img/local4.jpg', 'Zona atención al público', 7),
(16, 'Plaza', 'img/garaje1.jpg', 'Plaza amplia', 8),
(17, 'Acceso', 'img/garaje3.jpg', 'Acceso al garaje', 8),
(18, 'Vista interior', 'img/oficina3.jpg', 'Interior con mucha luz', 9),
(19, 'Zona trabajo', 'img/oficina5.jpg', 'Zona de trabajo abierta', 9),
(20, 'Fachada', 'img/local5.jpg', 'Fachada con cartel', 10),
(21, 'Interior', 'img/local1.jpg', 'Interior diáfano', 10),
(22, 'Plaza', 'img/garaje2.jpg', 'Plaza y señalización', 11),
(23, 'Acceso', 'img/garaje4.jpg', 'Acceso con rampa', 11),
(24, 'Despacho', 'img/oficina1.jpg', 'Despacho principal', 12),
(25, 'Sala espera', 'img/oficina4.jpg', 'Sala de espera', 12),
(26, 'Interior', 'img/local2.jpg', 'Interior reformado', 13),
(27, 'Fachada', 'img/local3.jpg', 'Fachada y escaparate', 13),
(28, 'Plaza', 'img/garaje3.jpg', 'Plaza y señalética', 14),
(29, 'Trastero', 'img/garaje4.jpg', 'Pequeño trastero incluido', 14),
(30, 'Escaparate', 'img/local5.jpg', 'Escaparate a calle principal', 4),
(31, 'Entrada garaje', 'img/garaje4.jpg', 'Acceso con mando', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `IdMensaje` int(11) NOT NULL,
  `TMensaje` smallint(5) UNSIGNED DEFAULT NULL,
  `Texto` varchar(4000) DEFAULT NULL,
  `Anuncio` int(11) DEFAULT NULL,
  `UsuOrigen` int(11) DEFAULT NULL,
  `UsuDestino` int(11) DEFAULT NULL,
  `FRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`IdMensaje`, `TMensaje`, `Texto`, `Anuncio`, `UsuOrigen`, `UsuDestino`, `FRegistro`) VALUES
(1, 1, '¿Sigue disponible?', 1, 2, 1, '2025-11-13 11:53:54'),
(2, 2, 'Quisiera visitar el piso el jueves por la tarde', 1, 3, 1, '2025-11-14 11:53:54'),
(3, 3, 'ten doy 1 euro con 5 céntimos por él, trato?', 2, 1, 1, '2025-11-13 12:00:55'),
(4, 2, 'tiene desván???', 2, 3, 1, '2025-11-14 12:00:55'),
(5, 1, '¿Está disponible a partir de diciembre?', 3, 2, 1, '2025-11-14 12:37:39'),
(6, 2, '¿Puedo verlo este viernes por la tarde?', 4, 1, 2, '2025-11-12 12:37:39'),
(7, 3, '¿Aceptaría 13.000€ si la pago en efectivo?', 5, 1, 3, '2025-11-10 12:37:39'),
(8, 1, '¿Incluye gastos de comunidad?', 6, 2, 1, '2025-11-08 12:37:39'),
(9, 2, 'Me interesa para tienda de ropa, ¿se puede visitar mañana?', 7, 1, 3, '2025-11-06 12:37:39'),
(10, 1, '¿Cuál es la cuota de comunidad anual?', 8, 3, 2, '2025-11-04 12:37:39'),
(11, 2, '¿Se puede alquilar por meses cortos?', 9, 2, 1, '2025-11-02 12:37:39'),
(12, 1, '¿Cuál es la duración mínima del contrato?', 10, 3, 2, '2025-10-31 12:37:39'),
(13, 1, '¿Está disponible a partir de diciembre?', 3, 2, 1, '2025-11-14 12:38:20'),
(14, 2, '¿Puedo verlo este viernes por la tarde?', 4, 1, 2, '2025-11-12 12:38:20'),
(15, 3, '¿Aceptaría 13.000€ si la pago en efectivo?', 5, 1, 3, '2025-11-10 12:38:20'),
(16, 1, '¿Incluye gastos de comunidad?', 6, 2, 1, '2025-11-08 12:38:20'),
(17, 2, 'Me interesa para tienda de ropa, ¿se puede visitar mañana?', 7, 1, 3, '2025-11-06 12:38:20'),
(18, 1, '¿Cuál es la cuota de comunidad anual?', 8, 3, 2, '2025-11-04 12:38:20'),
(19, 2, '¿Se puede alquilar por meses cortos?', 9, 2, 1, '2025-11-02 12:38:20'),
(20, 1, '¿Cuál es la duración mínima del contrato?', 10, 3, 2, '2025-10-31 12:38:20'),
(21, 3, 'Oferta de 15.000€, ¿aceptarían?', 11, 1, 3, '2025-10-29 12:38:20'),
(22, 1, '¿Se puede visitar el lunes?', 12, 1, 2, '2025-10-27 12:38:20'),
(23, 2, '¿Podéis dar más datos del contrato?', 13, 3, 1, '2025-10-25 12:38:20'),
(24, 1, '¿Se puede visitar el sábado?', 14, 1, 3, '2025-10-23 12:38:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `IdPais` int(11) NOT NULL,
  `NomPais` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`IdPais`, `NomPais`) VALUES
(1, 'España'),
(2, 'Reino Unido'),
(3, 'Francia'),
(4, 'Alemania'),
(5, 'Estados Unidos'),
(6, 'Sandralandia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `IdSolicitud` int(11) NOT NULL,
  `Anuncio` int(11) DEFAULT NULL,
  `Texto` varchar(4000) DEFAULT NULL,
  `Nombre` varchar(200) DEFAULT NULL,
  `Email` varchar(254) DEFAULT NULL,
  `Direccion` text DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Color` varchar(100) DEFAULT NULL,
  `Copias` int(11) DEFAULT NULL,
  `Resolucion` int(11) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `IColor` tinyint(1) DEFAULT NULL,
  `IPrecio` tinyint(1) DEFAULT NULL,
  `FRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `Coste` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`IdSolicitud`, `Anuncio`, `Texto`, `Nombre`, `Email`, `Direccion`, `Telefono`, `Color`, `Copias`, `Resolucion`, `Fecha`, `IColor`, `IPrecio`, `FRegistro`, `Coste`) VALUES
(1, 1, 'Enviar folleto del inmueble', 'María López', 'maria@example.com', 'C/ Mayor 1', '600123456', 'Blanco', 1, 300, '2025-11-01', 1, 0, '2025-11-09 12:03:43', 2.50),
(2, 2, 'Solicito información', 'Carlos Ruiz', 'carlos@example.com', 'Av. España 5', '600654321', 'Color', 2, 600, '2025-11-02', 0, 1, '2025-11-13 12:03:43', 5.00),
(3, 3, 'Solicito folleto y condiciones de venta', 'Laura García', 'laura@example.com', 'C/ Nueva 10', '600111222', 'Color', 1, 300, '2025-11-05', 1, 0, '2025-11-15 12:39:20', 3.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposanuncios`
--

CREATE TABLE `tiposanuncios` (
  `IdTAnuncio` smallint(5) UNSIGNED NOT NULL,
  `NomTAnuncio` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposanuncios`
--

INSERT INTO `tiposanuncios` (`IdTAnuncio`, `NomTAnuncio`) VALUES
(1, 'Venta'),
(2, 'Alquiler'),
(3, 'sandranuncio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposmensajes`
--

CREATE TABLE `tiposmensajes` (
  `IdTMensaje` smallint(5) UNSIGNED NOT NULL,
  `NomTMensaje` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposmensajes`
--

INSERT INTO `tiposmensajes` (`IdTMensaje`, `NomTMensaje`) VALUES
(1, 'Más información'),
(2, 'Solicitar una cita'),
(3, 'Comunicar una oferta'),
(4, 'sandramensaje');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposviviendas`
--

CREATE TABLE `tiposviviendas` (
  `IdTVivienda` smallint(5) UNSIGNED NOT NULL,
  `NomTVivienda` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposviviendas`
--

INSERT INTO `tiposviviendas` (`IdTVivienda`, `NomTVivienda`) VALUES
(1, 'Obra nueva'),
(2, 'Vivienda'),
(3, 'Oficina'),
(4, 'Local'),
(5, 'Garaje'),
(6, 'sandravivienda');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `IdUsuario` int(11) NOT NULL,
  `NomUsuario` varchar(15) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `Email` varchar(254) NOT NULL,
  `Sexo` smallint(6) DEFAULT NULL,
  `FNacimiento` date DEFAULT NULL,
  `Ciudad` varchar(255) DEFAULT NULL,
  `Pais` int(11) DEFAULT NULL,
  `Foto` varchar(255) DEFAULT NULL,
  `FRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `Estilo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`IdUsuario`, `NomUsuario`, `Clave`, `Email`, `Sexo`, `FNacimiento`, `Ciudad`, `Pais`, `Foto`, `FRegistro`, `Estilo`) VALUES
(1, 'daniel', '$2y$10$7Oq3c6kzt1O3jOIa.5VS/OFz1kR4gTNUp316V.cCuk.Ym9V9XdTeS', 'daniel@example.com', 1, '1993-02-10', 'Alicante', 1, 'img/daniel.jpg', '2025-11-05 14:20:07', 1),
(2, 'sandra', '$2y$10$jkeZTmyTFVc504KJZ.mWYONNb9Vw3nr46eIDADbByiCXcPMoChYt.', 'sandra@example.com', 2, '1991-06-18', 'Valencia', 1, 'img/sandra.jpg', '2025-11-08 14:20:07', 2),
(3, 'prueba', '$2y$10$9GFhrdc1tyqSgERLquczgeuQ/I5oFXTbBWV7cpUAqe6vmQIO5TTDC', 'prueba@example.com', 1, '2000-01-01', 'PruebaCity', 1, 'img/prueba.jpg', '2025-11-12 14:20:07', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`IdAnuncio`);

--
-- Indices de la tabla `estilos`
--
ALTER TABLE `estilos`
  ADD PRIMARY KEY (`IdEstilo`);

--
-- Indices de la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`IdFoto`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`IdMensaje`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`IdPais`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`IdSolicitud`);

--
-- Indices de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  ADD PRIMARY KEY (`IdTAnuncio`);

--
-- Indices de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  ADD PRIMARY KEY (`IdTMensaje`);

--
-- Indices de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  ADD PRIMARY KEY (`IdTVivienda`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IdUsuario`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `IdAnuncio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `estilos`
--
ALTER TABLE `estilos`
  MODIFY `IdEstilo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `fotos`
--
ALTER TABLE `fotos`
  MODIFY `IdFoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `IdMensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `IdPais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `IdSolicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  MODIFY `IdTAnuncio` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  MODIFY `IdTMensaje` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  MODIFY `IdTVivienda` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
