-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-11-2025 a las 19:01:11
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
) ;

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
) ;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `IdPais` int(11) NOT NULL,
  `NomPais` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposanuncios`
--

CREATE TABLE `tiposanuncios` (
  `IdTAnuncio` smallint(5) UNSIGNED NOT NULL,
  `NomTAnuncio` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposmensajes`
--

CREATE TABLE `tiposmensajes` (
  `IdTMensaje` smallint(5) UNSIGNED NOT NULL,
  `NomTMensaje` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposviviendas`
--

CREATE TABLE `tiposviviendas` (
  `IdTVivienda` smallint(5) UNSIGNED NOT NULL,
  `NomTVivienda` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `IdUsuario` int(11) NOT NULL,
  `NomUsuario` varchar(15) NOT NULL,
  `Clave` varchar(15) NOT NULL,
  `Email` varchar(254) NOT NULL,
  `Sexo` smallint(6) DEFAULT NULL,
  `FNacimiento` date DEFAULT NULL,
  `Ciudad` varchar(255) DEFAULT NULL,
  `Pais` int(11) DEFAULT NULL,
  `Foto` varchar(255) DEFAULT NULL,
  `FRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `Estilo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`IdAnuncio`),
  ADD KEY `fk_Anuncios_TiposAnuncios` (`TAnuncio`),
  ADD KEY `fk_Anuncios_TiposViviendas` (`TVivienda`),
  ADD KEY `fk_Anuncios_Paises` (`Pais`),
  ADD KEY `fk_Anuncios_Usuarios` (`Usuario`);

--
-- Indices de la tabla `estilos`
--
ALTER TABLE `estilos`
  ADD PRIMARY KEY (`IdEstilo`);

--
-- Indices de la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`IdFoto`),
  ADD KEY `fk_Fotos_Anuncios` (`Anuncio`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`IdMensaje`),
  ADD KEY `fk_Mensajes_TiposMensajes` (`TMensaje`),
  ADD KEY `fk_Mensajes_Anuncios` (`Anuncio`),
  ADD KEY `fk_Mensajes_UsuOrigen` (`UsuOrigen`),
  ADD KEY `fk_Mensajes_UsuDestino` (`UsuDestino`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`IdPais`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`IdSolicitud`),
  ADD KEY `fk_Solicitudes_Anuncios` (`Anuncio`);

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
  ADD UNIQUE KEY `uq_Usuarios_NomUsuario` (`NomUsuario`),
  ADD UNIQUE KEY `uq_Usuarios_Email` (`Email`),
  ADD KEY `fk_Usuarios_Paises` (`Pais`),
  ADD KEY `fk_Usuarios_Estilos` (`Estilo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `IdAnuncio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estilos`
--
ALTER TABLE `estilos`
  MODIFY `IdEstilo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fotos`
--
ALTER TABLE `fotos`
  MODIFY `IdFoto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `IdMensaje` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `IdPais` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `IdSolicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  MODIFY `IdTAnuncio` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  MODIFY `IdTMensaje` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  MODIFY `IdTVivienda` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdUsuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD CONSTRAINT `fk_Anuncios_Paises` FOREIGN KEY (`Pais`) REFERENCES `paises` (`IdPais`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Anuncios_TiposAnuncios` FOREIGN KEY (`TAnuncio`) REFERENCES `tiposanuncios` (`IdTAnuncio`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Anuncios_TiposViviendas` FOREIGN KEY (`TVivienda`) REFERENCES `tiposviviendas` (`IdTVivienda`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Anuncios_Usuarios` FOREIGN KEY (`Usuario`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD CONSTRAINT `fk_Fotos_Anuncios` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `fk_Mensajes_Anuncios` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Mensajes_TiposMensajes` FOREIGN KEY (`TMensaje`) REFERENCES `tiposmensajes` (`IdTMensaje`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Mensajes_UsuDestino` FOREIGN KEY (`UsuDestino`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Mensajes_UsuOrigen` FOREIGN KEY (`UsuOrigen`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_Solicitudes_Anuncios` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_Usuarios_Estilos` FOREIGN KEY (`Estilo`) REFERENCES `estilos` (`IdEstilo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Usuarios_Paises` FOREIGN KEY (`Pais`) REFERENCES `paises` (`IdPais`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
