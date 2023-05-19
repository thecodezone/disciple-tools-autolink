<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

/**
 * Class Disciple_Tools_Autolink_Groups_Tree
 */
class Disciple_Tools_Autolink_Groups_Tree {

    private static $_instance = null;
    const UNNESTED_META_KEY = 'dt_autolink_unnested';

    /**
     * Disciple_Tools_Autolink_Groups_Tree constructor.
     */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'p2p_created_connection' ] );
        add_action( 'dt_autolink_group_created', [ $this, 'dt_autolink_group_created' ], 10, 2 );

        $action = sanitize_key( wp_unslash( $_GET['action'] ?? '' ) );
        if ( $action !== 'tree' ) {
            return;
        }

        add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 100 );
    }

    /**
     * Return the instance of the class
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Enqueue scripts and styles
     */
    public function wp_enqueue_scripts() {
        $plugin_url  = plugins_url() . '/disciple-tools-autolink';
        $plugin_path = WP_PLUGIN_DIR . '/disciple-tools-autolink';

        wp_register_script( 'jquery-touch-punch', '/wp-includes/js/jquery/jquery.ui.touch-punch.js' ); // @phpcs:ignore
        wp_enqueue_script( 'portal-app-domenu-js', $plugin_url . '/magic-link/jquery.domenu-0.100.77.min.js', [ 'jquery' ],
        filemtime( $plugin_path . '/magic-link/jquery.domenu-0.100.77.min.js' ), false );

        wp_enqueue_style( 'portal-app-domenu-css', $plugin_url . '/magic-link/jquery.domenu-0.100.77.css', [],
        filemtime( $plugin_path . '/magic-link/jquery.domenu-0.100.77.css' ) );
    }

    /**
     * Add a meta to the group to indicate that it was created by autolink
     * so we know to handle it differently during tree nesting
     *
     * @param $group
     */
    public function dt_autolink_group_created( $group ) {
        add_post_meta( $group['ID'], self::UNNESTED_META_KEY, true );
    }

    /**
     * When a group is created, we don't need to nest it differently anymore
     */
    public function p2p_created_connection( $connection_id ) {
        $connection = p2p_get_connection( $connection_id );
        if ( $connection ) {
            return;
        }
        if ( $connection->p2p_type == 'groups_to_groups' ) {
            $group_id                     = $connection->p2p_to;
            $is_newly_created_by_autolink = get_post_meta( $group_id, self::UNNESTED_META_KEY, true );
            if ( $is_newly_created_by_autolink ) {
                return;
            }
            delete_post_meta( $group_id, self::UNNESTED_META_KEY );
        }
    }

    /**
     * Get the tree of groups
     */
    public function tree() {
        $tree                = [];
        $title_list          = [];
        $coach_list          = [];
        $pre_tree            = [];
        $has_parent_list     = [];
        $unassigned_group_id = 'u';

        $contact = DT_Posts::list_posts( 'contacts', [
            'corresponds_to_user' => get_current_user_id(),
            'limit' => 1000
        ], false )['posts'][0];

        $allowed_contact_ids = [ $contact['ID'] ];
        $allowed_group_ids   = [ $unassigned_group_id ];
        $allowed_user_ids    = [ get_current_user_id() ];

        if ( isset( $contact['coaching'] ) ) {
            foreach ( $contact['coaching'] as $child_contact ) {
                $allowed_contact_ids[] = $child_contact['ID'];
                $child_contact         = DT_Posts::get_post( 'contacts', $child_contact['ID'], false );
                $allowed_user_ids[]    = $child_contact['corresponds_to_user'];
            }
        }

        $groups = [];

        $result = DT_Posts::list_posts( 'groups', [
            'assigned_to' => $allowed_user_ids,
            'limit' => 1000
        ], false );

        foreach ( $result['posts'] ?? [] as $post ) {
            $groups[] = $post;
        }

        array_push( $allowed_group_ids, ...array_map( function ( $group ) {
            return (int) $group['ID'];
        }, $groups ) );

        foreach ( $groups as $p ) {
            $assigned_to_user    = $p['assigned_to'] ?? [];
            $assigned_to_contact = DT_Posts::list_posts( 'contacts', [
                'corresponds_to_user' => $assigned_to_user['id'],
                'limit' => 1000
            ], false )['posts'][0];
            $first_root_group    = null;
            foreach ( $pre_tree as $pre_group_id => $pre_parent_group_id ) {
                if ( ! $pre_parent_group_id ) {
                    $first_root_group = $pre_group_id;
                    break;
                }
            }

            $is_allowed_contact = in_array( $assigned_to_contact['ID'], $allowed_contact_ids );
            if ( ! $is_allowed_contact ) {
                continue;
            }

            $coaches = [];
            foreach ( $p['coaches'] as $coach ) {
                $coaches[] = $coach['ID'];
            }
            foreach ( $assigned_to_contact['coached_by'] as $coach ) {
                $coaches[] = $coach['ID'];
            }
            $parents = [];
            foreach ( $p['parent_groups'] as $parent_group ) {
                $parents[] = $parent_group['ID'];
            }

            $has_allowed_parent  = ! empty( $parents ) && array_filter( $parents, function ( $parent ) use ( $allowed_group_ids ) {
                    return in_array( $parent, $allowed_group_ids );
            } );
            $contact_is_coaching = in_array( $contact['ID'], $coaches );

            $contact_is_assigned = $assigned_to_contact['ID'] === $contact['ID'];


            if ( isset( $p['child_groups'] ) && ! empty( $p['child_groups'] ) ) {
                foreach ( $p['child_groups'] as $children ) {
                    $pre_tree[ $children['ID'] ] = $p['ID'];
                }
            }

            if ( ! $has_allowed_parent && ! $contact_is_coaching && $contact_is_assigned ) {
                $pre_tree[ $p['ID'] ] = null;
            } elseif ( ! $has_allowed_parent ) {
                if ( $contact_is_coaching ) {
                    $pre_tree[ $p['ID'] ] = $unassigned_group_id;
                } else {
                    $pre_tree[ $p['ID'] ] = null;
                }
            }

            $title                       = $p['name'];
            $title_list[ $p['ID'] ]      = $title;
            $coaching_list[ $p['ID'] ]   = $contact_is_coaching;
            $has_parent_list[ $p['ID'] ] = $has_allowed_parent;


            if ( isset( $pre_tree[ $p['ID'] ] ) && is_null( $pre_tree[ $p['ID'] ] ) && is_null( $first_root_group ) ) {
                $first_root_group = $p['ID'];
            }
        }

        if ( array_search( $unassigned_group_id, $pre_tree ) ) {
            $pre_tree[ $unassigned_group_id ]        = null;
            $title_list[ $unassigned_group_id ]      = __( 'Coached without Parent Group', 'disciple_tools' );
            $coaching_list[ $unassigned_group_id ]   = false;
            $has_parent_list[ $unassigned_group_id ] = false;
        }

        $tree = $this->parse_tree( $pre_tree, $title_list, $has_parent_list, $coaching_list, null, $allowed_group_ids );

        if ( is_null( $tree ) ) {
            $tree = [];
        }

        return [
            'parent_list' => $pre_tree,
            'title_list' => $title_list,
            'tree' => $tree
        ];
    }

    /**
     * Parse the tree
     *
     * @param $tree
     * @param $title_list
     * @param null $root
     * @param array $allowed_group_ids
     *
     * @return array
     */
    private function parse_tree( $tree, $title_list, $has_parent_list, $coaching_list, $root = null, $allowed_group_ids = [] ) {
        $return = [];
        # Traverse the tree and search for direct children of the root
        foreach ( $tree as $child => $parent ) {
            # A direct child is found
            if ( $parent == $root && in_array( $child, $allowed_group_ids ) ) {
                # Remove item from tree (we don't need to traverse this again)
                unset( $tree[ $child ] );
                # Append the child into result array and parse its children
                $return[] = [
                    'id' => $child,
                    'title' => $child,
                    'name' => $title_list[ $child ] ?? 'No Name',
                    'children' => $this->parse_tree( $tree, $title_list, $has_parent_list, $coaching_list, $child, $allowed_group_ids ),
                    'coaching' => $coaching_list[ $child ] ?? false,
                    'has_parent' => $has_parent_list[ $child ] ?? false,
                    '__domenu_params' => []
                ];
            }
        }

        return empty( $return ) ? null : $return;
    }
}

Disciple_Tools_Autolink_Groups_Tree::instance();

