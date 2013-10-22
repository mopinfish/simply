<?php

require 'core/ClassLoader.php';
require '../vendor/Twig/Autoloader.php';

$loader = new ClassLoader();
$loader->registerDir(dirname(__FILE__) . '/core');
$loader->registerDir(dirname(__FILE__) . '/models');
$loader->register();
Twig_Autoloader::register();
