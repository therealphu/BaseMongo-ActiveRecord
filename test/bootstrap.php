<?php

require_once __DIR__ . '/../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
  'BaseMongo' => __DIR__ . '/../src',
  'Test'      => __DIR__ . '/src'
));

$loader->register();