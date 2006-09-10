# User sessions, to stay connected even if the browser is closed
# hash - user_id - expiration
CREATE TABLE `sessions` (
  `hash` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expiration` date NOT NULL,
  PRIMARY KEY (`hash`)
);

# Completed upgrades
# version - upgrade_name
CREATE TABLE `upgrades` (
  `version` varchar(255) NOT NULL,
  `upgrade_name` varchar(255) NOT NULL,
  PRIMARY KEY (`version`, `upgrade_name`)
);
