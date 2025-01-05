<?php 
namespace Inc\Base;

use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Pet_Profile_Table extends \WP_List_Table {

    private $data;

    public function __construct( $data ) {
        parent::__construct( [
            'singular' => __( 'Pet Profile', 'pet-profile' ),
            'plural'   => __( 'Pet Profiles', 'pet-profile' ),
            'ajax'     => false,
        ] );
        $this->data = $data;
    }

    public function get_columns() {
        return [
            'cb'         => '<input type="checkbox" />',
            'qr_code'    => __( 'QR-Code', 'pet-profile' ),
            'name'       => __( 'Pet Name', 'pet-profile' ),
            'age'        => __( 'Age', 'pet-profile' ),
            'gender'     => __( 'Gender', 'pet-profile' ),
            'owner_name' => __( 'Owner Name', 'pet-profile' ),
            'mobile'     => __( 'Mobile', 'pet-profile' ),
            'location'   => __( 'Location', 'pet-profile' ),
            'facebook'   => __( 'Facebook', 'pet-profile' ),
            'actions'    => __( 'Actions', 'pet-profile' ),
        ];
    }

    public function get_bulk_actions() {
        return [
            'delete'   => __( 'Delete', 'pet-profile' ),
            'activate' => __( 'Activate', 'pet-profile' ),
        ];
    }

    public function process_bulk_actions() {
        if ( isset( $_POST['pet_profiles'] ) && !empty( $_POST['pet_profiles'] ) ) {
            $selected_ids = $_POST['pet_profiles'];

            print_r( $selected_ids );

             error_log( 'Selected IDs: ' . implode( ',', $selected_ids ) );
            error_log( 'Action: ' . $this->current_action() );
            die;

            switch ( $this->current_action() ) {
            case 'delete':
                foreach ( $selected_ids as $id ) {
                    if ( get_post_status( $id ) ) {
                        wp_delete_post( $id, true );
                    }
                }
                break;
            case 'activate':
                foreach ( $selected_ids as $id ) {
                    
                }
                break;
            default:
                break;
            }
        }
    }

    public function search_box( $text, $input_id ) {
        ?>
            <form method="get" style="float: right;">
                <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
                <input type="text" name="s" value="<?php echo esc_attr( $_REQUEST['s'] ?? '' ); ?>" placeholder="<?php echo esc_attr( $text ); ?>" />
                <input type="submit" class="button" value="<?php echo esc_attr( $text ); ?>" />
            </form>
        <?php
    }

    protected function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="pet_profiles[]" value="%s" />', esc_attr( $item['id'] ) );
    }

    protected function column_qr_code( $item ) {
        $url    = site_url( "/pet-profile/" . $item['identifier'] );
        $qrCode = new QrCode( $url );
        $output = new Output\Svg();
        return $output->output( $qrCode, 64, 'white', 'black' );
    }

    protected function column_default( $item, $column_name ) {
        return isset( $item[$column_name] ) ? esc_html( $item[$column_name] ) : '';
    }

    protected function column_facebook( $item ) {
        $facebook_url = isset( $item['facebook'] ) && !empty( $item['facebook'] ) ? $item['facebook'] : '';
        return sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $facebook_url ), esc_html( $facebook_url ) );
    }

    protected function column_actions( $item ) {
        $edit_url     = admin_url( 'admin.php?page=pet_profile&action=edit&id=' . $item['id'] );
        $download_url = admin_url( 'admin-post.php?action=download_pet&id=' . $item['identifier'] );

        return sprintf(
            '<a href="%s">%s</a> | <a href="%s">%s</a>',
            esc_url( $edit_url ),
            __( 'Edit', 'pet-profile' ),
            esc_url( $download_url ),
            __( 'Download', 'pet-profile' )
        );
    }

    public function prepare_items() {
        $columns               = $this->get_columns();
        $hidden                = [];
        $sortable              = [];
        $this->_column_headers = [$columns, $hidden, $sortable];

        $search = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

        if ( ! empty( $search ) ) {
            $this->items = array_filter( $this->data, function ( $item ) use ( $search ) {
                $name = isset( $item['name'] ) ? (string) $item['name'] : ''; // Ensure it's a string
                $owner_name = isset( $item['owner_name'] ) ? (string) $item['owner_name'] : ''; // Ensure it's a string

                return strpos( strtolower( $name ), strtolower( $search ) ) !== false || 
                    strpos( strtolower( $owner_name ), strtolower( $search ) ) !== false;
            });
            
        } else {
            $this->items = $this->data;
        }

        $per_page = $this->get_items_per_page( 'pet_profiles_per_page', 20 );
        $paged    = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1;
        $offset   = ( $paged - 1 ) * $per_page;

        $this->items = array_slice( $this->items, $offset, $per_page );

        $this->set_pagination_args( [
            'total_items' => count( $this->data ),
            'per_page'    => $per_page,
            'total_pages' => ceil( count( $this->data ) / $per_page ),
        ] );
    }


    public function display() {
        $this->search_box( __( 'Search Pet Profiles', 'pet-profile' ), 's' );
        echo '<form method="post" action="">';
        parent::display();
        echo '</form>';
    }
}
