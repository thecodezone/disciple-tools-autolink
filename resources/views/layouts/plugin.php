<header>
    <h1><?php $this->esc_html_e( 'Plugin', 'disciple_tools_autolink' ); ?></h1>
</header>

<div>
	<?php echo $this->section( 'content' ) ?>
</div>

<footer>
    <p>
		<?php $this->esc_html_e( 'Copyright ', 'disciple_tools_autolink' ); ?>

		<?php echo $this->e( gmdate( 'Y' ) ); ?>
    </p>
</footer>