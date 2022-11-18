<?php
$group = DT_Posts::get_post( 'groups', $church['ID'] );
?>
<div class="church__counts cloak">
    <?php foreach ( $church_count_fields as $key => $field ) : ?>
        <div class="church__count">
        <dt-modal context="default" hideHeader>
            <div slot="openButton">
                <span class="count__value"><?php echo esc_html( $group[$key] ?? 0 ); ?></span>
                <img class="count__icon" src="<?php echo esc_html( $field['icon'] ); ?>" alt="<?php echo esc_attr( $field['name'] ); ?>" width="25" height="25">
            </div>
            <span slot="content">
                <dt-number id="<?php echo esc_attr( 'group_' . $church['ID'] . '_' . $field['name'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" icon="<?php echo esc_html( $field['icon'] ); ?>" label="<?php echo esc_attr( $field['name'] ); ?>" onchange="" value="<?php echo esc_html( $group[$key] ?? 0 ); ?>"></dt-number>
            </span>
            </dt-modal>
        </div>
    <?php endforeach; ?>
</div>
