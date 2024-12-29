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

    public function show_all_pet_profile() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'giopio_pet_profile';

        $search_query = '';

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = sanitize_text_field($_GET['search']);
            $search_query = $wpdb->prepare(
                "WHERE name LIKE %s OR location LIKE %s OR mobile LIKE %s OR owner_name LIKE %s",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }

        // Get the results based on the search query
        $results = $wpdb->get_results("SELECT * FROM $table_name $search_query ORDER BY created_at DESC", ARRAY_A);

        // Bulk action handling
        if (isset($_POST['bulk_action']) && isset($_POST['pet_profiles'])) {
            $action = $_POST['bulk_action'];
            $pet_profiles = $_POST['pet_profiles'];

            if ($action == 'trash') {
                // Perform bulk delete
                foreach ($pet_profiles as $profile_id) {
                    $wpdb->delete($table_name, ['id' => $profile_id]);
                }
                wp_redirect(admin_url('admin.php?page=pet_profile&search=' . urlencode($search) . '&deleted=true'));
                exit;
                
            }
        }

        if (isset($_GET['deleted']) && $_GET['deleted'] === 'true') {
            echo '<div class="updated"><p>Selected pet profiles deleted successfully.</p></div>';
        }

        // Start building the HTML content
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Pet Profiles</h1>

            <!-- Search Form -->
            <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" class="search-form">
                <input type="hidden" name="page" value="pet_profile" /> <!-- Ensure the page stays the same -->
                <input type="text" name="search" value="<?php echo isset($_GET['search']) ? sanitize_text_field($_GET['search']) : ''; ?>" placeholder="Search by name or location" />
                <input type="submit" value="Search" class="button" />
            </form>


            <!-- Bulk Action Form -->
            <form method="POST" action="" id="bulk-action-form">
                <select name="bulk_action" class="bulk-action-selector" class="postform">
                    <option value="">Bulk Actions</option>
                    <option value="trash">Move to Trash</option>
                </select>

                <input type="hidden" name="action" value="giopio_pet_profile_bulk_action" />
                <input type="submit" value="Apply" class="button action" />

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th class="manage-column check-column"><input type="checkbox" class="select_all" /></th>
                            <th class="manage-column">QR-Code</th>
                            <th class="manage-column">Pet Name</th>
                            <th class="manage-column">Age</th>
                            <th class="manage-column">Gender</th>
                            <th class="manage-column">About</th>
                            <th class="manage-column">Owner Name</th>
                            <th class="manage-column">Mobile</th>
                            <th class="manage-column">Location</th>
                            <th class="manage-column">Facebook</th>
                            <th class="manage-column">Whatsapp ID</th>
                            <th class="manage-column">Vaccine Name</th>
                            <th class="manage-column">Vaccine Date</th>
                            <th class="manage-column">Vaccine Name 2</th>
                            <th class="manage-column">Vaccine Date 2</th>
                            <th class="manage-column">Gallery</th>
                            <th class="manage-column">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($results)) {
                            foreach ($results as $profile) {
                                ?>
                                <tr>
                                    <td><input type="checkbox" name="pet_profiles[]" value="<?php echo esc_attr($profile['id']); ?>" /></td>
                                    <td><?php echo esc_html($profile['id']); ?></td>
                                    <td><?php echo esc_html($profile['name']); ?></td>
                                    <td><?php echo esc_html($profile['age']); ?></td>
                                    <td><?php echo esc_html($profile['gender']); ?></td>
                                    <td><?php echo isset($profile['about']) ? esc_html(wp_trim_words($profile['about'], 10, '...')) : 'N/A'; ?></td>
                                    <td><?php echo esc_html($profile['owner_name']); ?></td>
                                    <td><?php echo esc_html($profile['mobile']); ?></td>
                                    <td><?php echo esc_html($profile['location']); ?></td>
                                    <td><a target="_blank" href="http://<?php echo esc_url($profile['facebook']); ?>"><?php echo esc_html($profile['facebook']); ?></a></td>
                                    <td><a target="_blank" href="http://<?php echo esc_url($profile['whatsapp_id']); ?>"><?php echo esc_html($profile['whatsapp_id']); ?></a></td>
                                    <td><?php echo esc_html($profile['vaccine_name']); ?></td>
                                    <td><?php echo esc_html($profile['vaccine_date']); ?></td>
                                    <td><?php echo esc_html($profile['vaccine_name_2']); ?></td>
                                    <td><?php echo esc_html($profile['vaccine_date_2']); ?></td>
                                    <td><?php echo $this->get_gallery_column($profile['gallery']); ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('admin.php?page=edit_pet_profile&id=' . $profile['id']); ?>">Edit</a> |
                                        <a href="<?php echo admin_url('admin-post.php?action=download_pet_pdf&id=' . $profile['id']); ?>" target="_blank">Download PDF</a>
                                    </td>

                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr><td colspan="14">No pet profiles found.</td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th class="manage-column check-column"><input type="checkbox" class="select_all" /></th>
                        <th class="manage-column">QR-Code</th>
                        <th class="manage-column">Pet Name</th>
                        <th class="manage-column">Age</th>
                        <th class="manage-column">Gender</th>
                        <th class="manage-column">About</th>
                        <th class="manage-column">Owner Name</th>
                        <th class="manage-column">Mobile</th>
                        <th class="manage-column">Location</th>
                        <th class="manage-column">Facebook</th>
                        <th class="manage-column">Whatsapp ID</th>
                        <th class="manage-column">Vaccine Name</th>
                        <th class="manage-column">Vaccine Date</th>
                        <th class="manage-column">Vaccine Name 2</th>
                        <th class="manage-column">Vaccine Date 2</th>
                        <th class="manage-column">Gallery</th>
                        <th class="manage-column">Action</th>
                    </tr>
                </tfoot>
                </table>
            </form>
        </div>

        <?php
    }


    public function edit_pet_profile() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'giopio_pet_profile';
        $profile_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if (!$profile_id) {
            echo '<div class="error"><p>Invalid pet profile ID.</p></div>';
            return;
        }

        // Get the existing pet profile data
        $profile = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $profile_id), ARRAY_A);

        if (!$profile) {
            echo '<div class="error"><p>Pet profile not found.</p></div>';
            return;
        }

        // If the form is submitted, update the pet profile
        if (isset($_POST['update_pet_profile'])) {
            // Sanitize and update the profile data
            $name = sanitize_text_field($_POST['name']);
            $age = sanitize_text_field($_POST['age']);
            $gender = sanitize_text_field($_POST['gender']);
            $about = sanitize_textarea_field($_POST['about']);
            $owner_name = sanitize_text_field($_POST['owner_name']);
            $mobile = sanitize_text_field($_POST['mobile']);
            $location = sanitize_text_field($_POST['location']);
            $facebook = sanitize_text_field($_POST['facebook']);
            $whatsapp_id = sanitize_text_field($_POST['whatsapp_id']);
            $vaccine_name = sanitize_text_field($_POST['vaccine_name']);
            $vaccine_date = sanitize_text_field($_POST['vaccine_date']);
            $vaccine_name_2 = sanitize_text_field($_POST['vaccine_name_2']);
            $vaccine_date_2 = sanitize_text_field($_POST['vaccine_date_2']);
            $gallery = isset($_POST['gallery']) ? json_encode($_POST['gallery']) : '';

            // Update the pet profile
            $wpdb->update(
                $table_name,
                [
                    'name' => $name,
                    'age' => $age,
                    'gender' => $gender,
                    'about' => $about,
                    'owner_name' => $owner_name,
                    'mobile' => $mobile,
                    'location' => $location,
                    'facebook' => $facebook,
                    'whatsapp_id' => $whatsapp_id,
                    'vaccine_name' => $vaccine_name,
                    'vaccine_date' => $vaccine_date,
                    'vaccine_name_2' => $vaccine_name_2,
                    'vaccine_date_2' => $vaccine_date_2,
                    'gallery' => $gallery
                ],
                ['id' => $profile_id]
            );

            echo '<div class="updated"><p>Pet profile updated successfully.</p></div>';
            // Redirect back to the listing page
            wp_redirect(admin_url('admin.php?page=pet_profile'));
            exit;
        }

        // Display the edit form with pre-filled data
        ?>
        <div class="wrap">
            <h1>Edit Pet Profile</h1>
            <form method="POST">
                <table class="form-table">
                    <tr>
                        <th><label for="name">Pet Name</label></th>
                        <td><input type="text" id="name" name="name" value="<?php echo esc_attr($profile['name']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="age">Age</label></th>
                        <td><input type="text" id="age" name="age" value="<?php echo esc_attr($profile['age']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="gender">Gender</label></th>
                        <td><input type="text" id="gender" name="gender" value="<?php echo esc_attr($profile['gender']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="about">About</label></th>
                        <td><textarea id="about" name="about" class="regular-text"><?php echo esc_textarea($profile['about']); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="owner_name">Owner Name</label></th>
                        <td><input type="text" id="owner_name" name="owner_name" value="<?php echo esc_attr($profile['owner_name']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="mobile">Mobile</label></th>
                        <td><input type="text" id="mobile" name="mobile" value="<?php echo esc_attr($profile['mobile']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="location">Location</label></th>
                        <td><input type="text" id="location" name="location" value="<?php echo esc_attr($profile['location']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="facebook">Facebook</label></th>
                        <td><input type="text" id="facebook" name="facebook" value="<?php echo esc_attr($profile['facebook']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="whatsapp_id">Whatsapp ID</label></th>
                        <td><input type="text" id="whatsapp_id" name="whatsapp_id" value="<?php echo esc_attr($profile['whatsapp_id']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="vaccine_name">Vaccine Name</label></th>
                        <td><input type="text" id="vaccine_name" name="vaccine_name" value="<?php echo esc_attr($profile['vaccine_name']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="vaccine_date">Vaccine Date</label></th>
                        <td><input type="date" id="vaccine_date" name="vaccine_date" value="<?php echo esc_attr($profile['vaccine_date']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="vaccine_name_2">Vaccine Name 2</label></th>
                        <td><input type="text" id="vaccine_name_2" name="vaccine_name_2" value="<?php echo esc_attr($profile['vaccine_name_2']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="vaccine_date_2">Vaccine Date 2</label></th>
                        <td><input type="date" id="vaccine_date_2" name="vaccine_date_2" value="<?php echo esc_attr($profile['vaccine_date_2']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="gallery">Gallery (JSON format)</label></th>
                        <td><textarea id="gallery" name="gallery" class="regular-text"><?php echo esc_textarea($profile['gallery']); ?></textarea></td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="update_pet_profile" id="submit" class="button button-primary" value="Update Profile">
                </p>
            </form>
        </div>
        <?php
    }


    // Helper function to generate image columns
    private function get_image_column( $image_url, $alt_text ) {
        if ( $image_url ) {
            ?>
            <td><img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $alt_text ); ?>" width="100" /></td>
            <?php
        } else {
                ?>
                <td>No <?php echo esc_html( $alt_text ); ?></td>
                <?php
        }
    }

    // Helper function to generate text columns
    private function get_text_column( $text ) {
        ?>
        <?php echo $text ? esc_html( $text ) : 'N/A'; ?>
        <?php
    }

    // Helper function to generate gallery column
    private function get_gallery_column( $gallery_data ) {

        if ( $gallery_data ) {
            $gallery = maybe_unserialize( $gallery_data );
            if ( is_array( $gallery ) ) {
                foreach ( $gallery as $image ) {
                    ?>
                    <img src="<?php echo esc_url( $image ); ?>" alt="Gallery Image" width="50" />
                    <?php
            }
            } else {
                echo 'No Gallery Images';
            }
        } else {
            echo 'No Gallery';
        }

    }
}
