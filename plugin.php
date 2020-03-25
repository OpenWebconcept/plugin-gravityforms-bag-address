<?php
/**
 * Plugin Name: OWC GravityForms Bag Address
 * Plugin URI: https://www.yard.nl
 * Description: Add a BAG addres field to GravityForms.
 * Author: Yard Digital Agency
 * Author URI: https://www.yard.nl
 * Version: 0.1
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

define('GF_B_A_PLUGIN_FILE', __FILE__);
define('GF_B_A_PLUGIN_SLUG', 'owc-gravityforms-bag-address');
define('GF_B_A_ROOT_PATH', __DIR__);
define('GF_B_A_VERSION', '0.1');

/**
 * Manual loaded file: the autoloader.
 */
require_once __DIR__.'/autoloader.php';
$autoloader = new \Yard\BAG\Autoloader();

/**
 * Begin execution of the plugin.
 */
add_action('plugins_loaded', function () {
    $plugin = (new \Yard\BAG\Foundation\Plugin(__DIR__))->boot();
});
