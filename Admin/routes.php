<?php

$routes = array(

	'/' => array(
		'methods' => array(
			'POST', 
			'GET'
		),
		'controller' => 'DashboardController',
		'module' => ADMIN_ROOT_MODULE
	),

	// =============[ LANGUAGES ] =============[

	'/languages' => array(
		'methods' => array(
			'POST', 
			'GET'
		),
		'controller' => 'LanguageController:index',
		'module' => 'languages'
	),

	'/languages/scanMessages' => array(
		'methods' => array(
			'POST'
		),
		'controller' => 'LanguageController:scan',
		'module' => 'languages'
	),




	'/sections' => array(
		'methods' => array(
			'POST', 
			'GET'
		),
		'controller' => 'SectionController',
		'module' => 'sections'
	),

	'/menus' => array(
		'methods' => array(
			'POST', 
			'GET'
		),
		'controller' => 'MenuController',
		'module' => 'menus'
	),
);