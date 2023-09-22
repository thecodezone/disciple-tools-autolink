<div class="wrap">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">

				<?php if ( $error ): ?>
                    <div class="notice notice-error is-dismissible">
                        <p>
							<?php echo esc_attr( $error ) ?>
                        </p>
                    </div>
				<?php endif; ?>

                <!-- Main Column -->

                <form method="post">
					<?php wp_nonce_field( 'dt_admin_form', 'dt_admin_form_nonce' ) ?>
                    <table class="widefat striped">
                        <thead>
                        <tr>
                            <th><?php _e( 'Options', 'disciple-tools-autolink' ) ?></th>
                            <th style="width: 99%;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="white-space: nowrap;">
								<?php _e( 'Allow parent group selection?', 'disciple-tools-autolink' ) ?>
                            </td>
                            <td style="text-align: left;">
                                <input type="checkbox"
                                       name="disciple_tools_autolink_allow_parent_group_selection"
                                       value="1"
								       <?php if ( $old['disciple_tools_autolink_allow_parent_group_selection'] === '1' ): ?>checked<?php endif; ?> />
                            </td>
                        </tr>
                        <tr>
                            <td style="white-space: nowrap;">
								<?php echo esc_attr( $training_videos_translations['label'] ) ?>
                            </td>
                            <td style="text-align: left;">
                                <div style="max-width: 600px">
                                    <admin-training-videos-field
                                            name="disciple_tools_autolink_training_videos"
                                            value='<?php echo esc_attr( $old['disciple_tools_autolink_training_videos'] ) ?>'
                                            default='<?php echo esc_attr( $default_training_videos ) ?>'
                                            translations='<?php echo esc_attr( json_encode( $training_videos_translations ) ) ?>'
                                    ></admin-training-videos-field>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button class="button">
									<?php _e( 'Save', 'disciple-tools-autolink' ) ?>
                                </button>
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
                <br>

                <!-- End Main Column -->
            </div><!-- end post-body-content -->
            <div id="postbox-container-1" class="postbox-container">
                <!-- Right Column -->

                <!-- Box -->
                <table class="widefat striped">
                    <thead>
                    <tr>
                        <th>
							<?php _e( 'Help', 'disciple-tools-autolink' ) ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <b>
								<?php _e( 'Allow parent group selection?', 'disciple-tools-autolink' ) ?>
                            </b>
                        </td>
                    </tr>
                    <tr>
                        <td>

                            <p>
								<?php _e( "If enabled, the user will be able to select a parent group when creating a new group.", 'disciple-tools-autolink' ) ?>
                            </p>

                            <p>
								<?php _e( "If disabled, the group will be assigned to the first leader's first group.", 'disciple-tools-autolink' ) ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
								<?php _e( 'Training Videos', 'disciple-tools-autolink' ) ?>
                            </b>
                        </td>
                    </tr>
                    <tr>
                        <td>

                            <p>
								<?php _e( 'Training vidoes are available from within the AutoLink main menu.', 'disciple-tools-autolink' ) ?>
                            </p>
                            <p>
                                <a class="button" href="<?php echo esc_attr( $training_videos_url ) ?>">
									<?php _e( 'View Training Videos', 'disciple-tools-autolink' ) ?>
                                </a>
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>

                <!-- End Right Column -->
            </div><!-- postbox-container 1 -->
            <div id="postbox-container-2" class="postbox-container">
            </div><!-- postbox-container 2 -->
        </div><!-- post-body meta box container -->
    </div><!--poststuff end -->
</div><!-- wrap end -->

