<?php

class Disciple_Tools_Autolink_Tree_Controller extends Disciple_Tools_Autolink_Controller
{
    public function show($params = [])
    {
        $magic_link = Disciple_Tools_Autolink_Magic_User_App::instance();
        $data = $this->global_data();
        extract( $data );
        $action = 'tree';
        $fetch_url = '/wp-json/autolink/v1/' . $magic_link->parts['type'];
        $parts = $magic_link->parts;

        include( __DIR__ . '/../templates/tree.php' );
    }

    public function process( WP_REST_Request $request, $params, $user_id ) {
        if ( !isset( $params['data']['previous_parent'] ) ) {
            $params['data']['previous_parent'] = 'domenu-0';
        }
        if ( ( ! isset( $params['data']['new_parent'] ) || ( ! isset( $params['data']['self'] ) ) ) ) {
            return 'false';
        }

        global $wpdb;
        if ( 'domenu-0' !== $params['data']['previous_parent'] ) {
            $wpdb->query( $wpdb->prepare(
                "DELETE
                FROM $wpdb->p2p
                WHERE p2p_from = %s
                    AND p2p_to = %s
                    AND p2p_type = 'groups_to_groups'", $params['data']['self'], $params['data']['previous_parent'] ) );
        }

        $wpdb->query( $wpdb->prepare(
            "INSERT INTO $wpdb->p2p (p2p_from, p2p_to, p2p_type)
                    VALUES (%s, %s, 'groups_to_groups');
            ", $params['data']['self'], $params['data']['new_parent'] ) );

        do_action( 'p2p_created_connection', $wpdb->insert_id );

        return true;
    }

    public function data( WP_REST_Request $request, $params, $user_id ) {
        $tree = [];
        $title_list = [];
        $pre_tree = [];
        $groups = DT_Posts::list_posts('groups', [
            'assigned_to' => [ get_current_user_id() ],
            'limit' => 1000
        ], false );

        $groups = $groups['posts'] ?? [];

        $contact = DT_Posts::list_posts('contacts', [
            'corresponds_to_user' => get_current_user_id(),
            'limit' => 1000
        ], false )['posts'][0];
        $allowed_contact_ids = [
            $contact['ID']
        ];
        $allowed_group_ids = array_map( function ( $group ) {
            return (int) $group['ID'];
        }, $groups );

        if ( isset( $contact['coaching'] ) ) {
            foreach ( $contact['coaching'] as $child_contact ) {
                $allowed_contact_ids[] = $child_contact['ID'];
                $child_contact = DT_Posts::get_post( 'contacts', $child_contact['ID'], false );
                $child_groups = DT_Posts::list_posts('groups', [
                    'assigned_to' => [ $child_contact['corresponds_to_user'] ],
                    'limit' => 1000
                ], false );

                if ( count( $child_groups['posts'] ) ) {
                    foreach ( $child_groups['posts'] as $child_group ) {
                        $allowed_group_ids[] = $child_group['ID'];
                        $groups[] = $child_group;
                    }
                }
            }
        }

        if ( ! empty( $groups ) ) {
            foreach ( $groups as $p ) {
                $assigned_to_user = $p['assigned_to'] ?? [];
                $assigned_to_contact = DT_Posts::list_posts('contacts', [
                    'corresponds_to_user' => $assigned_to_user['id'],
                    'limit' => 1000
                ], false )['posts'][0];
                $is_allowed_contact = in_array( $assigned_to_contact['ID'], $allowed_contact_ids );

                $has_allowed_parent = !empty( $p['parent_groups'] ) && array_filter($p['parent_groups'], function( $parent ) use ( $allowed_group_ids ) {
                    return in_array( $parent['ID'], $allowed_group_ids );
                });

                if ( !$is_allowed_contact ) {
                    continue;
                }

                if ( isset( $p['child_groups'] ) && ! empty( $p['child_groups'] ) ) {
                    foreach ( $p['child_groups'] as $children ) {
                        $pre_tree[$children['ID']] = $p['ID'];
                    }
                }
                if ( !$has_allowed_parent ) {
                    $pre_tree[$p['ID']] = null;
                }
                $title = $p['name'];
                $title_list[$p['ID']] = $title;
            }

            $tree = $this->parse_tree( $pre_tree, $title_list, null, $allowed_group_ids );
        }


        if ( is_null( $tree ) ) {
            $tree = [];
        }

        echo wp_json_encode([
            'parent_list' => $pre_tree,
            'title_list' => $title_list,
            'tree' => $tree
        ]);
    }

    private function parse_tree( $tree, $title_list, $root = null, $allowed_group_ids = [] ) {
        $return = [];
        # Traverse the tree and search for direct children of the root
        foreach ( $tree as $child => $parent ) {
            # A direct child is found
            if ( $parent == $root && in_array( $child, $allowed_group_ids ) ) {
                # Remove item from tree (we don't need to traverse this again)
                unset( $tree[$child] );
                # Append the child into result array and parse its children
                $return[] = [
                    'id' => $child,
                    'title' => $child,
                    'name' => $title_list[$child] ?? 'No Name',
                    'children' => $this->parse_tree( $tree, $title_list, $child, $allowed_group_ids ),
                    '__domenu_params' => []
                ];
            }
        }

        return empty( $return ) ? null : $return;
    }
}
