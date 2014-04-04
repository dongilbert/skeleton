<?php

/** @var \Joomla\Router\Router $router */

$router->get('/home', 'App\\Controllers\\HomeController@execute');

$router->get(
	'/hello/:name',
	function($name)
	{
		return "Hello $name!";
	},
	array('name' => '[a-zA-Z]+')
);
