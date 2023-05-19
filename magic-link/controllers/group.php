<?php

class Disciple_Tools_Autolink_Group_Controller extends Disciple_Tools_Autolink_Controller {
    const NONCE = 'dt_autolink_group';

    /**
     * Show the edit group form
     */
    public function edit( $params = [] ) {
        $group_id         = sanitize_key( wp_unslash( $_GET['post'] ?? "" ) );
        $params['action'] = $this->functions->get_edit_group_url();
        if ( ! $group_id ) {
            $this->functions->redirect_to_app();
            exit;
        }
        $this->form( $params );
    }

    /**
     * Show the edit/create group form
     * Expects $params['action'] to be set
     */
    private function form( $params = [] ) {
        if ( ! isset( $params['action'] ) ) {
            $this->functions->redirect_to_app();
            exit;
        }
        $group_id = sanitize_key( wp_unslash( $_GET['post'] ?? $params['post'] ?? null ) );
        if ( $group_id ) {
            $group = DT_Posts::get_post( 'groups', $group_id );
            if ( ! $group || is_wp_error( $group ) ) {
                $this->functions->redirect_to_app();
                exit;
            }
        }

        $group            = $group ?? [];
        $heading          = __( 'Create a Church', 'disciple-tools-autolink' );
        $name_label       = __( 'Church Name', 'disciple-tools-autolink' );
        $name_placeholder = __( 'Enter name...', 'disciple-tools-autolink' );
        $start_date_label = __( 'Church Start Date', 'disciple-tools-autolink' );
        $nonce            = self::NONCE;
        $action           = $params['action'];
        $cancel_url       = $this->functions->get_app_link();
        $cancel_label     = __( 'Cancel', 'disciple-tools-autolink' );
        $submit_label     = $group_id ? __( 'Edit Church', 'disciple-tools-autolink' ) : __( 'Create Church', 'disciple-tools-autolink' );
        $error            = $params['error'] ?? '';
        $group_fields     = DT_Posts::get_post_settings( 'groups' )['fields'];
        $name             = sanitize_text_field( wp_unslash( $params['name'] ?? "" ) );
        $contacts         = DT_Posts::list_posts( 'contacts', [
            "limit" => 1000,
        ] )['posts'] ?? [];
        $leaders_label    = __( 'Leaders', 'disciple-tools-autolink' );
        $leader_ids       = $params['leaders'] ?? array_map( function ( $leaders ) {
            return $leaders['ID'];
        }, $group['leaders'] ?? [] );
        $leader_options   = array_map( function ( $contact ) {
            return [
                'id' => (string) $contact['ID'],
                'label' => $contact['post_title'],
            ];
        }, $contacts );

        if ( ! $name ) {
            $name = $group['name'] ?? '';
        }

        $start_date = sanitize_text_field( wp_unslash( $_POST['start_date'] ?? "" ) );

        if ( ! $start_date ) {
            $start_date = $group['start_date'] ?? '';
        }

        if ( $start_date && is_array( $start_date ) ) {
            $start_date = $start_date ? dt_format_date( $start_date['timestamp'] ) : '';
        }

        include( __DIR__ . '/../templates/group-form.php' );
    }

    /**
     * Show the create group form
     */
    public function create( $params = [] ) {
        $group_id         = sanitize_key( wp_unslash( $_GET['post'] ?? "" ) );
        $params['action'] = $this->functions->get_create_group_url();
        if ( $group_id ) {
            $this->functions->redirect_to_app();
            exit;
        }
        $this->form( $params );
    }

    /**
     * Delete a group
     */
    public function delete( $params = [] ) {
        $app_controller = new Disciple_Tools_Autolink_App_Controller();

        $nonce        = sanitize_key( wp_unslash( $_GET['_wpnonce'] ?? '' ) );
        $verify_nonce = $nonce && wp_verify_nonce( $nonce, self::NONCE );
        $group_id     = sanitize_text_field( wp_unslash( $_GET['post'] ?? '' ) );

        if ( ! $verify_nonce ) {
            $app_controller->show( [ 'error' => __( 'Unauthorized action. Please refresh the page and try again.', 'disciple-tools-autolink' ) ] );

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
     * Show the DT group in an iframe
     */
    public function show( $params = [] ) {
        $post_id    = sanitize_key( wp_unslash( $_GET['post'] ?? '' ) );
        $back_link  = sanitize_key( wp_unslash( $_GET['return'] ?? '' ) );
        $back_label = __( 'Back to AutoLink', 'disciple-tools-autolink' );

        if ( ! $post_id || ! $back_link ) {
            $this->functions->redirect_to_app();

            return;
        }

        $group = DT_Posts::get_post( 'groups', $post_id );

        if ( is_wp_error( $group ) ) {
            $this->functions->redirect_to_app();

            return;
        }

        $src = get_the_permalink( $group['ID'] );

        include( __DIR__ . '/../templates/frame.php' );
    }

    public function update( $params = [] ) {
        $nonce            = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
        $verify_nonce     = $nonce && wp_verify_nonce( $nonce, self::NONCE );
        $id               = sanitize_key( wp_unslash( $_POST['id'] ?? '' ) );
        $action           = $this->functions->get_edit_group_url();
        $params['action'] = $action;
        $params['post']   = $id;

        if ( ! $verify_nonce ) {
            $this->form( [
                'error' => 'Invalid request',
                'action' => $action
            ] );

            return;
        }

        if ( ! $id ) {
            wp_redirect( $this->functions->get_app_link() );

            return;
        }


        $this->process( $params );
    }

    /**
     * Process the edit/create group form
     */
    private function process( $params = [] ) {

        $nonce        = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
        $verify_nonce = $nonce && wp_verify_nonce( $nonce, self::NONCE );

        $id         = sanitize_key( wp_unslash( $_POST['id'] ?? '' ) );
        $name       = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
        $start_date = strtotime( sanitize_text_field( wp_unslash( $_POST['start_date'] ?? '' ) ) );
        $location   = sanitize_text_field( wp_unslash( $_POST['location'] ?? '' ) );
        $leaders    = dt_recursive_sanitize_array( $_POST['leaders'] ?? '' );
        $location   = $location ? json_decode( $location, true ) : '';
        $action     = $params['action'];

        $get_params       = [
            'action' => $action,
            'name' => $name,
            'leaders' => $leaders,
        ];
        $users_contact_id = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );
        $contact          = DT_Posts::get_post( 'contacts', $users_contact_id, true, false );

        if ( isset( $location['user_location'] )
             && isset( $location['user_location']['location_grid_meta'] ) ) {
            $location = $location['user_location']['location_grid_meta'];
        }

        if ( ! $verify_nonce || ! $name ) {
            $this->form( array_merge( $get_params, [
                'error' => 'Invalid request',
                'post' => $id,
            ] ) );

            return;
        }

        foreach ( $leaders as $idx => $value ) {
            if ( ! is_numeric( $value ) ) {
                $title           = $value;
                $contact         = DT_Posts::create_post( 'contacts',
                    [
                        'name' => $title,
                        'coached_by' => [
                            "values" => [
                                [ "value" => $contact['ID'] ]
                            ]
                        ]
                ], true, false );
                $leaders[ $idx ] = $contact['ID'];
            }
        }

        $leaders[] = $users_contact_id;
        $leaders   = array_map( function ( $leader_id ) {
            return [ 'value' => abs( (int) $leader_id ) ];
        }, $leaders );

        $fields = [
            "title" => $name,
            "leaders" => [
                "values" => $leaders
            ],
            "coaches" => [
                "values" => $leaders
            ],
            "parent_groups" => [
                "values" => [
                    [ "value" => 0 ]
                ]
            ],
            "start_date" => $start_date
        ];

        if ( ! empty( $location ) ) {
            $fields['location_grid_meta'] = [
                "values" => $location
            ];
        }

        if ( $id ) {
            $group = DT_Posts::update_post( 'groups', (int) $id, $fields, false, false );
            if ( is_wp_error( $group ) ) {
                $this->form( array_merge( $get_params, [
                    'error' => $group->get_error_message(),
                    'post' => (int) $id,
                ] ) );

                return;
            }
            do_action( 'dt_autolink_group_updated', $group );
        } else {
            $group = DT_Posts::create_post( 'groups', $fields, false, false );
            if ( is_wp_error( $group ) ) {
                $this->form( array_merge( $get_params, [
                    'error' => $group->get_error_message()
                ] ) );

                return;
            }
            do_action( 'dt_autolink_group_created', $group );
        }

        if ( is_wp_error( $group ) ) {
            $this->form( array_merge( $get_params, [
                'error' => $group->get_error_message()
            ] ) );

            return;
        }

        wp_redirect( $this->functions->get_app_link() );
    }

    public function store( $params = [] ) {
        $nonce            = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
        $verify_nonce     = $nonce && wp_verify_nonce( $nonce, self::NONCE );
        $id               = sanitize_key( wp_unslash( $_POST['id'] ?? '' ) );
        $params['action'] = $this->functions->get_create_group_url();

        if ( ! $verify_nonce || $id ) {
            $this->form( [
                'error' => 'Invalid request'
            ] );

            return;
        }

        $this->process( $params );

    }
}
