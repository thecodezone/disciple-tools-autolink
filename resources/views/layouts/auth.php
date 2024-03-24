<div class="al__wrapper <?php echo esc_attr( $app_class ?? "al--default" ); ?>">
	<div class="al__inner">

		<?php echo $this->section('content'); ?>

	</div>
	<footer class="footer">
		<p><?php esc_html_e( 'Powered by', 'disciple-tools-autolink' ) ?>  <a href="https://disciple.tools/">Disciple.Tools</a></p>
	</footer>
</div>
