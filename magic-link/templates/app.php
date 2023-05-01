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
 * @var $church_health_label string
 * @var $tree_label string
 * @var $genmap_label string
 * @var $view_church_label string
 * @var $create_church_link string
 * @var $edit_church_link string
 * @var $contact array
 * @var $coach array
 * @var $group_fields array
 * @var $error string
 * @var $app_church_opened string
 * @var $app_link string
 * @var $app_url string
 * @var $app_link_label string
 * @var $app_link_help_text string
 * @var $church_fields array
 * @var $group_link string
 * @var $view_group_label string
 * @var $create_group_link string
 * @var $edit_group_link string
 * @var $group_health_label string
 * @var $group array
 * @var $edit_group_label string
 * @var $delete_group_label string
 * @var $delete_group_link string
 * @var $delete_group_confirm string
 */
?>
<?php include( 'parts/app-header.php' ); ?>

<?php include( 'parts/church-view-tabs.php' ); ?>

<div class="container">
     <?php if ( $error ): ?>
                <dt-alert context="alert"
                        dismissable>
                    <?php echo esc_html( $error ); ?>
                </dt-alert>
            <?php endif; ?>
    <dt-tile class="churches" title="<?php echo esc_attr( $churches_heading ); ?>">
        <div class="section__inner">
            <dt-button class="churches__add" context="success" href="<?php echo esc_url( $create_group_link ); ?>" rounded>
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
                            <app-church
                                group='<?php echo esc_attr( wp_json_encode( $church ) ); ?>'
                                fields='<?php echo esc_attr( wp_json_encode( $church_fields ) ); ?>' <?php echo esc_attr( $app_church_opened ); ?>>
                            </app-church>
                            <app-church-menu>
                                 <dt-button context="primary" href="<?php echo esc_url( $group_link . '&' . http_build_query([ 'post' => $church['ID'], 'return' => $app_link ]) ); ?>">
                                    <?php echo esc_html( $view_group_label ); ?>
                                </dt-button>
                                <dt-button context="primary" href="<?php echo esc_url( $edit_group_link . '&' . http_build_query([ 'post' => $church['ID'] ]) ); ?>">
                                    <?php echo esc_html( $edit_group_label ); ?>
                                </dt-button>
                                 <dt-button context="alert" href="<?php echo esc_url( $delete_group_link . '&' . http_build_query( [ 'post' =>  $church['ID'] ] )); ?>" confirm="<?php echo esc_html( $delete_group_confirm ) ?>">
                                     <?php echo esc_html( $delete_group_label ); ?>
                                </dt-button>
                            </app-church-menu>
                        </church-tile>
                    <?php endforeach; ?>
                </lazy-reveal>
            </div>
        </div>
    </dt-tile>
</div>


<?php include( 'parts/app-footer.php' ); ?>

