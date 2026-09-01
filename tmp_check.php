<?php
define('ENVIRONMENT', 'production');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('ROOTPATH', __DIR__);
require ROOTPATH . '\app\Config\Constants.php';
require ROOTPATH . '\app\Config\Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . DIRECTORY_SEPARATOR . 'Boot.php';
require ROOTPATH . '\vendor\autoload.php';
require ROOTPATH . '\app\Config\App.php';
echo "boot ok", PHP_EOL;
