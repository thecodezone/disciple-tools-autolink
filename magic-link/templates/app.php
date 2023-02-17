<?php

/**
 * @var $logo_url string
 * @var $greeting string
 * @var $user_name string
 * @var $coached_by_label string
 * @var $coach_name string
 * @var $link_heading string
 * @var $share_link string
 * @var $share_link_help_text string
 * @var $churches_heading string
 * @var $churches array;
 */
?>
<?php include( 'parts/app-header.php' ); ?>

<?php include( 'parts/church-view-tabs.php' ); ?>

<div class="container">
    <dt-tile class="churches" title="<?php echo esc_attr( $churches_heading ); ?>">
        <div class="section__inner">
            <dt-button class="churches__add" context="success" href="<?php echo esc_url( $create_church_link ); ?>" rounded>
                <dt-icon icon="ic:baseline-plus"></dt-icon>
            </dt-button>
            <div class="churches__list">
                <lazy-reveal>
                    <?php foreach ( $churches as $church ) : ?>
                        <?php
                        //If the church is the first one the tile is open if not it is closed.
                        $app_church_opened = "";
                        if ( $church === $churches[ array_key_first( $churches )] ) {
                            $app_church_opened = "opened";
                        }
                        ?>
                        <church-tile class="church" title="<?php echo esc_attr( $church['post_title'] ); ?>">
                            <?php include( "parts/health-counts.php" ); ?>
                            <app-church group='<?php echo esc_attr( wp_json_encode( $church ) ); ?>' fields='<?php echo esc_attr( wp_json_encode( $church_fields ) ); ?>' <?php echo esc_attr( $app_church_opened ); ?>> </app-church>
                            <dt-button class="church__link" context="link" href="<?php echo esc_url( site_url( 'groups/' . $church['ID'] ) ); ?>">
                                <dt-icon icon="ic:baseline-link"></dt-icon>
                            </dt-button>
                        </church-tile>
                    <?php endforeach; ?>
                </lazy-reveal>
            </div>
        </div>
    </dt-tile>
</div>


<?php include( 'parts/app-footer.php' ); ?>

