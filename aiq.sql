-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-02-2026 a las 23:01:05
-- Versión del servidor: 8.0.41
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `aiq`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posiciones`
--

CREATE TABLE `posiciones` (
  `id` int NOT NULL,
  `nombre` varchar(10) DEFAULT NULL,
  `tipo` enum('COMERCIAL','CARGA') DEFAULT NULL,
  `ocupada` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `posiciones`
--

INSERT INTO `posiciones` (`id`, `nombre`, `tipo`, `ocupada`) VALUES
(1, 'POS 1', 'COMERCIAL', 0),
(2, 'POS 2', 'COMERCIAL', 0),
(3, 'POS 2A', 'COMERCIAL', 0),
(4, 'POS 3', 'COMERCIAL', 0),
(5, 'POS 4', 'COMERCIAL', 0),
(6, 'POS 5', 'COMERCIAL', 0),
(7, 'POS 6', 'COMERCIAL', 0),
(8, 'POS 7', 'COMERCIAL', 0),
(9, 'POS 8', 'COMERCIAL', 0),
(10, 'POS 9', 'COMERCIAL', 0),
(11, 'POS 1E', 'COMERCIAL', 0),
(12, 'POS 2E', 'COMERCIAL', 0),
(13, 'POS 3E', 'COMERCIAL', 0),
(14, 'POS 4E', 'COMERCIAL', 0),
(15, 'POS 5E', 'COMERCIAL', 0),
(16, 'POS 6E', 'COMERCIAL', 0),
(17, 'POS 7E', 'COMERCIAL', 0),
(18, 'POS 8E', 'COMERCIAL', 0),
(19, 'POS 9E', 'COMERCIAL', 0),
(20, 'POS 10E', 'COMERCIAL', 0),
(21, 'POS 11E', 'COMERCIAL', 0),
(22, 'POS 12E', 'COMERCIAL', 0),
(23, 'POS 13E', 'COMERCIAL', 0),
(24, 'POS 14E', 'COMERCIAL', 0),
(25, 'POS 1C', 'CARGA', 0),
(26, 'POS 2C', 'CARGA', 0),
(27, 'POS 3C', 'CARGA', 0),
(28, 'POS 4C', 'CARGA', 0),
(29, 'POS 5C', 'CARGA', 0),
(30, 'POS 6C', 'CARGA', 0),
(31, 'POS 1CA', 'CARGA', 0),
(32, 'POS 2CA', 'CARGA', 0),
(33, 'POS 3CA', 'CARGA', 0),
(34, 'POS 4CA', 'CARGA', 0),
(35, 'POS 5CA', 'CARGA', 0),
(36, 'POS 6CA', 'CARGA', 0),
(37, 'POS 7CA', 'CARGA', 0),
(38, 'POS 8CA', 'CARGA', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vuelos`
--

CREATE TABLE `vuelos` (
  `id` int NOT NULL,
  `flight_number` varchar(10) DEFAULT NULL,
  `vuelo_salida` varchar(10) DEFAULT NULL,
  `airline_name` varchar(100) DEFAULT NULL,
  `hour` int DEFAULT NULL,
  `minute` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `date_int` int DEFAULT NULL,
  `destination` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flight_type` tinyint DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `iata_airline` varchar(10) DEFAULT NULL,
  `gate_id` varchar(10) DEFAULT NULL,
  `baggage_carousel_number` varchar(10) DEFAULT NULL,
  `posicion_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `vuelos`
--

INSERT INTO `vuelos` (`id`, `flight_number`, `vuelo_salida`, `airline_name`, `hour`, `minute`, `date`, `date_int`, `destination`, `origin`, `flight_type`, `status`, `iata_airline`, `gate_id`, `baggage_carousel_number`, `posicion_id`, `created_at`) VALUES
(1, '7890', NULL, 'VOLARIS', 0, 39, '2026-02-26', 20260226, 'Chicago', 'QUERÉTARO', 0, 'A Tiempo', 'VOI', '8', '0', NULL, '2026-02-26 09:35:33'),
(2, '815', NULL, 'IBEROJET', 19, 40, '2026-02-26', 20260226, 'QUERÉTARO', 'Madrid', 1, 'A Tiempo', 'EVE', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(3, '816', NULL, 'IBEROJET', 2, 30, '2026-02-26', 20260226, 'Madrid', 'QUERÉTARO', 0, 'Demorado', 'EVE', '7', '0', NULL, '2026-02-26 09:35:33'),
(4, '1201', NULL, 'UNITED', 6, 0, '2026-02-26', 20260226, 'Houston', 'QUERÉTARO', 0, 'A Tiempo', 'UAL', '9', '0', NULL, '2026-02-26 09:35:33'),
(5, '2461', NULL, 'AEROMEXICO', 6, 3, '2026-02-26', 20260226, 'México', 'QUERÉTARO', 0, 'A Tiempo', 'SLI', '2', '0', NULL, '2026-02-26 09:35:33'),
(6, '7806', NULL, 'VIVA AEROBÚS', 6, 20, '2026-02-26', 20260226, 'Monterrey', 'QUERÉTARO', 0, 'A Tiempo', 'VIV', '3', '0', NULL, '2026-02-26 09:35:33'),
(7, '814', NULL, 'TAR', 7, 0, '2026-02-26', 20260226, 'Torreón', 'QUERÉTARO', 0, 'A Tiempo', 'LTC', '10', '0', NULL, '2026-02-26 09:35:33'),
(8, '3515', NULL, 'VOLARIS', 8, 1, '2026-02-26', 20260226, 'Cancún', 'QUERÉTARO', 0, 'A Tiempo', 'VOI', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(9, '2027', NULL, 'AMERICAN AIRLINES', 8, 10, '2026-02-26', 20260226, 'Dallas', 'QUERÉTARO', 0, 'A Tiempo', 'AAL', '7', '0', NULL, '2026-02-26 09:35:33'),
(10, '5552', '5553', 'VOLARIS', 8, 50, '2026-02-26', 20260226, 'QUERÉTARO', 'Monterrey', 1, 'A Tiempo', 'VOI', '1', 'N/A', 2, '2026-02-26 09:35:33'),
(11, '2710', NULL, 'AEROMEXICO', 8, 55, '2026-02-26', 20260226, 'Atlanta', 'QUERÉTARO', 0, 'A Tiempo', 'SLI', '2', '0', NULL, '2026-02-26 09:35:33'),
(12, '4202', NULL, 'VIVA AEROBÚS', 8, 55, '2026-02-26', 20260226, 'QUERÉTARO', 'Monterrey', 1, 'A Tiempo', 'VIV', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(13, '5553', '5552', 'VOLARIS', 9, 25, '2026-02-26', 20260226, 'Monterrey', 'QUERÉTARO', 0, 'A Tiempo', 'VOI', '4', '0', 2, '2026-02-26 09:35:33'),
(14, '2061', NULL, 'VIVA AEROBÚS', 9, 25, '2026-02-26', 20260226, 'Cancún', 'QUERÉTARO', 0, 'A Tiempo', 'VIV', '3', '0', NULL, '2026-02-26 09:35:33'),
(15, '7891', NULL, 'VOLARIS', 10, 13, '2026-02-26', 20260226, 'QUERÉTARO', 'Chicago', 1, 'A Tiempo', 'VOI', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(16, '7512', NULL, 'VOLARIS', 10, 58, '2026-02-26', 20260226, 'Zihuatanejo', 'QUERÉTARO', 0, 'A Tiempo', 'VOI', '8', '0', NULL, '2026-02-26 09:35:33'),
(17, '4204', '4205', 'VIVA AEROBÚS', 11, 0, '2026-02-26', 20260226, 'QUERÉTARO', 'Monterrey', 1, 'A Tiempo', 'VIV', '1', 'N/A', 2, '2026-02-26 09:35:33'),
(18, '2302', NULL, 'AMERICAN AIRLINES', 11, 21, '2026-02-26', 20260226, 'QUERÉTARO', 'Dallas', 1, 'A Tiempo', 'AAL', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(19, '2463', NULL, 'AEROMEXICO', 11, 38, '2026-02-26', 20260226, 'México', 'QUERÉTARO', 0, 'A Tiempo', 'SLI', '2', '0', NULL, '2026-02-26 09:35:33'),
(20, '4205', '4204', 'VIVA AEROBÚS', 11, 40, '2026-02-26', 20260226, 'Monterrey', 'QUERÉTARO', 0, 'A Tiempo', 'VIV', '3', '0', 2, '2026-02-26 09:35:33'),
(21, '2068', NULL, 'UNITED', 11, 49, '2026-02-26', 20260226, 'QUERÉTARO', 'Houston', 1, 'A Tiempo', 'UAL', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(22, '2460', NULL, 'AEROMEXICO', 11, 59, '2026-02-26', 20260226, 'QUERÉTARO', 'México', 1, 'A Tiempo', 'SLI', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(23, '2302', NULL, 'AMERICAN AIRLINES', 12, 21, '2026-02-26', 20260226, 'Dallas', 'QUERÉTARO', 0, 'A Tiempo', 'AAL', '7', '0', NULL, '2026-02-26 09:35:33'),
(24, '2079', NULL, 'UNITED', 12, 47, '2026-02-26', 20260226, 'Houston', 'QUERÉTARO', 0, 'A Tiempo', 'UAL', '9', '0', NULL, '2026-02-26 09:35:33'),
(25, '3353', NULL, 'VOLARIS', 13, 24, '2026-02-26', 20260226, 'QUERÉTARO', 'Tijuana', 1, 'A Tiempo', 'VOI', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(26, '7513', NULL, 'VOLARIS', 13, 29, '2026-02-26', 20260226, 'QUERÉTARO', 'Zihuatanejo', 1, 'A Tiempo', 'VOI', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(27, '2608', NULL, 'AEROMEXICO', 13, 53, '2026-02-26', 20260226, 'Detroit', 'QUERÉTARO', 0, 'A Tiempo', 'SLI', '2', '0', NULL, '2026-02-26 09:35:33'),
(28, '4200', NULL, 'VIVA AEROBÚS', 13, 55, '2026-02-26', 20260226, 'QUERÉTARO', 'Monterrey', 1, 'A Tiempo', 'VIV', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(29, '7506', NULL, 'VOLARIS', 14, 1, '2026-02-26', 20260226, 'Puerto Vallarta', 'QUERÉTARO', 0, 'A Tiempo', 'VOI', '3', '0', NULL, '2026-02-26 09:35:33'),
(30, '3519', NULL, 'VOLARIS', 14, 7, '2026-02-26', 20260226, 'Cancún', 'QUERÉTARO', 0, 'A Tiempo', 'VOI', '4', '0', NULL, '2026-02-26 09:35:33'),
(31, '4203', NULL, 'VIVA AEROBÚS', 14, 25, '2026-02-26', 20260226, 'Monterrey', 'QUERÉTARO', 0, 'A Tiempo', 'VIV', '7', '0', NULL, '2026-02-26 09:35:33'),
(32, '703', NULL, 'TAR', 14, 30, '2026-02-26', 20260226, 'QUERÉTARO', 'Mazatlán', 1, 'A Tiempo', 'LTC', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(33, '6243', NULL, 'UNITED', 14, 35, '2026-02-26', 20260226, 'QUERÉTARO', 'Houston', 1, 'A Tiempo', 'UAL', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(34, '712', NULL, 'TAR', 14, 55, '2026-02-26', 20260226, 'Mazatlán', 'QUERÉTARO', 0, 'A Tiempo', 'LTC', '8', '0', NULL, '2026-02-26 09:35:33'),
(35, '2062', NULL, 'VIVA AEROBÚS', 15, 15, '2026-02-26', 20260226, 'QUERÉTARO', 'Cancún', 1, 'A Tiempo', 'VIV', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(36, '6316', NULL, 'UNITED', 15, 35, '2026-02-26', 20260226, 'Houston', 'QUERÉTARO', 0, 'A Tiempo', 'UAL', '9', '0', NULL, '2026-02-26 09:35:33'),
(37, '7054', NULL, 'VIVA AEROBÚS', 16, 10, '2026-02-26', 20260226, 'QUERÉTARO', 'Los Cabos', 1, 'A Tiempo', 'VIV', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(38, '560', NULL, 'VIVA AEROBÚS', 16, 20, '2026-02-26', 20260226, 'Houston', 'QUERÉTARO', 0, 'A Tiempo', 'VIV', '4', '0', NULL, '2026-02-26 09:35:33'),
(39, '7055', NULL, 'VIVA AEROBÚS', 16, 55, '2026-02-26', 20260226, 'Los Cabos', 'QUERÉTARO', 0, 'A Tiempo', 'VIV', '3', '0', NULL, '2026-02-26 09:35:33'),
(40, '7507', NULL, 'VOLARIS', 17, 5, '2026-02-26', 20260226, 'QUERÉTARO', 'Puerto Vallarta', 1, 'A Tiempo', 'VOI', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(41, '2711', NULL, 'AEROMEXICO', 17, 15, '2026-02-26', 20260226, 'QUERÉTARO', 'Atlanta', 1, 'A Tiempo', 'SLI', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(42, '5555', NULL, 'VOLARIS', 17, 45, '2026-02-26', 20260226, 'Monterrey', 'QUERÉTARO', 0, 'A Tiempo', 'VOI', '8', '0', NULL, '2026-02-26 09:35:33'),
(43, '2467', NULL, 'AEROMEXICO', 18, 10, '2026-02-26', 20260226, 'México', 'QUERÉTARO', 0, 'A Tiempo', 'SLI', '2', '0', NULL, '2026-02-26 09:35:33'),
(44, '4206', NULL, 'VIVA AEROBÚS', 18, 25, '2026-02-26', 20260226, 'QUERÉTARO', 'Monterrey', 1, 'A Tiempo', 'VIV', '1', 'N/A', NULL, '2026-02-26 09:35:33'),
(45, '4207', NULL, 'VIVA AEROBÚS', 18, 55, '2026-02-26', 20260226, 'Monterrey', 'QUERÉTARO', 0, 'A Tiempo', 'VIV', '4', '0', NULL, '2026-02-26 09:35:33'),
(46, '3518', NULL, 'VOLARIS', 19, 43, '2026-02-26', 20260226, 'QUERÉTARO', 'Cancún', 1, 'A Tiempo', 'VOI', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(47, '406', NULL, 'TAR', 20, 0, '2026-02-26', 20260226, 'Acapulco', 'QUERÉTARO', 0, 'A Tiempo', 'LTC', '8', '0', NULL, '2026-02-26 09:35:34'),
(48, '7896', NULL, 'VOLARIS', 20, 33, '2026-02-26', 20260226, 'Los Angeles', 'QUERÉTARO', 0, 'A Tiempo', 'VOI', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(49, '2464', NULL, 'AEROMEXICO', 20, 37, '2026-02-26', 20260226, 'QUERÉTARO', 'México', 1, 'A Tiempo', 'SLI', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(50, '1200', NULL, 'UNITED', 20, 59, '2026-02-26', 20260226, 'QUERÉTARO', 'Houston', 1, 'A Tiempo', 'UAL', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(51, '5554', NULL, 'VOLARIS', 21, 5, '2026-02-26', 20260226, 'QUERÉTARO', 'Monterrey', 1, 'A Tiempo', 'VOI', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(52, '561', NULL, 'VIVA AEROBÚS', 21, 45, '2026-02-26', 20260226, 'QUERÉTARO', 'Houston', 1, 'A Tiempo', 'VIV', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(53, '4208', NULL, 'VIVA AEROBÚS', 22, 15, '2026-02-26', 20260226, 'QUERÉTARO', 'Monterrey', 1, 'A Tiempo', 'VIV', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(54, '407', NULL, 'TAR', 22, 35, '2026-02-26', 20260226, 'QUERÉTARO', 'Acapulco', 1, 'A Tiempo', 'LTC', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(55, '4209', NULL, 'VIVA AEROBÚS', 22, 45, '2026-02-26', 20260226, 'Monterrey', 'QUERÉTARO', 0, 'A Tiempo', 'VIV', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(56, '2196', NULL, 'AMERICAN AIRLINES', 23, 51, '2026-02-26', 20260226, 'QUERÉTARO', 'Dallas', 1, 'A Tiempo', 'AAL', '1', 'N/A', NULL, '2026-02-26 09:35:34'),
(57, '2466', NULL, 'AEROMEXICO', 23, 59, '2026-02-26', 20260226, 'QUERÉTARO', 'México', 1, 'A Tiempo', 'SLI', '1', 'N/A', NULL, '2026-02-26 09:35:34');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `posiciones`
--
ALTER TABLE `posiciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `vuelos`
--
ALTER TABLE `vuelos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posicion_id` (`posicion_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `posiciones`
--
ALTER TABLE `posiciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `vuelos`
--
ALTER TABLE `vuelos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `vuelos`
--
ALTER TABLE `vuelos`
  ADD CONSTRAINT `vuelos_ibfk_1` FOREIGN KEY (`posicion_id`) REFERENCES `posiciones` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
