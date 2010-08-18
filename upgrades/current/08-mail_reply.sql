CREATE TABLE mail_reply (
  `mail_old_id` INT,
  `mail_new_id` INT,
  FOREIGN KEY (`mail_old_id`) REFERENCES courrier (`id`),
  FOREIGN KEY (`mail_new_id`) REFERENCES courrier (`id`)
);
