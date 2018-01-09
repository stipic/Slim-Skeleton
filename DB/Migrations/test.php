<?php
defined('APP') OR exit('No direct script access allowed');

$migration = array(
	'autoexec' => true,
	'exec_date' => '09.01.2018',
	'exec_time' => '19:15'
);

$checkSql = 'SELECT * FROM users';

$execMigrationSql = ' ';