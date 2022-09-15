<?php
/**
 * @var $logo_url string
 * @var $register_url string
 * @var $form_action string
 * @var $error string
 */
?>
<div class="container login">
    <dt-tile>
        <div class="logo">
            <img src="<?php echo esc_url( $logo_url ) ?>"
                 alt="Disciple.Tools"
                 class="logo__image">
        </div>

        <form action="<?php echo esc_attr( $form_action ) ?>"
              method="POST">
            <?php wp_nonce_field( 'dt_autolink_login' ); ?>

            <?php if ( !empty( $error ) ) : ?>
                <dt-alert context="alert"
                          dismissable>
                    <?php _e( $error ) ?>
                </dt-alert>
            <?php endif; ?>

            <dt-text name="username"
                     placeholder="<?php _e( 'Username', 'disciple-tools-autolink' ); ?>"
                     value=""
                     required></dt-text>
            <dt-text name="password"
                     placeholder="<?php _e( 'Password', 'disciple-tools-autolink' ); ?>"
                     value=""
                     type="password"
                     required></dt-text>

            <div class="login__buttons">
                <dt-button context="success"
                           type="submit">
                    <?php _e( 'Login', 'disciple-tools-autolink' ) ?>
                </dt-button>

                <dt-button context="link"
                           href="<?php echo esc_url( $register_url ); ?>"
                           title="<?php _e( 'Create Account', 'disciple-tools-autolink' ); ?>">
                    <?php _e( 'Create Account', 'disciple-tools-autolink' ) ?>
                    <dt-chevron-right></dt-chevron-right>
                </dt-button>
            </div>
        </form>
    </dt-tile>
</div>
