-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-07-2026 a las 23:40:23
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
(16, 3, 1, 0),
(17, 4, 2, 0),
(18, 5, 3, 0),
(19, 6, 4, 0),
(20, 8, 5, 0),
(21, 3, 1, 0),
(22, 4, 2, 0),
(23, 8, 3, 0),
(24, 3, 1, 0),
(25, 4, 2, 0),
(26, 8, 3, 0),
(27, 7, 4, 0),
(28, 3, 1, 1),
(29, 4, 2, 1),
(30, 8, 3, 1),
(31, 7, 4, 1),
(32, 1, 5, 1),
(33, 6, 6, 1);

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
(4, 5, 'Harry Potter y la Piedra Filosofal', 25000.00),
(5, 13, 'Dune', 32000.00),
(6, 13, 'Bajo la misma estrella', 9500.00),
(7, 13, 'Orgullo y Prejuicio', 12000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genero`
--

CREATE TABLE `genero` (
  `id_genero` int(11) NOT NULL,
  `nombre_genero` varchar(50) NOT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `genero`
--

INSERT INTO `genero` (`id_genero`, `nombre_genero`, `destacado`, `activo`, `imagen`) VALUES
(1, 'Fantasía', 0, 0, NULL),
(2, 'Terror', 0, 0, NULL),
(3, 'Romance', 0, 1, '1784141075_5d85cc8c.png'),
(4, 'Comedia', 0, 1, '1784141114_9c7f450e.jpg'),
(5, 'Poesia', 0, 0, NULL),
(6, 'Aventura', 0, 0, '1784139234_7a7a4456.jpg'),
(7, 'papoi', 0, 0, NULL),
(8, 'Terror', 0, 0, NULL),
(9, 'Terror', 1, 1, '1784141195_085c3206.png'),
(10, 'Fantasia', 0, 1, '1784141011_6343a5f2.jpg'),
(11, 'Papoi', 0, 0, '1784139257_91220985.jpg'),
(12, 'Ciencia ficcion', 0, 1, '1784144373_b8732d4b.png'),
(13, 'Misterio', 1, 1, '1784144450_cd8ca402.webp'),
(14, 'Clásicos', 0, 1, '1784144569_0b7ee5fd.jpg'),
(15, 'Biografías y memorias', 0, 1, '1784144721_338960a2.jpg'),
(16, 'Autoayuda', 0, 1, '1784144793_6c8133b0.jpg');

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
  `imagen2` varchar(255) DEFAULT NULL,
  `imagen3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `detalle`, `stock`, `precio`, `imagen`, `id_genero`, `autor`, `fecha_publicacion`, `imagen2`, `imagen3`) VALUES
(1, 'Harry Potter y la Piedra Filosofal', 'Un niño huérfano descubre que es un mago y asiste a una escuela de magia.', 50, 25000.00, 'hp1.jpg', 10, 'J.K. Rowling', '1997-06-26', '0', '0'),
(2, 'El Resplandor', 'Un escritor se vuelve loco en un hotel aislado durante el invierno.', 20, 18500.50, 'resplandor.jpg', 9, 'Stephen King', '1977-01-28', '0', '0'),
(3, 'Orgullo y Prejuicio', 'La historia de Elizabeth Bennet y su complicada relación con el Sr. Darcy.', 15, 12000.00, 'orgullo.jpg', 3, 'Jane Austen', '1813-01-28', '0', '0'),
(4, 'Dune', 'En un futuro lejano, familias nobles luchan por el control de un planeta desértico.', 30, 32000.00, 'dune.jpg', 10, 'Frank Herbert', '1965-08-01', '0', '0'),
(5, 'El Hobbit', 'Bilbo Bolsón se embarca en una aventura para recuperar un tesoro custodiado por un dragón.', 45, 21000.00, 'hobbit.jpg', 10, 'J.R.R. Tolkien', '1937-09-21', NULL, NULL),
(6, 'It (Eso)', 'Un grupo de niños es aterrorizado por una entidad que cambia de forma.', 10, 28000.00, 'it.jpg', 12, 'Stephen King', '1986-09-15', '0', '0'),
(7, 'Yo, Robot', 'Una colección de relatos sobre las tres leyes de la robótica.', 12, 15500.00, 'robot.jpg', 12, 'Isaac Asimov', '1950-12-02', '0', '0'),
(8, 'Bajo la misma estrella', 'Dos adolescentes con cáncer se enamoran tras conocerse en un grupo de apoyo.', 20, 9500.00, 'estrella.jpg', 3, 'John Green', '2012-01-10', NULL, NULL),
(10, 'Romper el circulo', 'Una emocionante historia sobre el amor, la superación y las difíciles decisiones que marcan el rumbo de una vida.', 40, 32000.00, '1784145008_0dd8a1ac.jpg', 3, 'Colleen Hoover', NULL, NULL, NULL),
(11, 'El día que dejó de nevar en Alaska', 'Una novela conmovedora sobre segundas oportunidades, el destino y cómo sanar el corazón en medio de la naturaleza indómita.', 40, 43000.00, '1784145094_3f1dc308.webp', 3, 'Alice Kellen', NULL, NULL, NULL),
(12, 'Rojo, blanco y sangre azul', 'Un romance moderno, divertido y secreto entre el hijo de la presidenta de los Estados Unidos y el príncipe de Inglaterra.', 29, 32000.00, '1784145205_4d9d281c.webp', 3, 'Casey McQuiston', NULL, NULL, NULL),
(13, 'Sin noticias de Gurb', 'El diario absurdo y desopilante de un extraterrestre que busca a su compañero perdido en la Barcelona previa a los Juegos Olímpicos.', 37, 30000.00, '1784145337_b39cb38b.jpeg', 4, 'Eduardo Mendoza', NULL, NULL, NULL),
(14, 'El abuelo que saltó por la ventana y se largó', 'El día de su cumpleaños número 100, Allan decide escapar de su asilo, iniciando una delirante aventura llena de criminales y maletas con dinero.', 45, 30000.00, '1784145453_67f82d78.jpeg', 4, 'Jonas Jonasson', NULL, NULL, NULL),
(15, 'Maldito Karma', 'Una presentadora de televisión muere y reencarna en hormiga por su mal comportamiento. Ahora debe acumular buen karma para volver a ser humana.', 44, 34000.00, '1784145629_478edbb8.webp', 4, 'David Safier', NULL, NULL, NULL),
(16, 'Guía del autoestopista galáctico', 'Una genial sátira británica de ciencia ficción que narra los viajes espaciales de un humano corriente tras la destrucción de la Tierra.', 56, 34000.00, '1784145788_28a5cb2e.jpeg', 4, 'Douglas Addams', NULL, NULL, NULL),
(17, 'En el pais de la nube blanca', 'Dos chicas emprenden la travesía en barco hacia Nueva Zelanda. Para ellas significa el comienzo de una nueva vida como futuras esposas de unos hombres a quienes no conocen. Gwyneira, de origen noble, está prometida al hijo de un magnate de la lana, mientras que Helen, institutriz de profesión, ha respondido a la solicitud de matrimonio de un granjero. Ambas deberán seguir su destino en una tierra a la que se compara con el paraíso. Pero ¿hallarán el amor y la felicidad en el extremo opuesto del mundo?\"En el país de la nube blanca\", el debut más exitoso de los últimos años en Alemania, es una novela cautivante sobre el amor y el odio, la confianza y la enemistad, y sobre dos familias cuyo sino está unido de forma indisoluble.', 43, 29000.00, '1784145919_119c002a.jpeg', 4, 'Sarah Lark', NULL, NULL, NULL),
(18, 'Drácula', 'La obra maestra de la literatura gótica que narra el viaje del Conde Drácula desde Transilvania hasta Inglaterra para extender su maldición.', 34, 43000.00, '1784146115_26f52509.webp', 9, 'Bram Stoker', NULL, NULL, NULL),
(19, 'Bird Box: a ciegas', 'Una aterradora historia de supervivencia en un mundo postapocalíptico donde las personas deben vendarse los ojos para no volverse locas por criaturas desconocidas.', 45, 45000.00, '1784146210_2ccc5ced.webp', 9, 'Josh Malerman', NULL, NULL, NULL),
(20, 'Otra vuelta de tuerca', 'Una perturbadora historia de fantasmas clásica donde una institutriz intenta proteger a dos niños de presencias siniestras en una mansión.', 45, 45000.00, '1784146307_d40f70bb.jpg', 9, 'Henry James', NULL, NULL, NULL),
(21, 'La llamada de Cthulhu', 'El relato insignia del terror cósmico que revela la existencia de entidades monstruosas y ancestrales que habitan los rincones oscuros del planeta.', 62, 34000.00, '1784146409_a93ab378.webp', 9, 'H.P Lovecraft', NULL, NULL, NULL),
(22, 'La casa de hojas', 'Un thriller de terror psicológico sobre una casa que, misteriosamente, es más grande por dentro de lo que sus dimensiones externas muestran.', 75, 45000.00, '1784146759_d0620cd3.png', 2, 'Mark Z. Danielewski', NULL, NULL, NULL),
(23, 'El nombre del viento', 'Kvothe narra en primera persona su propia leyenda, su juventud huérfana y su paso por la Universidad de magia más prestigiosa del mundo.', 79, 39000.00, '1784146884_60742f9e.jpeg', 10, 'Patrick Rothfuss', NULL, NULL, NULL),
(24, 'Percy Jackson y el ladrón del rayo', 'Un adolescente descubre que es un semidiós, hijo de Poseidón, y debe emprender una misión para evitar una guerra entre los dioses del Olimpo.', 67, 34000.00, '1784147116_65512bf2.webp', 1, 'Rick Riordan', NULL, NULL, NULL),
(25, 'Farenheit 451', 'Una inquietante distopía donde los bomberos tienen la misión de quemar libros por orden de un gobierno que busca suprimir el pensamiento crítico.', 56, 30000.00, '1784147408_89427f78.jpg', 12, 'Ray Bradbury', NULL, NULL, NULL),
(26, 'El problema de los tres cuerpos', 'Una brillante novela de ciencia ficción dura que comienza en la Revolución Cultural china y escala hasta un inminente contacto alienígena.', 56, 34000.00, '1784147487_e03dd0b0.jpg', 12, 'Cixin Liu', NULL, NULL, NULL),
(27, 'Granja de animales', '1984 es una novela distópica de George Orwell que presenta una sociedad totalitaria y asfixiante dominada por el \"Gran Hermano\" y el Partido. La historia sigue a Winston Smith, un empleado encargado de reescribir la historia para adaptarla a la propaganda oficial, quien comete el peor de los crímenes: pensar por sí mismo', 45, 34400.00, '1784147578_f6557b2c.png', 2, 'George Orwell', NULL, NULL, NULL);

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
(12, 11, NULL, 70500.50, '2026-07-14 16:18:58', 'efectivo'),
(13, 11, NULL, 53500.00, '2026-07-15 17:51:04', 'efectivo');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `id_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
