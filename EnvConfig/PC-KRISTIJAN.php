<?php
defined('APP') OR exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set('display_errors', 1);

//$config['base_url'] = 'http://local.webcore.lan';
$config['base_url'] = 'http://webcore.lan';
$config['sub_dir'] = FALSE;
$config['api_authentication']['relaxed'] = array(
	'local.safe-drive.org'
);

$config['db'] = array(
	'host' => 'localhost',
	'username' => 'root',
	'password' => 'idbupdrill0',
	//'password' => 'm11',
	'db' => 'webcore',
	'port' => 3306,
	'prefix' => '',
	'charset' => 'utf8'
);
$config['twig'][ADMIN_DIRECTORY]['env']['cache'] = false;
$config['twig'][APP_DIRECTORY]['env']['cache'] = false;