<?php

namespace App\Providers;

use Joomla\Registry\Registry;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

class ConfigProvider implements ServiceProviderInterface
{
	/**
	 * Register the config service to the container.
	 *
	 * @param Container $container
	 */
	public function register(Container $container)
	{
		$container->share(
			'config',
			function (Container $c)
			{
				return new Registry(APP_ROOT . '/etc/config.php');
			}
		);
	}
}