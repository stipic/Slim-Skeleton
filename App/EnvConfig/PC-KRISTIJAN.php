<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$config['base_url'] = 'http://slimframework.kristijan.lan';
$config['api_authentication']['relaxed'] = array(
	'local.safe-drive.org'
);

$config['db'] = array(
	'host' => 'localhost',
	'username' => 'root',
	'password' => 'm11',
	'db' => 'mpc_db',
	'port' => 3306,
	'prefix' => '',
	'charset' => 'utf8'
);