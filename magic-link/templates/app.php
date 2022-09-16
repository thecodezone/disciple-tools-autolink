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

    <dt-tile title="<?php echo esc_attr($link_heading); ?>">
        <!-- TODO: Make this a dt-copy-text component -->
        <dt-copy-text value="<?php echo esc_attr($share_link); ?>"></dt-copy-text>
        <span class="help-text">
            <?php echo esc_html($share_link_help_text) ?>
        </span>
    </dt-tile>

    <dt-tile title="<?php echo esc_attr($churches_heading); ?>">

        <!-- Loop through churches -->
        <dt-tile title="Church Name" class="church">
            <div class="church_health">
                <!-- Add 5 dummy icons with number to represent health metrics -->
                <!-- Skip the collapse icon and I will add that later -->
                <!-- Add a gray circle to match figma -->
                <!-- Church start date -->
            </div>
        </dt-tile>
    </dt-tile>
</div>

<?php include( 'parts/footer.php' ); ?>
