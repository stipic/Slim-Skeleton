<?php
namespace Lib;

class Auth 
{
	private $_config = array();

	private $_container;

	private $_twig;

	private $_auth;

	public function __construct($container, $config)
	{
		$this->_config = $config;
		$this->_container = $container;
		$this->_twig = $this->_container->get('Twig');
		$this->_auth = $this->_container->get('Auth');
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
}