<?php if ( count( $parent_group_options ) ): ?>
    <dt-single-select
            class="create-group__input"
            label="<?php echo esc_html( $parent_group_label ); ?>"
            name="parent_group"
            value="<?php echo esc_attr( $parent_group ) ?>"
            options='<?php echo esc_attr( json_encode( $parent_group_options ) ) ?>'
    ></dt-single-select>
<?php endif; ?>