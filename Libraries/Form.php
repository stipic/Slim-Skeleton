<?php
namespace Lib;
defined('APP') OR exit('No direct script access allowed');

class Form extends WebCore
{
	private $_config = array();

	private $_container;

	private $_available_rules = array(
		'isset',
		'not_empty',
		'is_valid_oib',
		'min_len',
		'max_len',
		'required',
		'exact_len',
		'is_numeric',
		'is_alpha',
		'is_alpha_numeric',
		'email',
		'equal'
	);

	public function __construct($container, $config)
	{
		$this->_config = $config;
		$this->_container = $container;
	}

	public function validate($rules = array(), $data = array()) 
	{
		
	}
}