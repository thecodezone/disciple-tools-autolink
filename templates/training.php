<?php include 'parts/app-header.php'; ?>

<div class="container">
	<?php foreach ( $videos as $video ): ?>
        <dt-tile title="<?php echo esc_attr( $video->title ) ?>">
            <div class="section__inner">
                <div class="embed embed--video">
					<?php
					/* phpcs:ignore */
					echo $video->embed
					?>
                </div>
            </div>
        </dt-tile>
	<?php endforeach; ?>
</div>

<?php include 'parts/app-footer.php'; ?>
