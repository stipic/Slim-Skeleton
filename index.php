<?php
define('PRODUCTION_ENV', 'production');
define('STAGING_ENV', 'staging');
define('DEVELOPMENT_ENV', 'dev');

define('COMPOSER_DIRECTORY', 'Plugins');
define('APP_DIRECTORY', 'App');
define('CONTROLLERS_DIRECTORY', 'Controllers');
define('PERSONAL_CONFIG_DIRECTORY', 'EnvConfig');
define('DEFAULT_CONTROLLER_METHOD', 'index');

require __DIR__.DIRECTORY_SEPARATOR.APP_DIRECTORY.DIRECTORY_SEPARATOR.'config.php';
require __DIR__.DIRECTORY_SEPARATOR.COMPOSER_DIRECTORY.DIRECTORY_SEPARATOR.'autoload.php';

$whoami = gethostname();
foreach($config['env_hostnames'] as $env => $computers) {
	if(in_array($whoami, $computers)) {
		$config['env'] = $env;
		
		$personal_config_path = __DIR__.DIRECTORY_SEPARATOR.APP_DIRECTORY.DIRECTORY_SEPARATOR.PERSONAL_CONFIG_DIRECTORY.DIRECTORY_SEPARATOR.$whoami.'.php';
		include $personal_config_path;
		break;
	}
}

if(!isset($config['env'])) {
	// Nema podešeni environment, baci na maintance page
	exit;
}

$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
$base_url = $_SERVER['SERVER_NAME'];
$server_url = $protocol.$base_url;
if($server_url !== $config['base_url']) {
	header('Location: '.$config['base_url']);
	exit; die();
}

$app = new \Slim\App(["settings" => $config]);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__.DIRECTORY_SEPARATOR.APP_DIRECTORY.DIRECTORY_SEPARATOR.'dependencies.php';
require __DIR__.DIRECTORY_SEPARATOR.APP_DIRECTORY.DIRECTORY_SEPARATOR.'routes.php';

$controllers = array();
if(!empty($single_routes)) {
	foreach($single_routes as $route => $param) {

		$c_and_method = explode(':', $param['controller']);
		$controller = $c_and_method[0];
		$method = (empty($c_and_method[1]) ? DEFAULT_CONTROLLER_METHOD : $c_and_method[1]);
		$full_controller = $controller . ':' . $method;
		
		$app->map($param['methods'], $route, $full_controller);
		if(!in_array($param['controller'], $controllers)) {
			$controllers[] = $param['controller'];
		}
	}
}

if(!empty($grouped_routes)) {
	foreach($grouped_routes as $group => $routes) {
		$app->group($group, function() use($routes, $app, &$controllers) {
			foreach($routes as $route => $param) {

				$c_and_method = explode(':', $param['controller']);
				$controller = $c_and_method[0];
				$method = (empty($c_and_method[1]) ? DEFAULT_CONTROLLER_METHOD : $c_and_method[1]);
				$full_controller = $controller . ':' . $method;

				$app->map($param['methods'], $route, $full_controller)->setName($param['route_name']);

				if(!in_array($param['controller'], $controllers)) {
					$controllers[] = $param['controller'];
				}
			}
		});
	}
}

$container[App\Controllers\Controller::class] = function($container) {
	return new App\Controllers\Controller($container);
};

foreach($controllers as $controller) {
	$container[$controller] = function($container) use($controller) {
		$dynamic_controller = '\\'.APP_DIRECTORY.'\\'.CONTROLLERS_DIRECTORY.'\\'.$controller;
		return new $dynamic_controller($container);
	};
}

$app->run();