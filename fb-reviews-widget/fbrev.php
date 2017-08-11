<?php


require(ABSPATH . 'wp-includes/version.php');

require_once(dirname(__FILE__) . '/api/fbrev-api.php');

define('FBREV_VERSION',          '1.1');
define('FBREV_GRAPH_API',        'https://graph.facebook.com/');
define('FBREV_PLUGIN_URL',       plugins_url(basename(plugin_dir_path(__FILE__ )), basename(__FILE__)));
define('FBREV_AVATAR',           FBREV_PLUGIN_URL . '/static/img/avatar.gif');

function fbrev_options() {
    return array(
        'fbrev_version',
        'fbrev_active',
    );
}

$fbrev_api = new FacebookReviewsAPI();

/*-------------------------------- Widget --------------------------------*/
function fbrev_init_widget() {
    if (!class_exists('Fb_Reviews_Widget' ) ) {
        require 'fbrev-widget.php';
    }
}

add_action('widgets_init', 'fbrev_init_widget');
//add_action('widgets_init', create_function('', 'register_widget("Fb_Reviews_Widget");'));

/*-------------------------------- Menu --------------------------------*/
/*function fbrev_setting_menu() {
     add_submenu_page(
         'options-general.php',
         'Facebook Reviews',
         'Facebook Reviews',
         'moderate_comments',
         'fbrev',
         'fbrev_setting'
     );
}
add_action('admin_menu', 'fbrev_setting_menu', 10);
*/
function fbrev_setting() {
    //include( dirname(__FILE__) . '/fbrev-setting.php');
    include dirname(__FILE__) . '/fbrev-setting.php';
}

function fbrev_review_listing() {
    include dirname(__FILE__) . '/fbrev-reviews-listing.php';
}

function fbrev_review_edit() {
    include dirname(__FILE__) . '/fbrev-review-edit.php';
}

/*-------------------------------- Links --------------------------------*/
function fbrev_plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=fbrev') . '">' . fbrev_i('Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'fbrev_plugin_action_links', 10, 2);


function fbrev_install() {
    $version = (string)get_option('fbrev_version');
    if (!$version) {
        $version = '0';
    }

    if (version_compare($version, FBREV_VERSION, '=')) {
        return;
    }

    add_option('fbrev_active', '1');
    update_option('fbrev_version', FBREV_VERSION);
}

function fbrev_lang_init() {
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain('fbrev', false, basename( dirname( __FILE__ ) ) . '/languages');
}
add_action('plugins_loaded', 'fbrev_lang_init');

/*-------------------------------- Helpers --------------------------------*/
function fbrev_enabled() {
    $active = get_option('fbrev_active');
    if (empty($active) || $active === '0') { return false; }
    return true;
}

function fbrev_does_need_update() {
    $version = (string)get_option('fbrev_version');
    if (empty($version)) {
        $version = '0';
    }
    if (version_compare($version, '1.0', '<')) {
        return true;
    }
    return false;
}

function fbrev_i($text, $params=null) {
    if (!is_array($params)) {
        $params = func_get_args();
        $params = array_slice($params, 1);
    }
    return vsprintf(__($text, 'fbrev'), $params);
}

?>