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

	'/articles' => array(
		'methods' => array(
			'POST', 
			'GET'
		),
		'controller' => 'DashboardController:users',
		'module' => 'articles'
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