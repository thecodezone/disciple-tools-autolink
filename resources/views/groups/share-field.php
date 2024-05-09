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
use function DT\Autolink\group_leader_share_url;

$user_repository = container()->make( UserRepository::class );
$user_name = $user_repository->display_name();
$coach_name = $user_repository->coach_name();
?>
<dt-copy-text value="<?php echo esc_url( group_leader_share_url($group_id) ); ?>" <?php language_attributes(); ?>></dt-copy-text>
