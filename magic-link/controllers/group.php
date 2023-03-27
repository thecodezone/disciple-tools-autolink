<?php

class Disciple_Tools_Autolink_Group_Controller extends Disciple_Tools_Autolink_Controller {
    /**
     * Show the DT group in an iframe
     */
    public function show( $params = [] ) {
        $post_id = wp_unslash( $_GET['post'] ?? '' );
        $back_link = wp_unslash( $_GET['return'] ?? '' );
        $back_label = __('Back to AutoLink', 'disciple-tools-autolink' );

        if (!$post_id || !$back_link) {
            $this->functions->redirect_to_app();
            return;
        }

        $group = DT_Posts::get_post( 'groups', $post_id);

        if ( is_wp_error( $group ) ) {
            $this->functions->redirect_to_app();
            return;
        }

        $src = get_the_permalink( $group['ID'] );

        include( __DIR__ . '/../templates/frame.php' );
    }

    /**
     * Show the create group form
     */
    public function create( $params = [] ) {
        $heading = __( 'Create a Church', 'disciple-tools-autolink' );
        $name_label = __( 'Church Name', 'disciple-tools-autolink' );
        $name_placeholder = __( 'Enter name...', 'disciple-tools-autolink' );
        $start_date_label = __( 'Church Start Date', 'disciple-tools-autolink' );
        $nonce = 'dt_autolink_create_group';
        $action = $this->functions->get_app_link() . '?action=create-group';
        $cancel_url = $this->functions->get_app_link();
        $cancel_label = __( 'Cancel', 'disciple-tools-autolink' );
        $submit_label = __( 'Create Church', 'disciple-tools-autolink' );
        $error = $params['error'] ?? '';
        $group_fields = DT_Posts::get_post_settings( 'groups' )['fields'];

        include( __DIR__ . '/../templates/create-group.php' );
    }

    /**
     * Delete a group
     */
    public function delete( $params = [] ) {
        $app_controller = new Disciple_Tools_Autolink_App_Controller();

        $nonce = sanitize_key( wp_unslash( $_GET['_wpnonce'] ?? '' ) );
        $verify_nonce = $nonce && wp_verify_nonce( $nonce, 'dt_autolink_delete_group' );
        $group_id = sanitize_text_field( wp_unslash( $_GET['post'] ?? '' ) );

        if(!$verify_nonce) {
            $app_controller->show( [ 'error' => __('Unauthorized action. Please refresh the page and try again.', 'disciple-tools-autolink' ) ] );
            return;
        }

        $group_id = (int) $group_id;

        $result = DT_Posts::delete_post( 'groups', $group_id, false );

        if ( is_wp_error( $result ) ) {
            $app_controller->show( [ 'error' => $result->get_error_message() ] );
        }

        $app_controller->show();
    }

     /**
     * Process the create group form
     */
    public function process($params = []) {
        $nonce = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
        $verify_nonce = $nonce && wp_verify_nonce( $nonce, 'dt_autolink_create_group' );
        $name = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
        $start_date = strtotime( sanitize_text_field( wp_unslash( $_POST['start_date'] ?? '' ) ) );
        $location = sanitize_text_field( wp_unslash( $_POST['location'] ?? '' ) );
        $location = $location ? json_decode( $location, true ) : '';

        if ( isset( $location['user_location'] )
        && isset( $location['user_location']['location_grid_meta'] ) ) {
            $location = $location['user_location']['location_grid_meta'];
        }

        if ( !$verify_nonce || !$name ) {
            $this->create( [ 'error' => 'Invalid request' ] );
            return;
        }

        $users_contact_id = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );

        $fields = [
        "title" => $name,
        "members" => [
            "values" => [
                [ "value" => $users_contact_id ]
            ]
        ],
        "leaders" => [
            "values" => [
                [ "value" => $users_contact_id ]
            ]
        ],
        "start_date" => $start_date
        ];

        if ( !empty( $location ) ) {
            $fields['location_grid_meta'] = [
                "values" => $location
            ];
        }

        $group = DT_Posts::create_post( 'groups', $fields, false, false );

        if ( is_wp_error( $group ) ) {
            $this->create( [ 'error' => $group->get_error_message() ] );
            return;
        }

        wp_redirect( $this->functions->get_app_link() );
    }
}
