<html>

<head>
    <!-- First add the elements you need in <head>; then last, add: -->
	<?php wp_head(); ?>
</head>

<body>
<h1>
    Hello, <?php echo esc_html( $name ); ?>!
</h1>
</body>


<?php wp_footer(); ?>

</html>