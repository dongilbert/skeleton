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
			function ()
			{
				$config = new Registry(APP_ROOT . '/etc/config.php');

				$preloadConfigs = $config->get('app.preloadConfigs', false);

				if ($preloadConfigs !== false)
				{
					foreach ((array) $preloadConfigs as $configFile)
					{
						if (! is_file(APP_ROOT . '/etc/' . $configFile))
						{
							throw new \InvalidArgumentException(sprintf('Invalid config file location: %s.', $configFile));
						}

						$config->loadArray(include APP_ROOT . '/etc/' . $configFile);
					}
				}

				return $config;
			}
		);
	}
}