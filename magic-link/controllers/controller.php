<?php

abstract class Disciple_Tools_Autolink_Controller {
    public $functions;
    static public $_instance;

    public function __construct() {
        $this->functions = Disciple_Tools_Autolink_Magic_Functions::instance();
    }

    /**
     * Global data is shared between many views in the app.
     */
    public function global_data() {
        $data = [];
        $post_type = get_post_type_object( 'groups' );
        $group_labels = get_post_type_labels( $post_type );

        $data['logo_url'] = $this->functions->fetch_logo();
        $data['greeting'] = __( 'Hello,', 'disciple-tools-autolink' );
        $data['user_name'] = dt_get_user_display_name( get_current_user_id() );
        $data['app_url'] = $this->functions->get_app_link();
        $data['coached_by_label'] = __( 'Coached by', 'disciple-tools-autolink' );
        $data['link_heading'] = __( 'My Link', 'disciple-tools-autolink' );
        $data['share_link_help_text'] = __( 'Copy this link and share it with people you are coaching.', 'disciple-tools-autolink' );
        $data['churches_heading'] = __( "My ", 'disciple-tools-autolink' ) . $group_labels->name;
        $data['share_link'] = $this->functions->get_share_link();
        $data['group_fields'] = DT_Posts::get_post_field_settings( 'groups' );
        $data['create_group_link'] = $this->functions->get_create_group_url();
        $data['edit_group_link'] = $this->functions->get_edit_group_url();
        $data['contact'] = Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );
        $data['coach'] = null;
        $data['coach_name'] = '';
        $data['view_church_label'] = __( 'View', 'disciple-tools-autolink' ) . ' ' . $group_labels->singular_name;
        $data['churches'] = [];
        $data['church_health_label'] = $group_labels->singular_name . ' ' . __( 'Health', 'disciple-tools-autolink' );
        $data['tree_label'] = __( 'Tree', 'disciple-tools-autolink' );
        $data['genmap_label'] = __( 'GenMap', 'disciple-tools-autolink' );

        if ( $data['contact'] ) {
            $result = null;
            $data['contact'] = DT_Posts::get_post( 'contacts', $data['contact'], false, false );
            if ( !is_wp_error( $result ) ) {
                $data['contact'] = $result;
            }
            $posts_response = $data['churches'] = DT_Posts::list_posts('groups', [
                'assigned_to' => [ get_current_user_id() ],
                'orderby' => 'modified',
                'order' => 'DESC',
            ], false);
            if ( is_wp_error( $result ) ) {
                $data['churches'] = $posts_response['posts'] ?? [];
            } else {
                $data['churches'] = [];
            }
        }

        if ( $data['contact'] && count( $data['contact']['coached_by'] ) ) {
            $coach = $data['contact']['coached_by'][0] ?? null;
            if ( $coach ) {
                $coach = DT_Posts::get_post( 'contacts', $coach['ID'], false, false );
                if ( is_wp_error( $coach ) ) {
                    $coach = '';
                }
                $coach_name = $coach['name'] ?? '';
            }
            $data['coach'] = $coach;
        }

        return $data;
    }
}
