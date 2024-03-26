<?php
	/**
	 * @var $action
	 */
	$this->layout( 'layouts/app' );
?>

<?php $this->push( 'top' ); ?>
	<?php $this->insert( 'partials/greeting' ); ?>
	<?php $this->insert( 'partials/tools-menu', ['action' => $action ?? null] ); ?>
<?php $this->end(); ?>

<?php echo $this->section( 'content' ); ?>
