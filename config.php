<?php
defined('APP') OR exit('No direct script access allowed');

$config['displayErrorDetails'] = TRUE;
$config['addContentLengthHeader'] = FALSE;
$config['db_trace_activity_log'] = TRUE;
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

$config['google_maps_key'] = 'AIzaSyBzXj_zQHtEpo0piY1cA_qdTgUyp5OCafA';
$config['mailjet_api'] = array(
	'host' => 'in-v3.mailjet.com',
	'username' => 'fbaba1f704d54754cc78abf0da284972',
	'password' => '4e8895e6a10196aff6f4d8bbc664d29d'
);

$config['email'] = array(
	'default_to' => 'kiki.stipic@gmail.com',
	'default_from' => 'web@safedrive.hr'
);

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
			'cache' => FALSE,
		)
	),

	APP_DIRECTORY => array(
		'templates' => APP . DIRECTORY_SEPARATOR . APP_DIRECTORY . DIRECTORY_SEPARATOR . APP_VIEW_DIRECTORY,
		'env' => array(
			'cache' => FALSE,
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