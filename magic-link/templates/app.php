<?php
/**
 * @var $logo_url string
 * @var $greeting string
 * @var $user_name string
 * @var $coached_by_label string
 * @var $coach_name string
 * @var $link_heading string
 */
?>
<?php include( 'parts/navbar.php' ); ?>

<div class="container app">
    <strong class="greeting">
        <?php echo esc_html( $greeting ); ?>
    </strong>
    <h1 class="user_name"><?php echo esc_html( $user_name ); ?></h1>
    <strong class="coached_by">
        <?php echo esc_html( $coached_by_label ); ?> <?php echo esc_html( $coach_name ); ?>
    </strong>

    <dt-tile>
        <h2><?php echo esc_html($link_heading); ?></h2>
    </dt-tile>
</div>
