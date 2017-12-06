<?php
defined('APP') OR exit('No direct script access allowed');

// Produkcijski server 
// ime: vmi123257.contaboserver.net

$config['base_url'] = 'https://www.online-filmovi.info';
$config['sub_dir'] = FALSE;
$config['api_authentication']['relaxed'] = array(
	'local.safe-drive.org'
);

$config['db'] = array(
	'host' => 'localhost',
	'username' => 'filmovi_wc',
	'password' => 's59QjH0paj[n',
	'db' => 'filmovi_wc',
	'port' => 3306,
	'prefix' => '',
	'charset' => 'utf8'
);