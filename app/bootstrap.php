<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

require_once __DIR__ . '/providers.php';
require_once __DIR__ . '/routes.php';
