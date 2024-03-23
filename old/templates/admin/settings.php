<div class="wrap">
    <h2><?php echo esc_html( $page_title ) ?></h2>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo esc_attr( $link ) . 'general' ?>"
           class="nav-tab <?php echo esc_html( ( $tab == 'general' || ! isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">
			<?php esc_html_e( 'General', 'disciple-tools-autolink' ) ?>
        </a>
    </h2>

	<?php $tab->content(); ?>
</div><!-- End wrap -->