<?php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'           => 'Mysql',
	'host'		    => '127.0.0.1',
	'port'		    => 3306,
	'dbname'	    => 'redqueen',
	'username'	    => 'redqueen',
        'password'	    => 'redqueen',
    ),
    'application' => array(
        'controllersDir'    => __DIR__ . '/../../app/Controllers/',
        'modelsDir'         => __DIR__ . '/../../app/Models/',
        'formsDir'          => __DIR__ . '/../../app/Forms/',
        'viewsDir'          => __DIR__ . '/../../app/Views/',
        'validatorDir'      => __DIR__ . '/../../app/Validators/',
        'pluginsDir'        => __DIR__ . '/../../app/plugins/',
        'libraryDir'        => __DIR__ . '/../../app/library/',
        'cacheDir'          => __DIR__ . '/../../app/cache/',
        'baseUri'           => '/',
    )
));
