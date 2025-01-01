<?php

namespace Inc\Base;

use Inc\Api\Settings_Api;
use Inc\Base\Base_Controller;
use Inc\Traits\MpdfConfig;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use function current_time;

use WP_Error;

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

        add_filter('query_vars', [$this,'custom_pet_profile_query_vars']);
        add_action('init', [$this,'custom_pet_profile_rewrite_rule']);
        add_action('template_redirect', [$this,'custom_pet_profile_template']);

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

        if ( isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) {
            $profile_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
            $this->edit_pet_profile( $profile_id );
            return;
        }

        global $wpdb;
        $table_name   = $wpdb->prefix . 'giopio_pet_profile';
        $search_query = $this->get_search_query();

        $results = $wpdb->get_results( "SELECT * FROM $table_name $search_query ORDER BY created_at DESC", ARRAY_A );

        $this->handle_bulk_action( $results );

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

        $profile = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $profile_id ), ARRAY_A );

        if ( !$profile ) {
            echo '<div class="error"><p>Pet profile not found.</p></div>';
            return;
        }

        if ( isset( $_POST['update_pet_profile'] ) ) {
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
            ];

            $data['user_id'] = ( isset( $_POST['user_id'] ) && !empty( $_POST['user_id'] ) && $_POST['user_id'] != 0 ) ? (int) $_POST['user_id']
: NULL;
            $wpdb->update( $table_name, $data, ['id' => $profile_id] );

            echo '<div class="updated"><p>Pet profile updated successfully.</p></div>';
            wp_redirect( admin_url( 'admin.php?page=pet_profile' ) );
            exit;
        }

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
                    $url    = site_url( "/pet-profile/{$pet_id}" );
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

        $pet_gallery = isset($_POST['gallery']) ? $_POST['gallery'] : null;
        $gallery_value = empty($pet_gallery) ? null : json_encode($pet_gallery);

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
                'gallery'        => $gallery_value,
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
        $url    = site_url( "/pet-profile/{$pet_id}" );
        $qrCode = new QrCode( $url );
        $output = new Output\Html();
        $html   = $output->output( $qrCode, 4 );
        $mpdf   = $this->getMpdfConfig();
        $mpdf->WriteHTML( $html );
        $mpdf->Output( 'pet-qr-' . $pet_id . '.pdf', 'I' );
        exit;
    }



    public function custom_pet_profile_rewrite_rule() {
        add_rewrite_rule(
            '^pet-profile/([0-9]+)/?$',
            'index.php?pet_profile_id=$matches[1]',
            'top'
        );
    }


    public function custom_pet_profile_query_vars($vars) {
        $vars[] = 'pet_profile_id';
        return $vars;
    }

    public function custom_pet_profile_template() {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $this->handle_pet_profile_submission();
            return;
        }

        $pet_profile_id = get_query_var('pet_profile_id');
        $pet_profile = $this->get_pet_profile($pet_profile_id);

        if (isset($pet_profile_id) && !empty($pet_profile_id)) {
            if (empty($pet_profile)) {
                $this->show_404();
            } else {
                $this->display_pet_profile($pet_profile);
            }
        }
    }

    private function handle_pet_profile_submission() {
        
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        // Sanitize form inputs
        $pet_profile_id = get_query_var('pet_profile_id');

        $profile = $this->get_pet_profile($pet_profile_id);


        $pet_vaccine_name = sanitize_text_field($_POST['pet_vaccine_name'] );
        $pet_vaccine_date = sanitize_text_field($_POST['pet_vaccine_date']);
        $pet_vaccine_name_2 = sanitize_text_field($_POST['pet_vaccine_name_2']) ;
        $pet_vaccine_date_2 = sanitize_text_field($_POST['pet_vaccine_date_2']);

        if (empty($pet_vaccine_name) || empty($pet_vaccine_date)) {
            $message =  'Vaccine name and date are required';
            wp_redirect( site_url("pet-profile/". $profile->id ."/?action=edit&message=$message") );
            exit;
        }

        if (empty($pet_vaccine_name_2) || empty($pet_vaccine_date_2)) {
            $message = 'Second vaccine name and date are required';
            wp_redirect( site_url("pet-profile/". $profile->id ."/?action=edit&messagetwo=$message") );
            exit;
        }

        $data = [
            'user_id'         => get_current_user_id(),
            'name'            => sanitize_text_field($_POST['pet_name'] ?: $profile->name),
            'age'             => intval($_POST['pet_age'] ?: $profile->age),
            'gender'          => sanitize_text_field($_POST['pet_gender'] ?: $profile->gender),
            'owner_name'      => sanitize_text_field($_POST['pet_owner_name'] ?: $profile->owner_name),
            'mobile'          => sanitize_text_field($_POST['pet_mobile'] ?: $profile->mobile),
            'location'        => sanitize_text_field($_POST['pet_location'] ?: $profile->location),
            'facebook'        => sanitize_text_field($_POST['pet_facebook'] ?: $profile->facebook),
            'whatsapp_id'     => sanitize_text_field($_POST['pet_whatsapp'] ?: $profile->whatsapp_id),
            'vaccine_name'    => $pet_vaccine_name ?: $profile->vaccine_name,
            'vaccine_date'    => $pet_vaccine_date ?: $profile->vaccine_date,
            'vaccine_name_2'  => $pet_vaccine_name_2 ?: $profile->vaccine_name_2,
            'vaccine_date_2'  => $pet_vaccine_date_2 ?: $profile->vaccine_date_2,
            'about'           => sanitize_textarea_field($_POST['pet_about'] ?: $profile->about),
            'cover_photo'     => $this->handle_file_upload('cover_photo', $pet_profile_id),
            'profile_picture' => $this->handle_file_upload('profile_picture', $pet_profile_id),
            'gallery'         => $this->handle_file_uploads('pet_gallery', $pet_profile_id),
        ];

        // Insert or update the pet profile
        global $wpdb;
        $table_name = $wpdb->prefix . 'giopio_pet_profile';
        
        if (isset($pet_profile_id) && $pet_profile_id) {
            $this->update_pet_profile($table_name, $data , $pet_profile_id);
        } else {
            $pet_profile_id = $this->insert_pet_profile($table_name, $data,);
        }

        // Redirect to the pet profile view page
        wp_redirect(home_url("/pet-profile/{$pet_profile_id}"));
        exit;
    }

    private function handle_file_uploads($field_name, $pet_profile_id = null) {
        if (isset($_FILES[$field_name]) && !empty($_FILES[$field_name]['name'][0])) {
            $uploaded_files = [];
            foreach ($_FILES[$field_name]['name'] as $index => $file_name) {
                $file = [
                    'name'     => $file_name,
                    'type'     => $_FILES[$field_name]['type'][$index],
                    'tmp_name' => $_FILES[$field_name]['tmp_name'][$index],
                    'error'    => $_FILES[$field_name]['error'][$index],
                    'size'     => $_FILES[$field_name]['size'][$index],
                ];
                $upload = wp_handle_upload($file, ['test_form' => false]);
                if (!isset($upload['error']) && isset($upload['url'])) {
                    $uploaded_files[] = $upload['url']; 
                }
            }

            return implode(',', $uploaded_files);
        }

        if ($pet_profile_id) {
            return $this->get_existing_gallery($pet_profile_id);
        }

        return '';
    }


    private function get_existing_gallery($pet_profile_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'giopio_pet_profile';
        $result = $wpdb->get_var($wpdb->prepare("SELECT gallery FROM {$table_name} WHERE id = %d", $pet_profile_id));
        return $result ? $result : '';
    }

    private function handle_file_upload($field_name, $pet_profile_id = null) {
        if (isset($_FILES[$field_name]) && !empty($_FILES[$field_name]['name'])) {
            $file = [
                'name'     => $_FILES[$field_name]['name'],
                'type'     => $_FILES[$field_name]['type'],
                'tmp_name' => $_FILES[$field_name]['tmp_name'],
                'error'    => $_FILES[$field_name]['error'],
                'size'     => $_FILES[$field_name]['size'],
            ];
            $upload = wp_handle_upload($file, ['test_form' => false]);
            if (!isset($upload['error']) && isset($upload['url'])) {
                return $upload['url']; // New image uploaded, return the new URL
            }
        }

        // If no new image is uploaded, return the existing image if updating
        if ($pet_profile_id) {
            return $this->get_existing_image($field_name, $pet_profile_id);
        }

        return ''; // Return empty if no image and no pet profile id
    }

    private function get_existing_image($field_name, $pet_profile_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'giopio_pet_profile';
        $result = $wpdb->get_var($wpdb->prepare("SELECT {$field_name} FROM {$table_name} WHERE id = %d", $pet_profile_id));

        return $result ? $result : '';
    }

    private function update_pet_profile($table_name, $data, $pet_profile_id) {
        global $wpdb;
        $updated = $wpdb->update(
            $table_name,
            $data,
            ['id' => $pet_profile_id],
            array_fill(0, count($data), '%s'), 
            ['%d']
        );
        if ($updated === false) return;
    }

    private function insert_pet_profile($table_name, $data) {
        global $wpdb;
        $inserted = $wpdb->insert(
            $table_name,
            $data,
            array_fill(0, count($data), '%s')
        );
        
        if ($inserted === false) return;

        return $wpdb->insert_id;
    }

    private function get_pet_profile($pet_profile_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'giopio_pet_profile';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $pet_profile_id));
    }

    private function show_404() {
        status_header(404);
        nocache_headers();
        get_header();
        include $this->plugin_path . 'templates/pet-profile/404.php';
        get_footer();
        exit;
    }

    private function display_pet_profile($pet_profile) {
        get_header();
        if ($pet_profile->name) {
            include $this->plugin_path . 'templates/pet-profile/view-user-pet.php';
        } else {
            include $this->plugin_path . 'templates/pet-profile/user-pet-profile.php';
        }
        get_footer();
        exit;
    }





}
