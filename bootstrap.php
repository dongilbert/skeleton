<?php

use Joomla\DI\Container;

require APP_ROOT . '/vendor/autoload.php';

$app = new App\FrontController;

$app->execute();
