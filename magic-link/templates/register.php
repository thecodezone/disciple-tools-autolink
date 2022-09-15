<?php
/**
 * @var $logo_url string
 * @var $form_action string
 * @var $error string
 */
?>
<div class="container register">
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

            <dt-text name="name"
                     placeholder="<?php _e( 'Name', 'dt_autolink' ); ?>"
                     value=""
                     required></dt-text>
            <dt-text name="username"
                     placeholder="<?php _e( 'Username', 'dt_autolink' ); ?>"
                     value=""
                     required></dt-text>
            <dt-text name="email"
                     placeholder="<?php _e( 'Email', 'dt_autolink' ); ?>"
                     value=""
                     required></dt-text>
            <dt-text name="password"
                     placeholder="<?php _e( 'Password', 'dt_autolink' ); ?>"
                     value=""
                     type="password"
                     required></dt-text>
            <dt-text name="confirm_password"
                     placeholder="<?php _e( 'Confirm Password', 'dt_autolink' ); ?>"
                     value=""
                     type="password"
                     required></dt-text>

            <button class="button button--primary">
                <?php _e( 'Register', 'dt_autolink' ) ?>
            </button>
        </form>
    </dt-tile>
</div>
