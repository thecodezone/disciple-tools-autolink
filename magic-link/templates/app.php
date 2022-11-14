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
<?php include( 'parts/header.php' ); ?>
<?php include( 'parts/navbar.php' ); ?>

<div class="container app">
    <strong class="greeting">
        <?php echo esc_html( $greeting ); ?>
    </strong>
    <h1 class="user_name"><?php echo esc_html( $user_name ); ?></h1>
    <strong class="coached_by">
        <?php echo esc_html( $coached_by_label ); ?> <?php echo esc_html( $coach_name ); ?>
    </strong>

    <dt-tile title="<?php echo esc_attr( $link_heading ); ?>">
        <dt-copy-text value="<?php echo esc_attr( $share_link ); ?>"></dt-copy-text>
        <span class="help-text cloak">
            <?php echo esc_html( $share_link_help_text ) ?>
        </span>
    </dt-tile>

    <dt-tile class="churches" title="<?php echo esc_attr( $churches_heading ); ?>">
     <dt-button class="churches__add" context="success" href="<?php echo esc_url( $create_church_link ); ?>" rounded>
        <dt-icon icon="ic:baseline-plus"></dt-icon>
    </dt-button>
        <ul class="churches__list">
            <?php foreach ( $churches as $church ) : ?>
                <?php
                    //If the church is the first one the tile is open if not it is closed.
                    $app_church_opened = "";
                if ( $church === $churches[ array_key_first( $churches )] ) {
                    $app_church_opened = "opened";
                }
                ?>
                <dt-tile class="church" title="<?php echo esc_attr( $church['post_title'] ); ?>">
                    <dt-button class="church__link" context="link" href="<?php echo esc_url( site_url( 'groups/' . $church['ID'] ) ); ?>">
                        <dt-icon icon="material-symbols:link-rounded"></dt-icon>
                    </dt-button>
                    <?php include( "parts/health-counts.php" ); ?>
                    <app-church group='<?php echo wp_json_encode( $church ); ?>' fields='<?php echo wp_json_encode( $church_fields ); ?>' <?php echo esc_attr( $app_church_opened ); ?> apiRoot=<?php echo esc_attr( get_site_url() ); ?>> </app-church>
                </dt-tile>
            <?php endforeach; ?>
    </dt-tile>
</div>
<?php include( 'parts/footer.php' ); ?>
