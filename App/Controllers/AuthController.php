<?php
namespace App\Controllers;
defined('APP') OR exit('No direct script access allowed');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthController
{
	protected $_container;

	protected $_db;

	public function __construct($c) 
	{
		$this->_container = $c;
		$this->_db = $c->get('db_mysqli');
	}

	public function index(Request $request, Response $response, $args) 
	{
		$route = $request->getAttribute('route');
		$name = $route->getName();
		$groups = $route->getGroups();
		$methods = $route->getMethods();
		$arguments = $route->getArguments();

		print_r($args);
		print '<br />================================ [ AUTH ]=========================';
		print_r($this->_container['settings']);
		print '================================ [ AUTH ]=========================';
	}
}