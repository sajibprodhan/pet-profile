<?php

/**
 * @package AlecadddPlugin
 */

namespace Inc\Api\Callbacks;

use Inc\Base\Base_Controller;
use Inc\Traits\Form_Error;
// Make sure to include the error trait

/**
 * Class Manager_Callbacks
 */
class Manager_Callbacks extends Base_Controller {
    use Form_Error;

    public function pet_profile_form() {
        ?>
        <div class="wrap">
            <h1>Pet Profile Form</h1>
            <form method="post" enctype="multipart/form-data">
                <h2>Pet Details</h2>
                <label>Pet Name:</label>
                <input type="text" name="pet_name">
                <?php if ( $this->has_error( 'pet_name' ) ): ?>
                    <span class="error"><?php echo $this->get_error( 'pet_name' ); ?></span>
                <?php endif;?><br><br>

                <label>Age:</label>
                <input type="number" name="pet_age"><br><br>

                <label>Gender:</label>
                <select name="pet_gender">
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select><br><br>

                <label>About:</label>
                <textarea name="pet_about"></textarea><br><br>

                <h2>Owner Details</h2>
                <label>Owner Name:</label>
                <input type="text" name="owner_name">
                <?php if ( $this->has_error( 'owner_name' ) ): ?>
                    <span class="error"><?php echo $this->get_error( 'owner_name' ); ?></span>
                <?php endif;?><br><br>

                <label>Mobile:</label>
                <input type="text" name="owner_mobile"><br><br>

                <label>Location:</label>
                <input type="text" name="owner_location"><br><br>

                <label>Facebook:</label>
                <input type="text" name="owner_facebook"><br><br>

                <label>WhatsApp ID:</label>
                <input type="text" name="owner_whatsapp_id"><br><br>

                <h2>Vaccination Details</h2>
                <label>Vaccine 1:</label>
                <input type="text" name="vaccine_name">
                <label>Date:</label>
                <input type="date" name="vaccine_date"><br><br>

                <label>Vaccine 2:</label>
                <input type="text" name="vaccine_name_2">
                <label>Date:</label>
                <input type="date" name="vaccine_date_2"><br><br>

                <h2>Gallery</h2>
                <label>Gallery Images:</label>
                <input type="file" name="gallery[]" multiple><br><br>

                <input type="submit" name="save_pet_profile" class="button-primary" value="Save Profile">
            </form>
        </div>
        <?php
}

    public function save_pet_profile() {
        if ( isset( $_POST['save_pet_profile'] ) ) {
        print_r( $_POST );
        die;
}
       
        global $wpdb;
        $table_name = $wpdb->prefix . 'giopio_pet_profile';

        if ( empty( $_POST['pet_name'] ) ) {
            $this->errors['pet_name'] = 'Pet Name is required';
        }


        if ( empty( $_POST['owner_name'] ) ) {
            $this->errors['owner_name'] = 'Owner Name is required';
        }

        // If errors exist, stop the process and return errors
        if ( !empty( $this->errors ) ) {
            foreach ( $this->errors as $error ) {
                echo '<div class="error notice"><p>' . $error . '</p></div>';
            }
            return;
        }

        $gallery = [];
        if ( !empty( $_FILES['gallery']['name'] ) ) {
            foreach ( $_FILES['gallery']['name'] as $index => $file ) {
                $file_type = wp_check_filetype( $file );
                if ( !in_array( $file_type['ext'], ['jpg', 'jpeg', 'png', 'gif'] ) ) {
                    $this->errors['gallery'] = 'Only image files are allowed in the gallery.';
                    break;
                }
                $upload    = wp_upload_bits( $file, null, file_get_contents( $_FILES['gallery']['tmp_name'][$index] ) );
                $gallery[] = $upload['url'];
            }
        }

        if ( isset( $this->errors['gallery'] ) ) {
            echo '<div class="error notice"><p>' . $this->errors['gallery'] . '</p></div>';
            return;
        }

        // Insert data into the database
        $wpdb->insert(
            $table_name,
            [
                'name'           => sanitize_text_field( $_POST['pet_name'] ),
                'age'            => intval( $_POST['pet_age'] ),
                'gender'         => sanitize_text_field( $_POST['pet_gender'] ),
                'about'          => sanitize_textarea_field( $_POST['pet_about'] ),
                'owner_name'     => sanitize_text_field( $_POST['owner_name'] ),
                'mobile'         => sanitize_text_field( $_POST['owner_mobile'] ),
                'location'       => sanitize_text_field( $_POST['owner_location'] ),
                'facebook'       => sanitize_text_field( $_POST['owner_facebook'] ),
                'whatsapp_id'    => sanitize_text_field( $_POST['owner_whatsapp_id'] ),
                'vaccine_name'   => sanitize_text_field( $_POST['vaccine_name'] ),
                'vaccine_date'   => sanitize_text_field( $_POST['vaccine_date'] ),
                'vaccine_name_2' => sanitize_text_field( $_POST['vaccine_name_2'] ),
                'vaccine_date_2' => sanitize_text_field( $_POST['vaccine_date_2'] ),
                'gallery'        => maybe_serialize( $gallery ),
                'created_at'     => current_time( 'mysql' ),
            ]
        );

        echo '<div class="updated notice"><p>Pet profile saved successfully!</p></div>';
    }
}
