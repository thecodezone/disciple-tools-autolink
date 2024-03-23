<?php include 'parts/app-header.php'; ?>
<?php include 'parts/app-greeting.php'; ?>
<?php include 'parts/church-view-tabs.php'; ?>

<div class="container">
	<?php if ( $error ): ?>
        <dt-alert context="alert"
                  dismissable>
			<?php echo esc_html( $error ); ?>
        </dt-alert>
	<?php endif; ?>
    <dt-tile class="churches">
        <div class="section__inner">
            <div class="churches__heading">
                <h3><?php echo esc_attr( $translations['groups_heading'] ); ?></h3>
                <dt-button class="churches__add"
                           context="success"
                           href="<?php echo esc_url( $links['create_group'] ); ?>"
                           rounded>
                    <dt-icon icon="ic:baseline-plus"></dt-icon>
                </dt-button>
            </div>

            <div class="churches__list">
                <app-churches
                        translations='<?php echo esc_attr( wp_json_encode( $translations ) ); ?>'
                        posts='<?php echo esc_attr( wp_json_encode( $churches['posts'] ) ); ?>'
                        total="<?php echo esc_attr( $churches['total'] ) ?>"
                        links='<?php echo esc_attr( wp_json_encode( $links ) ); ?>'
                        fields='<?php echo esc_attr( wp_json_encode( $church_fields ) ); ?>'
                        limit='<?php echo esc_attr( wp_json_encode( $limit ) ); ?>'
                        countFields='<?php echo esc_attr( wp_json_encode( $church_count_fields ) ); ?>'
                ></app-churches>
            </div>
        </div>
    </dt-tile>
</div>


<?php include 'parts/app-footer.php'; ?>

