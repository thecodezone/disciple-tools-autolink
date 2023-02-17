<?php
/**
 * @var $heading string
 * @var $name_label string
 * @var $name_placeholder string
 * @var $nonce string
 * @var $action string
 * @var $cancel_url string
 * @var $submit_label string
 * @var $cancel_label string
 * @var $error string
 */
?>
<?php include( 'parts/header.php' ); ?>
<?php include( 'parts/navbar.php' ); ?>

<div class="container login">
    <dt-tile>
        <div class="section__inner">
            <?php if ( $error ): ?>
                <dt-alert context="alert"
                        dismissable>
                    <?php echo esc_html( $error ); ?>
                </dt-alert>
            <?php endif; ?>
            <form class="create-group" action="<?php echo esc_attr( $action ) ?>" method="POST">
                <?php
                    wp_nonce_field( $nonce );
                ?>

                <dt-text class="create-group__input" label="<?php echo esc_html( $name_label ); ?>" type="text" name="name" value="" placeholder="<?php echo esc_attr( $name_placeholder ); ?>"></dt-text>

                <dt-date name="start_date"  label="<?php echo esc_html( $start_date_label ); ?>"></dt-date>

                <div class="location-field">
                    <?php
                        render_field_for_display( 'location_grid', $group_fields, [] );
                        render_field_for_display( 'location_grid_meta', $group_fields, [] );
                    ?>
                    <input type="hidden" name="location">
                </div>
               

                <div class="buttons">
                    <dt-button context="success"
                            type="submit">
                        <?php echo esc_html( $submit_label ) ?>
                    </dt-button>

                    <dt-button context="link"
                            href="<?php echo esc_url( $cancel_url ); ?>"
                            title="<?php echo esc_html( $cancel_label ) ?>">
                        <?php echo esc_html( $cancel_label ) ?>
                </dt-button>
            </form>
        </div>
    </dt-tile>
</div>
