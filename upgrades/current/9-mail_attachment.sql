-- Nouvelle table pour stocker des pièces jointes multiples
CREATE TABLE mail_attachment (
  id INT PRIMARY KEY auto_increment,
  mail_id INT,
  filename VARCHAR(255),
  FOREIGN KEY (`mail_id`) REFERENCES `courrier` (`id`)
);
