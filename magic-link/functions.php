<?php

class Disciple_Tools_Autolink_Magic_Functions {

    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function dt_magic_url_base_allowed_js( $allowed_js ) {
        $allowed_js[] = 'magic_link_scripts';
        return $allowed_js;
    }

    public function dt_magic_url_base_allowed_css( $allowed_css ) {
        $allowed_css[] = 'magic_link_css';
        return $allowed_css;
    }

    public function wp_enqueue_scripts(){
        wp_enqueue_script( 'magic_link_scripts', trailingslashit( plugin_dir_url( __FILE__ ) ) . '../dist/magic-link.js', [
            'jquery',
            'lodash',
        ], filemtime( plugin_dir_path( __FILE__ ) . 'magic-link.js' ), true );
        wp_localize_script(
            'magic_link_scripts', 'app', [
                'map_key' => DT_Mapbox_API::get_key(),
                'rest_base' => esc_url( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'translations' => [
                    'add' => __( 'Add Magic', 'disciple-tools-autolink' ),
                ]
            ]
        );
        wp_enqueue_style( 'magic_link_css', trailingslashit( plugin_dir_url( __FILE__ ) ) . '../dist/magic-link.css', [], filemtime( plugin_dir_path( __FILE__ ) . 'magic-link.css' ) );
    }

    /**
     * Activate the 3/3rds magic link for the current user
     */
    public function activate() {
        $app_user_key = get_user_option( 'dt_autolink' );
        if ( !$app_user_key ) {
            update_user_option( get_current_user_id(), 'dt_autolink', dt_create_unique_key() );
        }
    }

    /**
     * Get the magic link url
     * @return string
     */
    private function get_app_link() {
        $app_user_key = get_user_option( 'dt_autolink' );
        $app_url_base = trim( site_url(), '/' ) .'/autolink/app';
        return $app_user_key ? $app_url_base . '/' . $app_user_key : '';
    }

    public function redirect_to_app() {
        wp_redirect( $this->get_app_link() );
        exit;
    }

    public function redirect_to_link() {
        wp_redirect( '/autolink' );
        exit;
    }

}
