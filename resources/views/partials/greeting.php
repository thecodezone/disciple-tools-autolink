<?php
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
use DT\Autolink\Repositories\UserRepository;
use function DT\Autolink\container;
use function DT\Autolink\share_url;

$user_repository = container()->get( UserRepository::class );
$user_name = $user_repository->display_name();
$coach_name = $user_repository->coach_name();
?>
<div class="container">
	<strong class="greeting">
		<?php echo esc_html( __( 'Hello,', 'disciple-tools-autolink' ) ); ?>
	</strong>
	<h1 class="user_name"><?php echo esc_html( $user_name ); ?></h1>

	<?php if ( $coach_name ): ?>
		<strong class="coached_by">
			<?php echo esc_html( __( 'Coached by', 'disciple-tools-autolink' ) ); ?> <?php echo esc_html( $coach_name ); ?>
		</strong>
	<?php endif; ?>

	<dt-tile title="<?php echo esc_attr( __( 'My Link', 'disciple-tools-autolink' ) ); ?>"
	         class="app__link">
		<div class="section__inner">
			<dt-copy-text value="<?php echo esc_url( share_url() ); ?>" <?php language_attributes(); ?>></dt-copy-text>
			<span class="help-text cloak">
          <?php esc_html_e( 'Copy this link and share it with people you are coaching.', 'disciple-tools-autolink' ) ?>
      </span>
		</div>
	</dt-tile>
</div>