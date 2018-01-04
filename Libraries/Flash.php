<?php
namespace Lib;
defined('APP') OR exit('No direct script access allowed');

class Flash extends WebCore
{
	private $_config = array();

	private $_container;

	const TYPE_PREFIX = "type_";

	const STATUS_PREFIX = "status_";

	public function __construct($container, $config)
	{
		$this->_config = $config;
		$this->_container = $container;
	}

	// status | 200 = OK!
	// status != 200 = ERROR!
	public function set($type, $name, $message, $status = 200)
	{
		if(session_id())
		{
			$_SESSION[$name] = $message;
			$_SESSION[self::TYPE_PREFIX . $name] = $type;
			$_SESSION[self::STATUS_PREFIX . $name] = $status;

			return TRUE;
		}
		
		return FALSE;
	}

	public function get($name) 
	{
		if(isset($_SESSION[$name]) && isset($_SESSION[self::TYPE_PREFIX . $name])) 
		{
			$message = $_SESSION[$name];
			$type = $_SESSION[self::TYPE_PREFIX . $name];
			$status = $_SESSION[self::STATUS_PREFIX . $name];

			unset($_SESSION[$name], $_SESSION[self::TYPE_PREFIX . $name], $_SESSION[self::STATUS_PREFIX . $name]);

			return array(
				'flash' => array(
					'message' => $message,
					'type' => $type,
					'status' => $status
				)
			);
		}

		return array();
	}

	public function storageFormCrossHttp($formInputs = array())
	{
		$preparedKeys = array();
		if(session_id())
		{
			foreach($formInputs as $key => $value)
			{
				$_SESSION[$key] = $value;
				$preparedKeys[] = $key;
			}
			return TRUE;
		}
		
		return array_unique($preparedKeys);
	}

	public function getFormStorage($formInputs = array())
	{
		$resetedValues = array();
		foreach($formInputs as $input)
		{
			if(isset($_SESSION[$input])) 
			{
				$resetedValues[$input] = $_SESSION[$input];
			}
		}

		return array('storage' => $resetedValues);
	}
}