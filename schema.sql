CREATE SCHEMA `627089-doingsdone` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `627089-doingsdone`;
CREATE TABLE category (
id INT AUTO_INCREMENT PRIMARY KEY, 
name VARCHAR(64) NOT NULL,
user_id INT NOT NULL,
UNIQUE KEY (name, user_id));

CREATE TABLE user (
id INT AUTO_INCREMENT PRIMARY KEY, 
name VARCHAR(64) NOT NULL,
email VARCHAR(64) NOT NULL UNIQUE,
password VARCHAR(64) NOT NULL,
dt_reg DATETIME DEFAULT CURRENT_TIMESTAMP);

CREATE TABLE task (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(64) NOT NULL,
dt_add DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
dt_complete DATETIME DEFAULT NULL,
dt_due DATETIME DEFAULT NULL,
file VARCHAR(64) DEFAULT NULL,
category_id INT DEFAULT NULL,
user_id INT NOT NULL);

CREATE FULLTEXT INDEX task_ft_search
ON task(name);