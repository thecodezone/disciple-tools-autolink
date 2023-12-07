<header>
    <h1><?php $this->esc_html_e( 'Plugin', 'dt_plugin' ); ?></h1>
</header>

<div>
	<?php echo $this->section( 'content' ) ?>
</div>

<footer>
    <p>
		<?php $this->esc_html_e( 'Copyright ', 'dt_plugin' ); ?>

		<?php echo $this->e( gmdate( 'Y' ) ); ?>
    </p>
</footer>