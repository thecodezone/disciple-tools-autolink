<?php
use function DT\Autolink\route_url;
$this->layout( 'layouts/tool', [ 'action' => 'genmap' ] );

// phpcs:ignore
$chart_type = $chart_type ?? null;
?>

<div class="container--wide">
  <div class="genmap">
    <div class="container">
        <dt-button context="<?php echo $chart_type === 'circles' ? 'primary' : 'inactive'; ?>"
                   href="<?php echo esc_url( route_url( 'genmap/switch?chart=circles' ) ); ?>">
            <?php echo esc_html( __( 'Church Circles', 'disciple-tools-autolink' ) ); ?>
        </dt-button>
        <dt-button context="<?php echo $chart_type === 'tree' ? 'primary' : 'inactive'; ?>"
                   href="<?php echo esc_url( route_url( 'genmap/switch?chart=tree' ) ); ?>">
            <?php echo esc_html( __( 'Tree', 'disciple-tools-autolink' ) ); ?>
        </dt-button>
    </div>
    <div id="chart"></div>
  </div>
</div>
