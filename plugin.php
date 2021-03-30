<?php
/**
 * Plugin Name: Yard | GravityForms Bag Address
 * Plugin URI: https://www.yard.nl
 * Description: Add a BAG address field to GravityForms.
 * Author: Yard | Digital Agency
 * Author URI: https://www.yard.nl
 * Version: 1.0.1
 * License: GPL3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: owc-gravityforms-bag-address
 * Domain Path: /languages.
 */

/*
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

define('GF_BAG_FILE', __FILE__);
define('GF_BAG_SLUG', 'owc-gravityforms-bag-address');
define('GF_BAG_LANGUAGE_DOMAIN', GF_BAG_SLUG);
define('GF_BAG_ROOT_PATH', __DIR__);
define('GF_BAG_VERSION', '1.0.1');

/**
 * Manual loaded file: the autoloader.
 */
require_once __DIR__.'/autoloader.php';
$autoloader = new \Yard\BAG\Autoloader();

/**
 * Begin execution of the plugin.
 */
$plugin = (new \Yard\BAG\Foundation\Plugin(__DIR__))->boot();
