<?php
define('APP', __DIR__);
define('PRODUCTION_ENV', 'production');
define('STAGING_ENV', 'staging');
define('DEVELOPMENT_ENV', 'dev');

define('COMPOSER_DIRECTORY', 'Plugins');
define('APP_DIRECTORY', 'App');
define('CONTROLLERS_DIRECTORY', 'Controllers');
define('PERSONAL_CONFIG_DIRECTORY', 'EnvConfig');
define('DEFAULT_CONTROLLER_METHOD', 'index');
define('ADMIN_DIRECTORY', 'Admin');
define('APP_VIEW_DIRECTORY', 'Views');
define('ADMIN_VIEW_DIRECTORY', 'Views');
define('APP_VIEW_CACHE_DIRECTORY', 'Cache');
define('ADMIN_VIEW_CACHE_DIRECTORY', 'Cache');

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
	die('Nema podešeni environment, baci na maintance page');
	exit;
}

$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
$base_url = $_SERVER['SERVER_NAME'];
$uri = $_SERVER['REQUEST_URI'];
$server_url = $protocol.$base_url;
if($server_url !== $config['base_url']) {
	header('Location: '.$config['base_url']);
	exit; die();
}

$read_mode = APP_DIRECTORY; // 0 = App Dir! 1 = Admin Dir!
$dynamic_namespace = NULL;
$segments = explode('/', $uri);
if(isset($segments[1]) && $segments[1] == $config['acp_path']) {
	$read_mode = ADMIN_DIRECTORY;
}

$app = new \Slim\App(["settings" => $config]);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__.DIRECTORY_SEPARATOR.APP_DIRECTORY.DIRECTORY_SEPARATOR.'dependencies.php';

$controllers = array();
if($read_mode == APP_DIRECTORY)
{
	require __DIR__.DIRECTORY_SEPARATOR.APP_DIRECTORY.DIRECTORY_SEPARATOR.'routes.php';
	$dynamic_namespace = APP_DIRECTORY;

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
}
else
{
	require __DIR__.DIRECTORY_SEPARATOR.ADMIN_DIRECTORY.DIRECTORY_SEPARATOR.'routes.php';
	$dynamic_namespace = ADMIN_DIRECTORY;

	if(!empty($routes)) {
		foreach($routes as $route => $param) {

			$c_and_method = explode(':', $param['controller']);
			$controller = $c_and_method[0];
			$method = (empty($c_and_method[1]) ? DEFAULT_CONTROLLER_METHOD : $c_and_method[1]);
			$full_controller = $controller . ':' . $method;
			
			$app->map($param['methods'], '/' . $config['acp_path'] . $route, $full_controller);
			if(!in_array($param['controller'], $controllers)) {
				$controllers[] = $param['controller'];
			}
		}
	}

	$container[Admin\Controllers\Controller::class] = function($container) {
		return new Admin\Controllers\Controller($container);
	};

	$app->add(function(Request $request, Response $response, $next) use($app) {

		$container = $app->getContainer();
		$auth = $container->get('Auth');

		//$auth->login('kiki.stipic@gmail.com', '123321kiki123321');
		if(!$auth->isLoggedIn()) {
			die('E! Jbg nisi ulogiran...');
		}

		return $next($request, $response);
	});
}

foreach($controllers as $controller) {
	$container[$controller] = function($container) use($controller, $dynamic_namespace) {
		$dynamic_controller = '\\'.$dynamic_namespace.'\\'.CONTROLLERS_DIRECTORY.'\\'.$controller;
		return new $dynamic_controller($container);
	};
}

$app->run();