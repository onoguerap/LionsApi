-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-07-2020 a las 03:29:53
-- Versión del servidor: 10.4.11-MariaDB
-- Versión de PHP: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lionsdbpan`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_activities`
--

CREATE TABLE `tb_activities` (
  `id_activity` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `schedule` datetime DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `image_path` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_clubs`
--

CREATE TABLE `tb_clubs` (
  `id_club` int(11) NOT NULL,
  `name_club` varchar(150) DEFAULT NULL,
  `club_code` varchar(50) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `meeting_date` varchar(50) DEFAULT NULL,
  `meeting_hour` varchar(50) DEFAULT NULL,
  `id_region` varchar(50) DEFAULT NULL,
  `id_zone` varchar(50) DEFAULT NULL,
  `GMT` varchar(100) NOT NULL,
  `GLT` varchar(100) NOT NULL,
  `GST` varchar(100) NOT NULL,
  `LCIF` varchar(100) NOT NULL,
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_members`
--

CREATE TABLE `tb_members` (
  `id_member` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `last_name` varchar(150) DEFAULT NULL,
  `birthday` datetime DEFAULT NULL,
  `member_code` int(11) DEFAULT NULL,
  `club_code` varchar(50) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `cellphone` varchar(50) DEFAULT NULL,
  `id_rol_member` int(11) DEFAULT NULL,
  `gender` varchar(45) DEFAULT NULL,
  `admission_date` datetime DEFAULT NULL,
  `id_zone` varchar(45) DEFAULT NULL,
  `last_view` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `id_type_member` int(11) DEFAULT 8,
  `img_url` varchar(200) NOT NULL,
  `password` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_region`
--

CREATE TABLE `tb_region` (
  `id_region` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `description` varchar(150) CHARACTER SET utf8mb4 NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_rol`
--

CREATE TABLE `tb_rol` (
  `id_rol_member` int(11) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tb_rol`
--

INSERT INTO `tb_rol` (`id_rol_member`, `description`, `status`) VALUES
(1, 'Super', '1'),
(2, 'Admin', '1'),
(3, 'Regular', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_type`
--

CREATE TABLE `tb_type` (
  `id_type` int(11) NOT NULL,
  `description` varchar(150) CHARACTER SET utf8mb4 NOT NULL,
  `isGovernment` int(11) NOT NULL DEFAULT 0,
  `isClub` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tb_type`
--

INSERT INTO `tb_type` (`id_type`, `description`, `isGovernment`, `isClub`) VALUES
(1, 'Gobernador', 1, 0),
(2, 'Familia', 1, 0),
(3, 'PPG', 1, 0),
(4, 'PVG', 1, 0),
(5, 'Secretario', 1, 0),
(6, 'Tesorero', 1, 0),
(7, 'ID', 1, 0),
(8, 'Miembro', 0, 0),
(9, 'Otro', 0, 0),
(10, 'Jefe Region', 0, 0),
(11, 'Jefe Zona', 0, 0),
(12, 'Asesor', 0, 0),
(17, 'GST', 1, 0),
(18, 'GLT', 1, 0),
(19, 'GMT', 1, 0),
(20, 'LCIF', 1, 0),
(21, 'Presidente Club', 0, 1),
(22, 'Tesorero Club', 0, 1),
(23, 'Secretario Club', 0, 1),
(24, 'GST Club', 0, 1),
(25, 'GLT Club', 0, 1),
(26, 'GMT Club', 0, 1),
(27, 'Mercadotecnia Club', 0, 1),
(28, 'LCIF Club', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_type_members`
--

CREATE TABLE `tb_type_members` (
  `id_type` int(11) NOT NULL,
  `member_code` int(11) NOT NULL,
  `info` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_zone`
--

CREATE TABLE `tb_zone` (
  `id_zone` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `id_region` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `description` varchar(150) CHARACTER SET utf8mb4 NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tb_activities`
--
ALTER TABLE `tb_activities`
  ADD PRIMARY KEY (`id_activity`);

--
-- Indices de la tabla `tb_clubs`
--
ALTER TABLE `tb_clubs`
  ADD PRIMARY KEY (`id_club`);

--
-- Indices de la tabla `tb_members`
--
ALTER TABLE `tb_members`
  ADD PRIMARY KEY (`id_member`);

--
-- Indices de la tabla `tb_region`
--
ALTER TABLE `tb_region`
  ADD PRIMARY KEY (`id_region`);

--
-- Indices de la tabla `tb_rol`
--
ALTER TABLE `tb_rol`
  ADD PRIMARY KEY (`id_rol_member`);

--
-- Indices de la tabla `tb_type`
--
ALTER TABLE `tb_type`
  ADD PRIMARY KEY (`id_type`);

--
-- Indices de la tabla `tb_zone`
--
ALTER TABLE `tb_zone`
  ADD PRIMARY KEY (`id_zone`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tb_activities`
--
ALTER TABLE `tb_activities`
  MODIFY `id_activity` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_clubs`
--
ALTER TABLE `tb_clubs`
  MODIFY `id_club` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_members`
--
ALTER TABLE `tb_members`
  MODIFY `id_member` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_rol`
--
ALTER TABLE `tb_rol`
  MODIFY `id_rol_member` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_type`
--
ALTER TABLE `tb_type`
  MODIFY `id_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
