<?php
/**
 * @package  Whatsapp Plugin
 */
namespace Inc\Database;

class Giopio_Pet_Profile {

    public static function create() {
        global $wpdb;
        $table_name      = $wpdb->prefix . 'giopio_pet_profile';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED DEFAULT NULL,
            cover_photo VARCHAR(255) DEFAULT NULL,
            profile_picture VARCHAR(255) DEFAULT NULL,
            name VARCHAR(255) DEFAULT NULL,
            age INT(11) DEFAULT NULL,
            gender VARCHAR(10) DEFAULT NULL,
            about TEXT DEFAULT NULL,
            owner_name VARCHAR(255) DEFAULT NULL,
            mobile VARCHAR(50) DEFAULT NULL,
            location VARCHAR(255) DEFAULT NULL,
            facebook VARCHAR(255) DEFAULT NULL,
            whatsapp_id VARCHAR(50) DEFAULT NULL,
            vaccine_name VARCHAR(255) DEFAULT NULL,
            vaccine_date DATE DEFAULT NULL,
            vaccine_name_2 VARCHAR(255) DEFAULT NULL,
            vaccine_date_2 DATE DEFAULT NULL,
            gallery TEXT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        $wpdb->query( "ALTER TABLE $table_name AUTO_INCREMENT = 1000;" );
    }

}
