<?php
defined('APP') OR exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$config['base_url'] = 'http://local.safe-drive.org';
$config['api_authentication']['relaxed'] = array(
	'local.safe-drive.org'
);

$config['db'] = array(
	'host' => 'localhost',
	'username' => 'root',
	'password' => 'idbupdrill0',
	'db' => 'menu',
	'port' => 3306,
	'prefix' => '',
	'charset' => 'utf8'
);
