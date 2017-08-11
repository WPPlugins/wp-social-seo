<?php
/**
 * Facebook Reviews Widget
 *
 * @description: The Facebook Reviews Widget
 * @since      : 1.0
 */

class Fb_Reviews_Widget extends WP_Widget {

    public $options;

    public $widget_fields = array(
        'title'                => '',
        'page_id'              => '',
        'page_name'            => '',
        'page_access_token'    => '',
        'dark_theme'           => '',
        'view_mode'            => '',
        'cache'                => '',
    );

    public function __construct() {
        parent::__construct(
            'fbrev_widget', // Base ID
            'Facebook Reviews', // Name
            array(
                'classname'   => 'fb-reviews-widget',
                'description' => fbrev_i('Display Facebook Reviews on your website.', 'fbrev')
            )
        );

        add_action('admin_enqueue_scripts', array($this, 'fbrev_widget_scripts'));

        wp_register_script('fbrev_wpac_time_js', plugins_url('/static/js/wpac-time.js', __FILE__));
        wp_enqueue_script('fbrev_wpac_time_js', plugins_url('/static/js/wpac-time.js', __FILE__));
        wp_register_style('fbrev_widget_css', plugins_url('/static/css/facebook-review.css', __FILE__));
        wp_enqueue_style('fbrev_widget_css', plugins_url('/static/css/facebook-review.css', __FILE__));
    }

    function fbrev_widget_scripts($hook) {
        if ($hook == 'widgets.php' || ($hook == 'customize.php' && defined('SITEORIGIN_PANELS_VERSION'))) {
            wp_enqueue_script('jquery');
            wp_register_script('fbrev_wpac_js', plugins_url('/static/js/wpac.js', __FILE__));
            wp_enqueue_script('fbrev_wpac_js', plugins_url('/static/js/wpac.js', __FILE__));
            wp_register_style('fbrev_sidebar_widget_css', plugins_url('/static/css/fbrev-sidebar-widget.css', __FILE__));
            wp_enqueue_style('fbrev_sidebar_widget_css', plugins_url('/static/css/fbrev-sidebar-widget.css', __FILE__));
        }
    }

    function widget($args, $instance) {
        global $wpdb;
        global $fbrev_api;

        if (fbrev_enabled()) {
            extract($args);
            foreach ($instance as $variable => $value) {
                ${$variable} = !isset($instance[$variable]) ? $this->widget_fields[$variable] : esc_attr($instance[$variable]);
            }

            if (empty($page_id)) { ?>
                <div class="fbrev-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
                    <?php echo fbrev_i('Please check that this widget <b>Facebook Reviews</b> has a connected Facebook.'); ?>
                </div> <?php
                return false;
            }

            $reviews             = get_transient('fbrev_widget_api_' . $page_id);
            $widget_options      = get_transient('fbrev_widget_options_' . $page_id);
            $serialized_instance = serialize($instance);

            if ($reviews === false || $serialized_instance !== $widget_options) {
                $expiration = $cache;
                switch ($expiration) {
					case '1':
						$expiration = 3600;
						break;
					case '3':
						$expiration = 3600 * 3;
						break;
					case '6':
						$expiration = 3600 * 6;
						break;
					case '12':
						$expiration = 3600 * 12;
						break;
					case '24':
						$expiration = 3600 * 24;
						break;
					case '48':
						$expiration = 3600 * 48;
						break;
					case '168':
						$expiration = 3600 * 168;
						break;
                    default:
                        $expiration = 3600 * 24;
				}
                $reviews = $fbrev_api->reviews($page_id, array('access_token' => $page_access_token, 'limit' => 25));
				set_transient('fbrev_widget_api_' . $page_id, $reviews, $expiration);
				set_transient('fbrev_widget_options_' . $page_id, $serialized_instance, $expiration);
            }

            echo $before_widget;
            if ($title) { ?><h2 class="fbrev-widget-title widget-title"><?php echo $title; ?></h2><?php }
            include(dirname(__FILE__) . '/fbrev-reviews.php');
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

        <script type="text/javascript">
            function fbrev_popup(url, width, height, cb) {
                var top = top || (screen.height/2)-(height/2),
                    left = left || (screen.width/2)-(width/2),
                    win = window.open(url, '', 'location=1,status=1,resizable=yes,width='+width+',height='+height+',top='+top+',left='+left);
                function check() {
                    if (!win || win.closed != false) {
                        cb();
                    } else {
                        setTimeout(check, 100);
                    }
                }
                setTimeout(check, 100);
            }

            function fbrev_facebook(btn) {
                fbrev_popup('https://app.widgetpack.com/auth/fbrev?scope=manage_pages', 670, 520, function() {
                    WPacXDM.get('https://embed.widgetpack.com', 'https://app.widgetpack.com/widget/facebook/accesstoken', {}, function(res) {
                        WPacFastjs.jsonp('https://graph.facebook.com/me/accounts', {access_token: res.accessToken}, function(res) {
                            var pagesEL = WPacFastjs.next(btn);
                            WPacFastjs.each(res.data, function(page) {
                                var pageEL = WPacFastjs.create('div', 'fbrev-page');
                                //pageEL.innerHTML = '<div>' + page.name + '</div>';
                                pageEL.innerHTML =
                                    '<img src="https://graph.facebook.com/' + page.id +  '/picture" class="fbrev-page-photo">' +
                                    '<div class="fbrev-page-name">' + page.name + '</div>';
                                pagesEL.appendChild(pageEL);
                                WPacFastjs.on(pageEL, 'click', function() {
                                    WPacFastjs.next(pagesEL).value = page.name;
                                    WPacFastjs.next(WPacFastjs.next(pagesEL)).value = page.id;
                                    WPacFastjs.next(WPacFastjs.next(WPacFastjs.next(pagesEL))).value = page.access_token;
                                    WPacFastjs.remcl(pagesEL.querySelector('.active'), 'active');
                                    WPacFastjs.addcl(pageEL, 'active');
                                    return false;
                                });
                            });
                        });
                    });
                });
                return false;
            }

            jQuery(document).ready(function($) {
                $('.fbrev-options-toggle').unbind("click").click(function () {
                    $(this).toggleClass('toggled');
                    $(this).next().slideToggle();
                })
            });
        </script>

        <div class="fbrev-sidebar-widget">

            <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" placeholder="<?php echo fbrev_i('Widget title'); ?>" />

            <button onclick="return fbrev_facebook(this);">Connect to Facebook</button>

            <div class="fbrev-pages"></div>

            <input type="text" id="<?php echo $this->get_field_id('page_name'); ?>" name="<?php echo $this->get_field_name('page_name'); ?>" value="<?php echo $page_name; ?>" placeholder="<?php echo fbrev_i('Page Name'); ?>" readonly />

            <input type="text" id="<?php echo $this->get_field_id('page_id'); ?>" name="<?php echo $this->get_field_name('page_id'); ?>" value="<?php echo $page_id; ?>" placeholder="<?php echo fbrev_i('Page ID'); ?>" readonly />

            <input type="hidden" id="<?php echo $this->get_field_id('page_access_token'); ?>" name="<?php echo $this->get_field_name('page_access_token'); ?>" value="<?php echo $page_access_token; ?>" placeholder="<?php echo fbrev_i('Access token'); ?>" readonly />

            <h4 class="fbrev-options-toggle"><?php echo fbrev_i('Review Options'); ?></h4>
            <div class="fbrev-options" style="display:none">
                <div class="fbrev-form-group fbrev-disabled">
                    <label><input type="checkbox" disabled /> <?php echo fbrev_i('Enable Google Rich Snippets for rating (schema.org)'); ?></label>
                </div>
                <div class="fbrev-form-group fbrev-disabled">
                    <label><input type="checkbox" disabled /> <?php echo fbrev_i('Trim long reviews'); ?></label>
                </div>
                <div class="fbrev-form-group">
                    <?php echo fbrev_i('Minimum Review Rating'); ?>
                    <select disabled>
                        <option value="" selected="selected"><?php echo fbrev_i('No filter'); ?></option>
                        <option value="5"><?php echo fbrev_i('5 Stars'); ?></option>
                        <option value="4"><?php echo fbrev_i('4 Stars'); ?></option>
                        <option value="3"><?php echo fbrev_i('3 Stars'); ?></option>
                        <option value="2"><?php echo fbrev_i('2 Stars'); ?></option>
                        <option value="1"><?php echo fbrev_i('1 Star'); ?></option>
                    </select>
                </div>
                <div class="fbrev-form-group fbrev-disabled">
                    <?php echo fbrev_i('Limit Number of Reviews'); ?>
                    <input type="text" placeholder="25" disabled />
                </div>

            </div>

            <h4 class="fbrev-options-toggle"><?php echo fbrev_i('Display Options'); ?></h4>
            <div class="fbrev-options" style="display:none">
                <div class="form-group">
                    <label>
                        <input id="<?php echo $this->get_field_id('dark_theme'); ?>" name="<?php echo $this->get_field_name('dark_theme'); ?>" type="checkbox" value="1" <?php checked('1', $dark_theme); ?> />
                        <?php echo fbrev_i('Dark background'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <?php echo fbrev_i('Widget theme'); ?>
                    <select id="<?php echo $this->get_field_id('view_mode'); ?>" name="<?php echo $this->get_field_name('view_mode'); ?>">
                        <option value="list" <?php selected('list', $view_mode); ?>><?php echo fbrev_i('Review list'); ?></option>
                        <option value="badge" <?php selected('badge', $view_mode); ?> disabled><?php echo fbrev_i('Facebook badge'); ?></option>
                        <option value="badge_inner" <?php selected('badge_inner', $view_mode); ?> disabled><?php echo fbrev_i('Inner badge'); ?></option>
                    </select>
                </div>
                
            </div>

            <h4 class="fbrev-options-toggle"><?php echo fbrev_i('Advance Options'); ?></h4>
            <div class="fbrev-options" style="display:none">
                <div class="fbrev-form-group fbrev-disabled">
                    <label><input type="checkbox" disabled /> <?php echo fbrev_i('Open links in new Window'); ?></label>
                </div>
                <div class="fbrev-form-group fbrev-disabled">
                    <label><input type="checkbox" disabled /> <?php echo fbrev_i('User no follow links'); ?></label>
                </div>
                              <div class="form-group">
                    <?php echo fbrev_i('Cache data'); ?>
                    <select id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>">
                        <option value="1" <?php selected('1', $cache); ?>><?php echo fbrev_i('1 Hour'); ?></option>
                        <option value="3" <?php selected('3', $cache); ?>><?php echo fbrev_i('3 Hours'); ?></option>
                        <option value="6" <?php selected('6', $cache); ?>><?php echo fbrev_i('6 Hours'); ?></option>
                        <option value="12" <?php selected('12', $cache); ?>><?php echo fbrev_i('12 Hours'); ?></option>
                        <option value="24" <?php selected('24', $cache); ?> selected><?php echo fbrev_i('1 Day'); ?></option>
                        <option value="48" <?php selected('48', $cache); ?>><?php echo fbrev_i('2 Days'); ?></option>
                        <option value="168" <?php selected('168', $cache); ?>><?php echo fbrev_i('1 Week'); ?></option>
                    </select>
                </div>
            </div>

        </div>

        <?php
    }
}
?>
