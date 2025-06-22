-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 04:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e2496039`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`) VALUES
(2, 'fleur exterieur', 'juhrrrr'),
(5, 'bel', 'sdhsd');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `adresse` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `email`, `adresse`, `telephone`) VALUES
(1, 'bel', 'karimosuu', 'test@tesut.com', 'lavoisier', '514789654'),
(2, 'fleur exterieur', 'ania', 'test@test.com', NULL, '88'),
(3, 'bel', 'karimrrgt', 'tesggt@test.com', 'ww', '88');

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `date_commande` datetime DEFAULT current_timestamp(),
  `statut` enum('en cours','expédiée','livrée') DEFAULT 'en cours',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commandes`
--

INSERT INTO `commandes` (`id`, `id_client`, `date_commande`, `statut`, `total`, `notes`) VALUES
(14, 1, '2025-06-21 00:32:42', 'en cours', 20.00, NULL),
(15, 3, '2025-06-21 00:34:27', 'en cours', 70.00, NULL),
(16, 2, '2025-06-21 00:43:36', 'en cours', 20.00, NULL),
(17, 3, '2025-06-21 00:44:17', 'en cours', 30.00, NULL),
(18, 2, '2025-06-21 00:56:37', 'en cours', 20.00, NULL),
(19, 2, '2025-06-21 00:59:46', 'en cours', 20.00, NULL),
(20, 2, '2025-06-21 01:00:01', 'en cours', 10.00, NULL),
(21, 3, '2025-06-21 01:13:44', 'en cours', 10.00, NULL),
(22, 1, '2025-06-21 01:28:05', 'expédiée', 10.00, NULL),
(23, 1, '2025-06-21 01:28:23', 'en cours', 30.00, NULL),
(24, 1, '2025-06-21 01:30:35', 'en cours', 20.00, NULL),
(25, 3, '2025-06-21 01:30:58', 'en cours', 30.00, NULL),
(26, 2, '2025-06-21 21:42:32', 'en cours', 30.00, NULL),
(27, 1, '2025-06-21 22:55:14', 'en cours', 70.00, NULL),
(28, 1, '2025-06-21 23:29:10', 'expédiée', 50.00, 'test'),
(29, 3, '2025-06-22 02:10:47', 'livrée', 50.00, 'ytytyyty');

-- --------------------------------------------------------

--
-- Table structure for table `commande_details`
--

CREATE TABLE `commande_details` (
  `id` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_plante` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commande_details`
--

INSERT INTO `commande_details` (`id`, `id_commande`, `id_plante`, `quantite`, `prix_unitaire`) VALUES
(14, 14, 2, 2, 10.00),
(15, 15, 2, 2, 10.00),
(16, 16, 2, 2, 10.00),
(17, 17, 2, 2, 10.00),
(18, 18, 2, 1, 10.00),
(19, 19, 2, 1, 10.00),
(20, 19, 3, 1, 10.00),
(21, 20, 2, 1, 10.00),
(22, 21, 3, 1, 10.00),
(23, 22, 3, 1, 10.00),
(24, 23, 3, 3, 10.00),
(25, 24, 2, 2, 10.00),
(26, 25, 2, 3, 10.00),
(27, 26, 2, 3, 10.00),
(28, 27, 2, 3, 10.00),
(29, 27, 4, 2, 20.00),
(30, 28, 2, 1, 10.00),
(31, 28, 4, 2, 20.00),
(32, 29, 2, 3, 10.00),
(33, 29, 3, 2, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `plantes`
--

CREATE TABLE `plantes` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `taille` varchar(50) DEFAULT NULL,
  `exposition` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `id_categorie` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plantes`
--

INSERT INTO `plantes` (`id`, `nom`, `description`, `prix`, `taille`, `exposition`, `stock`, `image_url`, `id_categorie`) VALUES
(2, 'test', 'jhdsgh', 10.00, NULL, NULL, 0, 'calathea.jpg', 5),
(3, 'bibo', 'lklk', 10.00, NULL, NULL, 4, 'calathea.jpg', 2),
(4, 'plante', NULL, 20.00, NULL, NULL, 0, 'lavande.jpg', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

--
-- Indexes for table `commande_details`
--
ALTER TABLE `commande_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_plante` (`id_plante`);

--
-- Indexes for table `plantes`
--
ALTER TABLE `plantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `commande_details`
--
ALTER TABLE `commande_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `plantes`
--
ALTER TABLE `plantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commande_details`
--
ALTER TABLE `commande_details`
  ADD CONSTRAINT `commande_details_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_details_ibfk_2` FOREIGN KEY (`id_plante`) REFERENCES `plantes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
