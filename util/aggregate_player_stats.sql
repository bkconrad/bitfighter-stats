-- This file is not necessary; equivalent results (except for primary keys)
-- can be obtained through the INSERT triggers in player_stats_triggers.sql
-- However, this script is an order of magnitude faster on large datasets

    DROP TABLE IF EXISTS player_mv;
  CREATE TABLE player_mv ( player_id INT AUTO_INCREMENT PRIMARY KEY, player_name VARCHAR (255), phaser_shots INT NOT NULL, bouncer_shots INT NOT NULL, triple_shots INT NOT NULL, burst_shots INT NOT NULL, mine_shots INT NOT NULL, spybug_shots INT NOT NULL, phaser_shots_struck INT NOT NULL, bouncer_shots_struck INT NOT NULL, triple_shots_struck INT NOT NULL, burst_shots_struck INT NOT NULL, mine_shots_struck INT NOT NULL, spybug_shots_struck INT NOT NULL, win_count INT NOT NULL, lose_count INT NOT NULL, tie_count INT NOT NULL, dnf_count INT NOT NULL, points INT NOT NULL, kill_count INT NOT NULL, death_count INT NOT NULL, suicide_count INT NOT NULL, kill_death_ratio FLOAT NOT NULL, asteroid_crashes INT NOT NULL, flag_drops INT NOT NULL, flag_pickups INT NOT NULL, flag_returns INT NOT NULL, flag_scores INT NOT NULL, teleport_uses INT NOT NULL, turret_kills INT, ff_kills INT, asteroid_kills INT, turrets_engineered INT, ffs_engineered INT, teleports_engineered INT, distance_traveled INT, switched_team_count INT NOT NULL, last_played TIMESTAMP NOT NULL, time_period TIMESTAMP NOT NULL, is_authenticated INT, INDEX (player_name), INDEX (time_period));
  INSERT INTO player_mv ( player_name, phaser_shots, bouncer_shots, triple_shots, burst_shots, mine_shots, spybug_shots, phaser_shots_struck, bouncer_shots_struck, triple_shots_struck, burst_shots_struck, mine_shots_struck, spybug_shots_struck, win_count, lose_count, tie_count, dnf_count, points, kill_count, death_count, suicide_count, kill_death_ratio, asteroid_crashes, flag_drops, flag_pickups, flag_returns, flag_scores, teleport_uses, switched_team_count, turret_kills, ff_kills, asteroid_kills, turrets_engineered, ffs_engineered, teleports_engineered, distance_traveled, last_played, time_period, is_authenticated)
  SELECT player_name
       , SUM(phaser_shots) AS phaser_shots
       , SUM(bouncer_shots) AS bouncer_shots
       , SUM(triple_shots) AS triple_shots
       , SUM(burst_shots) AS burst_shots
       , SUM(mine_shots) AS mine_shots
       , SUM(spybug_shots) AS spybug_shots
       , SUM(phaser_shots_struck) AS phaser_shots_struck
       , SUM(bouncer_shots_struck) AS bouncer_shots_struck
       , SUM(triple_shots_struck) AS triple_shots_struck
       , SUM(burst_shots_struck) AS burst_shots_struck
       , SUM(mine_shots_struck) AS mine_shots_struck
       , SUM(spybug_shots_struck) AS spybug_shots_struck
       , SUM(if(result         = 'W', 1, 0)) AS win_count
       , SUM(if(result         = 'L', 1, 0)) AS lose_count
       , SUM(if(result         = 'T', 1, 0)) AS tie_count
       , SUM(if(result         = 'X', 1, 0)) AS dnf_count
       , SUM(points) AS points
       , SUM(kill_count) AS kill_count
       , SUM(death_count) AS death_count
       , SUM(suicide_count) AS suicide_count
       , SUM(kill_count) / SUM(death_count) AS kill_death_ratio
       , SUM(asteroid_crashes) AS asteroid_crashes
       , SUM(flag_drops) AS flag_drops
       , SUM(flag_pickups) AS flag_pickups
       , SUM(flag_returns) AS flag_returns
       , SUM(flag_scores) AS flag_scores
       , SUM(teleport_uses) AS teleport_uses
       , SUM(switched_team_count) AS switched_team_count
       , SUM(turret_kills) AS turret_kills
       , SUM(ff_kills) AS ff_kills
       , SUM(asteroid_kills) AS asteroid_kills
       , SUM(turrets_engineered) AS turrets_engineered
       , SUM(ffs_engineered) AS ffs_engineered
       , SUM(teleports_engineered) AS teleports_engineered
       , SUM(distance_traveled) AS distance_traveled
       , MAX(insertion_date) AS last_played
       , TIMESTAMP(CONCAT(YEAR(insertion_date),'-', MONTH(insertion_date),'-01')) AS time_period
       , is_authenticated
    FROM stats_player
    JOIN (SELECT stats_player_id
          , SUM(if(weapon = 'Phaser', shots, 0)) AS phaser_shots
          , SUM(if(weapon = 'Bouncer', shots, 0)) AS bouncer_shots
          , SUM(if(weapon = 'Triple', shots, 0)) AS triple_shots
          , SUM(if(weapon = 'Burst', shots, 0)) AS burst_shots
          , SUM(if(weapon = 'Mine', shots, 0)) AS mine_shots
          , SUM(if(weapon = 'Spy Bug', shots, 0)) AS spybug_shots
          , SUM(if(weapon = 'Phaser', shots_struck, 0)) AS phaser_shots_struck
          , SUM(if(weapon = 'Bouncer', shots_struck, 0)) AS bouncer_shots_struck
          , SUM(if(weapon = 'Triple', shots_struck, 0)) AS triple_shots_struck
          , SUM(if(weapon = 'Burst', shots_struck, 0)) AS burst_shots_struck
          , SUM(if(weapon = 'Mine', shots_struck, 0)) AS mine_shots_struck
          , SUM(if(weapon = 'Spy Bug', shots_struck, 0)) AS spybug_shots_struck
          FROM stats_player_shots
          GROUP BY stats_player_id
         ) AS shot_totals
   USING (stats_player_id)
   WHERE stats_player.is_robot = 0
   GROUP BY stats_player.player_name, is_authenticated, time_period

