<?php

namespace Inc\Base;

use Inc\Api\Settings_Api;
use Inc\Base\Base_Controller;
use Inc\Services\Pet_Profile_Service;
use Inc\Traits\MpdfConfig;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;

class Pet_Profile_Controller extends Base_Controller {
    use MpdfConfig;

    private $pet_service;
    public $settings;
    public $pages = [];
    public $subpages = [];

    public function register() {
        $this->settings = new Settings_Api();
        $this->pet_service = new Pet_Profile_Service();
        $this->set_pages();
        $this->set_sub_pages();

        add_action('wp_ajax_create_pet_profiles', [$this, 'create_pet_profiles']);
        add_action('wp_ajax_nopriv_create_pet_profiles', [$this, 'create_pet_profiles']);

        add_action('admin_post_download_pet', [$this, 'download_pet_profile_pdf']);
        add_action('admin_post_nopriv_download_pet', [$this, 'download_pet_profile_pdf']);

        //$this->settings->add_pages($this->pages)->add_sub_pages($this->subpages)->register();
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

    private function set_sub_pages() {
        $this->subpages = [
            [
                'parent_slug' => 'pet_profile',
                'page_title' => __('Edit Pet Profile', 'pet-profile'),
                'menu_title' => __('Edit Pet Profile', 'pet-profile'),
                'capability' => 'manage_options',
                'menu_slug'  => 'pet_profile',
                'callback'   => [$this, 'show_all_pet_profile'],
            ]
        ];
    }

    protected function getPluginPath() {
        return $this->plugin_path;
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
        $search_query = $this->pet_service->generate_search_query($_GET['search'] ?? '');
        $results = $this->pet_service->get_all_profiles($search_query);
        $this->handle_bulk_action($results);
        include $this->plugin_path . "templates/pet-profile/index.php";
    }

    private function edit_pet_profile($profile_id) {
        $profile = $this->pet_service->get_profile($profile_id);

        if (!$profile) {
            echo '<div class="error"><p>Pet profile not found.</p></div>';
            return;
        }

        if (isset($_POST['update_pet_profile']) && check_admin_referer('update_pet_profile')) {
            $this->pet_service->update_profile($profile_id, $_POST);
            wp_redirect(admin_url('admin.php?page=pet_profile'));
            exit;
        }

        include $this->plugin_path . 'templates/pet-profile/edit.php';
    }

    private function delete_pet_profile($profile_id) {
        $this->pet_service->delete_profile($profile_id);
        wp_redirect(admin_url('admin.php?page=pet_profile&deleted=true'));
        exit;
    }

    private function handle_bulk_action($results) {
        if (!isset($_POST['bulk_action'], $_POST['pet_profiles']) || empty($_POST['pet_profiles'])) {
            return;
        }

        $action = sanitize_text_field($_POST['bulk_action']);
        $pet_profiles = array_map('intval', $_POST['pet_profiles']);

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
            $this->pet_service->delete_bulk_profiles($pet_profiles);
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

        $this->pet_service->create_pet_profiles($pet_count);
        wp_send_json_success();
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
