<div class="autolink  al-cloak <?php echo esc_attr( $app_class ?? "app--default" ); ?>">
  <div class="autolink__inner">
	  <?php
      echo $this->section( 'content' )
		?>
  </div>
  <footer class="footer">
    <p><?php esc_html_e( 'Powered by', 'disciple-tools-autolink' ) ?>  <a href="https://disciple.tools/">Disciple.Tools</a></p>
  </footer>
</div>