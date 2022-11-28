<?php
$group = DT_Posts::get_post( 'groups', $church['ID'] );
?>
<div class="church__counts cloak">
    <?php foreach ( $church_count_fields as $key => $field ) :
        $field_name = str_replace( ' ', '_', strtolower( $field['name'] ) );
        ?>
        <div class="church__count" data-churchid="<?php echo esc_attr( $church['ID'] ); ?>" data-field="<?php echo esc_attr( $field_name ) ?>">
        <dt-modal context="default" hideHeader>
            <div slot="openButton">
                <span class="count__value"><?php echo esc_html( $group[$key] ?? 0 ); ?></span>
                <img class="count__icon" src="<?php echo esc_html( $field['icon'] ); ?>" alt="<?php echo esc_attr( $field['name'] ); ?>" width="25" height="25">
            </div>
            <span slot="content">
                <dt-number id="<?php echo esc_attr( 'group_' . $church['ID'] . '_' . $field_name ); ?>" name="<?php echo esc_attr( $field_name ); ?>" icon="<?php echo esc_html( $field['icon'] ); ?>" label="<?php echo esc_attr( $field['name'] ); ?>" onchange="" value="<?php echo esc_html( $group[$key] ?? 0 ); ?>" postType="groups" postID= <?php echo esc_attr( $church['ID'] ) ?> apiRoot=<?php echo esc_attr( rest_url() ) ?> nonce=<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?> ></dt-number>
            </span>
            </dt-modal>
        </div>
    <?php endforeach; ?>
</div>
