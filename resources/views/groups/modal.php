<?php
/**
 * @var $group
 */
use function DT\Autolink\group_label;
?>
<div class="group">
	<div class="buttons">
    <?php if ($group) : ?>
      <dt-button context="success"
                 class="group__add">
	      <?php echo esc_html_e("New", 'disciple-tools-autolink'); ?> <?php esc_html_e( group_label() ); ?> &nbsp; <dt-icon icon="ic:baseline-plus"></dt-icon>
      </dt-button>
    <?php endif; ?>

		<dt-button context="inactive"
		           class="group__close">
			<?php _e("Close", 'disciple-tools-autolink'); ?> &nbsp; <dt-icon icon="ic:baseline-close"></dt-icon>
		</dt-button>
	</div>

    <?php if ( ! empty( $group_id ) ): ?>
        <!--        --><?php //if ( request()->wantsJson() ): ?>
        <?php $this->insert( 'groups/share-field', get_defined_vars() ); ?>
        <!--        --><?php //endif; ?>
    <?php endif; ?>

	<?php $this->insert( 'groups/form', get_defined_vars() ); ?>
</div>
