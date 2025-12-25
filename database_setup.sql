-- Improved SQL Script for Driving Experience Database
-- DROP TABLES IF THEY EXIST
DROP TABLE IF EXISTS drivingExperience_maneuvers;
DROP TABLE IF EXISTS drivingExperience;
DROP TABLE IF EXISTS maneuvers;
DROP TABLE IF EXISTS weatherCondition;
DROP TABLE IF EXISTS roadType;
DROP TABLE IF EXISTS roadCondition;
DROP TABLE IF EXISTS trafficLevel;

-- CREATE TABLE: weatherCondition
CREATE TABLE IF NOT EXISTS weatherCondition (
  id_weatherCondition TINYINT NOT NULL AUTO_INCREMENT,
  weatherCondition VARCHAR(30) NOT NULL,
  PRIMARY KEY (id_weatherCondition)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CREATE TABLE: roadType
CREATE TABLE IF NOT EXISTS roadType (
  id_roadType TINYINT NOT NULL AUTO_INCREMENT,
  roadType VARCHAR(30) NOT NULL,
  PRIMARY KEY (id_roadType)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CREATE TABLE: roadCondition
CREATE TABLE IF NOT EXISTS roadCondition (
  id_roadCondition TINYINT NOT NULL AUTO_INCREMENT,
  roadCondition VARCHAR(30) NOT NULL,
  PRIMARY KEY (id_roadCondition)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CREATE TABLE: trafficLevel
CREATE TABLE IF NOT EXISTS trafficLevel (
  id_trafficLevel TINYINT NOT NULL AUTO_INCREMENT,
  trafficLevel VARCHAR(30) NOT NULL,
  PRIMARY KEY (id_trafficLevel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CREATE TABLE: maneuvers
CREATE TABLE IF NOT EXISTS maneuvers (
  id_maneuver TINYINT NOT NULL AUTO_INCREMENT,
  maneuver_type VARCHAR(30) NOT NULL,
  risk_level VARCHAR(10) NOT NULL,
  PRIMARY KEY (id_maneuver)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CREATE TABLE: drivingExperience
CREATE TABLE IF NOT EXISTS drivingExperience (
  id_drivingExperience INT NOT NULL AUTO_INCREMENT,
  date DATE NOT NULL,
  start_time TIME NOT NULL,
  finish_time TIME NOT NULL,
  km_traveled FLOAT NOT NULL,
  id_weatherCondition TINYINT NOT NULL,
  id_roadType TINYINT NOT NULL,
  id_roadCondition TINYINT NOT NULL,
  id_trafficLevel TINYINT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_drivingExperience),
  FOREIGN KEY (id_weatherCondition) REFERENCES weatherCondition(id_weatherCondition),
  FOREIGN KEY (id_roadType) REFERENCES roadType(id_roadType),
  FOREIGN KEY (id_roadCondition) REFERENCES roadCondition(id_roadCondition),
  FOREIGN KEY (id_trafficLevel) REFERENCES trafficLevel(id_trafficLevel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CREATE TABLE: drivingExperience_maneuvers (junction table for many-to-many)
CREATE TABLE IF NOT EXISTS drivingExperience_maneuvers (
  id_drivingExperience INT NOT NULL,
  id_maneuver TINYINT NOT NULL,
  PRIMARY KEY (id_drivingExperience, id_maneuver),
  FOREIGN KEY (id_drivingExperience) REFERENCES drivingExperience(id_drivingExperience) ON DELETE CASCADE,
  FOREIGN KEY (id_maneuver) REFERENCES maneuvers(id_maneuver) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- INSERT INTO weatherCondition
INSERT INTO weatherCondition (weatherCondition) VALUES
('Rainy'),
('Sunny'),
('Stormy'),
('Cloudy'),
('Windy');

-- INSERT INTO roadType
INSERT INTO roadType (roadType) VALUES
('Highway'),
('Urban'),
('Mountain');

-- INSERT INTO roadCondition
INSERT INTO roadCondition (roadCondition) VALUES
('Dry'),
('Wet'),
('Snowy');

-- INSERT INTO trafficLevel
INSERT INTO trafficLevel (trafficLevel) VALUES
('Low'),
('Medium'),
('High');

-- INSERT INTO maneuvers
INSERT INTO maneuvers (maneuver_type, risk_level) VALUES
('Turn Left', 'Low'),
('Turn Right', 'Low'),
('Overtake', 'High'),
('Brake Hard', 'Medium'),
('Change Lane', 'Medium'),
('Reverse', 'Medium'),
('U-Turn', 'High'),
('Park', 'Low');

-- INSERT SAMPLE DATA INTO drivingExperience
INSERT INTO drivingExperience (
  date, start_time, finish_time, km_traveled,
  id_weatherCondition, id_roadType, id_roadCondition, id_trafficLevel
) VALUES
('2024-12-01', '08:30:00', '09:15:00', 25.5, 2, 1, 1, 2),
('2024-12-03', '14:00:00', '14:45:00', 18.2, 1, 2, 2, 3),
('2024-12-05', '10:15:00', '11:30:00', 42.8, 4, 1, 1, 1),
('2024-12-08', '16:30:00', '17:20:00', 31.0, 2, 3, 1, 2),
('2024-12-10', '09:00:00', '10:15:00', 38.5, 5, 2, 3, 3);

-- INSERT SAMPLE DATA INTO drivingExperience_maneuvers
INSERT INTO drivingExperience_maneuvers (id_drivingExperience, id_maneuver) VALUES
(1, 1), -- Experience 1: Turn Left
(1, 5), -- Experience 1: Change Lane
(2, 2), -- Experience 2: Turn Right
(2, 4), -- Experience 2: Brake Hard
(3, 1), -- Experience 3: Turn Left
(3, 3), -- Experience 3: Overtake
(3, 5), -- Experience 3: Change Lane
(4, 7), -- Experience 4: U-Turn
(4, 2), -- Experience 4: Turn Right
(5, 4), -- Experience 5: Brake Hard
(5, 5); -- Experience 5: Change Lane
