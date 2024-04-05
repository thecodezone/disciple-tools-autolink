<?php
use function DT\Autolink\route_url;
use function DT\Autolink\groups_label;
// phpcs:ignore
$action = $action ?? null;
?>

<div class="container">
    <?php if ( function_exists( 'dt_genmapper_metrics' ) ): ?>
        <dt-button context="<?php echo $action === 'genmap' ? 'primary' : 'inactive'; ?>"
                   href="<?php echo esc_url( route_url( '' ) ); ?>">
            <?php echo esc_html( __( 'GenMap', 'disciple-tools-autolink' ) ); ?>
        </dt-button>
    <?php endif; ?>
	<dt-button context="<?php echo $action === 'coaching-tree' ? 'primary' : 'inactive'; ?>"
	           href="<?php echo esc_url( route_url( 'coaching-tree' ) ) ?>">
		<?php echo esc_html( __( 'Coaching Tree', 'disciple-tools-autolink' ) ); ?>
	</dt-button>
    <dt-button context="<?php echo !$action ? 'primary' : 'inactive'; ?>"
               href="<?php echo esc_url( route_url('groups') ); ?>">
        <?php echo esc_html( __( "My ", 'disciple-tools-autolink' ) . groups_label() ); ?>
    </dt-button>
</div>
