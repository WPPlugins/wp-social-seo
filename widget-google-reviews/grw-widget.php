<?php

/**
 * Google Reviews Widget
 *
 * @description: The Google Reviews Widget
 * @since      : 1.0
 */

//class name start with Goog_ instead Google_ coz it failed with w3c-total-cache plugin
//https://wordpress.org/support/topic/fix-for-fatal-error-require/
class Goog_Reviews_Widget extends WP_Widget {

    public $options;
    public $api_key;

    public $widget_fields = array(
        'title'                => '',
        'place_name'           => '',
        'place_id'             => '',
        'text_size'            => '',
        'dark_theme'           => '',
        'view_mode'            => '',
    );

    public function __construct() {
        parent::__construct(
            'grw_widget', // Base ID
            'Google Reviews Widget', // Name
            array(
                'classname'   => 'google-reviews-widget',
                'description' => grw_i('Display Google Places Reviews on your website.', 'grw')
            )
        );

        add_action('admin_enqueue_scripts', array($this, 'grw_widget_scripts'));

        wp_register_script('grp_time_js', plugins_url('/static/js/grp-time.js', __FILE__));
        wp_enqueue_script('grp_time_js', plugins_url('/static/js/grp-time.js', __FILE__));
        //wp_register_style('grp_widget_css', plugins_url('/static/css/grp-widget.css', __FILE__));
        wp_enqueue_style('grp_widget_css', plugins_url('/static/css/grp-widget.css', __FILE__));
    }

    function grw_widget_scripts($hook) {
        if ($hook == 'widgets.php' || ($hook == 'customize.php' && defined('SITEORIGIN_PANELS_VERSION'))) {
            wp_enqueue_script('jquery');
            wp_register_script('grp_place_finder_js', plugins_url('/static/js/grp-place-finder.js', __FILE__));
            wp_enqueue_script('grp_place_finder_js', plugins_url('/static/js/grp-place-finder.js', __FILE__));
            wp_register_style('grp_place_widget_css', plugins_url('/static/css/grp-place-widget.css', __FILE__));
            wp_enqueue_style('grp_place_widget_css', plugins_url('/static/css/grp-place-widget.css', __FILE__));
        }
    }

    function widget($args, $instance) {
        global $wpdb;

        if (grw_enabled()) {
            extract($args);
            foreach ($instance as $variable => $value) {
                ${$variable} = !isset($instance[$variable]) ? $this->widget_fields[$variable] : esc_attr($instance[$variable]);
            }

            echo $before_widget;
            if ($place_id) {
                if ($title) { ?><h2 class="grp-widget-title widget-title"><?php echo $title; ?></h2><?php }
                include(dirname(__FILE__) . '/grw-reviews.php');
                if ($view_mode == 'badge') {
                    ?>
                    <style>
                    #<?php echo $this->id; ?> {
                      margin: 0;
                      padding: 0;
                      border: none;
                    }
                    </style>
                    <?php
                }
            } else { ?>
                <div class="grp-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
                    <?php echo grw_i('Please check that this widget <b>Google Reviews</b> has a Google Place ID set.'); ?>
                </div>
            <?php }
            echo $after_widget;
        }
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        foreach ($this->widget_fields as $field => $value) {
            $instance[$field] = strip_tags(stripslashes($new_instance[$field]));
        }
        return $instance;
    }

    function form($instance) {
        global $wp_version;
        foreach ($this->widget_fields as $field => $value) {
            ${$field} = !isset($instance[$field]) ? $value : esc_attr($instance[$field]);
        } ?>
        <div class="form-group">
            <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" placeholder="<?php echo grw_i('Widget title'); ?>" />
        </div>

        <?php wp_nonce_field('grw-wpnonce_grw_textsearch', 'grw-form_nonce_grw_textsearch'); ?>

        <div id="<?php echo $this->id; ?>"></div>
        <script type="text/javascript">
        function sidebar_widget(widgetData) {

            var widgetId = widgetData.widgetId,
                placeId = widgetData.placeId,
                placeName = widgetData.placeName;

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

            <?php if (!$place_id) { ?>
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
        </script>
        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
          data-widget-id="<?php echo $this->id; ?>"
          data-place-id="<?php echo $this->get_field_id("place_id"); ?>"
          data-place-name="<?php echo $this->get_field_id("place_name"); ?>"
          onload="sidebar_widget({
              widgetId: this.getAttribute('data-widget-id'),
              placeId: this.getAttribute('data-place-id'),
              placeName: this.getAttribute('data-place-name')
          })" style="display:none">

        <div class="form-group">
            <input type="text" id="<?php echo $this->get_field_id('place_name'); ?>" name="<?php echo $this->get_field_name('place_name'); ?>" value="<?php echo $place_name; ?>" placeholder="<?php echo grw_i('Google Place Name'); ?>" readonly />
        </div>

        <div class="form-group">
            <input type="text" id="<?php echo $this->get_field_id('place_id'); ?>" name="<?php echo $this->get_field_name('place_id'); ?>" value="<?php echo $place_id; ?>" placeholder="<?php echo grw_i('Google Place ID'); ?>" readonly />
        </div>

        <h4 class="grp-options-toggle"><?php echo grw_i('Review Options'); ?></h4>
        <div class="grp-options" style="display:none">
            <div class="form-group wpgrev-disabled">
                <input type="checkbox" disabled />
                <label><?php echo grw_i('Save Google reviews to my WordPress database'); ?></label>
            </div>
            <div class="form-group wpgrev-disabled">
                <input type="checkbox" disabled />
                <label><?php echo grw_i('Auto-download new reviews from Google'); ?></label>
            </div>
            <div class="form-group wpgrev-disabled">
                <input type="checkbox" disabled />
                <label><?php echo grw_i('Enable Google Rich Snippets (schema.org)'); ?></label>
            </div>
            <div class="form-group">
                <?php echo grw_i('Pagination'); ?>
                <select disabled>
                    <option><?php echo grw_i('Show all reviews'); ?></option>
                    <option><?php echo grw_i('10'); ?></option>
                    <option><?php echo grw_i('5'); ?></option>
                    <option><?php echo grw_i('4'); ?></option>
                    <option><?php echo grw_i('3'); ?></option>
                    <option><?php echo grw_i('2'); ?></option>
                    <option><?php echo grw_i('1'); ?></option>
                </select>
            </div>
            <div class="form-group">
                <?php echo grw_i('Sorting'); ?>
                <select disabled>
                    <option><?php echo grw_i('Default'); ?></option>
                    <option><?php echo grw_i('Most recent'); ?></option>
                    <option><?php echo grw_i('Most oldest'); ?></option>
                    <option><?php echo grw_i('Highest score'); ?></option>
                    <option><?php echo grw_i('Lowest score'); ?></option>
                </select>
            </div>
            <div class="form-group">
                <?php echo grw_i('Minimum Review Rating'); ?>
                <select disabled>
                    <option><?php echo grw_i('No filter'); ?></option>
                    <option><?php echo grw_i('5 Stars'); ?></option>
                    <option><?php echo grw_i('4 Stars'); ?></option>
                    <option><?php echo grw_i('3 Stars'); ?></option>
                    <option><?php echo grw_i('2 Stars'); ?></option>
                    <option><?php echo grw_i('1 Star'); ?></option>
                </select>
            </div>

                  </div>

        <h4 class="grp-options-toggle"><?php echo grw_i('Display Options'); ?></h4>
        <div class="grp-options" style="display:none">
            <div class="form-group wpgrev-disabled">
                <label><?php echo grw_i('Change place photo'); ?></label>
            </div>
            <div class="form-group wpgrev-disabled">
                <input type="text" placeholder="<?php echo grw_i('Review text limit before \'read more\' link: 200'); ?>" disabled />
            </div>
                        <div class="form-group">
                <input id="<?php echo $this->get_field_id('dark_theme'); ?>" name="<?php echo $this->get_field_name('dark_theme'); ?>" type="checkbox" value="1" <?php checked('1', $dark_theme); ?> />
                <label for="<?php echo $this->get_field_id('dark_theme'); ?>"><?php echo grw_i('Dark theme'); ?></label>
            </div>
            <div class="form-group">
                <?php echo grw_i('Widget theme'); ?>
                <select id="<?php echo $this->get_field_id('view_mode'); ?>" name="<?php echo $this->get_field_name('view_mode'); ?>">
                    <option value="list" <?php selected('list', $view_mode); ?>><?php echo grw_i('Review list'); ?></option>
                    <option value="badge" <?php selected('badge', $view_mode); ?>><?php echo grw_i('Google badge'); ?></option>
                    <option value="badge_inner" <?php selected('badge_inner', $view_mode); ?>><?php echo grw_i('Inner badge'); ?></option>
                </select>
            </div>
        </div>

        <h4 class="grp-options-toggle"><?php echo grw_i('Advance Options'); ?></h4>
        <div class="grp-options" style="display:none">
            <div class="form-group wpgrev-disabled">
                <input type="text" placeholder="<?php echo grw_i('Write a review link'); ?>" disabled />
                <small><?php echo grw_i('Allows to write Google reviews straight in widget on your website'); ?></small>
            </div>
            <div class="form-group wpgrev-disabled">
                <input type="checkbox" disabled />
                <label><?php echo grw_i('Disable G+ profile links'); ?></label>
            </div>
            <div class="form-group wpgrev-disabled">
                <input type="checkbox" disabled />
                <label><?php echo grw_i('Open links in new Window'); ?></label>
            </div>
            <div class="form-group wpgrev-disabled">
                <input type="checkbox" disabled />
                <label><?php echo grw_i('User no follow links'); ?></label>
            </div>

        </div>

        <br>
        <?php
    }
}
?>
