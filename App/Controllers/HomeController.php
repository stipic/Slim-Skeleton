<?php
namespace App\Controllers;
defined('APP') OR exit('No direct script access allowed');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\TestModel;

class HomeController extends Controller
{
	protected $_container;

	protected $_db;

	protected $_twig;

	protected $_webcore;

	public function __construct($c) 
	{
		$this->_container = $c;
		$this->_db = $c->get('db_mysqli');
		$this->_twig = $c->get('Twig');
		$this->_webcore = $c->get('WebCore');
	}

	public function index(Request $request, Response $response, $args) 
	{
		/*$methods = $this->_webcore->load_class('Methods');
		echo $methods->test();*/
		$this->_db->get("users");
		echo $this->_twig->render('index.twig', array('name' => 'Fabien'));
	}

}