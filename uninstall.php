<?php

/**
 * Trigger this file on Plugin uninstall
 *
 * @package  AlecadddPlugin
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// $books = get_posts( array( 'post_type' => 'book', 'numberposts' => -1 ) );

// foreach( $books as $book ) {
// 	wp_delete_post( $book->ID, true );
// }

// global $wpdb;
// $wpdb->query( "DELETE FROM wp_posts WHERE post_type = 'book'" );
// $wpdb->query( "DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)" );
// $wpdb->query( "DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)" );


global $wpdb;

delete_option('whatsapp_plugin');
delete_option('whatsapp_plugin_version');
delete_option('whatsapp_plugin_install_time');


$tables = [
    $wpdb->prefix . 'contact_forms',
    $wpdb->prefix . 'another_table',
];

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");
}
