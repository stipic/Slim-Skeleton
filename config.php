<?php
defined('APP') OR exit('No direct script access allowed');

$config['displayErrorDetails'] = TRUE;
$config['addContentLengthHeader'] = FALSE;
$config['determineRouteBeforeAppMiddleware'] = TRUE;
$config['text_domain'] = 'wc';

$config['languages'] = array(
	'hr' => array(
		'locale' => 'hr_HR',
		'date_format' => '',
		'time_format' => '',
		'timezone' => '',
		'lang_name' => 'Croatian'
	),
	'en' => array(
		'locale' => 'en_EN',
		'date_format' => '',
		'time_format' => '',
		'timezone' => '',
		'lang_name' => 'English'
	),
	'rs' => array(
		'locale' => 'rs_RS',
		'date_format' => '',
		'time_format' => '',
		'timezone' => '',
		'lang_name' => 'Serbian'
	)
);
$config['url_lang_flags'] = TRUE; // or FALSE for cookie storage
$config['current_lang'] = 'hr'; // default hr
$config['default_language_use_flag'] = FALSE; // defualtni jezik nece imati language flagove u URLu

$config['project_name'] = 'WebCore';
$config['base_url'] = 'https://safe-drive.org';
$config['sub_dir'] = FALSE;
$config['acp_path'] = 'ACP';

$config['404_template'] = '404/404.twig';
$config['maintenance_template'] = 'maintenance.twig';

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