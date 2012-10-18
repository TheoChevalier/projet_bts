-- phpMyAdmin SQL Dump
-- version 3.4.6-rc1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Mer 19 Octobre 2011 à 18:16
-- Version du serveur: 5.1.53
-- Version de PHP: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `ppe`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `art_id` int(11) NOT NULL AUTO_INCREMENT,
  `art_name` varchar(100) COLLATE latin1_general_cs NOT NULL,
  `art_desc` text CHARACTER SET utf8 NOT NULL,
  `art_prix` float NOT NULL,
  `art_date` date NOT NULL,
  `art_stock` int(11) NOT NULL,
  PRIMARY KEY (`art_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=56 ;

--
-- Contenu de la table `articles`
--

INSERT INTO `articles` (`art_id`, `art_name`, `art_desc`, `art_prix`, `art_date`, `art_stock`) VALUES
(51, 'Masque Intégral - "Crane, Tête De Mort"', 'Masque Intégral - "Crane, Tête De Mort" - Avec Protection Arrière - Abs - Paintball / Airsoft...', 29.9, '2011-10-09', 10),
(52, 'Masque Intégrale De Protection Du Visage - "Crane, Tête De Mort"', 'Matière: ABS - protection des yeux par grillage - il est fortement conseillé de porter des lunettes de protection en plus ou à la place des grilles - Couleur Squelette', 40, '2011-10-09', 5),
(53, 'Sig Sauer Sp2022', 'Airsoft Sig Sauer Sp2022 Police Française À Gaz Co2 1 Joule', 55, '2011-10-12', 5),
(54, 'Sterling Shield', 'Sachet de 500 billes jaunes, calibre 0.68, coque dure, peu tachantes.\r\n\r\n', 10.9, '2011-10-13', 150),
(55, 'RPS Premium', 'Sachet de 500 Billes bicolores, bordeaux, et or, tachant modérément et possédant une coque équilibrée.', 16.9, '2011-10-13', 30);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `cat_code` int(11) NOT NULL AUTO_INCREMENT,
  `cat_nom` varchar(50) COLLATE latin1_general_cs NOT NULL,
  `cat_avatar` enum('0','1') COLLATE latin1_general_cs NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=9 ;

--
-- Contenu de la table `categories`
--

INSERT INTO `categories` (`cat_code`, `cat_nom`, `cat_avatar`) VALUES
(1, 'Billes', '1'),
(2, 'Casques', '1'),
(3, 'Armes', '1'),
(4, 'Marqueurs', '0'),
(5, 'Vêtements', '0'),
(6, 'Canons', '0'),
(7, 'Pièces pour marqueurs', '0'),
(8, 'Protections', '0');

-- --------------------------------------------------------

--
-- Structure de la table `cat_correspondances`
--

CREATE TABLE IF NOT EXISTS `cat_correspondances` (
  `art_code` int(11) NOT NULL,
  `cat_code` int(11) NOT NULL,
  PRIMARY KEY (`art_code`,`cat_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Contenu de la table `cat_correspondances`
--

INSERT INTO `cat_correspondances` (`art_code`, `cat_code`) VALUES
(51, 2),
(51, 5),
(52, 2),
(53, 3),
(54, 1),
(55, 1);

-- --------------------------------------------------------

--
-- Structure de la table `equipes`
--

CREATE TABLE IF NOT EXISTS `equipes` (
  `equipe_id` int(11) NOT NULL AUTO_INCREMENT,
  `equipe_nom` varchar(255) COLLATE latin1_general_cs NOT NULL,
  `equipe_desc` text COLLATE latin1_general_cs NOT NULL,
  `equipe_avatar` enum('0','1') COLLATE latin1_general_cs NOT NULL DEFAULT '0',
  PRIMARY KEY (`equipe_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=7 ;

--
-- Contenu de la table `equipes`
--

INSERT INTO `equipes` (`equipe_id`, `equipe_nom`, `equipe_desc`, `equipe_avatar`) VALUES
(1, 'Les roxxeurs de ponays', 'Les roxxeurs de ponays, roxxent du ponay depuis 1896. Team de niveau ça roxxe sa maman tellement ils sont bons. Il est 1h du matin, j''ai vraiment la flemme de trouver une description plausible d''une équipe de paintball, alors m.', '0'),
(2, 'Les loutres vengeuses', '', '0'),
(3, 'Cactus', 'Le Club Cactus Paintball Compétition est une association loi 1901, affilié à la Fédération de Paintball Sportif dont le but est de développer la pratique, l''enseignement et la promotion du jeu nommé Paintball.', '1'),
(4, 'Paintball Legend', '', '1'),
(5, 'The Punishers', '', '1'),
(6, 'Snake', 'L''équipe des Snake !', '1');

-- --------------------------------------------------------

--
-- Structure de la table `equipes_correspondances`
--

CREATE TABLE IF NOT EXISTS `equipes_correspondances` (
  `equipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`equipe_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Contenu de la table `equipes_correspondances`
--

INSERT INTO `equipes_correspondances` (`equipe_id`, `user_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 31),
(2, 0),
(2, 1),
(2, 8),
(2, 11),
(2, 12),
(2, 15),
(2, 22);

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_titre` varchar(150) COLLATE latin1_general_cs NOT NULL,
  `news_desc` text CHARACTER SET utf8 NOT NULL,
  `news_date` date NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=5 ;

--
-- Contenu de la table `news`
--

INSERT INTO `news` (`news_id`, `news_titre`, `news_desc`, `news_date`) VALUES
(1, 'Dernière minute', 'Pour information, la réunion de Comité Directeur, qui était prévue ce week-end, samedi 15 octobre 2011 est annulée.\r\nLa prochaine réunion aura lieu samedi 03 décembre 2011, à 09h30, au siège de la Fédération.\r\nPour toutes questions, prière de contacter votre Président.', '2011-10-13'),
(2, 'Réunion', 'Samedi 15 octobre 2011, se tiendra au siège de la FPS, une réunion de Comité Directeur.', '2011-10-10'),
(3, 'Présélection France : le communiqué du sélectionneur', '<p>Voici les raisons qui ont présidé au choix de cette première liste.\r\n<br />Le rendez vous est donné pour le 1er juillet, où la liste définitive sera arrêtée.\r\n<br />La liste sera publiée demain dans la soirée.</p>\r\n\r\n<p>Vous m''avez confié la responsabilité d''être le sélectionneur de l''équipe nationale pour la Nation Cup 2011 du Millennium.\r\n<br />Je voudrais d''abord vous remercier. Mais j''aimerais aussi vous dire que vous avez fait le bon choix. L''honneur de la France est en jeu. Je suis convaincu que cette sélection représentera fièrement les couleurs de notre pays et qu''elle saura rassembler tous les français derrière elle.</p>\r\n\r\n<p>Parlons travail :\r\n<br />Comme vous le savez, j''ai demandé à Laurent Hamet de porter le Roster «jouant» à 10 joueurs (et non à 7 comme c''est le cas en M7). Il est favorable à ce changement, il s''occupe de faire la modification.</p>\r\n\r\n<p>Nous avons donc 10 joueurs français à sélectionner pour la Nation Cup qui se tiendra à Londres.</p>\r\n\r\n<p>Ma sélection s''opère en deux étapes :\r\n<br />1. une présélection qui tient compte du niveau global de chaque joueur à son poste.\r\n<br />Je me suis basé sur ce que j''ai observé depuis le début de la saison au Millénnium et à la LNP.\r\n18 joueurs ont été présélectionnés (voir plus bas).</p>\r\n\r\n<p>2. une sélection de 10 joueurs parmi les 18 présélectionnés. La liste sera rendue définitive le vendredi 1 juillet à 20h00 à Londres.\r\n<br />Elle pendra en compte les performances des joueurs sur le terrain (layout) et l''état de forme physique et psychologique. Les présélectionnés seront aussi jugés sur leur capacité à s''adapter au système de jeu de l''équipe de France.</p>\r\n', '2011-05-26'),
(4, 'News 4', 'Texte de la news', '2011-10-06');

-- --------------------------------------------------------

--
-- Structure de la table `resultats`
--

CREATE TABLE IF NOT EXISTS `resultats` (
  `resultats_id` int(11) NOT NULL AUTO_INCREMENT,
  `resultats_equipe_1` int(11) NOT NULL,
  `resultats_equipe_2` int(11) NOT NULL,
  `resultats_date` date NOT NULL,
  `resultats_morts_1` int(11) NOT NULL,
  `resultats_morts_2` int(11) NOT NULL,
  PRIMARY KEY (`resultats_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=4 ;

--
-- Contenu de la table `resultats`
--

INSERT INTO `resultats` (`resultats_id`, `resultats_equipe_1`, `resultats_equipe_2`, `resultats_date`, `resultats_morts_1`, `resultats_morts_2`) VALUES
(1, 1, 2, '2011-10-14', 2, 5),
(2, 2, 3, '2011-10-17', 5, 5),
(3, 2, 5, '2011-10-04', 5, 0);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `user_password` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `user_mail` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `user_date` date NOT NULL,
  `user_activation` enum('0','1') CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL DEFAULT '0',
  `user_key` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `user_rang` tinyint(4) NOT NULL DEFAULT '2',
  `user_avatar` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL DEFAULT '0.jpg',
  `user_titre` enum('1','2','3') CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `user_nom` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `user_prenom` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `user_datenaiss` date NOT NULL,
  `user_adresse` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `user_cp` varchar(7) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `user_ville` varchar(100) NOT NULL,
  `user_tel` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `user_password`, `user_mail`, `user_date`, `user_activation`, `user_key`, `user_rang`, `user_avatar`, `user_titre`, `user_nom`, `user_prenom`, `user_datenaiss`, `user_adresse`, `user_cp`, `user_ville`, `user_tel`) VALUES
(1, 'jiminy', 'plop', 'theo.chevalier11@gmail.com', '0000-00-00', '1', 'abcdef0123456789', 2, '0.jpg', '1', 'Chevalier', 'Théo', '0000-00-00', '', '', '', ''),
(2, 'test2', 'plop', 'kjgff@hg.hy', '2011-09-16', '1', 'abcdef0123456789', 2, 'defaut.png', '1', '', '', '0000-00-00', '', '', '', ''),
(4, 'plop', '64a4e8faed1a1aa0bf8bf0fc84938d25', 'plop@hgt.fr', '2011-09-17', '1', 'abcdef0123456789', 2, 'defaut.png', '1', 'Rouget', 'Paul', '1970-10-09', '2 rue des pandas roux', '42000', 'Mountain View', '0611223344'),
(39, 'mozyl', 'f193efef3c946cf506be1a08278f273f', 'theo.chevalier2.0@gmail.com', '2011-10-16', '0', '30a7ecb2458f19d6', 2, '0.jpg', '1', 'Chevalier', 'Théo', '0000-00-00', '2 rue des lauriers', '11600', 'Fraïsse-Cabardès', '677314599'),
(31, 'Calvinator', 'e8598081398825b4e71418469c271d4e', 'calvin@hotmail.fr', '2011-10-14', '1', 'd9a8153b207ec4f6', 2, '0.jpg', '1', 'Fichant', 'Calvin', '0000-00-00', '1 rue des roxxors', '42000', 'Ponay land', '699001122'),
(32, 'DAVAI', 'ffc43558d5658ba540c20cdd8dd3939e', 'shomononosh@hotmail.fr', '2011-10-15', '1', 'd05eb9847a2f6c13', 2, '0.jpg', '1', 'Wittelsheim', 'André', '0000-00-00', '4 impasse du Saule', '57220', 'Guinkirchen', '958789845');
