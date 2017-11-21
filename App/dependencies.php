<?php
defined('APP') OR exit('No direct script access allowed');

$container = $app->getContainer();
$container['db_mysqli'] = function($container)
{
	return new MysqliDb($container['settings']['db']);
};

$container['Auth'] = function($container) use($config)
{
	$db = new \PDO('mysql:dbname='.$config['db']['db'].';host='.$config['db']['host'].';charset='.$config['db']['charset'], $config['db']['username'], $config['db']['password']);
	return new \Delight\Auth\Auth($db);
};

$container['Twig'] = function($container) use($config, $read_mode)
{
	$loader = new Twig_Loader_Filesystem($config['twig'][$read_mode]['templates']);
	$twig = new Twig_Environment($loader, $config['twig'][$read_mode]['env']);
	return $twig;
};