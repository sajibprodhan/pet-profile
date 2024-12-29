<?php
/**
 * @package  Order Invoice Plugin
 */
namespace Inc\Base;

use WP_Query;
use Inc\Database\Create_Tables;

class Base_Controller {

    const PLUGIN_VERSION = '1.0.0';

    public $plugin_version;

    public $plugin_path;

    public $plugin_url;

    public $plugin;
    
    public $plugin_slug;

    public $managers = array();

    public function __construct() {
        $this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
        $this->plugin_url  = plugin_dir_url( dirname( __FILE__, 2 ) );
        $this->plugin      = plugin_basename( dirname( __FILE__, 3 ) ) . '/pet-profile-plugin.php';
        $this->plugin_slug   =  basename($this->plugin, '.php');
        $this->store_plugin_version();
        $this->database_migration();

        $this->create_pet_page_on_activation();
        
    }


    private function store_plugin_version()
    {
        $saved_install_time = get_option('pet_profile_install_time');
        if (!$saved_install_time) {
            update_option('pet_profile_install_time', time());
        }

        $saved_version = get_option('pet_profile_plugin_version');
        if ( !$saved_version ) {
            update_option('pet_profile_plugin_version', self::PLUGIN_VERSION);
        }
    }

    function create_pet_page_on_activation() {
        $query = new WP_Query( array(
            'post_type'   => 'page',
            'post_title'  => 'Pet Profile',
            'posts_per_page' => 1,
            'post_status' => 'publish',
        ) );

        if ( ! $query->have_posts() ) {
            $new_page = array(
                'post_title'   => 'Pet Profile',
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'page',
            );

            $page_id = wp_insert_post( $new_page );

            if ( $page_id ) {
                update_post_meta( $page_id, '_wp_page_template', 'single-pet.php' );
            }
        }
        
        // Reset the query
        wp_reset_postdata();
    }

    private function database_migration(){
        Create_Tables::create_all_tables();
    }


}