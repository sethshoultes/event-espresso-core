<?php

function event_espresso_manage_templates() {
	global $wpdb, $org_options, $notices;
	//print_r($org_options);
	if (isset($_POST['update_org']) && check_admin_referer('espresso_form_check', 'ee_template_settings_update') ) {
		$org_options['display_description_on_multi_reg_page'] = empty($_POST['display_description_on_multi_reg_page']) ? '' : $_POST['display_description_on_multi_reg_page'];
		$org_options['display_short_description_in_event_list'] = $_POST['display_short_description_in_event_list'];
		$org_options['display_address_in_event_list'] = $_POST['display_address_in_event_list'];
		$org_options['display_address_in_regform'] = $_POST['display_address_in_regform'];
		$org_options['thunbnail_popup_lists'] = $_POST['thunbnail_popup_lists'];
		$org_options['use_custom_post_types'] = $_POST['use_custom_post_types'];
		$org_options['enable_default_style'] = $_POST['enable_default_style'];
		$org_options['selected_style'] = $_POST['selected_style'];
		$org_options['style_color'] = $_POST['style_color'];
		$org_options['style_themeroller'] = $_POST['style_themeroller'];

		update_option('events_organization_settings', $org_options);
		
		$notices['updates'][] = __('Template Settings Updated', 'event_espresso') ;
	}
	
	$values = array(
			array('id' => 'Y', 'text' => __('Yes', 'event_espresso')),
			array('id' => 'N', 'text' => __('No', 'event_espresso'))
	);

	?>

<div class="wrap">
  <div id="icon-options-event" class="icon32"> </div>
  <h2>
    <?php _e('Event Template Settings', 'event_espresso'); ?>
  </h2>
  <?php do_action('espresso_admin_notices'); ?>
  <div id="poststuff" class="metabox-holder has-right-sidebar">
    <?php event_espresso_display_right_column(); ?>
    <div id="post-body">
      <div id="post-body-content">
        <form id="template-settings-form" class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
          <div class="meta-box-sortables ui-sortables">
            <?php #### metaboxes ####  ?>
            <div class="metabox-holder">
              <div class="postbox template-gen-settings">
                <div title="Click to toggle" class="handlediv"><br />
                </div>
                <h3 class="hndle">
                  <?php _e('Template Options', 'event_espresso'); ?>
                </h3>
                <div class="inside">
                  <div class="padding">
                    <?php
						if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'includes/admin-files/templates/event_template_settings.php')) {
							require_once(EVENT_ESPRESSO_PLUGINFULLPATH . 'includes/admin-files/templates/event_template_settings.php');
						}
					?>
                    <p class="submit-buttons">
                      <input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Options', 'event_espresso'); ?>" id="save_organization_setting_1" />
                    </p>
                  </div>
                  <!-- / .padding --> 
                </div>
                <!-- / .inside --> 
              </div>
              <!-- / .postbox --> 
            </div>
            <!-- / .metabox-holder -->
            
            <div class="metabox-holder">
              <div class="postbox">
                <div title="Click to toggle" class="handlediv"><br />
                </div>
                <h3 class="hndle">
                  <?php _e('Template Stylesheet Options', 'event_espresso'); ?>
                </h3>
                <div class="inside">
                  <div class="padding">
                    <ul id="ee-styles">
                      <li>
                        <label>
                          <?php _e('Enable built in style sheets?', 'event_espresso'); ?>
                        </label>
                        <?php echo select_input('enable_default_style', $values, isset($org_options['enable_default_style']) ? $org_options['enable_default_style'] : ''); ?> <a class="thickbox"  href="#TB_inline?height=400&amp;width=500&amp;inlineId=enable_styles_info" target="_blank"><img src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>images/question-frame.png" width="16" height="16" alt="" /></a> </li>
                      <li>
                        <?php include(EVENT_ESPRESSO_PLUGINFULLPATH . 'includes/admin-files/templates/event_styles_settings.php'); ?>
                      </li>
                    </ul>
                    <p>
                      <input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Options', 'event_espresso'); ?>" id="save_organization_setting_2" />
                    </p>
                  </div>
                  <!-- / .padding --> 
                </div>
                <!-- / .inside --> 
              </div>
              <!-- / .postbox --> 
            </div>
            <!-- / .metabox-holder -->
            
            <?php if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'includes/admin-files/templates/template_files.php')) { ?>
            <h2>
              <?php _e('Developers Only', 'event_espresso') ?>
            </h2>
            <hr />
            <div class="metabox-holder">
              <div class="postbox">
                <div title="Click to toggle" class="handlediv"><br />
                </div>
                <h3 class="hndle">
                  <?php _e('Developer templates', 'event_espresso'); ?>
                </h3>
                <div class="inside">
                  <div class="padding">
                    <?php require_once(EVENT_ESPRESSO_PLUGINFULLPATH . 'includes/admin-files/templates/template_files.php'); ?>
                  </div>
                  <!-- / .padding --> 
                </div>
                <!-- / .inside --> 
              </div>
              <!-- / .postbox --> 
            </div>
            <!-- / .metabox-holder -->
            
            <?php } ?>
            <?php #### finish metaboxes ####  ?>
          </div>
          <!-- / .meta-box-sortables -->
          <?php  // create our nonces and do our form submit ?>
          <?php wp_nonce_field( 'espresso_form_check', 'ee_template_settings_update' ); ?>
          <input type="hidden" name="update_org" value="update" />
        </form>
        <?php include_once(EVENT_ESPRESSO_PLUGINFULLPATH . 'includes/admin-files/templates/templates_help.php'); ?>
      </div>
      <!-- / #post-body-content --> 
    </div>
    <!-- / #post-body --> 
  </div>
  <!-- / #poststuff --> 
</div>
<!-- / #wrap --> 
<script type="text/javascript" charset="utf-8">
		//<![CDATA[
		jQuery(document).ready(function() {
			postboxes.add_postbox_toggles('template_conf');

		});
		//]]>
	</script>
<?php
	return;
}

