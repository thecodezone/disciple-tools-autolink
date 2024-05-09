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
        <div class="container">
            <dt-tile class="app__link" title="<?php echo esc_attr( __( 'Share', 'disciple-tools-autolink' ) ); ?> <?php  esc_html_e( \DT\Autolink\group_label() );  ?>">
                <div class="section__inner">
                    <?php $this->insert( 'groups/share-field', get_defined_vars() ); ?>
                </div>
            </dt-tile>
            <dt-tile class="app__link" title="<?php echo esc_attr( __( 'Health Church', 'disciple-tools-autolink' ) ); ?>">
                <div class="section__inner">
                    <div class="churches__list">
                        <al-church
                            group='<?php echo htmlspecialchars(json_encode($group)); ?>'
                            fields='<?php echo esc_attr(wp_json_encode($church_fields)); ?>'
                            opened='<?php echo htmlspecialchars(json_encode($opened)); ?>'
                        ></al-church>
                    </div>
                </div>
            </dt-tile>
        </div>

        <dt-tile title="<?php echo esc_attr( __( 'Edit', 'disciple-tools-autolink' ) ); ?> <?php  esc_html_e( \DT\Autolink\group_label() );  ?>"
        >
            <div class="section__inner">
                <?php $this->insert( 'groups/form', get_defined_vars() ); ?>
            </div>
        </dt-tile>
    <?php endif; ?>


</div>
