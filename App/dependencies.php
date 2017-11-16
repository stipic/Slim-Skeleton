<?php

$container = $app->getContainer();
$container['db_mysqli'] = function($container)
{
	return new MysqliDb($container['settings']['db']);
};