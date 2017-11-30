<?php
defined('APP') OR exit('No direct script access allowed');

$container = $app->getContainer();
$container['db_mysqli'] = function($container)
{
	return new MysqliDb($container['settings']['db']);
};

$container['WebCore'] = function($container) use($config)
{
	return new \Lib\WebCore($container, $config);
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

	$filter = new Twig_Filter('assets_root', function ($string) use($config) {
		return $config['base_url'] . '/'. ASSETS_ROOT . '/' . $string;
	});
	$twig->addFilter($filter);

	$filter = new Twig_Filter('base_url', function ($string) use($config) {
		return $config['base_url'] . (!empty($string) ? '/' . $string : '');
	});
	$twig->addFilter($filter);

	$filter = new Twig_Filter('css_root', function ($string) use($config) {
		return  $config['base_url'] . '/'. CSS_ROOT . '/' . $string;
	});
	$twig->addFilter($filter);

	$filter = new Twig_Filter('fonts_root', function ($string) use($config) {
		return  $config['base_url'] . '/'. FONTS_ROOT . '/' . $string;
	});
	$twig->addFilter($filter);

	$filter = new Twig_Filter('images_root', function ($string) use($config) {
		return  $config['base_url'] . '/'. IMAGES_ROOT . '/' . $string;
	});
	$twig->addFilter($filter);

	$filter = new Twig_Filter('scripts_root', function ($string) use($config) {
		return  $config['base_url'] . '/'. SCRIPTS_ROOT . '/' . $string;
	});
	$twig->addFilter($filter);

	$filter = new Twig_Filter('plugins_root', function ($string) use($config) {
		return  $config['base_url'] . '/'. PLUGINS_ROOT . '/' . $string;
	});
	$twig->addFilter($filter);

	$filter = new Twig_Filter('less_root', function ($string) use($config) {
		return  $config['base_url'] . '/'. LESS_ROOT . '/' . $string;
	});
	$twig->addFilter($filter);

	return $twig;
};

$container['notFoundHandler'] = function($container) use($config) 
{
	$twig = $container->get('Twig');
	return function ($request, $response) use($config, $twig) {
		$twig->display($config['404_template'], array(
			'title' => $config['project_name']
		));
		exit;die();
	};
};