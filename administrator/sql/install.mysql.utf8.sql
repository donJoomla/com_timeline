CREATE TABLE IF NOT EXISTS `#__timeline_timelines` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`title` VARCHAR(255)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`width` VARCHAR(255)  NOT NULL ,
`height` VARCHAR(255)  NOT NULL ,
`start_at_slide` INT NOT NULL ,
`start_zoom_adjust` INT(11)  NOT NULL ,
`hash_bookmark` VARCHAR(255)  NOT NULL ,
`maptype` VARCHAR(255)  NOT NULL ,
`font` VARCHAR(255)  NOT NULL ,
`headline` VARCHAR(255)  NOT NULL ,
`type` VARCHAR(255)  NOT NULL ,
`text` TEXT NOT NULL ,
`media` VARCHAR(255)  NOT NULL ,
`credit` TEXT NOT NULL ,
`caption` TEXT NOT NULL ,
`lang` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__timeline_items` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`timeline` INT NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`headline` VARCHAR(255)  NOT NULL ,
`startdate` DATE NOT NULL ,
`enddate` DATE NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`text` TEXT NOT NULL ,
`tag` TEXT NOT NULL ,
`media` VARCHAR(255)  NOT NULL ,
`thumbnail` VARCHAR(255)  NOT NULL ,
`credit` TEXT NOT NULL ,
`caption` TEXT NOT NULL ,
`classname` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

