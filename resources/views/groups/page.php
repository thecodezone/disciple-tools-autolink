<?php
use function DT\Autolink\request;
use function DT\Autolink\request_wants_json;
/**
* @var string $heading
 * @var string $error
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
 * @var string $show_location_field
 * @var string $group_fields
 * @var string $group
 * @var string $submit_label
 * @var string $cancel_url
 * @var string $cancel_label

if ( !request_wants_json(request()) ) {
	$this->layout( "layouts/app" );
}
?>

<div class="app">
	<div class="container login">
		<dt-tile title="<?php echo esc_attr( $heading ?? '' ); ?>">
			<div class="section__inner">
				<?php if ( $error ?? false ): ?>
					<dt-alert context="alert"
					          dismissable>
						<?php echo esc_html( $error ); ?>
					</dt-alert>
				<?php endif; ?>
		    <?php $this->insert( 'groups/form', get_defined_vars() ); ?>
      </div>
		</dt-tile>
	</div>
</div>
