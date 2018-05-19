<?php

use \Slim\App;

require './vendor/autoload.php';
$app = new App(require __DIR__ . '/../src/config.php');
require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/routes.php';
$app->run();