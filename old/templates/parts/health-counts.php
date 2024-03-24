<?php
$group = DT_Posts::get_post( 'groups', $church['ID'], false, false );
?>
<div class="church__counts">
	<?php foreach ( $church_count_fields as $key => $field ) : ?>
        <div class="church__count"
             data-churchId="<?php echo esc_attr( $church['ID'] ); ?>"
             data-field="<?php echo esc_attr( $key ) ?>">
            <dt-modal context="default"
                      hideHeader>
                <div slot="openButton">
                    <img class="count__icon"
                         src="<?php echo esc_html( $field['icon'] ); ?>"
                         alt="<?php echo esc_attr( $field['name'] ); ?>"
                         width="25"
                         height="25">
                    <span class="count__value"><?php echo esc_html( $group[ $key ] ?? 0 ); ?></span>
                </div>
                <span slot="content">
                <al-church-health-field
                        id="<?php echo esc_attr( 'groups_' . $church['ID'] . '_' . $key ); ?>"
                        name="<?php echo esc_attr( $key ); ?>"
                        icon="<?php echo esc_html( $field['icon'] ); ?>"
                        label="<?php echo esc_attr( $field['name'] ); ?>"
                        onchange=""
                        value="<?php echo esc_html( $group[ $key ] ?? 0 ); ?>"
                        postType="groups"
                        postID="<?php echo esc_attr( $church['ID'] ) ?>"
                        apiRoot="<?php echo esc_attr( "/wp-json/" ) ?>"
                        min="0"
                        placeholder="0"
                        nonce=<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>
                ></al-church-health-field>
            </span>
            </dt-modal>
        </div>
	<?php endforeach; ?>
</div>
