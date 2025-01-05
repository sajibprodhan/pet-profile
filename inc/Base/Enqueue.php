<?php

/**
 * @package  Order Invoice Plugin
 */

namespace Inc\Base;

use Inc\Base\Base_Controller;

/**
 * Handles script and style enqueuing for the admin panel.
 */
class Enqueue extends Base_Controller {
    public function register() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }

    public function enqueue_admin_assets() {
        // Ensure jQuery is loaded
        if ( !wp_script_is( 'jquery', 'enqueued' ) ) {
            wp_enqueue_script( 'jquery' );
        }

        // Enqueue CSS files
        wp_enqueue_style( 'petprofile-style', $this->plugin_url . 'assets/admin/css/pet-profile.css', array(), time() );
        

        // Enqueue custom admin scripts
        wp_enqueue_script( 'qrcode-lib', 'https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js', array(), null, true );
        wp_enqueue_script( 'jquery-qrcode', 'https://cdn.rawgit.com/jeromeetienne/jquery-qrcode/1.0.0/jquery.qrcode.min.js', array( 'jquery' ), null, true );
        wp_enqueue_script( 'petprofile-script', $this->plugin_url . 'assets/admin/js/pet-profile.js', array( 'jquery' ), time(), true );
        wp_enqueue_script( 'jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', array(), null, true );

        // Localize the script for AJAX usage
        wp_localize_script( 'petprofile-script', 'ajax_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'ajax_nonce' ),
        ) );
    }

    public function enqueue_frontend_assets()
	{
        if ( !wp_script_is( 'jquery', 'enqueued' ) ) {
            wp_enqueue_script( 'jquery' );
        }
        
		wp_enqueue_style( 'style-css', $this->plugin_url . 'assets/css/style.css', array(),  time() );
        wp_enqueue_style( 'lightbox-style', $this->plugin_url . 'assets/css/lightbox.min.css', array(), time() );
        wp_enqueue_style( 'responsive-css', $this->plugin_url . 'assets/css/responsive.css', array(),  time() );

        wp_enqueue_script( 'petprofile-script', $this->plugin_url . 'assets/admin/js/pet-profile.js', array( 'jquery' ), time(), true );
        wp_enqueue_script( 'lightbox-script', $this->plugin_url . 'assets/js/lightbox.min.js', array( 'jquery' ), time(), true );

	}
}
