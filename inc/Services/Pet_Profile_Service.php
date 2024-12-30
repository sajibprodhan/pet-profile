<?php
namespace Inc\Services;

class Pet_Profile_Service {

    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb       = $wpdb;
        $this->table_name = $wpdb->prefix . 'giopio_pet_profile';
    }

    public function get_all_profiles( $search_query = '' ) {
        return $this->wpdb->get_results( "SELECT * FROM {$this->table_name} $search_query ORDER BY created_at DESC", ARRAY_A );
    }

    public function get_profile( $profile_id ) {
        return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $profile_id ), ARRAY_A );
    }

    public function create_pet_profiles( $count ) {
        $data = array(
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
        );
        for ( $i = 0; $i < $count; $i++ ) {
            $this->wpdb->insert(
                $this->table_name,$data
            );
        }
    }

    public function update_profile( $profile_id, $data ) {
        $data = [
            'name'           => sanitize_text_field($data['name']),
            'age'            => sanitize_text_field($data['age']),
            'gender'         => sanitize_text_field($data['gender']),
            'about'          => sanitize_textarea_field($data['about']),
            'owner_name'     => sanitize_text_field($data['owner_name']),
            'mobile'         => sanitize_text_field($data['mobile']),
            'location'       => sanitize_text_field($data['location']),
            'facebook'       => esc_url($data['facebook']),
            'whatsapp_id'    => esc_url($data['whatsapp_id']),
            'vaccine_name'   => sanitize_text_field($data['vaccine_name']),
            'vaccine_date'   => sanitize_text_field($data['vaccine_date']),
            'vaccine_name_2' => sanitize_text_field($data['vaccine_name_2']),
            'vaccine_date_2' => sanitize_text_field($data['vaccine_date_2']),
            'gallery'        => isset($data['gallery']) ? json_encode(array_map('sanitize_text_field', $data['gallery'])) : '',
        ];

        $this->wpdb->update( $this->table_name, $data, ['id' => $profile_id] );
    }

    public function delete_profile( $profile_id ) {
        $this->wpdb->delete( $this->table_name, ['id' => $profile_id] );
    }

    public function delete_bulk_profiles( $profile_ids ) {
        foreach ( $profile_ids as $profile_id ) {
            $this->delete_profile( $profile_id );
        }
    }

    public function generate_search_query( $search_term ) {
        if ( !empty( $search_term ) ) {
            $search = sanitize_text_field( $search_term );
            return $this->wpdb->prepare(
                "WHERE name LIKE %s OR location LIKE %s OR mobile LIKE %s OR owner_name LIKE %s",
                '%' . $this->wpdb->esc_like( $search ) . '%',
                '%' . $this->wpdb->esc_like( $search ) . '%',
                '%' . $this->wpdb->esc_like( $search ) . '%',
                '%' . $this->wpdb->esc_like( $search ) . '%'
            );
        }
        return '';
    }
}
