<?php
defined('APP') OR exit('No direct script access allowed');

$container = $app->getContainer();
$container['db_mysqli'] = function($container)
{
	return new MysqliDb($container['settings']['db']);
};