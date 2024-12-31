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


    private function database_migration(){
        Create_Tables::create_all_tables();
    }



}