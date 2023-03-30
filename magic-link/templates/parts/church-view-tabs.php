<div class="container">
     <dt-button context="<?php echo empty( $action ) ? 'parimary' : 'inactive'; ?>" href="<?php echo esc_url( $app_url ); ?>">
        <?php echo esc_html( $church_health_label ); ?>
    </dt-button>
    <dt-button context="<?php echo $action === 'tree' ? 'parimary' : 'inactive'; ?>" href="<?php echo esc_url( $app_url . "?" . http_build_query( [ 'action' => 'tree' ] ) ); ?>">
      <?php echo esc_html( $tree_label ); ?>
    </dt-button>
    <?php if ( !class_exists( 'DT_Tree_Groups_chart' ) ): ?>
    <dt-button context="<?php echo $action === 'genmap' ? 'parimary' : 'inactive'; ?>" href="<?php echo esc_url( $app_url . "?" . http_build_query( [ 'action' => 'genmap' ] ) ); ?>">
        <?php echo esc_html( $genmap_label ); ?>
    </dt-button>
    <?php endif; ?>
</div>