/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `books_store`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id_detalle`, `id_venta`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 1, 25000.00),
(2, 1, 5, 1, 21000.00),
(3, 2, 2, 1, 18500.50),
(4, 2, 7, 1, 15500.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genero`
--

CREATE TABLE `genero` (
  `id_genero` int(11) NOT NULL,
  `nombre_genero` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `genero`
--

INSERT INTO `genero` (`id_genero`, `nombre_genero`) VALUES
(1, 'Fantasía'),
(2, 'Terror'),
(3, 'Romance'),
(4, 'Comedia'),
(5, 'Poesía');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `detalle` varchar(1000) NOT NULL,
  `stock` int(11) NOT NULL,
  `precio` decimal(11,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `id_genero` int(11) DEFAULT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `detalle`, `stock`, `precio`, `imagen`, `id_genero`, `autor`, `fecha_publicacion`) VALUES
(1, 'Harry Potter y la Piedra Filosofal', 'Un niño huérfano descubre que es un mago y asiste a una escuela de magia.', 50, 25000.00, 'hp1.jpg', 1, 'J.K. Rowling', '1997-06-26'),
(2, 'El Resplandor', 'Un escritor se vuelve loco en un hotel aislado durante el invierno.', 20, 18500.50, 'resplandor.jpg', 2, 'Stephen King', '1977-01-28'),
(3, 'Orgullo y Prejuicio', 'La historia de Elizabeth Bennet y su complicada relación con el Sr. Darcy.', 15, 12000.00, 'orgullo.jpg', 3, 'Jane Austen', '1813-01-28'),
(4, 'Dune', 'En un futuro lejano, familias nobles luchan por el control de un planeta desértico.', 30, 32000.00, 'dune.jpg', 4, 'Frank Herbert', '1965-08-01'),
(5, 'El Hobbit', 'Bilbo Bolsón se embarca en una aventura para recuperar un tesoro custodiado por un dragón.', 45, 21000.00, 'hobbit.jpg', 1, 'J.R.R. Tolkien', '1937-09-21'),
(6, 'It (Eso)', 'Un grupo de niños es aterrorizado por una entidad que cambia de forma.', 10, 28000.00, 'it.jpg', 2, 'Stephen King', '1986-09-15'),
(7, 'Yo, Robot', 'Una colección de relatos sobre las tres leyes de la robótica.', 12, 15500.00, 'robot.jpg', 4, 'Isaac Asimov', '1950-12-02'),
(8, 'Bajo la misma estrella', 'Dos adolescentes con cáncer se enamoran tras conocerse en un grupo de apoyo.', 25, 9500.00, 'estrella.jpg', 3, 'John Green', '2012-01-10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reseña`
--

CREATE TABLE `reseña` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reseña`
--

INSERT INTO `reseña` (`id`, `usuario_id`, `producto_id`, `comentario`) VALUES
(1, 1, 1, 'Muy entretenido, una excelente introducción al mundo mágico.'),
(2, 2, 2, 'Un clásico del terror. Muy recomendable.'),
(3, 1, 5, 'Una aventura fantástica que me gustó mucho.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `rol_descripcion` varchar(155) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre_rol`, `rol_descripcion`) VALUES
(1, 'Admin', 'El administrador puede crear y administrar usuarios, ventas y la tienda'),
(2, 'Empleado', 'El empleado maneja las ventas y los registros de libros disponibles'),
(3, 'Cliente', 'El cliente solamente puede comprar en la tienda');

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
(2, 'juan Garcia', 3, '$2y$10$7/d.PA3Jbevkc2hfrDStlOGpzFlBF3ScAJ3NfHexRoMqDM4Nk7epq', 'juan@mail.com', 'juan', '1137742172', 'calle falsa 123'),
(3, 'Nicole Admin', 1, '$2y$10$61sf3aT9/nf1vrN8snnCGeHniFJ2otiNP30CeRetHwsh.r3SWyvPK', 'nicole@gmail.com', 'nicole', '000', 'Administración'),
(4, 'Denise Admin', 1, '$2y$10$kpkxSAX0HHnHiQHOZJcJWeEmMwBHqQQPMF.v85/O/5GyV7WruEjre', 'denise@gmail.com', 'deniseadmin', '000', 'Administración'),
(5, 'Rocio Admin', 1, '$2y$10$CrWhxPzbVlC2PQ/WuK01RucLkGceJkN5BIHpAck0BvMD1cWf6.MSu', 'rocioe@gmail.com', 'rocio', '000', 'Administración'),
(6, 'Ivan Roglich', 3, '$2y$10$O/PwSo7PdeCbRKWo5f/g5OlAT2UHwTPpSCjuwtSwy6BZ7hyhWzluC', 'ivan@gmail.com', 'ivan1', '0111568694218', 'jose maria moreno'),
(7, 'Valentina Gonzales', 3, '$2y$10$76Cc1a1F9L5gnUHaubZJKe0vbv.TiBfHhk0ojuinZLWTS3z1DxTwm', 'valen@gmail.com', 'valen2', '11543176548', 'formosa 242'),
(8, 'Tito Calderon', 3, '$2y$10$oqvWHWr18sZPMYP51HNUh.uF65aGn8Wi2fJnx7MUbeYvqOhHzYzjq', 'momo@gmail.com', 'momo', '1176382182', 'san isidro 232');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `total` decimal(11,2) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `metodo_pago` varchar(50) DEFAULT 'Transferencia/Efectivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_usuario`, `total`, `fecha`, `metodo_pago`) VALUES
(1, 1, 46000.00, '2026-03-18 16:52:08', 'Transferencia'),
(2, 2, 34000.50, '2026-03-19 13:15:00', 'Efectivo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `fk_detalle_venta` (`id_venta`),
  ADD KEY `fk_detalle_producto` (`id_producto`);

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
  ADD KEY `fk_producto_genero` (`id_genero`);

--
-- Indices de la tabla `reseña`
--
ALTER TABLE `reseña`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resena_usuario` (`usuario_id`),
  ADD KEY `fk_resena_producto` (`producto_id`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_usuario_rol` (`rol_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `fk_ventas_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `id_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `reseña`
--
ALTER TABLE `reseña`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detalle_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_producto_genero` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `reseña`
--
ALTER TABLE `reseña`
  ADD CONSTRAINT `fk_resena_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_resena_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id_rol`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;