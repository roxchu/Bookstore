-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 16-03-2026 a las 17:48:33
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
-- Estructura de tabla para la tabla `producto`
CREATE TABLE `genero` (
  `id_genero` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_genero` varchar(50) NOT NULL,
  PRIMARY KEY (`id_genero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertamos algunos géneros de prueba
INSERT IGNORE INTO `genero` (`id_genero`, `nombre_genero`) VALUES
(1, 'Fantasía'),
(2, 'Terror'),
(3, 'Romance'),
(4, 'Ciencia Ficción');

CREATE TABLE `producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `detalle` varchar(1000) NOT NULL,
  `stock` int(100) NOT NULL,
  `precio` decimal(11,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL, -- Aquí guardarás "libro.jpg"
  `id_genero` int(11) DEFAULT NULL,    -- Relación con el género
  PRIMARY KEY (`id`),
  KEY `id_genero` (`id_genero`),
  CONSTRAINT `fk_producto_genero` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `producto` (`nombre`, `detalle`, `stock`, `precio`, `imagen`, `id_genero`) VALUES
('Harry Potter y la Piedra Filosofal', 'Un niño huérfano descubre que es un mago y asiste a una escuela de magia.', 50, 25000.00, 'hp1.jpg', 1),
('El Resplandor', 'Un escritor se vuelve loco en un hotel aislado durante el invierno.', 20, 18500.50, 'resplandor.jpg', 2),
('Orgullo y Prejuicio', 'La historia de Elizabeth Bennet y su complicada relación con el Sr. Darcy.', 15, 12000.00, 'orgullo.jpg', 3),
('Dune', 'En un futuro lejano, familias nobles luchan por el control de un planeta desértico.', 30, 32000.00, 'dune.jpg', 4),
('El Hobbit', 'Bilbo Bolsón se embarca en una aventura para recuperar un tesoro custodiado por un dragón.', 45, 21000.00, 'hobbit.jpg', 1),
('It (Eso)', 'Un grupo de niños es aterrorizado por una entidad que cambia de forma.', 10, 28000.00, 'it.jpg', 2),
('Yo, Robot', 'Una colección de relatos sobre las tres leyes de la robótica.', 12, 15500.00, 'robot.jpg', 4),
('Bajo la misma estrella', 'Dos adolescentes con cáncer se enamoran tras conocerse en un grupo de apoyo.', 25, 9500.00, 'estrella.jpg', 3);
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
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reseña`
--
ALTER TABLE `reseña`
  ADD CONSTRAINT `reseña_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `reseña_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
