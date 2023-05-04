<?php

class Disciple_Tools_Autolink_Tree_Controller extends Disciple_Tools_Autolink_Controller {
    const nonce = 'dt_autolink_tree';
    private $tree_chart = null;

    public function __construct() {
        $this->functions  = Disciple_Tools_Autolink_Magic_Functions::instance();
        $this->tree_chart = Disciple_Tools_Autolink_Groups_Tree::instance();
    }

    public function show( $params = [] ) {
        $magic_link = Disciple_Tools_Autolink_Magic_User_App::instance();
        $data       = $this->global_data();
        extract( $data );
        $action    = 'tree';
        $fetch_url = '/wp-json/autolink/v1/' . $magic_link->parts['type'];
        $parts     = $magic_link->parts;

        include( __DIR__ . '/../templates/tree.php' );
    }

    public function process( WP_REST_Request $request, $params, $user_id ) {
        if ( ! isset( $params['data']['previous_parent'] ) ) {
            $params['data']['previous_parent'] = 'domenu-0';
        }
        if ( ( ! isset( $params['data']['new_parent'] ) || ( ! isset( $params['data']['self'] ) ) ) ) {
            return 'false';
        }

        $group     = DT_Posts::get_post( 'groups', $params['data']['self'], true, false );
        $new_group = ( $params['data']['new_parent'] != 'domenu-0' ) ? DT_Posts::get_post( 'groups', $params['data']['new_parent'], true, false ) : null;

        if ( ! $new_group
             && ( (int) $group["assigned_to"]["id"] !== $user_id ) ) {
            return "reload";
        } elseif ( $new_group
                   && ( (int) $new_group['assigned_to']["id"] !== $user_id ) ) {
            return "reload";
        }
        global $wpdb;

        $wpdb->query( $wpdb->prepare(
            "DELETE
                FROM $wpdb->p2p
                WHERE p2p_from = %s
                    AND p2p_to = %s
                    AND p2p_type = 'groups_to_groups'",
            $params['data']['self'],
            $params['data']['previous_parent']
        ) );

        if ( $params['data']['new_parent'] !== 'domenu-0' ) {
            $wpdb->query( $wpdb->prepare(
                "INSERT INTO $wpdb->p2p (p2p_from, p2p_to, p2p_type)
                    VALUES (%s, %s, 'groups_to_groups');
            ",
                $params['data']['self'],
                $params['data']['new_parent']
            ) );

            do_action( 'p2p_created_connection', $wpdb->insert_id );
        }

        return true;
    }

    public function data( WP_REST_Request $request, $params, $user_id ) {
        echo wp_json_encode( $this->tree_chart->tree() );
    }
}
