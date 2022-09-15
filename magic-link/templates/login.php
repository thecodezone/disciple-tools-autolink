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
            <img src="<?php echo esc_url( $logo_url ) ?>" alt="Disciple.Tools" class="logo__image">
        </div>

        <form action="<?php echo esc_attr( $form_action ) ?>" method="POST">
            <?php wp_nonce_field( 'dt_autolink_login' ); ?>

            <?php if ( ! empty( $error ) ) : ?>
                <dt-alert context="alert" dismissable>
                    <?php _e($error) ?>
                </dt-alert>
            <?php endif; ?>

            <dt-text name="username" placeholder="<?php _e( 'Username', 'dt_autolink' ); ?>" value="" required></dt-text>
            <dt-text name="password" placeholder="<?php _e( 'Password', 'dt_autolink' ); ?>" value="" type="password" required></dt-text>

            <button class="button button--primary">
                <?php _e( 'Login', 'dt_autolink' ) ?>
            </button>
            <a class="button button--link" href="<?php echo esc_url( $register_url ); ?>" title="<?php _e( 'Create Account', 'dt_autolink' ); ?>">
                <?php _e( 'Create Account', 'dt_autolink' ) ?>
            </a>
        </form>
    </dt-tile>
</div>
