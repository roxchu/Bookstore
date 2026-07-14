-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-07-2026 a las 01:33:24
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
-- Base de datos: `books_store`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrusel`
--

CREATE TABLE `carrusel` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `posicion` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrusel`
--

INSERT INTO `carrusel` (`id`, `id_producto`, `posicion`, `activo`) VALUES
(1, 1, 1, 0),
(2, 2, 2, 0),
(3, 3, 3, 0),
(4, 4, 4, 0),
(5, 5, 5, 0),
(6, 6, 6, 0),
(7, 2, 1, 0),
(8, 3, 2, 0),
(9, 4, 3, 0),
(10, 5, 4, 0),
(11, 6, 5, 0),
(12, 3, 1, 0),
(13, 4, 2, 0),
(14, 5, 3, 0),
(15, 6, 4, 0),
(16, 3, 1, 1),
(17, 4, 2, 1),
(18, 5, 3, 1),
(19, 6, 4, 1),
(20, 8, 5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `nombre_producto` varchar(255) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id_detalle`, `id_venta`, `nombre_producto`, `precio`) VALUES
(1, 2, 'El Resplandor', 18500.50),
(2, 3, 'El Resplandor', 18500.50),
(3, 4, 'El Resplandor', 18500.50),
(4, 5, 'Harry Potter y la Piedra Filosofal', 25000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genero`
--

CREATE TABLE `genero` (
  `id_genero` int(11) NOT NULL,
  `nombre_genero` varchar(50) NOT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `genero`
--

INSERT INTO `genero` (`id_genero`, `nombre_genero`, `destacado`, `activo`) VALUES
(1, 'Fantasía', 0, 1),
(2, 'Terror', 0, 0),
(3, 'Romance', 0, 1),
(4, 'Comedia', 0, 1),
(5, 'Poesia', 0, 0),
(6, 'Aventura', 0, 1),
(7, 'papoi', 0, 0),
(8, 'Terror', 1, 0),
(9, 'Terror', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `detalle` varchar(1000) NOT NULL,
  `stock` int(100) NOT NULL,
  `precio` decimal(11,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `id_genero` int(11) DEFAULT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `imagen2` int(11) NOT NULL,
  `imagen3` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `detalle`, `stock`, `precio`, `imagen`, `id_genero`, `autor`, `fecha_publicacion`, `imagen2`, `imagen3`) VALUES
(1, 'Harry Potter y la Piedra Filosofal', 'Un niño huérfano descubre que es un mago y asiste a una escuela de magia.', 50, 25000.00, 'hp1.jpg', 1, 'J.K. Rowling', '1997-06-26', 0, 0),
(2, 'El Resplandor', 'Un escritor se vuelve loco en un hotel aislado durante el invierno.', 20, 18500.50, 'resplandor.jpg', 2, 'Stephen King', '1977-01-28', 0, 0),
(3, 'Orgullo y Prejuicio', 'La historia de Elizabeth Bennet y su complicada relación con el Sr. Darcy.', 15, 12000.00, 'orgullo.jpg', 3, 'Jane Austen', '1813-01-28', 0, 0),
(4, 'Dune', 'En un futuro lejano, familias nobles luchan por el control de un planeta desértico.', 30, 32000.00, 'dune.jpg', 4, 'Frank Herbert', '1965-08-01', 0, 0),
(5, 'El Hobbit', 'Bilbo Bolsón se embarca en una aventura para recuperar un tesoro custodiado por un dragón.', 45, 21000.00, 'hobbit.jpg', 6, 'J.R.R. Tolkien', '1937-09-21', 0, 0),
(6, 'It (Eso)', 'Un grupo de niños es aterrorizado por una entidad que cambia de forma.', 10, 28000.00, 'it.jpg', 2, 'Stephen King', '1986-09-15', 0, 0),
(7, 'Yo, Robot', 'Una colección de relatos sobre las tres leyes de la robótica.', 12, 15500.00, 'robot.jpg', 4, 'Isaac Asimov', '1950-12-02', 0, 0),
(8, 'Bajo la misma estrella', 'Dos adolescentes con cáncer se enamoran tras conocerse en un grupo de apoyo.', 25, 9500.00, 'estrella.jpg', 3, 'John Green', '2012-01-10', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reseña`
--

CREATE TABLE `reseña` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) DEFAULT NULL,
  `rol_descripcción` varchar(155) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre_rol`, `rol_descripcción`) VALUES
(1, 'Admin', 'El administrador puede crear y administrar usuarios, ventas y la tienda'),
(2, 'Empleado', 'El empleado maneja las ventas y los registros de libros disponibles'),
(3, 'Cliente', 'EL cliente solamente puede comprar en la tienda ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `realname` varchar(100) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `telefono` varchar(110) NOT NULL,
  `direccion` varchar(110) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `realname`, `rol_id`, `pass`, `email`, `username`, `telefono`, `direccion`) VALUES
(1, 'denise roglich', 3, '$2y$10$/w7vHAU6/StCWkD0.KVmheJbtWGQzCt3We4wovsbj14Gh0ip9dxnu', 'denuarmy279@gmail.com', 'denu', '1137742173', 'ahda 121'),
(2, 'juan Garcica', 3, '$2y$10$7/d.PA3Jbevkc2hfrDStlOGpzFlBF3ScAJ3NfHexRoMqDM4Nk7epq', 'juan@mail.com', 'juan', '1137742172', 'calle falsa 123'),
(3, 'Nicole Admin', 1, '$2y$10$61sf3aT9/nf1vrN8snnCGeHniFJ2otiNP30CeRetHwsh.r3SWyvPK', 'nicole@gmail.com', 'nicole', '000', 'Administración'),
(4, 'Denise Admin', 1, '$2y$10$kpkxSAX0HHnHiQHOZJcJWeEmMwBHqQQPMF.v85/O/5GyV7WruEjre', 'denise@gmail.com', 'denise', '000', 'Administración'),
(5, 'Rocio Admin', 1, '$2y$10$CrWhxPzbVlC2PQ/WuK01RucLkGceJkN5BIHpAck0BvMD1cWf6.MSu', 'Rocioe@gmail.com', 'rocio', '000', 'Administración'),
(6, 'Rocio Monzon', 1, '$2y$10$kDddohvHOAGnvtDnOOp0u.3Q.R5u6KqbS5bSqnj9VHCTnVA5mCS4G', 'email@gmail.con', 'rocio.monzon.t1vl@gmail.com', '0000', 'una calle 123'),
(7, 'Admin Uno', 1, '$2y$10$0YkHmP.woLvE4OSOSAfCiOu3mY2i9fV43j14E1c9loIdi/44o0uGO', 'rocio.monzon.t1vl@gmail.com', 'Admin1', '000', 'sghmj'),
(8, 'Juana azurduy', 3, '$2y$10$3iH5vngIFxWISGLE8SZJ0.4pCP5KBKQKq76g6W8YY2B8swNIQsuoO', 'email1@gmail.com', 'juana1', '0000', 'una casa'),
(9, 'ashgda sghagd', 3, '$2y$10$Drveq/iefLR/CX4cK4rtIuUs8bS/9Fe.r34Lfk2ijScSWvzID3lMa', 'email@email.com', 'ysgagd', '000', 'una calle'),
(10, 'rocio wernicke', 1, '$2y$10$rou.4PpJnFj1/IWBPq/TleyLddSJEaeYM3PWamhiPRPAs59jWiSsC', 'rocio.monzon.t1vl@gmail.com', 'wernicke123', '111111', 'Una calle'),
(11, 'asgdah gdfshagfd', 1, '$2y$10$tQzBmDWfsOgWhf9yD2qxYOC77hhHrNtNQEWiI.bf.V6HKOHIXJ/xG', 'kiramanacatalina@gmail.com', 'kiramanacatalina', '1111', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `productos` varchar(255) DEFAULT NULL,
  `total` decimal(11,2) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `metodo_pago` varchar(50) DEFAULT 'Transferencia/Efectivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_usuario`, `productos`, `total`, `fecha`, `metodo_pago`) VALUES
(1, 1, 'El Resplandor', 16650.45, '2026-03-18 16:52:08', 'transferencia'),
(2, 1, '', 16650.45, '2026-03-18 16:54:03', 'transferencia'),
(3, 1, '', 16650.45, '2026-03-18 17:00:15', 'transferencia'),
(4, 1, '', 16650.45, '2026-03-18 17:02:11', 'transferencia'),
(5, 1, '', 22500.00, '2026-03-18 17:02:27', 'transferencia'),
(6, 10, NULL, 15500.00, '2026-07-01 04:32:35', 'Efectivo'),
(7, 10, NULL, 25000.00, '2026-07-01 21:49:42', 'Efectivo'),
(8, 11, NULL, 60500.50, '2026-07-14 01:40:38', 'efectivo'),
(9, 11, NULL, 46501.00, '2026-07-14 01:51:44', 'efectivo'),
(10, 11, NULL, 15500.00, '2026-07-14 02:43:29', 'efectivo'),
(11, 11, NULL, 30500.50, '2026-07-14 02:53:00', 'efectivo'),
(12, 11, NULL, 70500.50, '2026-07-14 16:18:58', 'efectivo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrusel`
--
ALTER TABLE `carrusel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`id_genero`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_genero` (`id_genero`);

--
-- Indices de la tabla `reseña`
--
ALTER TABLE `reseña`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrusel`
--
ALTER TABLE `carrusel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `id_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `reseña`
--
ALTER TABLE `reseña`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrusel`
--
ALTER TABLE `carrusel`
  ADD CONSTRAINT `carrusel_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_producto_genero` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`) ON DELETE SET NULL;

--
-- Filtros para la tabla `reseña`
--
ALTER TABLE `reseña`
  ADD CONSTRAINT `reseña_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `reseña_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id_rol`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
