<?php

class Disciple_Tools_Autolink_Back_Widget {
    private static $_instance = null;
    private $query_var = 'back_to_autolink';
    private $label_var = 'back_to_autolink_label';

    public function __construct() {
        add_filter( 'query_vars', [$this, 'query_vars'], 1, 1);

        if ($this->should_display()) {
            add_action('wp_footer', [$this, 'wp_footer']);
        }
    }

    public function should_display()
    {
        return !!$this->get_path();
    }

    protected function get_path() {
        return sanitize_url( $_GET[ $this->query_var ] ?? '' );
    }

    protected function get_label() {
        $label = sanitize_text_field( get_query_var( $this->label_var . '_label') );
        if (!$label) {
            return __('Back to AutoLink', 'disciple-tools-autolink' );
        }
    }

    public function wp_footer() {
        $path = $this->get_path();
        $label = $this->get_label();

        if (!$path) {
            return;
        }

        include('templates/parts/back-widget.php');
    }


    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance

    function query_vars( $allowed ) {
        $allowed[] = $this->query_var;
        $allowed[] = $this->label_var;
        return $allowed;
    }
}

new Disciple_Tools_Autolink_Back_Widget();
