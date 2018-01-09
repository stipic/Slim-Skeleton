<?php
defined('APP') OR exit('No direct script access allowed');

$migration = array(
	'autoexec' => true,
	'exec_date' => '09.01.2018',
	'exec_time' => '19:15'
);

$checkSql = 'SHOW TABLES LIKE "articles"';

$execMigrationSql = 
'CREATE TABLE `articles` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(250) DEFAULT NULL,
	`content` mediumtext DEFAULT NULL,
	`slug` varchar(250) DEFAULT NULL,
	`author` int(11) DEFAULT NULL,
	`created` int(50) DEFAULT 0,
  PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';