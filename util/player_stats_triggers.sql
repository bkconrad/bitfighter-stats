delimiter |
DROP TRIGGER IF EXISTS update_player_stats;
|
CREATE TRIGGER update_player_stats AFTER INSERT ON stats_player
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

         -- calculate kdr
         UPDATE player_mv
            SET kill_death_ratio = kill_count/death_count
          WHERE player_name      = NEW.player_name
            AND time_period      = new_record_time_period
            AND is_authenticated = NEW.is_authenticated;
      END IF;
   END
|
DROP TRIGGER IF EXISTS update_player_shots_stats
|
CREATE TRIGGER update_player_shots_stats AFTER INSERT ON stats_player_shots
   FOR EACH ROW
   BEGIN
      DECLARE new_record_player_name VARCHAR(20);
      DECLARE new_record_is_robot, new_record_is_authenticated INT;
      DECLARE new_record_time_period DATETIME;

      -- we need to do a subquery to determine the actual player data
      -- this assumes the stats_player entry is already in place
      SELECT player_name, is_robot, is_authenticated, TIMESTAMP(CONCAT(YEAR(insertion_date),'-',MONTH(insertion_date),'-01'))
      FROM stats_player
      WHERE stats_player.stats_player_id = NEW.stats_player_id
      INTO new_record_player_name, new_record_is_robot, new_record_is_authenticated, new_record_time_period;

      -- ignore robots
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
   END
|
