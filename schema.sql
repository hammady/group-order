DROP TABLE IF EXISTS `shop`;
CREATE TABLE  `shop` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `phone_number` varchar(10) NOT NULL,
  `date_modified` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `meal`;
CREATE TABLE  `meal` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `description` varchar(200) NOT NULL,
  `price` decimal(10,2) default NULL,
  `shop_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_meal_1` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `foodorder`;
CREATE TABLE  `foodorder` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `subject` varchar(200) NOT NULL,
  `state` varchar(1) NOT NULL,
  `date_created` date NOT NULL,
  `owner` varchar(100) NOT NULL,
  `voted_shop_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_foodorder_1` (`voted_shop_id`),
  CONSTRAINT `FK_foodorder_1` FOREIGN KEY (`voted_shop_id`) REFERENCES `shop` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `order_meal`;
CREATE TABLE  `order_meal` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `order_id` int(10) unsigned NOT NULL,
  `meal_id` int(10) unsigned default NULL,
  `owner` varchar(50) NOT NULL,
  `count` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Index_4` (`order_id`,`meal_id`,`owner`),
  KEY `FK_New Table_2` (`meal_id`),
  CONSTRAINT `FK_New Table_1` FOREIGN KEY (`order_id`) REFERENCES `foodorder` (`id`),
  CONSTRAINT `FK_New Table_2` FOREIGN KEY (`meal_id`) REFERENCES `meal` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;

ALTER TABLE `meal` ADD CONSTRAINT `FK_meal_1` FOREIGN KEY `FK_meal_1` (`shop_id`)
    REFERENCES `shop` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
#11-11-2008
CREATE TABLE `New Table` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(16) NOT NULL,
  `password` VARCHAR(8) NOT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  PRIMARY KEY(`id`)
)
ENGINE = InnoDB;
ALTER TABLE `new table` RENAME TO `user`
, ENGINE = InnoDB;

#12-11-2008
ALTER TABLE `meal` MODIFY COLUMN `name` VARCHAR(45) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL;

#14-11-2008
ALTER TABLE `foodorder` MODIFY COLUMN `owner` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL;
update `foodorder` set owner = null;
ALTER TABLE `foodorder` MODIFY COLUMN `owner` INTEGER UNSIGNED DEFAULT NULL;
ALTER TABLE `foodorder` CHANGE COLUMN `owner` `owner_id` INTEGER UNSIGNED DEFAULT NULL,
 ADD CONSTRAINT `FK_foodorder_2` FOREIGN KEY `FK_foodorder_2` (`owner_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
ALTER TABLE `order_meal` MODIFY COLUMN `owner` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL;
update `order_meal` set owner = null;
ALTER TABLE `order_meal` CHANGE COLUMN `owner` `owner_id` INTEGER UNSIGNED DEFAULT NULL,
 DROP INDEX `Index_4`,
 ADD UNIQUE INDEX `Index_4` USING BTREE(`order_id`, `meal_id`, `owner_id`),
 ADD CONSTRAINT `FK_order_meal_3` FOREIGN KEY `FK_order_meal_3` (`owner_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
#16-11-2008
ALTER TABLE `meal` MODIFY COLUMN `description` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL;
ALTER TABLE `meal` MODIFY COLUMN `name` VARCHAR(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
 MODIFY COLUMN `description` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
#17-11-2008 from Home
ALTER TABLE `foodorder` MODIFY COLUMN `subject` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL;
ALTER TABLE `foodorder` MODIFY COLUMN `subject` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `shop` MODIFY COLUMN `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL;
ALTER TABLE `shop` MODIFY COLUMN `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
#21-11-2008
ALTER TABLE `user` ADD COLUMN `ip` VARCHAR(20) NOT NULL AFTER `full_name`,
 ADD COLUMN `group_name` VARCHAR(150) NOT NULL AFTER `ip`;
ALTER TABLE `user` MODIFY COLUMN `group_name` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL;

#3-12-2008
CREATE TABLE `cnfg` (
  `server` VARCHAR(100) NOT NULL,
  `ipmsg_path` VARCHAR(100) NOT NULL
)
ENGINE = InnoDB;

#4-12-2008
ALTER TABLE `shop` ADD COLUMN `delivery` DECIMAL(10,2) NOT NULL AFTER `date_modified`
, ENGINE = InnoDB;
#20-12-2008
ALTER TABLE `user` ADD COLUMN `class` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `group_name`;
#1-1-2009
ALTER TABLE `foodorder` MODIFY COLUMN `date_created` DATETIME DEFAULT NULL;
ALTER TABLE `foodorder` MODIFY COLUMN `date_created` DATETIME NOT NULL;
#31-01-2009
ALTER TABLE `order_meal` ADD COLUMN `notes` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `count`;
ALTER TABLE `order_meal` DROP INDEX `Index_4`,
 ADD UNIQUE INDEX `Index_4` USING BTREE(`order_id`, `meal_id`, `owner_id`, `notes`);
#20-03-2009
CREATE TABLE `menu` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` VARCHAR(45) NOT NULL,
  `path` VARCHAR(256) NOT NULL,
  `shop_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(`id`),
  CONSTRAINT `FK_menu_1` FOREIGN KEY `FK_menu_1` (`shop_id`)
    REFERENCES `shop` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;

ALTER TABLE `menu` ADD COLUMN `img` BLOB NOT NULL AFTER `shop_id`
, ENGINE = InnoDB;
ALTER TABLE `menu` MODIFY COLUMN `img` LONGBLOB NOT NULL
, ENGINE = InnoDB;
ALTER TABLE `menu` DROP COLUMN `img`
, ENGINE = InnoDB;
#7-4-2009
ALTER TABLE `shop` ADD COLUMN `ban_date` DATETIME NOT NULL AFTER `delivery`
, ENGINE = InnoDB;
#11-4-2009
ALTER TABLE `meal` ADD COLUMN `creator_id` INTEGER UNSIGNED NOT NULL DEFAULT 1 AFTER `shop_id`,
 ADD CONSTRAINT `FK_meal_2` FOREIGN KEY `FK_meal_2` (`creator_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
#18-04-2009
ALTER TABLE `user` ADD COLUMN `ordersPerPage` INTEGER UNSIGNED NOT NULL DEFAULT 20 AFTER `class`;
#20-04-2009
ALTER TABLE `user` ADD COLUMN `receiveWhenNewOrder` VARCHAR(1) NOT NULL DEFAULT 'Y' AFTER `ordersPerPage`;
#4-6-2009
ALTER TABLE `user` ADD COLUMN `seenNote1` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `receiveWhenNewOrder`
, ENGINE = InnoDB;
#18-07-2009
ALTER TABLE `foodorder` ADD COLUMN `managed` VARCHAR(1) NOT NULL AFTER `voted_shop_id`;
UPDATE foodorder SET managed = 'Y';
#28-12-2009
CREATE TABLE `trans` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INTEGER UNSIGNED NOT NULL,
  `amount` INTEGER UNSIGNED NOT NULL,
  `order_id` INTEGER UNSIGNED NOT NULL,
  `active` VARCHAR(1) NOT NULL DEFAULT 'N',
  `type` VARCHAR(1) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_trans_1` FOREIGN KEY `FK_trans_1` (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `FK_trans_2` FOREIGN KEY `FK_trans_2` (`order_id`)
    REFERENCES `foodorder` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;

ALTER TABLE `user` ADD COLUMN `balance` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `seenNote1`;

ALTER TABLE `trans` ADD UNIQUE INDEX `Index_4`(`user_id`, `order_id`);

ALTER TABLE `cnfg` ADD COLUMN `management` INTEGER UNSIGNED NOT NULL AFTER `ipmsg_path`;

ALTER TABLE `trans` MODIFY COLUMN `amount` DECIMAL(10,2) NOT NULL;

ALTER TABLE `trans` MODIFY COLUMN `order_id` INT(10) UNSIGNED DEFAULT NULL;

ALTER TABLE `user` MODIFY COLUMN `balance` DECIMAL(10,2) NOT NULL DEFAULT 0;

ALTER TABLE `trans` ADD COLUMN `date` DATETIME NOT NULL AFTER `type`;
#19-01-2010
ALTER TABLE `cnfg` ADD COLUMN `default_shop_id` INTEGER UNSIGNED NOT NULL AFTER `management`;
#26-01-2010
ALTER TABLE `user` MODIFY COLUMN `password` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
#update user set  password = password(password);
#30-01-2010
ALTER TABLE `user` ADD COLUMN `passwordResetCode` VARCHAR(100) AFTER `balance`;
#07-04-2010
CREATE TABLE `poll` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(256) NOT NULL,
  `description` VARCHAR(5000) NOT NULL,
  `owner_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_poll_1` FOREIGN KEY `FK_poll_1` (`owner_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;
CREATE TABLE `poll_option` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `poll_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_poll_option_1` FOREIGN KEY `FK_poll_option_1` (`poll_id`)
    REFERENCES `poll` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;
#20-04-2010
CREATE TABLE `poll_vote` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_id` INTEGER UNSIGNED NOT NULL,
  `poll_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_poll_vote_1` FOREIGN KEY `FK_poll_vote_1` (`owner_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `FK_poll_vote_2` FOREIGN KEY `FK_poll_vote_2` (`poll_id`)
    REFERENCES `poll` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;
ALTER TABLE `poll_vote` ADD COLUMN `poll_option_id` INTEGER UNSIGNED NOT NULL AFTER `poll_id`,
 ADD CONSTRAINT `FK_poll_vote_3` FOREIGN KEY `FK_poll_vote_3` (`poll_option_id`)
    REFERENCES `poll_vote` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
ALTER TABLE `poll_vote`
 DROP FOREIGN KEY `FK_poll_vote_3`;

ALTER TABLE `poll_vote` ADD CONSTRAINT `FK_poll_vote_3` FOREIGN KEY `FK_poll_vote_3` (`poll_option_id`)
    REFERENCES `poll_option` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
    
CREATE TABLE `poll_discuss` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `desc` VARCHAR(4000) NOT NULL,
  `poll_id` INTEGER UNSIGNED NOT NULL,
  `owner_id` INTEGER UNSIGNED NOT NULL,
  `time` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_poll_discuss_1` FOREIGN KEY `FK_poll_discuss_1` (`poll_id`)
    REFERENCES `poll` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `FK_poll_discuss_2` FOREIGN KEY `FK_poll_discuss_2` (`owner_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;
    
    ALTER TABLE `poll_discuss` CHANGE COLUMN `desc` `discuss` VARCHAR(4000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
 CHANGE COLUMN `time` `discuss_time` DATETIME NOT NULL;
    
    ALTER TABLE `poll_discuss` MODIFY COLUMN `discuss` VARCHAR(4000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
    
    
    ALTER TABLE `poll` ADD COLUMN `state` VARCHAR(1) NOT NULL AFTER `owner_id`;
    
    ALTER TABLE `poll` MODIFY COLUMN `description` VARCHAR(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
    
	
#10/10/2010
	ALTER TABLE `shop` ADD COLUMN `banned` VARCHAR(1) NOT NULL DEFAULT 'N'  AFTER `ban_date` ;

