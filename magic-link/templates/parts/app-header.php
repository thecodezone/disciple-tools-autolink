<?php include( 'header.php' ); ?>
<?php include( 'navbar.php' ); ?>

<div class="app cloak">
    <div class="container">
        <strong class="greeting">
            <?php echo esc_html( $greeting ); ?>
        </strong>
        <h1 class="user_name"><?php echo esc_html( $user_name ); ?></h1>

        <?php if ( $coach_name ): ?>
            <strong class="coached_by">
                <?php echo esc_html( $coached_by_label ); ?> <?php echo esc_html( $coach_name ); ?>
            </strong>
        <?php endif; ?>

        <dt-tile title="<?php echo esc_attr( $link_heading ); ?>">
            <div class ="section__inner" >
                <dt-copy-text value="<?php echo esc_attr( $share_link ); ?>" <?php language_attributes(); ?>></dt-copy-text>
                <span class="help-text cloak">
                    <?php echo esc_html( $share_link_help_text ) ?>
                </span>
            </div>
        </dt-tile>
    </div>

