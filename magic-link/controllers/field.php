<?php

class Disciple_Tools_Autolink_Field_Controller extends Disciple_Tools_Autolink_Controller
{
    /**
     * Update a field
     */
    public function update( WP_REST_Request $request, $params, $user_id ) {
        $body = $request->get_json_params();
        $whitelist = apply_filters('autolink_updatable_group_fields', []);
        if (!$body['id'] || !$body['value']) {
            wp_send_json_error( [ "message" => "Invalid request" ] );
        }

        $id = sanitize_key( wp_unslash( $body['id'] ) );
        $field_info = explode( "_", $id );

        if (!is_array( $field_info ) || count( $field_info ) < 3) {
            wp_send_json_error( [ "message" => "Invalid request" ] );
        }

        $post_type = array_shift( $field_info );
        $id = array_shift( $field_info );
        $field = implode( "_", $field_info );

        $value = sanitize_text_field( wp_unslash( $body['value'] ) );
        $is_allowed = in_array( $field, $whitelist );

        if (!$is_allowed) {
            wp_send_json_error( [ "message" => "Invalid request" ] );
        }

        try {
            $result = DT_Posts::update_post( $post_type, $id, [
                $field => $value
            ] );
        } catch (Exception $e) {
            wp_send_json_error( [ "message" => $e->getMessage() ] );
        }


        if (!is_wp_error( $result )) {
            wp_send_json_success( $result );
        }

        wp_send_json_error( [ "message" => $result->get_error_message() ] );
    }
}
