<?php
/*
	HTTP METHODS LIST
	-----------------
	GET
	POST
	PUT
	DELETE
	HEAD
	PATCH
	OPTIONS
*/

$single_routes = array(

	'/' => array(
		'methods' => array(
			'POST', 
			'GET'
		),
		'controller' => 'HomeController'
	),
	
);

$grouped_routes = array(
	
	'/api' => array(
		'/auth' => array(
			'methods' => array(
				'POST', 
				'GET'
			),
			'controller' => 'AuthController',
			'route_name' => 'Auth'
		),
		'/login' => array(
			'methods' => array(
				'POST', 
				'GET'
			),
			'controller' => 'HomeController',
			'route_name' => 'Login'
		)
	)
);