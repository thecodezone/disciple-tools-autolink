<?php
/**
 * @var $logo_url string
 * @var $form_action string
 * @var $error string
 */
?>
<?php include( 'parts/header.php' ); ?>

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
                     placeholder="<?php _e( 'Name', 'disciple-tools-autolink' ); ?>"
                     value=""
                     required></dt-text>
            <dt-text name="username"
                     placeholder="<?php _e( 'Username', 'disciple-tools-autolink' ); ?>"
                     value=""
                     required></dt-text>
            <dt-text name="email"
                     placeholder="<?php _e( 'Email', 'disciple-tools-autolink' ); ?>"
                     value=""
                     required></dt-text>
            <dt-text name="password"
                     placeholder="<?php _e( 'Password', 'disciple-tools-autolink' ); ?>"
                     value=""
                     type="password"
                     required></dt-text>
            <dt-text name="confirm_password"
                     placeholder="<?php _e( 'Confirm Password', 'disciple-tools-autolink' ); ?>"
                     value=""
                     type="password"
                     required></dt-text>

            <dt-button context="success" type="submit" class="button--large button--block">
                <?php _e( 'Register', 'disciple-tools-autolink' ) ?>
            </dt-button>
        </form>
    </dt-tile>
</div>

<?php include( 'parts/footer.php' ); ?>
