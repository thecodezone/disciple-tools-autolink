<?php include 'parts/app-header.php'; ?>
<?php include 'parts/app-greeting.php'; ?>

<?php include 'parts/church-view-tabs.php'; ?>

    <div class="container">
        <app-groups-tree
                title="<?php echo esc_attr( $translations['tree_title'] ); ?>"
                unassigned-title="<?php echo esc_attr( $translations['unassigned_title'] ); ?>"
                unassigned-tip="<?php echo esc_attr( $translations['unassigned_tip'] ); ?>"
                key-title="<?php echo esc_attr( $translations['key_title'] ); ?>"
                assigned-label="<?php echo esc_attr( $translations['assigned_label'] ); ?>"
                endpoint="<?php echo esc_url( $fetch_url ); ?>"
        ></app-groups-tree>
    </div>

<?php include 'parts/app-footer.php'; ?>