<?php

namespace App;

use Joomla\DI\Container;
use Joomla\Router\Router;
use Joomla\Registry\Registry;
use Joomla\Application\AbstractWebApplication;

class FrontController extends AbstractWebApplication
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var Router
	 */
	protected $router;

	/**
	 * Initialize the app requirements
	 *
	 * @return void
	 */
	public function initialise()
	{
		$this->container = new Container;
		$this->router = new Router;

		$this->registerRoutes();
		$this->registerProviders();
	}

	/**
	 * Register the application routes.
	 *
	 * @return void
	 */
	public function registerRoutes()
	{
		// The $router variable is available in the routes file.
		$router = $this->router;

		// Include the routes file.
		include 'routes.php';
	}

	/**
	 * Register the application service providers
	 *
	 * @return void
	 */
	public function registerProviders()
	{
		foreach ($this->config->get('providers', array()) as $provider)
		{
			$this->container->registerServiceProvider(new $provider);
		}
	}

	public function doExecute()
	{
		$route = $this->router->parseRoute($this->config->get('uri.route'), $_SERVER['REQUEST_METHOD']);

		if ($route['controller'] instanceof \Closure)
		{
			$content = call_user_func_array($route['controller'], $route['vars']);
		}
		else
		{
			@list($controllerClass, $method) = explode('@', $route['controller']);

			$method or $method = 'execute';

			$controller = $this->container->buildObject($controllerClass);

			if (! method_exists($controller, $method))
			{
				throw new \InvalidArgumentException(sprintf('Invalid Method: %s::%s', $controllerClass, $method));
			}

			$content = $controller->{$method}();
		}

		$this->setBody($content);
	}
}
