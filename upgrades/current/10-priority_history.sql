-- Nouvelle table pour historiser la priorité (modifiable) d'un courrier
CREATE TABLE `mail_priority_history` (
  id INT PRIMARY KEY auto_increment,
  mail_id INT,
  event_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  old_value INT,
  FOREIGN KEY (`mail_id`) REFERENCES `courrier` (`id`),
  FOREIGN KEY (`old_value`) REFERENCES `priorite` (`id`)
);
