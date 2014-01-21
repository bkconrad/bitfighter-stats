-- MySQL dump 10.13  Distrib 5.1.69, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: bf_stats
-- ------------------------------------------------------
-- Server version	5.1.69

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `achievements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `gamejolt_id` int(11) NOT NULL COMMENT 'The ID that GameJolt uses to track this acheivement',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_achievements`
--

DROP TABLE IF EXISTS `player_achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player_achievements` (
  `player_name` text NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `date_awarded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `accomplishment_id` (`achievement_id`,`player_name`(50))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player_mv`
--

DROP TABLE IF EXISTS `player_mv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player_mv` (
  `player_id` int(11) NOT NULL AUTO_INCREMENT,
  `player_name` varchar(255) DEFAULT NULL,
  `phaser_shots` int(11) NOT NULL,
  `bouncer_shots` int(11) NOT NULL,
  `triple_shots` int(11) NOT NULL,
  `burst_shots` int(11) NOT NULL,
  `mine_shots` int(11) NOT NULL,
  `spybug_shots` int(11) NOT NULL,
  `phaser_shots_struck` int(11) NOT NULL,
  `bouncer_shots_struck` int(11) NOT NULL,
  `triple_shots_struck` int(11) NOT NULL,
  `burst_shots_struck` int(11) NOT NULL,
  `mine_shots_struck` int(11) NOT NULL,
  `spybug_shots_struck` int(11) NOT NULL,
  `win_count` int(11) NOT NULL,
  `lose_count` int(11) NOT NULL,
  `tie_count` int(11) NOT NULL,
  `dnf_count` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `kill_count` int(11) NOT NULL,
  `death_count` int(11) NOT NULL,
  `suicide_count` int(11) NOT NULL,
  `kill_death_ratio` float NOT NULL,
  `asteroid_crashes` int(11) NOT NULL,
  `flag_drops` int(11) NOT NULL,
  `flag_pickups` int(11) NOT NULL,
  `flag_returns` int(11) NOT NULL,
  `flag_scores` int(11) NOT NULL,
  `teleport_uses` int(11) NOT NULL,
  `turret_kills` int(11) DEFAULT NULL,
  `ff_kills` int(11) DEFAULT NULL,
  `asteroid_kills` int(11) DEFAULT NULL,
  `turrets_engineered` int(11) DEFAULT NULL,
  `ffs_engineered` int(11) DEFAULT NULL,
  `teleports_engineered` int(11) DEFAULT NULL,
  `distance_traveled` int(11) DEFAULT NULL,
  `switched_team_count` int(11) NOT NULL,
  `last_played` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_period` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_authenticated` int(11) DEFAULT NULL,
  PRIMARY KEY (`player_id`),
  KEY `player_name` (`player_name`),
  KEY `time_period` (`time_period`)
) ENGINE=MyISAM AUTO_INCREMENT=9947 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `server`
--

DROP TABLE IF EXISTS `server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `server` (
  `server_id` int(11) NOT NULL AUTO_INCREMENT,
  `server_name` text COLLATE utf8_unicode_ci,
  `ip_address` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`server_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4922 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_game`
--

DROP TABLE IF EXISTS `stats_game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_game` (
  `stats_game_id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `game_type` text COLLATE utf8_unicode_ci NOT NULL,
  `is_official` tinyint(1) NOT NULL,
  `player_count` smallint(5) unsigned NOT NULL,
  `duration_seconds` smallint(5) unsigned NOT NULL,
  `level_name` text COLLATE utf8_unicode_ci NOT NULL,
  `is_team_game` tinyint(1) NOT NULL,
  `team_count` tinyint(3) unsigned DEFAULT NULL,
  `insertion_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stats_game_id`),
  KEY `server_id` (`server_id`),
  KEY `is_official` (`is_official`),
  KEY `player_count` (`player_count`),
  KEY `is_team_game` (`is_team_game`),
  KEY `team_count` (`team_count`),
  KEY `insertion_date` (`insertion_date`),
  KEY `game_type` (`game_type`(20))
) ENGINE=MyISAM AUTO_INCREMENT=62987 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_level`
--

DROP TABLE IF EXISTS `stats_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_level` (
  `stats_level_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(255) DEFAULT NULL,
  `creator` varchar(255) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `game_type` varchar(32) NOT NULL,
  `has_levelgen` tinyint(1) NOT NULL,
  `team_count` int(11) NOT NULL,
  `winning_score` int(11) NOT NULL,
  `game_duration` int(11) NOT NULL,
  PRIMARY KEY (`stats_level_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6426 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_player`
--

DROP TABLE IF EXISTS `stats_player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_player` (
  `stats_player_id` int(11) NOT NULL AUTO_INCREMENT,
  `stats_game_id` int(11) NOT NULL,
  `player_name` text COLLATE utf8_unicode_ci NOT NULL,
  `is_authenticated` tinyint(1) NOT NULL,
  `is_robot` tinyint(1) NOT NULL,
  `result` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `points` int(11) NOT NULL,
  `kill_count` smallint(5) unsigned NOT NULL,
  `death_count` smallint(5) unsigned NOT NULL,
  `suicide_count` smallint(5) unsigned NOT NULL,
  `asteroid_crashes` smallint(11) NOT NULL,
  `flag_drops` smallint(11) NOT NULL,
  `flag_pickups` smallint(11) NOT NULL,
  `flag_returns` smallint(11) NOT NULL,
  `flag_scores` smallint(11) NOT NULL,
  `teleport_uses` smallint(11) NOT NULL,
  `turret_kills` int(11) NOT NULL DEFAULT '0',
  `ff_kills` int(11) NOT NULL DEFAULT '0',
  `asteroid_kills` int(11) NOT NULL DEFAULT '0',
  `turrets_engineered` int(11) NOT NULL DEFAULT '0',
  `ffs_engineered` int(11) NOT NULL DEFAULT '0',
  `teleports_engineered` int(11) NOT NULL DEFAULT '0',
  `distance_traveled` int(11) NOT NULL DEFAULT '0',
  `switched_team_count` smallint(5) DEFAULT NULL,
  `stats_team_id` int(11) DEFAULT NULL,
  `insertion_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stats_player_id`),
  KEY `is_authenticated` (`is_authenticated`),
  KEY `result` (`result`),
  KEY `stats_team_id` (`stats_team_id`),
  KEY `insertion_date` (`insertion_date`),
  KEY `player_name` (`player_name`(50)),
  KEY `is_robot` (`is_robot`),
  KEY `stats_game_id` (`stats_game_id`)
) ENGINE=MyISAM AUTO_INCREMENT=244671 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`bf_stats`@`localhost`*/ /*!50003 TRIGGER update_player_stats AFTER INSERT ON stats_player
   FOR EACH ROW
   BEGIN
      DECLARE new_record_time_period DATETIME;
      DECLARE already_exists INT;
      SET already_exists = 0;
      SET new_record_time_period = TIMESTAMP(CONCAT(YEAR(NEW.insertion_date),'-',MONTH(NEW.insertion_date),'-01'));

      IF NEW.is_robot = 0 THEN
         SELECT COUNT(*)
           FROM player_mv
          WHERE time_period = new_record_time_period
            AND player_name = NEW.player_name
            AND is_authenticated = NEW.is_authenticated
           INTO already_exists;

         IF already_exists = 0 THEN
            INSERT INTO player_mv ( player_name, win_count, lose_count, tie_count, dnf_count, points, kill_count, death_count, suicide_count, asteroid_crashes, flag_drops, flag_pickups, flag_returns, flag_scores, teleport_uses,turret_kills, ff_kills, asteroid_kills, turrets_engineered, ffs_engineered, teleports_engineered, distance_traveled, switched_team_count, last_played, time_period, is_authenticated)
            VALUES                ( NEW.player_name, if(NEW.result = 'W', 1, 0), if(NEW.result = 'L', 1, 0), if(NEW.result = 'T', 1, 0), if(NEW.result = 'X', 1, 0), NEW.points, NEW.kill_count, NEW.death_count, NEW.suicide_count, NEW.asteroid_crashes, NEW.flag_drops, NEW.flag_pickups, NEW.flag_returns, NEW.flag_scores, NEW.teleport_uses, NEW.turret_kills,  NEW.ff_kills,  NEW.asteroid_kills,  NEW.turrets_engineered,  NEW.ffs_engineered,  NEW.teleports_engineered,  NEW.distance_traveled, NEW.switched_team_count, NEW.insertion_date, new_record_time_period, NEW.is_authenticated);
         ELSE
            UPDATE player_mv
               SET win_count            = win_count + if(NEW.result = 'W', 1, 0)
                 , lose_count           = lose_count + if(NEW.result = 'L', 1, 0)
                 , tie_count            = tie_count + if(NEW.result = 'T', 1, 0)
                 , dnf_count            = dnf_count + if(NEW.result = 'X', 1, 0)
                 , points               = points + NEW.points
                 , kill_count           = kill_count + NEW.kill_count
                 , death_count          = death_count + NEW.death_count
                 , suicide_count        = suicide_count + NEW.suicide_count
                 , asteroid_crashes     = asteroid_crashes + NEW.asteroid_crashes
                 , flag_drops           = flag_drops + NEW.flag_drops
                 , flag_pickups         = flag_pickups + NEW.flag_pickups
                 , flag_returns         = flag_returns + NEW.flag_returns
                 , flag_scores          = flag_scores + NEW.flag_scores
                 , teleport_uses        = teleport_uses + NEW.teleport_uses
                 , turret_kills         = turret_kills + NEW.turret_kills
                 , ff_kills             = ff_kills + NEW.ff_kills
                 , asteroid_kills       = asteroid_kills + NEW.asteroid_kills
                 , turrets_engineered   = turrets_engineered + NEW.turrets_engineered
                 , ffs_engineered       = ffs_engineered + NEW.ffs_engineered
                 , teleports_engineered = teleports_engineered + NEW.teleports_engineered
                 , distance_traveled    = distance_traveled + NEW.distance_traveled
                 , switched_team_count  = switched_team_count + NEW.switched_team_count
                 , last_played          = NEW.insertion_date
             WHERE player_name = NEW.player_name
               AND time_period = new_record_time_period
               AND is_authenticated = NEW.is_authenticated;
         END IF;

         
         UPDATE player_mv
            SET kill_death_ratio = kill_count/death_count
          WHERE player_name      = NEW.player_name
            AND time_period      = new_record_time_period
            AND is_authenticated = NEW.is_authenticated;
      END IF;
   END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `stats_player_loadout`
--

DROP TABLE IF EXISTS `stats_player_loadout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_player_loadout` (
  `stats_player_loadout_id` int(11) NOT NULL AUTO_INCREMENT,
  `stats_player_id` int(11) NOT NULL,
  `loadout` int(11) NOT NULL,
  PRIMARY KEY (`stats_player_loadout_id`),
  KEY `stats_player_id` (`stats_player_id`)
) ENGINE=MyISAM AUTO_INCREMENT=199566 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_player_shots`
--

DROP TABLE IF EXISTS `stats_player_shots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_player_shots` (
  `stats_player_shots_id` int(11) NOT NULL AUTO_INCREMENT,
  `stats_player_id` int(11) NOT NULL,
  `weapon` text COLLATE utf8_unicode_ci NOT NULL,
  `shots` int(11) NOT NULL,
  `shots_struck` int(11) NOT NULL,
  PRIMARY KEY (`stats_player_shots_id`),
  UNIQUE KEY `stats_player_id` (`stats_player_id`,`weapon`(10)),
  KEY `weapon` (`weapon`(10))
) ENGINE=MyISAM AUTO_INCREMENT=350488 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`bf_stats`@`localhost`*/ /*!50003 TRIGGER update_player_shots_stats AFTER INSERT ON stats_player_shots
   FOR EACH ROW
   BEGIN
      DECLARE new_record_player_name VARCHAR(20);
      DECLARE new_record_is_robot, new_record_is_authenticated INT;
      DECLARE new_record_time_period DATETIME;

      
      
      SELECT player_name, is_robot, is_authenticated, TIMESTAMP(CONCAT(YEAR(insertion_date),'-',MONTH(insertion_date),'-01'))
      FROM stats_player
      WHERE stats_player.stats_player_id = NEW.stats_player_id
      INTO new_record_player_name, new_record_is_robot, new_record_is_authenticated, new_record_time_period;

      
      IF new_record_is_robot = 0 THEN
         UPDATE player_mv SET
              phaser_shots = phaser_shots + if(NEW.weapon='Phaser',NEW.shots,0)
            , bouncer_shots = bouncer_shots + if(NEW.weapon='Bouncer',NEW.shots,0)
            , triple_shots = triple_shots + if(NEW.weapon='Triple',NEW.shots,0)
            , burst_shots = burst_shots + if(NEW.weapon='Burst',NEW.shots,0)
            , mine_shots = mine_shots + if(NEW.weapon='Mine',NEW.shots,0)
            , spybug_shots = spybug_shots + if(NEW.weapon='Spy Bug',NEW.shots,0)
            , phaser_shots_struck = phaser_shots_struck + if(NEW.weapon='Phaser',NEW.shots_struck,0)
            , bouncer_shots_struck = bouncer_shots_struck + if(NEW.weapon='Bouncer',NEW.shots_struck,0)
            , triple_shots_struck = triple_shots_struck + if(NEW.weapon='Triple',NEW.shots_struck,0)
            , burst_shots_struck = burst_shots_struck + if(NEW.weapon='Burst',NEW.shots_struck,0)
            , mine_shots_struck = mine_shots_struck + if(NEW.weapon='Mine',NEW.shots_struck,0)
            , spybug_shots_struck = spybug_shots_struck + if(NEW.weapon='Spy Bug',NEW.shots_struck,0)
         WHERE player_mv.time_period = new_record_time_period
           AND player_mv.player_name = new_record_player_name
           AND player_mv.is_authenticated = new_record_is_authenticated
         ;
      END IF;
   END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `stats_team`
--

DROP TABLE IF EXISTS `stats_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats_team` (
  `stats_team_id` int(11) NOT NULL AUTO_INCREMENT,
  `stats_game_id` int(11) NOT NULL,
  `team_name` text COLLATE utf8_unicode_ci,
  `color_hex` text COLLATE utf8_unicode_ci NOT NULL,
  `team_score` int(11) DEFAULT NULL,
  `result` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `insertion_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stats_team_id`),
  KEY `stats_game_id` (`stats_game_id`),
  KEY `result` (`result`),
  KEY `insertion_date` (`insertion_date`)
) ENGINE=MyISAM AUTO_INCREMENT=116035 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `v_authenticated_game`
--

DROP TABLE IF EXISTS `v_authenticated_game`;
/*!50001 DROP VIEW IF EXISTS `v_authenticated_game`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_authenticated_game` (
 `stats_game_id` tinyint NOT NULL,
  `authenticated_count` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_current_week_top_player_games`
--

DROP TABLE IF EXISTS `v_current_week_top_player_games`;
/*!50001 DROP VIEW IF EXISTS `v_current_week_top_player_games`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_current_week_top_player_games` (
 `player_name` tinyint NOT NULL,
  `game_count` tinyint NOT NULL,
  `is_authenticated` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_current_week_top_player_official_wins`
--

DROP TABLE IF EXISTS `v_current_week_top_player_official_wins`;
/*!50001 DROP VIEW IF EXISTS `v_current_week_top_player_official_wins`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_current_week_top_player_official_wins` (
 `player_name` tinyint NOT NULL,
  `win_count` tinyint NOT NULL,
  `is_authenticated` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_last_week_top_player_games`
--

DROP TABLE IF EXISTS `v_last_week_top_player_games`;
/*!50001 DROP VIEW IF EXISTS `v_last_week_top_player_games`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_last_week_top_player_games` (
 `player_name` tinyint NOT NULL,
  `game_count` tinyint NOT NULL,
  `is_authenticated` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_last_week_top_player_official_wins`
--

DROP TABLE IF EXISTS `v_last_week_top_player_official_wins`;
/*!50001 DROP VIEW IF EXISTS `v_last_week_top_player_official_wins`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_last_week_top_player_official_wins` (
 `player_name` tinyint NOT NULL,
  `win_count` tinyint NOT NULL,
  `is_authenticated` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_latest_bbb_winners`
--

DROP TABLE IF EXISTS `v_latest_bbb_winners`;
/*!50001 DROP VIEW IF EXISTS `v_latest_bbb_winners`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_latest_bbb_winners` (
 `player_name` tinyint NOT NULL,
  `rank` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_official_game`
--

DROP TABLE IF EXISTS `v_official_game`;
/*!50001 DROP VIEW IF EXISTS `v_official_game`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_official_game` (
 `stats_game_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_authenticated_game`
--

/*!50001 DROP TABLE IF EXISTS `v_authenticated_game`*/;
/*!50001 DROP VIEW IF EXISTS `v_authenticated_game`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_authenticated_game` AS select `stats_player`.`stats_game_id` AS `stats_game_id`,count(`stats_player`.`stats_player_id`) AS `authenticated_count` from `stats_player` where (`stats_player`.`is_authenticated` = 1) group by `stats_player`.`stats_game_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_current_week_top_player_games`
--

/*!50001 DROP TABLE IF EXISTS `v_current_week_top_player_games`*/;
/*!50001 DROP VIEW IF EXISTS `v_current_week_top_player_games`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_current_week_top_player_games` AS select `sp`.`player_name` AS `player_name`,count(`sp`.`stats_game_id`) AS `game_count`,`sp`.`is_authenticated` AS `is_authenticated` from `stats_player` `sp` where ((`sp`.`is_robot` = 0) and (unix_timestamp(`sp`.`insertion_date`) > unix_timestamp((cast((utc_timestamp() + interval 8 hour) as date) - interval ((dayofweek((utc_timestamp() + interval 8 hour)) + 5) % 7) day)))) group by `sp`.`player_name`,`sp`.`is_authenticated` order by count(`sp`.`stats_game_id`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_current_week_top_player_official_wins`
--

/*!50001 DROP TABLE IF EXISTS `v_current_week_top_player_official_wins`*/;
/*!50001 DROP VIEW IF EXISTS `v_current_week_top_player_official_wins`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_current_week_top_player_official_wins` AS select `sp`.`player_name` AS `player_name`,count(`og`.`stats_game_id`) AS `win_count`,`sp`.`is_authenticated` AS `is_authenticated` from (`stats_player` `sp` join `v_official_game` `og`) where ((`sp`.`stats_game_id` = `og`.`stats_game_id`) and (`sp`.`result` = _utf8'W') and (`sp`.`is_robot` = 0) and (unix_timestamp(`sp`.`insertion_date`) > unix_timestamp((cast((utc_timestamp() + interval 8 hour) as date) - interval ((dayofweek((utc_timestamp() + interval 8 hour)) + 5) % 7) day)))) group by `sp`.`player_name`,`sp`.`is_authenticated` order by count(`og`.`stats_game_id`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_last_week_top_player_games`
--

/*!50001 DROP TABLE IF EXISTS `v_last_week_top_player_games`*/;
/*!50001 DROP VIEW IF EXISTS `v_last_week_top_player_games`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_last_week_top_player_games` AS select `sp`.`player_name` AS `player_name`,count(`sp`.`stats_game_id`) AS `game_count`,`sp`.`is_authenticated` AS `is_authenticated` from `stats_player` `sp` where ((`sp`.`is_robot` = 0) and (unix_timestamp(`sp`.`insertion_date`) >= unix_timestamp((cast((utc_timestamp() + interval 8 hour) as date) - interval (((dayofweek((utc_timestamp() + interval 8 hour)) + 5) % 7) + 7) day))) and (unix_timestamp(`sp`.`insertion_date`) < unix_timestamp((cast((utc_timestamp() + interval 8 hour) as date) - interval ((dayofweek((utc_timestamp() + interval 8 hour)) + 5) % 7) day)))) group by `sp`.`player_name`,`sp`.`is_authenticated` order by count(`sp`.`stats_game_id`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_last_week_top_player_official_wins`
--

/*!50001 DROP TABLE IF EXISTS `v_last_week_top_player_official_wins`*/;
/*!50001 DROP VIEW IF EXISTS `v_last_week_top_player_official_wins`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_last_week_top_player_official_wins` AS select `sp`.`player_name` AS `player_name`,count(`og`.`stats_game_id`) AS `win_count`,`sp`.`is_authenticated` AS `is_authenticated` from (`stats_player` `sp` join `v_official_game` `og`) where ((`sp`.`stats_game_id` = `og`.`stats_game_id`) and (`sp`.`result` = _utf8'W') and (`sp`.`is_robot` = 0) and (unix_timestamp(`sp`.`insertion_date`) >= unix_timestamp((cast((utc_timestamp() + interval 8 hour) as date) - interval (((dayofweek((utc_timestamp() + interval 8 hour)) + 5) % 7) + 7) day))) and (unix_timestamp(`sp`.`insertion_date`) < unix_timestamp((cast((utc_timestamp() + interval 8 hour) as date) - interval ((dayofweek((utc_timestamp() + interval 8 hour)) + 5) % 7) day)))) group by `sp`.`player_name`,`sp`.`is_authenticated` order by count(`og`.`stats_game_id`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_latest_bbb_winners`
--

/*!50001 DROP TABLE IF EXISTS `v_latest_bbb_winners`*/;
/*!50001 DROP VIEW IF EXISTS `v_latest_bbb_winners`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_latest_bbb_winners` AS select group_concat(`player_achievements`.`player_name` separator ', ') AS `player_name`,(`player_achievements`.`achievement_id` - 2) AS `rank` from `player_achievements` where ((`player_achievements`.`server_id` = (select `pa`.`server_id` from `player_achievements` `pa` where (`pa`.`achievement_id` = 3) order by `pa`.`date_awarded` desc limit 1)) and (`player_achievements`.`achievement_id` in (3,4,5))) group by `player_achievements`.`achievement_id` order by `player_achievements`.`achievement_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_official_game`
--

/*!50001 DROP TABLE IF EXISTS `v_official_game`*/;
/*!50001 DROP VIEW IF EXISTS `v_official_game`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_official_game` AS select `sg`.`stats_game_id` AS `stats_game_id` from (`stats_game` `sg` join `v_authenticated_game` `tmp`) where ((`sg`.`stats_game_id` = `tmp`.`stats_game_id`) and (`tmp`.`authenticated_count` > 1) and (`sg`.`player_count` > 3)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-21 20:26:29
