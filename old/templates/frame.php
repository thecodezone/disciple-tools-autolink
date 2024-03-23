<?php include 'parts/header.php'; ?>

<style>
    body#blank-template-body {
        border-top: 0px;
    }
</style>

<div class="frame__container">
    <iframe src="<?php echo esc_attr( $src ); ?>" width="100%" height="100%" style="border: none;" class="frame"></iframe>
</div>

<div class="frame__footer">
    <dt-button context="success" href="<?php echo esc_attr( $back_link ); ?>" title="<?php echo esc_html( $back_label ); ?>">
        <?php echo esc_html( $back_label ); ?>
    </dt-button>
</div>
