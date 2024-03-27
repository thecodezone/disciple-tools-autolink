<?php
/**
* @var array $videos
 */
$this->layout('layouts/tool', ['action' => 'training']);
?>
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