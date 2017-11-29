<?php

namespace Lib;

class WebCore
{
	private $_config = array();

	private $_container;

	public function __construct($container, $config)
	{
		$this->_config = $config;
		$this->_container = $container;
	}

	public function load_class($class_name)
	{
		$dynamic = '\\Lib\\'.$class_name;
		return new $dynamic($this->_container, $this->_config);
	}
}