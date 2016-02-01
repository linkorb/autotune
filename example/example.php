<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';
if (class_exists('AutoTune\Tuner')) {
  \AutoTune\Tuner::init($loader, __DIR__ . '/..');
}

$logger = new \Monolog\Logger('example');
var_dump($logger);
