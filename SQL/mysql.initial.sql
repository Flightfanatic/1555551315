-- Crystal Webmail initial database structure


/*!40014  SET FOREIGN_KEY_CHECKS=0 */;

-- Table structure for table `session`

CREATE TABLE `session` (
 `sess_id` varchar(40) NOT NULL,
 `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `ip` varchar(40) NOT NULL,
 `vars` mediumtext NOT NULL,
 PRIMARY KEY(`sess_id`),
 INDEX `changed_index` (`changed`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


-- Table structure for table `users`

CREATE TABLE `users` (
 `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `username` varchar(128) NOT NULL,
 `mail_host` varchar(128) NOT NULL,
 `alias` varchar(128) NOT NULL,
 `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `last_login` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `language` varchar(5),
 `preferences` text,
 PRIMARY KEY(`user_id`),
 INDEX `username_index` (`username`),
 INDEX `alias_index` (`alias`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


-- Table structure for table `messages`

CREATE TABLE `messages` (
 `message_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 `del` tinyint(1) NOT NULL DEFAULT '0',
 `cache_key` varchar(128) /*!40101 CHARACTER SET ascii COLLATE ascii_general_ci */ NOT NULL,
 `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `idx` int(11) UNSIGNED NOT NULL DEFAULT '0',
 `uid` int(11) UNSIGNED NOT NULL DEFAULT '0',
 `subject` varchar(255) NOT NULL,
 `from` varchar(255) NOT NULL,
 `to` varchar(255) NOT NULL,
 `cc` varchar(255) NOT NULL,
 `date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `size` int(11) UNSIGNED NOT NULL DEFAULT '0',
 `headers` text NOT NULL,
 `structure` text,
 PRIMARY KEY(`message_id`),
 INDEX `created_index` (`created`),
 INDEX `index_index` (`user_id`, `cache_key`, `idx`),
 UNIQUE `uniqueness` (`user_id`, `cache_key`, `uid`),
 CONSTRAINT `user_id_fk_messages` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`)
   /*!40008
     ON DELETE CASCADE
     ON UPDATE CASCADE */
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


-- Table structure for table `cache`

CREATE TABLE `cache` (
 `cache_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `cache_key` varchar(128) /*!40101 CHARACTER SET ascii COLLATE ascii_general_ci */ NOT NULL ,
 `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `data` longtext NOT NULL,
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY(`cache_id`),
 INDEX `created_index` (`created`),
 INDEX `user_cache_index` (`user_id`,`cache_key`),
 CONSTRAINT `user_id_fk_cache` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`)
   /*!40008
     ON DELETE CASCADE
     ON UPDATE CASCADE */
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


-- Table structure for table `contacts`

CREATE TABLE `contacts` (
 `contact_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `del` tinyint(1) NOT NULL DEFAULT '0',
 `name` varchar(128) NOT NULL,
 `email` varchar(128) NOT NULL,
 `firstname` varchar(128) NOT NULL,
 `surname` varchar(128) NOT NULL,
 `vcard` text NULL,
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY(`contact_id`),
 INDEX `user_contacts_index` (`user_id`,`email`),
 CONSTRAINT `user_id_fk_contacts` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`)
   /*!40008
     ON DELETE CASCADE
     ON UPDATE CASCADE */
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


-- Table structure for table `identities`

CREATE TABLE `identities` (
 `identity_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `del` tinyint(1) NOT NULL DEFAULT '0',
 `standard` tinyint(1) NOT NULL DEFAULT '0',
 `name` varchar(128) NOT NULL,
 `organization` varchar(128) NOT NULL DEFAULT '',
 `email` varchar(128) NOT NULL,
 `reply-to` varchar(128) NOT NULL DEFAULT '',
 `bcc` varchar(128) NOT NULL DEFAULT '',
 `signature` text,
 `html_signature` tinyint(1) NOT NULL DEFAULT '0',
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY(`identity_id`),
 CONSTRAINT `user_id_fk_identities` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`)
   /*!40008
     ON DELETE CASCADE
     ON UPDATE CASCADE */
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
