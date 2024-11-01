<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://test.com
 * @since             1.0.0
 * @package           Sites_Generator
 *
 * @wordpress-plugin
 * Plugin Name:       Sites generator
 * Plugin URI:        http://test.com
 * Description:       This plugin allows managing creating multiple sites within a multisite more conveniently.
 * Version:           1.0.0
 * Author:            Author
 * Author URI:        http://test.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sites-generator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SITES_GENERATOR_VERSION', '1.0.0' );

require_once plugin_dir_path( __FILE__ ) . 'config/constants.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sites-generator-activator.php
 */
function activate_sites_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sites-generator-activator.php';
	Sites_Generator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sites-generator-deactivator.php
 */
function deactivate_sites_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sites-generator-deactivator.php';
	Sites_Generator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sites_generator' );
register_deactivation_hook( __FILE__, 'deactivate_sites_generator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sites-generator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sites_generator() {

	$plugin = new Sites_Generator();
	$plugin->run();

}
run_sites_generator();
