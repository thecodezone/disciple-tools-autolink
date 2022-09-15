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

    <dt-tile title="<?php echo esc_attr($link_heading); ?>">
        <!-- TODO: Make this a dt-copy-text component -->
        <dt-text value="<?php echo esc_attr($share_link); ?>"></dt-text>
        <span class="help-text">
            <?php echo esc_html($share_link_help_text) ?>
        </span>
    </dt-tile>

    <dt-tile title="<?php echo esc_attr($churches_heading); ?>">
    </dt-tile>
</div>
