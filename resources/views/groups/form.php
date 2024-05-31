<?php
use function DT\Autolink\request;

/**
* @var string $action
 * @var string $nonce
 * @var string $group_id
 * @var string $name_label
 * @var string $name
 * @var string $name_placeholder
 * @var string $leaders_label
 * @var string $leader_ids
 * @var string $leader_options
 * @var string $parent_group_field_callback
 * @var string $start_date_label
 * @var string $start_date
 * @var string $location_label
 * @var string $location
 * @var string $submit_label
 * @var string $cancel_label
 * @var string $cancel_url
 * @var array $group_fields
 * @var array $group
 * @var bool $show_location_field
 * @var string $parent_group
 */
?>
<form class="create-group"
      action="<?php echo esc_attr( $action ) ?>"
      method="POST">

	<?php wp_nonce_field(  'disciple-tools-autolink' ); ?>


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
		value="<?php echo esc_attr( wp_json_encode( $leader_ids ) ) ?>"
		options="<?php echo esc_attr( wp_json_encode( $leader_options ) ) ?>"
	></dt-tags>

  <?php if ( request()->wantsJson() ): ?>
    <input type="hidden" name="parent_group" value="<?php echo esc_attr( $parent_group ) ?>" />
  <?php else : ?>
    <al-ajax-field
      callback="<?php echo esc_attr( $parent_group_field_callback ); ?>"
      watch="leaders"
      events="<?php echo esc_attr( wp_json_encode( [ 'change' ] ) ); ?>"
      prefetch
    >
    </al-ajax-field>
  <?php endif; ?>

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


	<?php if ( !request()->wantsJson() ): ?>
		<div class="buttons">
			<al-submit-button context="success"
			                  type="submit">
				<?php echo esc_html( $submit_label ) ?>
			</al-submit-button>

			<dt-button context="link"
			           href="<?php echo esc_url( $cancel_url ); ?>"
			           title="<?php echo esc_html( $cancel_label ) ?>">
				<?php echo esc_html( $cancel_label ) ?>
			</dt-button>
		</div>
	<?php endif; ?>
</form>
