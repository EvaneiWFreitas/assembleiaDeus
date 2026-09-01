<?php
define('ENVIRONMENT', 'production');
define('FCPATH', 'C:\\xampp\\htdocs\\assembleiaDeus\\public' . DIRECTORY_SEPARATOR);
define('ROOTPATH', 'C:\\xampp\\htdocs\\assembleiaDeus');
require ROOTPATH . '\\vendor\\autoload.php';
require ROOTPATH . '\\app\\Config\\Constants.php';
require ROOTPATH . '\\app\\Config\\Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . DIRECTORY_SEPARATOR . 'Common.php';
require $paths->systemDirectory . DIRECTORY_SEPARATOR . 'Boot.php';
$r = \CodeIgniter\Config\Services::routes();
foreach ($r->getRoutes(true) as $m => $rs) {
    foreach ($rs as $k => $v) {
        $t = is_string($v) ? $v : (is_array($v) ? (string)$v[0] : '?');
        echo strtoupper($m) . " {$k} => {$t}\n";
    }
}
