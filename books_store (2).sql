-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-07-2026 a las 21:12:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

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
  `destacado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `genero`
--

INSERT INTO `genero` (`id_genero`, `nombre_genero`, `destacado`) VALUES
(1, 'Fantasía', 0),
(2, 'Terror', 0),
(3, 'Romance', 1),
(4, 'Comedia', 1),
(6, 'Poesia', 0);

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
(3, 'Nicole Roglich', 3, '$2y$10$VAFvasf.ItBiAd9pnAOFPOt6mtxaQeHsjOqhKiiDr7dcpdO/3EuUS', 'nicoleroglich022@gmail.com', 'Nicole', '1171808435', 'El Indio 262'),
(4, 'Admin Bookstore', 1, '$2y$10$lfKfIxEYgpEf68D33Araa.UTlKN4QXsu6JcjL79/SPw5GkzawMNza', 'admin@gmail.com', 'admin', '000', 'Administracion'),
(5, 'Avril Veron', 3, '$2y$10$UFY4GJFoUqfJ/SR7TKaHCejFs3HoQy6TjYT2qhOWrfga70YlqGn0a', 'avrilxdlol777@gmail.com', 'Avril', '1123232424', 'El Indio 262');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_usuario_rol` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
