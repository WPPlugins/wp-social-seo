<?php

if ( isset( $_POST['grw'] ) && $_POST['grw'] !== '') {
	update_option( 'wp_social_seo_google_tab', $_POST['grw'] );
}




if (isset($_POST['grw_active']) && isset($_GET['grw_active'])) {
	update_option('grw_active', ($_GET['grw_active'] == '1' ? '1' : '0'));
}

if (isset($_POST['grw_setting'])) {
	update_option('grw_google_api_key', $_POST['grw_google_api_key']);
}

wp_enqueue_script('jquery');

wp_register_script('grp_bootstrap_js', plugins_url('/static/js/bootstrap.min.js', __FILE__));
wp_enqueue_script('grp_bootstrap_js', plugins_url('/static/js/bootstrap.min.js', __FILE__));
wp_register_style('grp_bootstrap_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));
wp_enqueue_style('grp_bootstrap_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));

wp_register_script('grp_place_finder_js', plugins_url('/static/js/grp-place-finder.js', __FILE__));
wp_enqueue_script('grp_place_finder_js', plugins_url('/static/js/grp-place-finder.js', __FILE__));

wp_register_style('grp_setting_css', plugins_url('/static/css/grp-setting.css', __FILE__));
wp_enqueue_style('grp_setting_css', plugins_url('/static/css/grp-setting.css', __FILE__));
wp_register_style('grp_place_widget_css', plugins_url('/static/css/grp-place-widget.css', __FILE__));
wp_enqueue_style('grp_place_widget_css', plugins_url('/static/css/grp-place-widget.css', __FILE__));

$grw_enabled = get_option('grw_active') == '1';
$grw_google_api_key = get_option('grw_google_api_key');
if (strlen($grw_google_api_key) == 0) {
	$grw_google_api_key = grw_google_api_key();
}
?>
<?php error_reporting(EP_ALL);

$google_data = (array)get_option( 'wp_social_seo_google_tab' );

$dn = new wpsocial_DotNotation( $google_data );

$place_id = ''; ?>



			<!-- Configuration form -->
			<form method="POST" enctype="multipart/form-data">
				<?php wp_nonce_field('grw-wpnonce_grw_settings', 'grw-form_nonce_grw_settings'); ?>
				<div class="social-form">
					<div class="form-left">
						<div class="form-group">
							<label><?php echo grw_i('Google API Key'); ?></label>
							<input type="text" name="grw_google_api_key" value="<?php echo esc_attr($grw_google_api_key); ?>"/>
							<p><?php echo grw_i('Add your exising Google Places API Key or <a href="https://console.developers.google.com/flows/enableapi?apiid=places_backend&keyType=SERVER_SIDE&reusekey=true" target="_blank">create a new one</a> wwith Google.'); ?></p>
						</div>
					</div>
				</div>
			</form>

			<form action="" method="POST" id="">
				<?php wp_nonce_field('grw-wpnonce_grw_reset', 'grw-form_nonce_grw_reset'); ?>

				<?php wp_nonce_field('grw-wpnonce_grw_textsearch', 'grw-form_nonce_grw_textsearch'); ?>
				<div class="social-form">
					<div class="form-group" id="grw-google-places-review-form">
						
					</div>
					<div class="form-left">

						<div class="form-group">
							<label>Current Google Place</label>
							<input id="widget-grw_widget-2-place_name" name="grw[place_nam]e" value="<?php echo $dn->get( 'place_nam' ); ?>" placeholder="Google Place Name" readonly="" type="text">
						</div>

						<div class="form-group">
							<input id="widget-fbrev_widget-2-dark_theme" name="grw[dark_theme]" value="1" type="checkbox" <?php checked( 1, $dn->get( 'dark_theme' ) ); ?>>
							<label for="widget-grw_widget-2-dark_theme" class="for_checkbox">Enable visuals for a dark background</label>
						</div>

						<div class="form-group">
							<input class="button-primary" type="submit" value="Update" name="submit" />
						</div>
					</div>

					<div class="form-right">
						<div class="form-group">
							<label>Place ID</label>
							<input id="widget-grw_widget-2-place_id" name="grw[place_id]" value="<?php echo $dn->get( 'place_id' ); ?>" placeholder="Google Place ID" readonly="" type="text">
						</div>

						<div class="form-group">
							<label>Type of listing</label>
							<select id="widget-grw_widget-2-view_mode" name="grw[view_mode]">
								<option value="list" <?php selected( 'list', $dn->get( 'view_mode' ) ); ?>>Review list</option>
								<option value="badge" <?php selected( 'badge', $dn->get( 'view_mode', 'badge' ) ); ?>>Google badge</option>
								<option value="badge_inner" <?php selected( 'badge_inner', $dn->get('view_mode' ) ); ?>>Inner badge
								</option>
							</select>
						</div>
					</div>
					
				</div>
			</form>

		<?php ob_start(); ?>
	        function sidebar_widget(widgetData) {

	            var widgetId = 'grw-google-places-review-form',
	                placeId = 'widget-grw_widget-2-place_id',
	                placeName = 'widget-grw_widget-2-place_name';

	            function set_fields(place) {
	                var place_id_el = document.getElementById(placeId);
	                var place_name_el = document.getElementById(placeName);
	                place_id_el.value = place.place_id;
	                place_name_el.value = place.name;
	            }

	            function show_tooltip() {
	                var el = document.getElementById(widgetId);
	                var insideEl = WPacFastjs.parents(el, 'widget-inside');
	                if (insideEl) {
	                    var controlEl = insideEl.querySelector('.widget-control-actions');
	                    if (controlEl) {
	                        var tooltip = WPacFastjs.create('div', 'grp-tooltip');
	                        tooltip.innerHTML = '<div class="grp-corn1"></div>' +
	                                            '<div class="grp-corn2"></div>' +
	                                            '<div class="grp-close">×</div>' +
	                                            '<div class="grp-text">Please don\'t forget to <b>Save</b> the widget.</div>';
	                        controlEl.appendChild(tooltip);
	                        setTimeout(function() {
	                            WPacFastjs.addcl(tooltip, 'grp-tooltip-visible');
	                        }, 100);
	                        WPacFastjs.on2(tooltip, '.grp-close', 'click', function() {
	                            WPacFastjs.rm(tooltip);
	                        });
	                    }
	                }
	            }

	            function google_key_save_listener(params, cb) {
	                var gkey = document.querySelector('#' + widgetId + ' .wp-gkey');
	                if (gkey) {
	                    WPacFastjs.on(gkey, 'change', function() {
	                        if (!this.value) return;
	                        jQuery.post('<?php echo admin_url('options-general.php?page=grw&cf_action=grw_google_api_key'); ?>', {
	                            key: this.value,
	                            _textsearch_wpnonce: jQuery('#grw-form_nonce_grw_textsearch').val()
	                        });
	                    });
	                }
	            }

	            <?php if ( !$place_id) { ?>
	            GRPPlaceFinder.main({
	                el: widgetId,
	                app_host: '<?php echo admin_url('options-general.php?page=grw'); ?>',
	                nonce: '#grw-form_nonce_grw_textsearch',
	                callback: {
	                    add: [function(place) {
	                        set_fields(place);
	                        show_tooltip();
	                    }],
	                    ready: [function(arg) {
	                        var placeInput = document.querySelector('#' + widgetId + ' .wp-place');
	                        if (placeInput) {
	                            placeInput.focus();
	                        }
	                        google_key_save_listener();
	                    }]
	                }
	            });
	            <?php } else { ?>
	            jQuery('.grp-tooltip').remove();
	            <?php } ?>

	            jQuery(document).ready(function($) {
	                var $widgetContent = $('#' + widgetId).parent();
	                $('.grp-options-toggle', $widgetContent).click(function () {
	                    $(this).toggleClass('toggled');
	                    $(this).next().slideToggle();
	                });
	            });
	        }
	        sidebar_widget('');

        <?php $js_content = ob_get_clean();

        wp_add_inline_script( 'grp_place_finder_js', $js_content ); ?>

