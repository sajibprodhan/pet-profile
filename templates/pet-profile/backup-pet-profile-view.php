<?php

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'giopio_pet_profile';

        // Handle file uploads for pet_gallery
        $uploaded_files = array();
        if ( isset( $_FILES['pet_gallery'] ) && !empty( $_FILES['pet_gallery']['name'][0] ) ) {
            $files = $_FILES['pet_gallery'];
            for ( $i = 0; $i < count( $files['name'] ); $i++ ) {
                $file = array(
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                );

                $upload = wp_handle_upload( $file, array( 'test_form' => false ) );
                if ( ! isset( $upload['error'] ) ) {
                    $uploaded_files[] = $upload['url'];
                }
            }
        }

        $pet_gallery = empty( $uploaded_files ) ? '' : implode( ',', $uploaded_files );

        $cover_photo = '';
        if ( isset( $_FILES['pet_cover_photo'] ) && !empty( $_FILES['pet_cover_photo']['name'] ) ) {
            $upload = wp_handle_upload( $_FILES['pet_cover_photo'], array( 'test_form' => false ) );
            if ( ! isset( $upload['error'] ) ) {
                $cover_photo = $upload['url'];
            }
        }

        $profile_picture = '';
        if ( isset( $_FILES['profile_picture'] ) && !empty( $_FILES['profile_picture']['name'] ) ) {
            $upload = wp_handle_upload( $_FILES['profile_picture'], array( 'test_form' => false ) );
            if ( ! isset( $upload['error'] ) ) {
                $profile_picture = $upload['url'];
            }
        }

        // Sanitize form fields
        $pet_name            = sanitize_text_field( $_POST['pet_name'] );
        $pet_age             = intval( $_POST['pet_age'] );
        $pet_gender          = sanitize_text_field( $_POST['pet_gender'] );
        $pet_owner_name      = sanitize_text_field( $_POST['pet_owner_name'] );
        $pet_mobile          = sanitize_text_field( $_POST['pet_mobile'] );
        $pet_location        = sanitize_text_field( $_POST['pet_location'] );
        $pet_facebook        = sanitize_text_field( $_POST['pet_facebook'] );
        $pet_whatsapp        = sanitize_text_field( $_POST['pet_whatsapp'] );
        $pet_vaccine_name    = sanitize_text_field( $_POST['pet_vaccine_name'] );
        $pet_vaccine_date    = sanitize_text_field( $_POST['pet_vaccine_date'] );
        $pet_vaccine_name_2  = sanitize_text_field( $_POST['pet_vaccine_name_2'] );
        $pet_vaccine_date_2  = sanitize_text_field( $_POST['pet_vaccine_date_2'] );
        $pet_about           = sanitize_textarea_field( $_POST['pet_about'] );

        // Prepare the data to insert or update
        $data = array(
            'name'            => $pet_name,
            'age'             => $pet_age,
            'gender'          => $pet_gender,
            'owner_name'      => $pet_owner_name,
            'mobile'          => $pet_mobile,
            'location'        => $pet_location,
            'facebook'        => $pet_facebook,
            'whatsapp_id'     => $pet_whatsapp,
            'vaccine_name'    => $pet_vaccine_name,
            'vaccine_date'    => $pet_vaccine_date,
            'vaccine_name_2'  => $pet_vaccine_name_2,
            'vaccine_date_2'  => $pet_vaccine_date_2,
            'about'           => $pet_about,
            'cover_photo'     => $cover_photo,
            'profile_picture' => $profile_picture,
            'gallery'         => $pet_gallery ?? null,
        );

        $pet_profile_id = isset( $_POST['pet_profile_id'] ) ? intval( $_POST['pet_profile_id'] ) : 0;
        if ( $pet_profile_id ) {
            $updated = $wpdb->update( $table_name, $data, array( 'id' => $pet_profile_id ), array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ), array( '%d' ) );

            if ( $updated === false ) return;

        } else {
            $inserted = $wpdb->insert( $table_name, $data, array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
            if ( $inserted === false )  return;
            $pet_profile_id = $wpdb->insert_id;
        }

        $pet_profile = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $pet_profile_id ) );

        if ( $pet_profile ) {
            include $this->plugin_path . 'templates/pet-profile/view-user-pet.php';
        }
        exit;
    }

    // Fetch data from your custom database table using $pet_profile_id
    // global $wpdb;
    // $table_name = $wpdb->prefix . 'giopio_pet_profile';
    // $pet_profile_id = get_query_var( 'pet_profile_id' );

    // // Query to fetch pet profile details based on the numeric ID
    // $query       = $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $pet_profile_id );
    // $pet_profile = $wpdb->get_row( $query );


    // if ( $pet_profile->name ) {
    //     include $this->plugin_path . 'templates/pet-profile/view-user-pet.php';
    // } else {
    //     include $this->plugin_path . 'templates/pet-profile/user-pet-profile.php';
    // }

?>
