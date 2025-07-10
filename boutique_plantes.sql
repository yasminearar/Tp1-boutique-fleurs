-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 09 juil. 2025 à 05:20
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `boutique_plantes`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
                              `id` int(11) NOT NULL,
                              `nom` varchar(100) NOT NULL,
                              `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`) VALUES
                                                          (2, 'fleur exterieur', 'juhrrrr'),
                                                          (5, 'bel', 'sdhsd');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
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
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `email`, `adresse`, `telephone`) VALUES
                                                                                   (1, 'bel', 'karimosuu', 'test@tesut.com', 'lavoisier', '514789654'),
                                                                                   (2, 'fleur exterieur', 'ania', 'test@test.com', NULL, '88'),
                                                                                   (3, 'bel', 'karimrrgt', 'tesggt@test.com', 'ww', '88');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
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
-- Déchargement des données de la table `commandes`
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
-- Structure de la table `commande_details`
--

CREATE TABLE `commande_details` (
                                    `id` int(11) NOT NULL,
                                    `id_commande` int(11) NOT NULL,
                                    `id_plante` int(11) NOT NULL,
                                    `quantite` int(11) NOT NULL,
                                    `prix_unitaire` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande_details`
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
-- Structure de la table `plantes`
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
-- Déchargement des données de la table `plantes`
--

INSERT INTO `plantes` (`id`, `nom`, `description`, `prix`, `taille`, `exposition`, `stock`, `image_url`, `id_categorie`) VALUES
                                                                                                                             (2, 'test', 'jhdsgh', 10.00, NULL, NULL, 0, 'calathea.jpg', 5),
                                                                                                                             (3, 'bibo', 'lklk', 10.00, NULL, NULL, 4, 'calathea.jpg', 2),
                                                                                                                             (4, 'plante', NULL, 20.00, NULL, NULL, 0, 'lavande.jpg', 2);

-- --------------------------------------------------------

--
-- Structure de la table `privileges`
--

CREATE TABLE `privileges` (
                              `id` int(11) NOT NULL,
                              `privilege` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `privileges`
--

INSERT INTO `privileges` (`id`, `privilege`) VALUES
                                                 (1, 'admin'),
                                                 (2, 'user'),
                                                 (3, 'staff');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
                         `id` int(11) NOT NULL,
                         `name` varchar(100) NOT NULL,
                         `username` varchar(50) NOT NULL,
                         `password` varchar(255) NOT NULL,
                         `email` varchar(100) NOT NULL,
                         `privilege_id` int(11) NOT NULL,
                         `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `email`, `privilege_id`, `created_at`) VALUES
                                                                                                      (1, 'Administrateur', 'admin@boutique.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@boutique.com', 1, '2025-07-09 02:27:31'),
                                                                                                      (2, 'Utilisateur', 'user@boutique.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user@boutique.com', 2, '2025-07-09 02:27:31');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
    ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `commande_details`
--
ALTER TABLE `commande_details`
    ADD PRIMARY KEY (`id`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_plante` (`id_plante`);

--
-- Index pour la table `plantes`
--
ALTER TABLE `plantes`
    ADD PRIMARY KEY (`id`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `privileges`
--
ALTER TABLE `privileges`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_user_privilege` (`privilege_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `commande_details`
--
ALTER TABLE `commande_details`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `plantes`
--
ALTER TABLE `plantes`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `privileges`
--
ALTER TABLE `privileges`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
    ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `commande_details`
--
ALTER TABLE `commande_details`
    ADD CONSTRAINT `commande_details_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_details_ibfk_2` FOREIGN KEY (`id_plante`) REFERENCES `plantes` (`id`);

--
-- Contraintes pour la table `plantes`
--
ALTER TABLE `plantes`
    ADD CONSTRAINT `plantes_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
    ADD CONSTRAINT `fk_user_privilege` FOREIGN KEY (`privilege_id`) REFERENCES `privileges` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
