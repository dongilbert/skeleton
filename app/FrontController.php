<?php

namespace App;

use Joomla\DI\Container;
use Joomla\Router\Router;
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

		$this->registerRoutes();
		$this->registerProviders();
	}

	/**
	 * Register the application routes.
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function registerRoutes()
	{
		// Include the routes file.
		$router = include 'routes.php';

		if (! ($router instanceof Router))
		{
			throw new \Exception('Your routes file must return an instance of Joomla\\Router\\Router.');
		}

		$this->router = $router;
	}

	/**
	 * Register the application service providers
	 *
	 * @return void
	 */
	public function registerProviders()
	{
		$providers = $this->getDefaultServiceProviders();
		$providers += (array) $this->config->get('app.serviceProviders', array());

		foreach ($providers as $provider)
		{
			$this->container->registerServiceProvider(new $provider);
		}
	}

	/**
	 * Pre-load all providers found in the /app/Providers/ directory.
	 *
	 * @return array
	 */
	protected function getDefaultServiceProviders()
	{
		$providerClassFiles = (array) glob(APP_ROOT . '/app/Providers/*.php');

		return array_map(
			function($file) {
				$className = str_replace(APP_ROOT . '/app/', '', rtrim($file, '.php'));

				return __NAMESPACE__ . '\\' . str_replace('/', '\\', $className);
			},
			$providerClassFiles
		);
	}

	public function doExecute()
	{
		$route = $this->router->parseRoute(
			$this->config->get('uri.route'),
			$this->getRequestMethod()
		);

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

	protected function getRequestMethod()
	{
		if ($this->config->get('http.allowMethodOverride', false) === true)
		{
			return $this->input->get($this->config->get('http.methodOverrideName', '_METHOD'));
		}

		return $_SERVER['REQUEST_METHOD'];
	}
}
