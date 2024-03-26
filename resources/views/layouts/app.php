<?php
	use function DT\Autolink\logo_url;
	$this->layout('layouts/plugin');
?>

<?php $this->unshift('header') ?>
  <header class="navbar">
    <a href="/autolink" title="<?php esc_attr_e( 'Autolink Home', 'disciple-tools-autolink' ) ?>">
      <img class="navbar__logo" src="<?php echo esc_url( logo_url() ) ?>">
    </a>
    <al-menu></al-menu>
  </header>
<?php $this->end() ?>


<?php echo $this->section('header'); ?>

<div class="app">

	<?php echo $this->section('top'); ?>

	<?php echo $this->section('content'); ?>
</div>