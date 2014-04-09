<?php

return array(
	'debug' => false,
	'app' => array(
		'serviceProviders' => array(),
		'preloadConfigs' => array(
			'session'
		)
	),
	'http' => array(
		'allowMethodOverride' => true,
		'methodOverrideName' => '_METHOD'
	)
);