<?php
/**
 * @var $group
 */
use function DT\Autolink\group_label;
?>
<div class="group">
	<div class="buttons">
		<dt-button context="inactive"
		           class="group__close">
			<?php _e("Close", 'disciple-tools-autolink'); ?> &nbsp; <dt-icon icon="ic:baseline-close"></dt-icon>
		</dt-button>
	</div>

	<div class="section__inner">
		<?php $this->insert( 'groups/form', get_defined_vars() ); ?>
	</div>
</div>
