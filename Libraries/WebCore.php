<?php
namespace Lib;

class WebCore
{
	private $_config = array();

	private $_container;

	private $_auth;

	private $_twig;

	public function __construct($container, $config)
	{
		$this->_config = $config;
		$this->_container = $container;
		$this->_auth = $this->_container->get('Auth');
		$this->_twig = $this->_container->get('Twig');
	}

	public function load_class($class_name)
	{
		$dynamic = '\\Lib\\'.$class_name;
		return new $dynamic($this->_container, $this->_config);
	}

	public function TRY_access_admin($request, $response, $next) 
	{
		$error = '';
		if(!$this->_auth->isLoggedIn()) 
		{
			if($request->isPost()) 
			{
				$email = $request->getParam('email');
				$password = $request->getParam('password');

				try 
				{
 					$this->_auth->login($email, $password);
 					return TRUE;
				}
				catch (\Delight\Auth\InvalidEmailException $e) {
					$error .= 'Wrong login credentials! #1';
				}
				catch (\Delight\Auth\InvalidPasswordException $e) {
					$error .= 'Wrong login credentials! #2';
				}
				catch (\Delight\Auth\EmailNotVerifiedException $e) {
					$error .= 'Wrong login credentials! #3';
				}
				catch (\Delight\Auth\TooManyRequestsException $e) {
					$error .= 'Wrong login credentials! #4';
				}
			}
		}
		else 
		{
			if($this->_auth->hasRole(\Delight\Auth\Role::ADMIN) && $this->_auth->isNormal()) {
				return TRUE;
			}
			else if($request->isPost()) {
				$error .= 'Wrong login credentials! #5';
			}
		}

		$this->_twig->display('Auth/login.twig', array(
			'title' => $this->_config['project_name'],
			'action' => '',
			'status' => $error
		));

		exit; die();
	}

	public function getIP() 
	{
		$ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
		foreach($ip_keys as $key) 
		{
			if(array_key_exists($key, $_SERVER) === true) 
			{
				foreach(explode(',', $_SERVER[$key]) as $ip) 
				{
					$ip = trim($ip);
					if($this->validate_ip($ip)) 
					{
						return $ip;
					}
				}
			}
		}
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
	}
	
	public function validate_ip($ip)
	{
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) 
		{
			return false;
		}
		return true;
	}
}