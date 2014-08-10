<?php

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array(
        'default' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'redqueen',
            'user'      => 'root',
            'password'  => '',
            'charset'   => 'utf8',
        )
    )
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());