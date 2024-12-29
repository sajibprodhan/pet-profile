<?php

namespace Inc\Base;

use Inc\Api\Settings_Api;
use Inc\Base\Base_Controller;
use Inc\Traits\MpdfConfig;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;

class Pet_Profile_Controller extends Base_Controller {
    use MpdfConfig;

    public $settings;
    public $pages = [];
    public $subpages = [];
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'giopio_pet_profile';
    }

    public function register() {
        $this->settings = new Settings_Api();
        $this->set_pages();

        add_action('wp_ajax_create_pet_profiles', [$this, 'create_pet_profiles']);
        add_action('wp_ajax_nopriv_create_pet_profiles', [$this, 'create_pet_profiles']);

        add_action('admin_post_download_pet', [$this, 'download_pet_profile_pdf']);
        add_action('admin_post_nopriv_download_pet', [$this, 'download_pet_profile_pdf']);

        $this->settings->add_pages($this->pages)->register();
    }

    private function set_pages() {
        $this->pages = [
            [
                'page_title' => __('Pet Profile', 'pet-profile'),
                'menu_title' => __('Pet Profile', 'pet-profile'),
                'capability' => 'manage_options',
                'menu_slug'  => 'pet_profile',
                'callback'   => [$this, 'show_all_pet_profile'],
                'icon_url'   => 'dashicons-pets',
                'position'   => 110,
            ],
        ];
    }

    public function show_all_pet_profile() {
        $action = $_GET['action'] ?? 'list';
        $profile_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        switch ($action) {
            case 'edit':
                $this->edit_pet_profile($profile_id);
                break;
            case 'delete':
                $this->delete_pet_profile($profile_id);
                break;
            default:
                $this->list_pet_profiles();
                break;
        }
    }

    private function list_pet_profiles() {
        global $wpdb;
        $search_query = $this->get_search_query();
        $results = $wpdb->get_results("SELECT * FROM {$this->table_name} $search_query ORDER BY created_at DESC", ARRAY_A);

        $this->handle_bulk_action($results);
        include $this->plugin_path . "/templates/pet-profile/index.php";
    }

    private function edit_pet_profile($profile_id) {
        global $wpdb;

        $profile = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $profile_id), ARRAY_A);

        if (!$profile) {
            echo '<div class="error"><p>Pet profile not found.</p></div>';
            return;
        }

        if (isset($_POST['update_pet_profile']) && check_admin_referer('update_pet_profile')) {
            $data = [
                'name'           => sanitize_text_field($_POST['name']),
                'age'            => sanitize_text_field($_POST['age']),
                'gender'         => sanitize_text_field($_POST['gender']),
                'about'          => sanitize_textarea_field($_POST['about']),
                'owner_name'     => sanitize_text_field($_POST['owner_name']),
                'mobile'         => sanitize_text_field($_POST['mobile']),
                'location'       => sanitize_text_field($_POST['location']),
                'facebook'       => esc_url($_POST['facebook']),
                'whatsapp_id'    => esc_url($_POST['whatsapp_id']),
                'vaccine_name'   => sanitize_text_field($_POST['vaccine_name']),
                'vaccine_date'   => sanitize_text_field($_POST['vaccine_date']),
                'vaccine_name_2' => sanitize_text_field($_POST['vaccine_name_2']),
                'vaccine_date_2' => sanitize_text_field($_POST['vaccine_date_2']),
                'gallery'        => isset($_POST['gallery']) ? json_encode(array_map('sanitize_text_field', $_POST['gallery'])) : '',
            ];

            $wpdb->update($this->table_name, $data, ['id' => $profile_id]);
            wp_redirect(admin_url('admin.php?page=pet_profile'));
            exit;
        }

        include $this->plugin_path . '/templates/pet-profile/edit.php';
    }

    private function delete_pet_profile($profile_id) {
        global $wpdb;
        $wpdb->delete($this->table_name, ['id' => $profile_id]);
        wp_redirect(admin_url('admin.php?page=pet_profile&deleted=true'));
        exit;
    }

    private function get_search_query() {
        global $wpdb;
        if (!empty($_GET['search'])) {
            $search = sanitize_text_field($_GET['search']);
            return $wpdb->prepare(
                "WHERE name LIKE %s OR location LIKE %s OR mobile LIKE %s OR owner_name LIKE %s",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }
        return '';
    }

    private function handle_bulk_action($results) {
        if (!isset($_POST['bulk_action'], $_POST['pet_profiles']) || empty($_POST['pet_profiles'])) {
            return;
        }

        $action = sanitize_text_field($_POST['bulk_action']);
        $pet_profiles = array_map('intval', $_POST['pet_profiles']);
        global $wpdb;

        if ($action === 'download') {
            $mpdf = $this->getMpdfConfig();
            $output = new Output\Html();

            ob_start();
            foreach ($pet_profiles as $pet_id) {
                $url = site_url("/?pet_id=" . $pet_id);
                $qrCode = new QrCode($url);
                echo $output->output($qrCode, 4);
            }
            $html = ob_get_clean();
            $mpdf->WriteHTML($html);
            $mpdf->Output('pet_qr_codes.pdf', 'I');
        }

        if ($action === 'trash') {
            foreach ($pet_profiles as $profile_id) {
                $wpdb->delete($this->table_name, ['id' => $profile_id]);
            }
            wp_redirect(admin_url('admin.php?page=pet_profile&deleted=true'));
            exit;
        }
    }

    public function create_pet_profiles() {
        check_ajax_referer('ajax_nonce', 'nonce');

        $pet_count = isset($_POST['pet_count']) ? intval($_POST['pet_count']) : 0;
        if ($pet_count <= 0) {
            wp_send_json_error('Invalid pet count');
        }

        global $wpdb;
        for ($i = 0; $i < $pet_count; $i++) {
            $wpdb->insert($this->table_name, [
                'name' => NULL,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]);
        }

        wp_send_json_success();
    }
}
