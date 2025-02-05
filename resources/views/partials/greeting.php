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
$share_link = esc_url( share_url() );
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
        <div class="section__inner" style="text-align: center;">
            <dt-copy-text id="copyTextElement" value="<?php echo $share_link; ?>" <?php language_attributes(); ?>>
            </dt-copy-text>

            <span id="help-text" class="help-text cloak">
                <?php esc_html_e( 'Copy this link and share it with people you are coaching.', 'disciple-tools-autolink' ); ?>
            </span>
          <span id="copyMessage" class="help-text cloak" style="display: none; color: #139513; font-weight: bold;">
                Link copied! Share it with your coaching participants.
                <br>Need help? <a href="https://vimeo.com/854811711" target="_blank" style="color: #235463">Watch this tutorial</a>.
          </span>
            <!-- Copy Confirmation Message -->
            <div id="copyToast" class="copy-toast">
                <span><?php esc_html_e( 'Link copied!', 'disciple-tools-autolink' ); ?></span>
                <button class="close-btn" id="hide-copy-toast">✖</button>
            </div>
        </div>
    </dt-tile>
</div>
