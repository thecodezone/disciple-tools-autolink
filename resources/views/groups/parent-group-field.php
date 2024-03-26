<?php
/**
 * @var string $parent_group_label
 * @var string $parent_group
 * @var array $parent_group_options
 * @var bool $allow_parent_group_selection
 */
?>

<?php if ( count( $parent_group_options ) ): ?>
	<?php if ( $allow_parent_group_selection ): ?>
		<dt-single-select
			class="create-group__input"
			label="<?php echo esc_html( $parent_group_label ); ?>"
			name="parent_group"
			value="<?php echo esc_attr( $parent_group ) ?>"
			options='<?php echo esc_attr( json_encode( $parent_group_options ) ) ?>'
		></dt-single-select>
	<?php else : ?>
		<dt-text type="hidden" name="parent_group" value="<?php echo esc_attr( $parent_group ) ?>">
	<?php endif; ?>
<?php endif; ?>