<?php
use DT\Autolink\Services\Language;
use function DT\Autolink\logo_url;
$this->layout( 'layouts/plugin' );
$user = wp_get_current_user();
$lang = new Language();
$available_languages = $lang->get_available_languages( $user->ID );
?>

<?php $this->unshift( 'header' ) ?>
  <header class="navbar">
    <a href="/autolink" title="<?php esc_attr_e( 'Autolink Home', 'disciple-tools-autolink' ) ?>">
      <img class="navbar__logo" src="<?php echo esc_url( logo_url() ) ?>">
    </a>
   <al-menu data-lang="<?php echo esc_attr( json_encode( $available_languages ) ); ?>"></al-menu>
  </header>


<?php echo $this->section( 'header' ); ?>

<div class="app">

	<?php echo $this->section( 'top' ); ?>

	<?php echo $this->section( 'content' ); ?>
</div>
