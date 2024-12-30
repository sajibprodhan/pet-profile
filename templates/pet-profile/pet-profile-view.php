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
        $pet_gallery = implode( ',', $uploaded_files );

        // Handle file uploads for cover_photo and profile_picture
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

        // Get the pet_profile_id if available
        

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
            'gallery'         => $pet_gallery,
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
            ?>
            <div class="pet-profile">
                <h1>Pet Profile: <?php echo esc_html( $pet_profile->name ); ?></h1>
                <p><strong>Age:</strong> <?php echo esc_html( $pet_profile->age ); ?></p>
                <p><strong>Gender:</strong> <?php echo esc_html( $pet_profile->gender ); ?></p>
                <p><strong>Owner:</strong> <?php echo esc_html( $pet_profile->owner_name ); ?></p>
                <p><strong>Mobile:</strong> <?php echo esc_html( $pet_profile->mobile ); ?></p>
                <p><strong>Location:</strong> <?php echo esc_html( $pet_profile->location ); ?></p>
                <p><strong>Facebook:</strong> <?php echo esc_html( $pet_profile->facebook ); ?></p>
                <p><strong>WhatsApp ID:</strong> <?php echo esc_html( $pet_profile->whatsapp_id ); ?></p>
                <p><strong>Vaccine Name 1:</strong> <?php echo esc_html( $pet_profile->vaccine_name ); ?> (<?php echo esc_html( $pet_profile->vaccine_date ); ?>)</p>
                <p><strong>Vaccine Name 2:</strong> <?php echo esc_html( $pet_profile->vaccine_name_2 ); ?> (<?php echo esc_html( $pet_profile->vaccine_date_2 ); ?>)</p>
                <p><strong>About:</strong> <?php echo esc_html( $pet_profile->about ); ?></p>
                <p><strong>Gallery:</strong> <?php echo esc_html( $pet_profile->gallery ); ?></p>
                <!-- Add more fields as needed -->
            </div>
            <?php
        }
        exit;
    }

    // Fetch data from your custom database table using $pet_profile_id
    global $wpdb;
    $table_name = $wpdb->prefix . 'giopio_pet_profile';
    $pet_profile_id = get_query_var( 'pet_profile_id' );

    // Query to fetch pet profile details based on the numeric ID
    $query       = $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $pet_profile_id );
    $pet_profile = $wpdb->get_row( $query );

    // If the pet profile exists, display the pet profile
    if ( $pet_profile ) {
        ?>
        <div class="pet-profile">
            <h1>Pet Profile: <?php echo esc_html( $pet_profile->name ); ?></h1>
            <p><strong>Age:</strong> <?php echo esc_html( $pet_profile->age ); ?></p>
            <p><strong>Gender:</strong> <?php echo esc_html( $pet_profile->gender ); ?></p>
            <p><strong>Owner:</strong> <?php echo esc_html( $pet_profile->owner_name ); ?></p>
            <p><strong>Mobile:</strong> <?php echo esc_html( $pet_profile->mobile ); ?></p>
            <p><strong>Location:</strong> <?php echo esc_html( $pet_profile->location ); ?></p>
            <p><strong>Facebook:</strong> <?php echo esc_html( $pet_profile->facebook ); ?></p>
            <p><strong>WhatsApp ID:</strong> <?php echo esc_html( $pet_profile->whatsapp_id ); ?></p>
            <p><strong>Vaccine Name 1:</strong> <?php echo esc_html( $pet_profile->vaccine_name ); ?> (<?php echo esc_html( $pet_profile->vaccine_date ); ?>)</p>
            <p><strong>Vaccine Name 2:</strong> <?php echo esc_html( $pet_profile->vaccine_name_2 ); ?> (<?php echo esc_html( $pet_profile->vaccine_date_2 ); ?>)</p>
            <p><strong>About:</strong> <?php echo esc_html( $pet_profile->about ); ?></p>
            <p><strong>Gallery:</strong> <?php echo esc_html( $pet_profile->gallery ); ?></p>
            <!-- Add more fields as needed -->
        </div>
        <?php
    } else {
        // If the pet profile does not exist, display a form to create a new pet profile
        ?>
        <div class="pet-profile-form">
            <h1>Create a New Pet Profile</h1>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="pet_name">Pet Name</label>
                <input type="text" id="pet_name" name="pet_name" required>

                <label for="pet_age">Age</label>
                <input type="number" id="pet_age" name="pet_age" required>

                <label for="pet_gender">Gender</label>
                <select id="pet_gender" name="pet_gender">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>

                <label for="pet_owner_name">Owner Name</label>
                <input type="text" id="pet_owner_name" name="pet_owner_name" required>

                <label for="pet_mobile">Mobile</label>
                <input type="text" id="pet_mobile" name="pet_mobile" required>

                <label for="pet_location">Location</label>
                <input type="text" id="pet_location" name="pet_location" required>

                <label for="pet_facebook">Facebook</label>
                <input type="text" id="pet_facebook" name="pet_facebook">

                <label for="pet_whatsapp">WhatsApp ID</label>
                <input type="text" id="pet_whatsapp" name="pet_whatsapp">

                <label for="pet_vaccine_name">Vaccine Name 1</label>
                <input type="text" id="pet_vaccine_name" name="pet_vaccine_name" required>

                <label for="pet_vaccine_date">Vaccine Date 1</label>
                <input type="date" id="pet_vaccine_date" name="pet_vaccine_date" required>

                <label for="pet_vaccine_name_2">Vaccine Name 2</label>
                <input type="text" id="pet_vaccine_name_2" name="pet_vaccine_name_2">

                <label for="pet_vaccine_date_2">Vaccine Date 2</label>
                <input type="date" id="pet_vaccine_date_2" name="pet_vaccine_date_2">

                <label for="pet_about">About</label>
                <textarea id="pet_about" name="pet_about"></textarea>

                <label for="profile_picture">Profile picture</label>
                <input type="file" id="profile_picture" name="profile_picture">


                <label for="pet_cover_photo">Cover Photo URL</label>
                <input type="file" id="pet_cover_photo" name="pet_cover_photo">


                <label for="pet_gallery">Gallery</label>
                <input multiple type="file" id="pet_gallery" name="pet_gallery[]">

                <button type="submit">Submit</button>
            </form>
        </div>
        <?php
    }

get_footer();
?>
