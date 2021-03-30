<?php

/**
 * PHPUnit bootstrap file
 */

/**
 * Load dependencies with Composer autoloader.
 */
require __DIR__ . '/../../vendor/autoload.php';

/**
 * Load all stubs.
 */
$files = glob(__DIR__ .'/../Stubs/WordPress/*.php');
array_map(function ($file) {
    require_once $file;
}, $files);

define('WP_PLUGIN_DIR', __DIR__);
define('WP_DEBUG', false);
define('GF_BAG_FILE', basename(__FILE__));
define('GF_BAG_SLUG', basename(__FILE__, '.php'));
define('GF_BAG_LANGUAGE_DOMAIN', GF_BAG_SLUG);
define('GF_BAG_DIR', basename(__DIR__));
define('GF_BAG_ROOT_PATH', __DIR__);
define('GF_BAG_VERSION', '1.0.1');


/**
 * Bootstrap WordPress Mock.
 */
\WP_Mock::setUsePatchwork(true);
\WP_Mock::bootstrap();

$GLOBALS[GF_BAG_SLUG] = [
    'active_plugins' => [GF_BAG_SLUG .'/'. GF_BAG_FILE],
];
