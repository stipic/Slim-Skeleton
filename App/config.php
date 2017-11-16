<?php
defined('APP') OR exit('No direct script access allowed');

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;
$config['determineRouteBeforeAppMiddleware'] = true;
$config['base_url'] = 'https://safe-drive.org';

$config['env_hostnames'] = array(
	DEVELOPMENT_ENV => array(
		'PC-KRISTIJAN'
	),

	STAGING_ENV => array(
	),

	PRODUCTION_ENV => array(
		'vmi123257.contaboserver.net'
	)
);

$config['db'] = array(
	'host' => 'localhost',
	'username' => 'safedriv_e_prod',
	'password' => 'E4~]U8~QIFPA',
	'db' => 'safedriv_e_prod_db',
	'port' => 3306,
	'prefix' => '',
	'charset' => 'utf8'
);