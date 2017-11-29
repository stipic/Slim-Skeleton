<?php

$routes = array(

	'/' => array(
		'methods' => array(
			'POST', 
			'GET'
		),
		'controller' => 'DashboardController',
		'module' => 'dashboard'
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