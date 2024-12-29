<?php
/**
 * @package  Whatsapp Plugin
 */
namespace Inc\Api\Callbacks;

use Inc\Base\Base_Controller;

class Admin_Callbacks extends Base_Controller {
    public function admin_dashboard() {
        return require_once "$this->plugin_path/templates/admin.php";
    }

    public function admin_cpt() {
        return require_once "$this->plugin_path/templates/cpt.php";
    }

}