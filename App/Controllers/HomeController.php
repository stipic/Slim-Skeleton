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

	public $menu_array = array();

	public function __construct($c) 
	{
		$this->_container = $c;
		$this->_db = $c->get('db_mysqli');
		$this->_twig = $c->get('Twig');
	}

	public function index(Request $request, Response $response, $args) 
	{
		echo $this->_twig->render('index.twig', array('name' => 'Fabien'));
	}

}