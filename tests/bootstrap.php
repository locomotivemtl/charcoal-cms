<?php

use \Charcoal\Charcoal as Charcoal;

// Composer autoloader for Charcoal's psr4-compliant Unit Tests
$autoloader = require __DIR__ . '/../vendor/autoload.php';
$autoloader->add('Charcoal\\Cms\\', __DIR__.'/../src/');
$autoloader->add('Charcoal\\Cms\\Tests\\', __DIR__);


// This var needs to be set automatically, for now
Charcoal::init();
Charcoal::config()['base_path'] = '';
