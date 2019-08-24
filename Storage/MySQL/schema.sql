
/* Categories */
DROP TABLE IF EXISTS `bono_module_announcement_categories`;
CREATE TABLE `bono_module_announcement_categories` (
	`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`name` varchar(254) NOT NULL,
	`class` varchar(255) NOT NULL COMMENT 'Class to simplify rendering'
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

/* Announces */
DROP TABLE IF EXISTS `bono_module_announcement_announces`;
CREATE TABLE `bono_module_announcement_announces` (
	`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`category_id` INT NOT NULL,
	`order` INT NOT NULL,
    `icon` varchar(255) NOT NULL,
	`published` varchar(1) NOT NULL,
	`seo` varchar(1) NOT NULL,

    FOREIGN KEY (category_id) REFERENCES bono_module_announcement_categories(id) DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_announcement_announces_translations`;
CREATE TABLE `bono_module_announcement_announces_translations` (
    `id` INT NOT NULL COMMENT 'Announce ID',
    `lang_id` INT NOT NULL COMMENT 'Attached language ID',
    `web_page_id` INT COMMENT 'Web page related ID',
    `title` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`intro` LONGTEXT NOT NULL,
	`full` LONGTEXT NOT NULL,
	`keywords` TEXT NOT NULL,
	`meta_description` TEXT NOT NULL,

    FOREIGN KEY (id) REFERENCES bono_module_announcement_announces(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE,
    FOREIGN KEY (web_page_id) REFERENCES bono_module_cms_webpages(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;
