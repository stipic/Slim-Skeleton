<?php

$routes = array(

	'/' => array(
		'methods' => array(
			'POST', 
			'GET'
		),
		'controller' => 'DashboardController'
	),

	'/users' => array(
		'methods' => array(
			'POST', 
			'GET'
		),
		'controller' => 'DashboardController:users'
	)
);