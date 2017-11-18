<?php
namespace Admin\Controllers;
defined('APP') OR exit('No direct script access allowed');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DashboardController extends Controller
{
	protected $_container;

	protected $_db;

	public $menu_array = array();

	public function __construct($c) 
	{
		$this->_container = $c;
		$this->_db = $c->get('db_mysqli');
	}

	public function index(Request $request, Response $response, $args) 
	{
		die('Yo');
	}

	public function users(Request $request, Response $response, $args) 
	{
		die('Yo Users');
	}
}