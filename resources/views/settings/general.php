<?php
use function DT\Autolink\namespace_string;

/**
 * @var string $tab
 * @var string $link
 * @var string $page_title
 * @var string $error
 * @var array $old
 * @var string $default_training_videos
 * @var array $training_videos_translations
 */
?>
<?php
$this->layout( 'layouts/settings', compact( 'tab', 'error' ) )
?>

<form method="post">
  <?php wp_nonce_field( 'dt_admin_form_nonce' ) ?>
  <table class="widefat striped">
    <thead>
    <tr>
      <th><?php esc_html_e( 'Options', 'disciple-tools-autolink' ) ?></th>
      <th style="width: 99%;"></th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="white-space: nowrap;">
    <?php esc_html_e( 'Allow parent group selection?', 'disciple-tools-autolink' ) ?>
      </td>
      <td style="text-align: left;">
        <input type="checkbox"
               name="allow_parent_group_selection"
               value="1"
           <?php if ( $old['allow_parent_group_selection'] === '1' ): ?>checked<?php endif; ?> />
      </td>
    </tr>
    <tr>
      <td style="white-space: nowrap;">
    <?php esc_html_e( 'Add main DT menu link?', 'disciple-tools-autolink' ) ?>
      </td>
      <td style="text-align: left;">
        <input type="checkbox"
               name="show_in_menu"
               value="1"
           <?php if ( $old['show_in_menu'] === '1' ): ?>checked<?php endif; ?> />
      </td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">
            <?php esc_html_e( 'Show nested Genmap?', 'disciple-tools-autolink' ) ?>
        </td>
        <td style="text-align: left;">
            <input type="checkbox"
                   name="show_nested_genmap"
                   value="1"
                   <?php if ( $old['show_nested_genmap'] === '1' ): ?>checked<?php endif; ?> />
        </td>
    </tr>
    <tr>
      <td style="white-space: nowrap;">
    <?php echo esc_attr( $training_videos_translations['label'] ) ?>
      </td>
      <td style="text-align: left;">
        <div style="max-width: 600px">
          <al-training-videos-field
            name="training_videos"
            value='<?php echo esc_attr( wp_json_encode( $old['training_videos'] ) ) ?>'
            default='<?php echo esc_attr( wp_json_encode( $default_training_videos ) ) ?>'
            translations='<?php echo esc_attr( json_encode( $training_videos_translations ) ) ?>'
          ></al-training-videos-field>
        </div>
      </td>
    </tr>
    <tr>
      <td>
        <button class="button">
      <?php esc_html_e( 'Save', 'disciple-tools-autolink' ) ?>
        </button>
      </td>
      <td></td>
    </tr>
    </tbody>
  </table>
</form>

<?php $this->start( 'right' ) ?>

    <!-- Add some content to the right side -->

<?php $this->stop() ?>
