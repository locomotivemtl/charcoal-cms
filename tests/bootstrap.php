<?php

use \Charcoal\App\App;
use \Charcoal\App\AppConfig;
use \Charcoal\App\AppContainer;

// Composer autoloader for Charcoal's psr4-compliant Unit Tests
$autoloader = require __DIR__.'/../vendor/autoload.php';
$autoloader->add('Charcoal\\Cms\\', __DIR__.'/../src/');
$autoloader->add('Charcoal\\Cms\\Tests\\', __DIR__);

$config = new AppConfig([
    'base_path' => (dirname(__DIR__).'/'),
    'metadata' => [
        'paths' => [
            dirname(__DIR__).'/metadata/'
        ]
    ],
    'translator'=> [
        'locales'=>[
            'languages' =>[
                'en'=>[
                    'locale'=>'en_US.UTF-8'
                ]
            ]
        ],
        'translations'=>[
            'paths'=>[

            ]
        ]
    ]
]);
$GLOBALS['container'] = new AppContainer([
    'config' => $config,
    'cache'  => new \Stash\Pool(),
    'logger' => new \Psr\Log\NullLogger()
]);

$GLOBALS['logger'] = new \Psr\Log\NullLogger();

// Charcoal / Slim is the main app
$GLOBALS['app'] = App::instance($GLOBALS['container']);
$GLOBALS['app']->setLogger($GLOBALS['logger']);
