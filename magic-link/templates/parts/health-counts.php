<?php
$group = DT_Posts::get_post( 'groups', $church['ID'] );
?>
<div class="church-counts">
    <?php foreach ( $church_count_fields as $key => $field ) : ?>
        <div class="church-count">
            <span class="church-count__value"><?php echo $group[$key] ?? 0; ?></span>
            <span class="church-count__label"><?php echo $field['name']; ?></span>
            <img class="church-count__icon" src="<?php echo $field['icon']; ?>" alt="<?php echo $field['name']; ?>" width="25" height="25">
        </div>
    <?php endforeach; ?>
</div>
