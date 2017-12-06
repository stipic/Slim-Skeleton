<?php
namespace Admin\Controllers;
defined('APP') OR exit('No direct script access allowed');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DashboardController extends Controller
{
	protected $_container;

	protected $_db;

	protected $_twig;

	protected $_config;

	protected $_data = array();

	public function __construct($c) 
	{
		$this->_container = $c;
		$this->_db = $c->get('db_mysqli');
		$this->_twig = $c->get('Twig');
		$this->_config = $c->get('settings');

		$this->_data['page_template'] = 'dashboard.twig';
		$this->_data['base_url'] = $this->_config['base_url'];
	}

	public function index(Request $request, Response $response, $args) 
	{
		$get = $request->getQueryParams();

		$this->_data['page_template'] = 'dashboard.twig';
		$this->_twig->display('layout.twig', $this->_data);
	}

	public function users(Request $request, Response $response, $args) 
	{
		$get = $request->getQueryParams();

		$this->_data['page_template'] = 'dashboard.twig';
		$this->_twig->display('layout.twig', $this->_data);
	}
}