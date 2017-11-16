<?php
namespace App\Models;
defined('APP') OR exit('No direct script access allowed');

class TestModel
{
	protected $_container;

	protected $_db;

	public function __construct()
	{
		global $app;
		$container = $app->getContainer();

		$this->_container = $container;
		$this->_db = $container->get('db_mysqli');
		
		print_r($this->_container['settings']);
		$users = $this->_db->get('onlinefilmovi_users');
		var_dump($users);
	}
}