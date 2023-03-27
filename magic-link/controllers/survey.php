<?php

class Disciple_Tools_Autolink_Survey_Controller extends Disciple_Tools_Autolink_Controller
{
    /**
     * Show the survey template
     */
    public function show($params = [])
    {
        $survey = $this->functions->survey();
        $page = sanitize_key( wp_unslash( $_GET['paged'] ?? 0 ) );
        $question = $survey[$page] ?? null;
        if ( !$question ) {
            wp_redirect( $this->functions->get_app_link() . '?action=survey' );
            return;
        }
        $answer = get_user_meta( get_current_user_id(), $question['name'], true );
        $answer = $answer ? $answer : 0;
        $action = $this->functions->get_app_link() . '?action=survey&paged=' . $page;
        $previous_url = $page > 0 ? $this->functions->get_app_link() . '?action=survey&paged=' . ( $page - 1 ) : null;
        $progress = ( $page + 1 ) / count( $survey );
        $progress = number_format( $progress * 100, 0 ) . '%';
        include( __DIR__ . '/../templates/survey.php' );
    }

    /**
     * Process the survey form
     */
    public function process($params = [])
    {
        $survey = $this->functions->survey();
        $page = (int) sanitize_text_field( wp_unslash( $_GET['paged'] ?? 0 ) );
        $question = $survey[$page] ?? null;
        $next_page = $page + 1;
        $question_name = $question['name'];
        $nonce = sanitize_key( wp_unslash( $_POST['_wpnonce'] ?? '' ) );
        $verify_nonce = $nonce && wp_verify_nonce( $nonce, 'dt_autolink_survey' );

        if ( !$verify_nonce || !$question ) {
            wp_redirect( $this->functions->get_app_link() . '?action=survey' );
            return;
        }

        $answer = sanitize_key( wp_unslash( $_POST[$question_name] ?? null ) );

        if ( $answer === null ) {
            wp_redirect( $this->functions->get_app_link() . '?action=survey&paged=' . $page );
            return;
        }
        update_user_meta( get_current_user_id(), $question['name'], $answer );

        if ( isset( $survey[$next_page] ) ) {
            wp_redirect( $this->functions->get_app_link() . '?action=survey&paged=' . $next_page );
            return;
        }

        wp_redirect( $this->functions->get_app_link() );
    }
}
