-- Historique générique d'un courrier
CREATE TABLE mail_history (
  id INT PRIMARY KEY auto_increment,
  mail_id INT,
  event_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  service_id INT,
  message VARCHAR(255),  
  FOREIGN KEY (`mail_id`) REFERENCES `courrier` (`id`),
  FOREIGN KEY (`service_id`) REFERENCES `service` (`id`)
);
