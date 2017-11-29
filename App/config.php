<?php
defined('APP') OR exit('No direct script access allowed');

$config['displayErrorDetails'] = TRUE;
$config['addContentLengthHeader'] = FALSE;
$config['determineRouteBeforeAppMiddleware'] = TRUE;
$config['project_name'] = 'WebCore';
$config['base_url'] = 'https://safe-drive.org';
$config['sub_dir'] = FALSE;
$config['acp_path'] = 'ACP';

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

$config['twig'] = array(
	
	ADMIN_DIRECTORY => array(
		'templates' => APP . DIRECTORY_SEPARATOR . ADMIN_DIRECTORY . DIRECTORY_SEPARATOR . ADMIN_VIEW_DIRECTORY,
		'env' => array(
			'cache' => APP . DIRECTORY_SEPARATOR . ADMIN_DIRECTORY . DIRECTORY_SEPARATOR . ADMIN_VIEW_CACHE_DIRECTORY,
		)
	),

	APP_DIRECTORY => array(
		'templates' => APP . DIRECTORY_SEPARATOR . APP_DIRECTORY . DIRECTORY_SEPARATOR . APP_VIEW_DIRECTORY,
		'env' => array(
			'cache' => APP . DIRECTORY_SEPARATOR . APP_DIRECTORY . DIRECTORY_SEPARATOR . APP_VIEW_CACHE_DIRECTORY,
		)
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