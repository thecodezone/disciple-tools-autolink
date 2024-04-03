<?php
/**
 * @var string $fetch_url
 * @var array $translations
 */
$this->layout( 'layouts/tool', [ "action" => "coaching-tree" ] );
?>

<div class="container">
	<al-coaching-tree
		title="<?php echo esc_attr( $translations['tree_title'] ); ?>"
		unassignedTitle="<?php echo esc_attr( $translations['unassigned_title'] ); ?>"
		unassignedTip="<?php echo esc_attr( $translations['unassigned_tip'] ); ?>"
		keyTitle="<?php echo esc_attr( $translations['key_title'] ); ?>"
		assignedLabel="<?php echo esc_attr( $translations['assigned_label'] ); ?>"
		coachedLabel="<?php echo esc_attr( $translations['coached_label'] ); ?>"
		generationLabel="<?php echo esc_attr( $translations['generation_label'] ); ?>"
		noGroupsMessage="<?php echo esc_attr( $translations['no_groups_message'] ); ?>"
		leadingLabel="<?php echo esc_attr( $translations['leading_label'] ); ?>"
		endpoint="<?php echo esc_url( $fetch_url ); ?>"
	></al-coaching-tree>
</div>