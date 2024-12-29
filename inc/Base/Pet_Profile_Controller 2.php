<?php

namespace Inc\Base;

use Inc\Api\Settings_Api;
use Inc\Base\Base_Controller;
use Inc\Traits\MpdfConfig;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use function current_time;

class Pet_Profile_Controller extends Base_Controller {

    use MpdfConfig;

    public $settings;
    public $callbacks_mngr;
    public $pages    = [];
    public $subpages = [];

    public function register() {
        $this->settings = new Settings_Api();
        $this->set_pages();
        add_action( 'wp_ajax_create_pet_profiles', [$this, 'create_pet_profiles'] );
        add_action( 'wp_ajax_nopriv_create_pet_profiles', [$this, 'create_pet_profiles'] );

        add_action( 'admin_post_download_pet', [$this, 'download_pet_profile_pdf'] );
        add_action( 'admin_post_nopriv_download_pet', [$this, 'download_pet_profile_pdf'] );

        $this->settings->add_pages( $this->pages )->register();
    }

    protected function getPluginPath() {
        return $this->plugin_path;
    }

    private function set_pages() {
        $this->pages = [
            [
                'page_title' => __( 'Pet Profile', 'pet-profile' ),
                'menu_title' => __( 'Pet Profile', 'pet-profile' ),
                'capability' => 'manage_options',
                'menu_slug'  => 'pet_profile',
                'callback'   => [$this, 'show_all_pet_profile'],
                'icon_url'   => 'dashicons-pets',
                'position'   => 110,
            ],
        ];
    }
    /**
     * Handles the display of the pet profile listing page.
     */
    public function show_all_pet_profile() {

        if ( isset( $_GET['pet_id'] ) && !empty( $_GET['pet_id'] ) ) {
            $profile_id = isset( $_GET['pet_id'] ) ? (int) $_GET['pet_id'] : 0;
            echo "done";
            return;
        }

        if ( isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) {
            $profile_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
            $this->edit_pet_profile( $profile_id );
            return;
        }

        global $wpdb;
        $table_name   = $wpdb->prefix . 'giopio_pet_profile';
        $search_query = $this->get_search_query();

        // Get the results based on the search query
        $results = $wpdb->get_results( "SELECT * FROM $table_name $search_query ORDER BY created_at DESC", ARRAY_A );

        // Handle bulk actions
        $this->handle_bulk_action( $results );

        // Display results
        include $this->plugin_path . "/templates/pet-profile/index.php";

    }

    /**
     * Handle the edit pet profile form submission and display the edit form.
     *
     * @since 1.0.0
     */
    public function edit_pet_profile( $profile_id ) {

        global $wpdb;

        $table_name = $wpdb->prefix . 'giopio_pet_profile';
        $profile_id = (int) $profile_id;

        if ( !$profile_id ) {
            return;
        }

        // Get the existing pet profile data
        $profile = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $profile_id ), ARRAY_A );

        if ( !$profile ) {
            echo '<div class="error"><p>Pet profile not found.</p></div>';
            return;
        }

        // If the form is submitted, update the pet profile
        if ( isset( $_POST['update_pet_profile'] ) ) {

            // Sanitize and update the profile data
            $data = [
                'name'           => sanitize_text_field( $_POST['name'] ),
                'age'            => sanitize_text_field( $_POST['age'] ),
                'gender'         => sanitize_text_field( $_POST['gender'] ),
                'about'          => sanitize_textarea_field( $_POST['about'] ),
                'owner_name'     => sanitize_text_field( $_POST['owner_name'] ),
                'mobile'         => sanitize_text_field( $_POST['mobile'] ),
                'location'       => sanitize_text_field( $_POST['location'] ),
                'facebook'       => esc_url( $_POST['facebook'] ),
                'whatsapp_id'    => esc_url( $_POST['whatsapp_id'] ),
                'vaccine_name'   => sanitize_text_field( $_POST['vaccine_name'] ),
                'vaccine_date'   => sanitize_text_field( $_POST['vaccine_date'] ),
                'vaccine_name_2' => sanitize_text_field( $_POST['vaccine_name_2'] ),
                'vaccine_date_2' => sanitize_text_field( $_POST['vaccine_date_2'] ),
                'gallery'        => isset( $_POST['gallery'] ) ? json_encode( array_map( 'sanitize_text_field', $_POST['gallery'] ) ) : '',
            ];

            $wpdb->update( $table_name, $data, ['id' => $profile_id] );

            echo '<div class="updated"><p>Pet profile updated successfully.</p></div>';
            // Redirect back to the listing page
            wp_redirect( admin_url( 'admin.php?page=pet_profile' ) );
            exit;
        }

        // Display the edit form with pre-filled data
        include $this->plugin_path . '/templates/pet-profile/edit.php';

    }

    /**
     * Constructs a SQL query fragment for searching pet profiles by name, location, mobile, or owner's name.
     */

    private function get_search_query() {
        global $wpdb;
        if ( !empty( $_GET['search'] ) ) {
            $search = sanitize_text_field( $_GET['search'] );
            return $wpdb->prepare(
                "WHERE name LIKE %s OR location LIKE %s OR mobile LIKE %s OR owner_name LIKE %s",
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%'
            );
        }
        return '';
    }

    /**
     * Handles bulk actions for pet profiles, such as deleting selected profiles.
     * @param array $results The results of the pet profiles query, not directly used in this function.
     *
     */

    private function handle_bulk_action( $results ) {

        $mpdf = $this->getMpdfConfig();
        if ( isset( $_POST['bulk_action'], $_POST['pet_profiles'] ) && !empty( $_POST['pet_profiles'] ) ) {
            $action       = $_POST['bulk_action'];
            $pet_profiles = $_POST['pet_profiles'];
            global $wpdb;

            if ( empty( $pet_profiles ) ) {
                return;
            }

            if ( $action === 'download' ) {
                ob_start();

                $output = new Output\Html();
                foreach ( $pet_profiles as $pet_id ) {
                    $url = site_url( "/?pet_id=" . $pet_id );
                    $qrCode = new QrCode( $url );
                    echo $output->output( $qrCode, 4 );
                }

                $html = ob_get_clean();
                $mpdf->WriteHTML( $html );
                $mpdf->Output( 'pet_qr_codes.pdf', 'I' );
            }

            if ( $action === 'trash' ) {
                foreach ( $pet_profiles as $profile_id ) {
                    $wpdb->delete( $wpdb->prefix . 'giopio_pet_profile', ['id' => $profile_id] );
                }
                wp_redirect( admin_url( 'admin.php?page=pet_profile&search=' . urlencode( $_GET['search'] ?? '' ) . '&deleted=true' ) );
                exit;
            }
        }

        if ( isset( $_GET['deleted'] ) && $_GET['deleted'] === 'true' ) {
            echo '<div class="updated"><p>Selected pet profiles deleted successfully.</p></div>';
        }
    }

    /**
     * Generates HTML for a gallery column using provided gallery data.
     *
     * @param mixed $gallery_data Serialized gallery data, expected to be an array of image URLs after unserialization.
     *
     * Outputs HTML <img> tags for each image in the gallery or a message indicating no images or gallery found.
     */
    private function get_gallery_column( $gallery_data ) {
        if ( $gallery_data ) {
            $gallery = maybe_unserialize( $gallery_data );
            if ( is_array( $gallery ) ) {
                foreach ( $gallery as $image ) {
                    echo '<img src="' . esc_url( $image ) . '" alt="Gallery Image" width="50" />';
                }
            }
        }
    }

    /**
     * Handles the download of a pet profile PDF
     *
     * @return void
     */
    public function download_pet_profile_pdf( $pet_id = null ) {
        if ( !$pet_id && isset( $_GET['id'] ) ) {
            $pet_id = (int) $_GET['id'];
        }

        if ( $pet_id ) {
            $this->generate_pdf( $pet_id );
        }
    }

    /**
     * Handles the creation of blank pet profiles via an AJAX request.
     * @return void
     */

    function create_pet_profiles() {

        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'ajax_nonce' ) ) {
            wp_send_json_error();
            return;
        }

        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'ajax_nonce' ) || empty( $_POST['pet_count'] ) || !is_numeric( $_POST['pet_count'] ) || $_POST['pet_count'] <= 0 ) {
            wp_send_json_error();
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'giopio_pet_profile';
        $pet_count  = (int) $_POST['pet_count'];

        for ( $i = 0; $i < $pet_count; $i++ ) {
            $wpdb->insert( $table_name, [
                'user_id'        => NULL,
                'name'           => NULL,
                'age'            => NULL,
                'gender'         => NULL,
                'about'          => NULL,
                'owner_name'     => NULL,
                'mobile'         => NULL,
                'location'       => NULL,
                'facebook'       => NULL,
                'whatsapp_id'    => NULL,
                'vaccine_name'   => NULL,
                'vaccine_date'   => NULL,
                'vaccine_name_2' => NULL,
                'vaccine_date_2' => NULL,
                'gallery'        => json_encode([]),
                'created_at'     => current_time('mysql'),
                'updated_at'     => current_time( 'mysql' ),
            ] );
        }

        wp_send_json_success();
    }

    /**
     * Generates a PDF for a specific pet profile.
     * @param int $pet_id The ID of the pet profile to generate the PDF for.
     * @return void
     */

    private function generate_pdf( $pet_id ) {
        $url    = site_url( "/pet-profile-edit/?pet_id=" . $pet_id );
        $qrCode = new QrCode( $url );
        $output = new Output\Html();
        $html   = $output->output( $qrCode, 4 );
        $mpdf   = $this->getMpdfConfig();
        $mpdf->WriteHTML( $html );
        $mpdf->Output( 'pet-qr-' . $pet_id . '.pdf', 'I' );
        exit;
    }




}
