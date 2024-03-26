<?php
use function DT\Autolink\magic_url;
use function DT\Autolink\groups_label;
$action = $action ?? null;
?>

<div class="container">
	<dt-button context="<?php echo !$action ? 'primary' : 'inactive'; ?>"
	           href="<?php echo esc_url( magic_url() ); ?>">
		<?php echo esc_html( __( "My ", 'disciple-tools-autolink' ) . groups_label() ); ?>
	</dt-button>
	<dt-button context="<?php echo $action === 'tree' ? 'primary' : 'inactive'; ?>"
	           href="<?php echo esc_url( magic_url( 'tree' ) ) ?>">
		<?php echo esc_html( __( 'Tree', 'disciple-tools-autolink' ) ); ?>
	</dt-button>
	<?php if ( function_exists( 'dt_genmapper_metrics' ) ): ?>
		<dt-button context="<?php echo $action === 'genmap' ? 'primary' : 'inactive'; ?>"
		           href="<?php echo esc_url( magic_url( 'genmap' ) ); ?>">
			<?php echo esc_html( __( 'GenMap', 'disciple-tools-autolink' ) ); ?>
		</dt-button>
	<?php endif; ?>
</div>
