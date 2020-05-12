CREATE TABLE `user` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `email` VARCHAR(255) NOT NULL DEFAULT '',
        `password` VARCHAR(255) NOT NULL DEFAULT '',
        `telefon` VARCHAR(255), `birthday` DATETIME NOT NULL,
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME NOT NULL,
        `photo` VARCHAR(255),
        `token` VARCHAR(255),
        `wallet` FLOAT(8,2),
        `activated` BOOLEAN NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bank` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) UNSIGNED NOT NULL,
        `owner_name` VARCHAR(255) NOT NULL DEFAULT '',
        `IBAN` VARCHAR(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`id`),
        CONSTRAINT `bank_ibfk_1` FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `transaction` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `email_sender` VARCHAR(255) NOT NULL DEFAULT '',
        `email_receiver` VARCHAR(255) NOT NULL DEFAULT '',
        `quantity` FLOAT(8,2) UNSIGNED NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `requests` (
       `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
       `email_sender` VARCHAR(255) NOT NULL DEFAULT '',
       `email_receiver` VARCHAR(255) NOT NULL DEFAULT '',
       `quantity` FLOAT(8,2) UNSIGNED NOT NULL,
       `paid` BOOLEAN DEFAULT FALSE,
       PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;