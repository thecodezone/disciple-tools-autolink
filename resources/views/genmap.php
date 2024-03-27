<?php
use function DT\Autolink\group_label;
$this->layout( 'layouts/tool', [ 'action' => 'genmap' ] );
?>

<div class="container--wide">
	<dt-tile title="<?php echo esc_attr( group_label() ) . ' ' . esc_html( 'Generation Map', 'disciple-tools-autolink' ); ?>">
		<div class="section__inner">
			<div class="genmap">
				<div id="chart"></div>
			</div>
		</div>
	</dt-tile>
</div>
