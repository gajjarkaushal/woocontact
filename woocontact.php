<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://gajjarkaushal.com
 * @since             1.0.0
 * @package           Woocontact
 *
 * @wordpress-plugin
 * Plugin Name:       WooContact Form
 * Plugin URI:        https://gajjarkaushal.com
 * Description:       The WordPress plugin offers support for B2B website users by enabling the integration of WooCommerce as a quote feature directly on product pages. Additionally, it utilizes Contact Form 7 to display forms and include default form emails on product pages. Users can also integrate existing product details, such as product names and URLs.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Gajjar Kaushal
 * Author URI:        https://gajjarkaushal.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocontact
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce, contact-form-7
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
define( 'WOOCONTACT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocontact-activator.php
 */
function activate_woocontact() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocontact_woocommerce_notice' );
		return;
	}
	if ( ! class_exists( 'WPCF7' ) ) {
		add_action( 'admin_notices', 'woocontact_contact_form_7_notice' );
		return;
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocontact-activator.php';
	Woocontact_Activator::activate();
}
/**
 * Displays a notice if WooCommerce plugin is not installed and activated.
 *
 */
function woocontact_woocommerce_notice() {
	echo '<div class="error"><p>' . sprintf( __( 'WooCommerce plugin is required to be installed and activated. Please go to <a href="%s">Plugins</a> page and activate the WooCommerce plugin.', 'woocontact' ), admin_url( 'plugins.php' ) ) . '</p></div>';
}
/**
 * Displays an error notice if Contact Form 7 plugin is not installed or activated.
 */

function woocontact_contact_form_7_notice() {
	echo '<div class="error"><p>' . sprintf( __( 'Contact Form 7 plugin is required to be installed and activated. Please go to <a href="%s">Plugins</a> page and activate the Contact Form 7 plugin.', 'woocontact' ), admin_url( 'plugins.php' ) ) . '</p></div>';
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocontact-deactivator.php
 */
function deactivate_woocontact() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocontact-deactivator.php';
	Woocontact_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocontact' );
register_deactivation_hook( __FILE__, 'deactivate_woocontact' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocontact.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocontact() {

	$plugin = new Woocontact();
	$plugin->run();

}
run_woocontact();