<?php

/**
 * @package  Pet Profile
 */
/*
Plugin Name: Pet Profile
Plugin URI: https://sajibprodhan.github.io
Description: A plugin to generate and send order invoices for WooCommerce stores.
Version: 1.0.0
Author: Sajib Prodhan
Author URI: https://sajibprodhan.github.io
License: GPLv2 or later
GitHub Plugin URI: https://gitlab.com/sajibprodhan/order-invoice-plugin
GitHub Branch: main
Text Domain: pet-profile
*/



// If this file is called firectly, abort!!!
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation
 */
register_activation_hook( __FILE__, [ 'Inc\Base\Activate', 'activate' ] );

/**
 * The code that runs during plugin deactivation
 */
register_deactivation_hook( __FILE__, [ 'Inc\Base\Deactivate', 'deactivate' ] );

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'Inc\\Init' ) ) {
	Inc\Init::register_services();
}
