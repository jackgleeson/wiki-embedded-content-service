<?php
/**
 * Define all application dependencies in this file.
 */
use Slim\Container;

/**
 * @var Container $container
 */
$container = $app->getContainer();

/**
 * @param \Slim\Container $c
 *
 * @return \App\Service
 */
$container['service'] = function (Container $c) {
    return new App\Service();
};
