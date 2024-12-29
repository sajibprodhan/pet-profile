<?php
/**
 * @package  Whatsapp Plugin
 */
namespace Inc\Database;

use Inc\Database\Another_Table;
use Inc\Database\Giopio_Pet_Profile;

class Create_Tables {
    
    public static function create_all_tables() {
        Giopio_Pet_Profile::create();
    }
}
