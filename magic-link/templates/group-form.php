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
 * @var $group_id string
 * @var $name string
 * @var $leaders array
 * @var $leaders_label string
 * @var $leader_options array
 * @var $leader_ids array
 * @var $start_date_label string
 * @var $start_date string
 * @var $location_label string
 * @var $location string
 * @var $show_location_field boolean
 * @var $group_fields array
 * @var $group array
 * @var $parent_group_field_nonce string
 * @var $parent_group_field_callback string
 */
?>
<?php include 'parts/header.php'; ?>
<?php include 'parts/navbar.php'; ?>

<div class="container login">
    <dt-tile title="<?php echo esc_attr( $heading ); ?>">
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
                        value="<?php echo esc_attr( json_encode( $leader_ids ) ) ?>"
                        options='<?php echo esc_attr( json_encode( $leader_options ) ) ?>'
                ></dt-tags>

                <ajax-field
                        callback="<?php echo esc_attr( $parent_group_field_callback ); ?>"
                        watch="leaders"
                        events="<?php echo esc_attr( wp_json_encode( [ 'change' ] ) ); ?>"
                        prefetch
                >
                </ajax-field>

                <dt-date
                        format=""
                        name="start_date"
                        label="<?php echo esc_html( $start_date_label ); ?>"
                        value="<?php echo esc_attr( $start_date ) ?>"
                ></dt-date>

				<?php if ( $show_location_field ): ?>
                    <div class="location-field">
						<?php
						render_field_for_display( 'location_grid_meta', $group_fields, $group );
						?>
                        <input type="hidden"
                               name="location">
                    </div>
				<?php else : ?>
                    <input type="hidden"
                           name="location">
				<?php endif; ?>


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
