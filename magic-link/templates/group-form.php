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
 * @var $id string
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
            <form class="create-group"
                  action="<?php echo esc_attr( $action ) ?>"
                  method="POST">
                <?php
                wp_nonce_field( $nonce );
                ?>

                <?php if ( ! empty( $group_id ) ): ?>
                    <input type="hidden"
                           name="id"
                           value="<?php echo esc_attr( $group_id ); ?>">
                <?php endif; ?>

                <dt-text
                    class="create-group__input"
                    label="<?php echo esc_html( $name_label ); ?>"
                    type="text"
                    name="name"
                    value="<?php echo esc_attr( $name ) ?>"
                    placeholder="<?php echo esc_attr( $name_placeholder ); ?>"
                ></dt-text>

                <dt-tags
                    allowAdd
                    class="create-group__input"
                    label="<?php echo esc_html( $leaders_label ); ?>"
                    name="leaders"
                    values="<?php echo esc_attr( json_encode( $leader_values ) ) ?>"
                    options='<?php echo esc_attr( json_encode( $leader_options ) ) ?>'
                ></dt-tags>

                <dt-date
                    format=""
                    name="start_date"
                    label="<?php echo esc_html( $start_date_label ); ?>"
                    value="<?php echo esc_attr( $start_date ) ?>"
                ></dt-date>

                <div class="location-field">
                    <?php
                    render_field_for_display( 'location_grid', $group_fields, $group );
                    render_field_for_display( 'location_grid_meta', $group_fields, $group );
                    ?>
                    <input type="hidden"
                           name="location">
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
