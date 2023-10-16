<?php include 'parts/app-header.php'; ?>
<?php include 'parts/app-greeting.php'; ?>

<?php include 'parts/church-view-tabs.php'; ?>

    <div class="container">
        <app-groups-tree
                title="<?php echo esc_attr( $translations['tree_title'] ); ?>"
                unassignedTitle="<?php echo esc_attr( $translations['unassigned_title'] ); ?>"
                unassignedTip="<?php echo esc_attr( $translations['unassigned_tip'] ); ?>"
                keyTitle="<?php echo esc_attr( $translations['key_title'] ); ?>"
                assignedLabel="<?php echo esc_attr( $translations['assigned_label'] ); ?>"
                coachedLabel="<?php echo esc_attr( $translations['coached_label'] ); ?>"
                generationLabel="<?php echo esc_attr( $translations['generation_label'] ); ?>"
                noGroupsMessage="<?php echo esc_attr( $translations['no_groups_message'] ); ?>"
                endpoint="<?php echo esc_url( $fetch_url ); ?>"
        ></app-groups-tree>
    </div>

<?php include 'parts/app-footer.php'; ?>