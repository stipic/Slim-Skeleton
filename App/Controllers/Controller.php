<?php
namespace App\Controllers;

use Slim\Container;

class Controller
{
	protected $_container;

	public function __construct(Container $container)
	{
		$this->_container = $container;
	}
	
	public function getContainer()
	{
		return $this->_container;
	}

	public function __get($name)
	{
		return $this->_container->{$name};
	}

	public function __set($name, $value)
	{
		$this->_container->{$name} = $value;
	}
}