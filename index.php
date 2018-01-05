<?php
/**
 *
 *
 *	██╗    ██╗███████╗██████╗  ██████╗ ██████╗ ██████╗ ███████╗
 *	██║    ██║██╔════╝██╔══██╗██╔════╝██╔═══██╗██╔══██╗██╔════╝
 *	██║ █╗ ██║█████╗  ██████╔╝██║     ██║   ██║██████╔╝█████╗  
 *	██║███╗██║██╔══╝  ██╔══██╗██║     ██║   ██║██╔══██╗██╔══╝  
 *	╚███╔███╔╝███████╗██████╔╝╚██████╗╚██████╔╝██║  ██║███████╗
 *	 ╚══╝╚══╝ ╚══════╝╚═════╝  ╚═════╝ ╚═════╝ ╚═╝  ╚═╝╚══════╝
 *
 *
 * 
 */

if(!defined('PHP_MAJOR_VERSION') || PHP_MAJOR_VERSION < 7) {
	// TODO baci exception koji otvara maintaince stranicu s error code-om
	die('!@#$%& PHP 7+');
	exit;
}

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
define('SYSTEM_DIR', 'Libraries');
define('STORAGE_DIRECTORY', 'Public');
define('LANG_DIRECTORY', 'Languages');

define('ADMIN_ROOT_MODULE', 'dashboard');

define('ASSETS_DIRECTORY', 'assets');
define('CSS_DIRECTORY', 'css');
define('FONTS_DIRECTORY', 'fonts');
define('IMAGES_DIRECTORY', 'images');
define('SCRIPTS_DIRECTORY', 'scripts');
define('PLUGINS_DIRECTORY', 'plugins');
define('LESS_DIRECTORY', 'less');

define('ASSETS_ROOT', STORAGE_DIRECTORY . '/' . ASSETS_DIRECTORY);
define('CSS_ROOT', STORAGE_DIRECTORY . '/' . ASSETS_DIRECTORY . '/' . CSS_DIRECTORY);
define('FONTS_ROOT', STORAGE_DIRECTORY . '/' . ASSETS_DIRECTORY . '/' . FONTS_DIRECTORY);
define('IMAGES_ROOT', STORAGE_DIRECTORY . '/' . ASSETS_DIRECTORY . '/' . IMAGES_DIRECTORY);
define('SCRIPTS_ROOT', STORAGE_DIRECTORY . '/' . ASSETS_DIRECTORY . '/' . SCRIPTS_DIRECTORY);
define('PLUGINS_ROOT', STORAGE_DIRECTORY . '/' . ASSETS_DIRECTORY . '/' . PLUGINS_DIRECTORY);
define('LESS_ROOT', STORAGE_DIRECTORY . '/' . ASSETS_DIRECTORY . '/' . LESS_DIRECTORY);
define('LANG_ROOT', __DIR__ . DIRECTORY_SEPARATOR . LANG_DIRECTORY);

require __DIR__.DIRECTORY_SEPARATOR.'config.php';
require __DIR__.DIRECTORY_SEPARATOR.COMPOSER_DIRECTORY.DIRECTORY_SEPARATOR.'autoload.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$whoami = gethostname();
foreach($config['env_hostnames'] as $env => $computers) {
	if(in_array($whoami, $computers)) {
		$config['env'] = $env;
		
		$personal_config_path = __DIR__.DIRECTORY_SEPARATOR.PERSONAL_CONFIG_DIRECTORY.DIRECTORY_SEPARATOR.$whoami.'.php';
		include $personal_config_path;
		break;
	}
}

if(!isset($config['env'])) {
	// TODO baci exception koji otvara maintaince stranicu s error code-om
	die('Nema podešeni environment, baci na maintance page');
	exit;
}

$protocol  = "http://";
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
	$protocol  = "https://";
}

$activity_group_id = -1;
$base_url = $_SERVER['SERVER_NAME'];
$uri = $_SERVER['REQUEST_URI'];
$server_url = $protocol.$base_url;
$base_config = $config['base_url'];
$ACP_key_to_read = 1;

if(isset($config['sub_dir']) && $config['sub_dir'] !== NULL && $config['sub_dir'] !== FALSE) {
	$server_url = $protocol.$base_url.$config['sub_dir'];
	$base_config = $config['base_url'].$config['sub_dir'];
	$sub_segments = explode('/', $config['sub_dir']);
	$ACP_key_to_read = count($sub_segments);
}

if($server_url !== $base_config) {
	// TODO baci exception koji otvara maintaince stranicu s error code-om
	// TODO: vidjeti dali je bolje redaktirati ili baciti na maintance
	header('Location: '.$base_config);
	exit; die();
}


$config['acp_base'] = $base_config.'/'.$config['acp_path'].'/'; // ne mora uvijek biti tocno, ali se koristi samo za Twig Filter pa nije toliko hitno testirati

putenv('LC_ALL=' . $config['languages'][$config['current_lang']]['locale']);
setlocale(LC_ALL, $config['languages'][$config['current_lang']]['locale']);
bindtextdomain($config['text_domain'], LANG_ROOT);
bind_textdomain_codeset($config['text_domain'], 'UTF-8'); 
textdomain($config['text_domain']);

$read_mode = APP_DIRECTORY; // 0 = App Dir! 1 = Admin Dir!
$dynamic_namespace = NULL;
$segments = explode('/', $uri);
if(isset($segments[$ACP_key_to_read]) && $segments[$ACP_key_to_read] == $config['acp_path']) {
	$read_mode = ADMIN_DIRECTORY;
}

$app = new \Slim\App(["settings" => $config]);

require __DIR__.DIRECTORY_SEPARATOR.'dependencies.php';

$controllers = array();
if($read_mode == APP_DIRECTORY)
{
	require __DIR__.DIRECTORY_SEPARATOR.APP_DIRECTORY.DIRECTORY_SEPARATOR.'routes.php';
	$dynamic_namespace = APP_DIRECTORY;

	if(!empty($single_routes)) 
	{
		foreach($single_routes as $route => $param) 
		{
			$c_and_method = explode(':', $param['controller']);
			$controller = $c_and_method[0];
			$method = (empty($c_and_method[1]) ? DEFAULT_CONTROLLER_METHOD : $c_and_method[1]);
			$full_controller = $controller . ':' . $method;
			
			$app->map($param['methods'], $route, $full_controller)->setName(strtolower($param['name']));
			$controllers[$controller] = $controller;
		}
	}

	if(!empty($grouped_routes)) 
	{
		foreach($grouped_routes as $group => $routes) 
		{
			$app->group($group, function() use($routes, $app, &$controllers) 
			{
				foreach($routes as $route => $param) 
				{

					$c_and_method = explode(':', $param['controller']);
					$controller = $c_and_method[0];
					$method = (empty($c_and_method[1]) ? DEFAULT_CONTROLLER_METHOD : $c_and_method[1]);
					$full_controller = $controller . ':' . $method;

					$app->map($param['methods'], $route, $full_controller)->setName(strtolower($param['route_name']));

					$controllers[$controller] = $controller;
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
			
			$app->map($param['methods'], '/' . $config['acp_path'] . $route, $full_controller)->setName($param['module']);
			$controllers[$controller] = $controller;
		}
	}

	$container[Admin\Controllers\Controller::class] = function($container) {
		return new Admin\Controllers\Controller($container);
	};

	$app->add(function(Request $request, Response $response, $next) use($app, $uri, $config) 
	{
		$container = $app->getContainer();
		$wc = $container->get('WebCore');
		$wc->TRY_access_admin($request, $response, $next);

		/*$admin_uri = explode('/', $uri);
		print_r($admin_uri);exit;
		$redirect = FALSE;
		if(!empty($admin_uri[count($admin_uri) - 1]) && $admin_uri[count($admin_uri) - 2] == $config['acp_path']) {
			$redirect = TRUE;
		}

		if($redirect == TRUE) 
		{
			// TODO: napraviti da ukoliko pristupa s npr. /ACP/ERG/REG/E/RG/ERG/ da u trenutku logina
			// zadrži /ERG/REG/E/RG/ERG/
			$admin_uri = $request->getUri()->withPath($this->router->pathFor(ADMIN_ROOT_MODULE));
			return $response = $response->withRedirect($admin_uri, 301);
		}*/
		return $next($request, $response);
	});
}

foreach($controllers as $controller) 
{
	$container[$controller] = function($container) use($dynamic_namespace, $controller) {
		$dynamic_controller = '\\'.$dynamic_namespace.'\\'.CONTROLLERS_DIRECTORY.'\\'.$controller;
		return new $dynamic_controller($container);
	};
}

$app->run();
/*
if($config['activity_log'] == TRUE)
{
	$container = $app->getContainer();
	$db = $container->get('db_mysqli');
	$wc = $container->get('WebCore');
	$session_trace = print_r($db->trace, true);
	
	$data = array(
		"activity_group_id" => $activity_group_id,
		"timestamp" => time(),
		"log" => $session_trace,
		"category" => "mysql"
	);
	$db->insert("activity_log", $data);

	$data = array(
		"finished" => time()
	);
	$db->where('id', $activity_group_id);
	$db->update("activity_group", $data);
}*/