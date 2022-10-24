<?php
$group = DT_Posts::get_post( 'groups', $church['ID'] );
?>
<div class="church__counts cloak">
    <?php foreach ( $church_count_fields as $key => $field ) : ?>
        <div class="church__count">
            <span class="count__value"><?php echo esc_html( $group[$key] ?? 0 ); ?></span>
            <img class="count__icon" src="<?php echo esc_html( $field['icon'] ); ?>" alt="<?php echo esc_attr( $field['name'] ); ?>" width="25" height="25">
        </div>
    <?php endforeach; ?>
</div>
