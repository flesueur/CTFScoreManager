SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `ressi`
--
CREATE USER 'ctf'@'localhost' IDENTIFIED BY 'ctf';
CREATE DATABASE `ctf` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALL PRIVILEGES ON ctf. * TO 'ctf'@'localhost';
USE `ctf`;

-- --------------------------------------------------------

--
-- Structure de la table `ctf_log`
--

CREATE TABLE IF NOT EXISTS `ctf_log` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_src` varchar(50) NOT NULL DEFAULT '-1',
  `user_id` int(11) NOT NULL DEFAULT '-1',
  `is_auth` int(11) NOT NULL DEFAULT '-1',
  `is_admin` int(11) NOT NULL DEFAULT '-1',
  `php_session` varchar(50) NOT NULL DEFAULT '-1',
  `get` text NOT NULL,
  `post` text NOT NULL,
  `URI` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ctf_people`
--

CREATE TABLE IF NOT EXISTS `ctf_people` (
  `people_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `university` varchar(50) NOT NULL,
  `cursus` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`people_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;


--
-- Structure de la table `ctf_reports`
--

CREATE TABLE IF NOT EXISTS `ctf_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `time_submitted` datetime NOT NULL,
  `time_graded` datetime NOT NULL,
  `details` text NOT NULL,
  `quick_grade` int(11) NOT NULL DEFAULT '-1',
  `final_grade` int(11) NOT NULL DEFAULT '-1',
  `previous_id` int(11) NOT NULL DEFAULT '-1',
  `graded_by` varchar(50) NOT NULL,
  `name` text NOT NULL,
  `solution` text NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=109 ;


-- --------------------------------------------------------

--
-- Structure de la table `ctf_services`
--

CREATE TABLE IF NOT EXISTS `ctf_services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(50) NOT NULL,
  `service_patch` text,
  PRIMARY KEY (`service_id`),
  UNIQUE KEY `uid` (`service_id`,`service_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Contenu de la table `ctf_services`
--

INSERT INTO `ctf_services` (`service_id`, `service_name`, `service_patch`) VALUES
(-1, 'undef', ''),
(1, 'Le point 1', ''),
(2, 'Le point 2', '');

-- --------------------------------------------------------

--
-- Structure de la table `ctf_users`
--

CREATE TABLE IF NOT EXISTS `ctf_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_local` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`,`email`),
  UNIQUE KEY `user_name_2` (`user_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=66 ;

--
-- Contenu de la table `ctf_users`
--

INSERT INTO `ctf_users` (`user_id`, `user_name`, `email`, `password`, `is_admin`, `is_local`) VALUES
(40, 'Profs', 'profs@profs.fr', '*81F5E21E35407D884A6CD4A731AEBFB6AF209E1B', 1, 1);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `ctf_v_achievements`
--
CREATE TABLE IF NOT EXISTS `ctf_v_achievements` (
`user_id` int(11)
,`service_id` int(11)
,`Max` int(11)
);
-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `ctf_v_completedservices`
--
CREATE TABLE IF NOT EXISTS `ctf_v_completedservices` (
`service_id` int(11)
,`nbcomplete` bigint(21)
);
-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `ctf_v_partialservices`
--
CREATE TABLE IF NOT EXISTS `ctf_v_partialservices` (
`service_id` int(11)
,`nbpartial` bigint(21)
);
-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `ctf_v_scores`
--
CREATE TABLE IF NOT EXISTS `ctf_v_scores` (
`user_id` int(11)
,`score` decimal(32,0)
);
-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `ctf_v_servicesstats`
--
CREATE TABLE IF NOT EXISTS `ctf_v_servicesstats` (
`service_id` int(11)
,`nbcomplete` bigint(20)
,`nbpartial` bigint(20)
);
-- --------------------------------------------------------

--
-- Structure de la vue `ctf_v_achievements`
--
DROP TABLE IF EXISTS `ctf_v_achievements`;

CREATE VIEW `ctf_v_achievements` AS select `ctf_users`.`user_id` AS `user_id`,`ctf_reports`.`service_id` AS `service_id`,max(`ctf_reports`.`quick_grade`) AS `Max` from (`ctf_users` join `ctf_reports` on((`ctf_reports`.`user_id` = `ctf_users`.`user_id`))) where (`ctf_reports`.`quick_grade` <> -(1)) group by `ctf_users`.`user_id`,`ctf_reports`.`service_id`;

-- --------------------------------------------------------

--
-- Structure de la vue `ctf_v_completedservices`
--
DROP TABLE IF EXISTS `ctf_v_completedservices`;

CREATE VIEW `ctf_v_completedservices` AS select `ctf_v_achievements`.`service_id` AS `service_id`,count(0) AS `nbcomplete` from `ctf_v_achievements` where (`ctf_v_achievements`.`Max` = 2) group by `ctf_v_achievements`.`service_id`;

-- --------------------------------------------------------

--
-- Structure de la vue `ctf_v_partialservices`
--
DROP TABLE IF EXISTS `ctf_v_partialservices`;

CREATE VIEW `ctf_v_partialservices` AS select `ctf_v_achievements`.`service_id` AS `service_id`,count(0) AS `nbpartial` from `ctf_v_achievements` where (`ctf_v_achievements`.`Max` = 1) group by `ctf_v_achievements`.`service_id`;

-- --------------------------------------------------------

--
-- Structure de la vue `ctf_v_scores`
--
DROP TABLE IF EXISTS `ctf_v_scores`;

CREATE VIEW `ctf_v_scores` AS select `ctf_users`.`user_id` AS `user_id`,coalesce(sum(`ctf_v_achievements`.`Max`),0) AS `score` from (`ctf_users` left join `ctf_v_achievements` on((`ctf_users`.`user_id` = `ctf_v_achievements`.`user_id`))) where (`ctf_users`.`is_admin` <> 1) group by `ctf_users`.`user_id` order by coalesce(sum(`ctf_v_achievements`.`Max`),0) desc;

-- --------------------------------------------------------

--
-- Structure de la vue `ctf_v_servicesstats`
--
DROP TABLE IF EXISTS `ctf_v_servicesstats`;

CREATE VIEW `ctf_v_servicesstats` AS select `ctf_services`.`service_id` AS `service_id`,coalesce(`ctf_v_completedservices`.`nbcomplete`,0) AS `nbcomplete`,coalesce(`ctf_v_partialservices`.`nbpartial`,0) AS `nbpartial` from ((`ctf_services` left join `ctf_v_partialservices` on((`ctf_v_partialservices`.`service_id` = `ctf_services`.`service_id`))) left join `ctf_v_completedservices` on((`ctf_v_completedservices`.`service_id` = `ctf_services`.`service_id`)));
