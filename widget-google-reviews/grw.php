<?php

require(ABSPATH . 'wp-includes/version.php');

require_once(dirname(__FILE__) . '/api/grw-api.php');

define('GRW_VERSION',             '1.49');
define('GRW_GOOGLE_PLACE_API',    'https://maps.googleapis.com/maps/api/place/');
define('GRW_GOOGLE_AVATAR',       'https://lh3.googleusercontent.com/-8hepWJzFXpE/AAAAAAAAAAI/AAAAAAAAAAA/I80WzYfIxCQ/s64-c/114307615494839964028.jpg');
define('GRW_PLUGIN_URL',          plugins_url(basename(plugin_dir_path(__FILE__ )), basename(__FILE__)));

function grw_google_api_key() {
    $keys = array(
        'AIzaSyAi0nomk8c1CBMRiKWuFxdfyF3QFFXZSUM',
        'AIzaSyA_7b55nlagZcTLT8Fiz_x8Hae-D93H5_g',
        'AIzaSyALYBD3qLHSyI86bRtrh_5vr_4Tr2fLoIM',
        'AIzaSyB84O9Qy34aHpilZT2wrDYtWc6X8j2q-XM',
        'AIzaSyB6S2Rl0iczRwO_8udMuGqaQA1ttCE1NgA',
        'AIzaSyBuUPywXpgXMu-RTsuEIBB5gQgp0N114cI',
        'AIzaSyAETBwpe6xxwNXN6fa6q0ray9g5nAq8Dl0',
        'AIzaSyDLTEEEUBSdmeCbps5rLKKBRCC14KmcXv4',
        'AIzaSyDcxWMaaAhpeOFikmam_s3xwXJF7iSlJsc',
        'AIzaSyC12ZI0DTBuvfTbxw7IAkFdhuuaaRsX6lw',
        'AIzaSyCQ2rPW43kyRmlAh1x9k17dQfNi0BDm5Ds',
        'AIzaSyDhhxmCvkORZxJ7Fpo4pElNuDkfV_r74Jc',
        'AIzaSyB3k4oWDJPFR30LLjj27k5MdupZ9yMfXqE',
        'AIzaSyAw9xnTPxpBnYvFuE73_Son-Yy_-opo9Fs',
        'AIzaSyA42G8C-Q0Kr3uLJHmNcngYHaIK5kB6geg',
        'AIzaSyBuXKuG_ITnAg92vsLdyExiY56TEXUphnE',
        'AIzaSyDrYf4yX5Wf5ugvbljgnMfyd8kRhi9hoik'
    );
    $idx = array_rand($keys);
    return $keys[$idx];
}

function grw_options() {
    return array(
        'grw_version',
        'grw_active',
        'grw_google_api_key',
    );
}

$grw_api = new GoogleReviewsWidgetAPI();

/*-------------------------------- Widget --------------------------------*/
function grw_init_widget() {
    if (!class_exists('Goog_Reviews_Widget' ) ) {
        require 'grw-widget.php';
    }
}

add_action('widgets_init', 'grw_init_widget');
add_action('widgets_init', create_function('', 'register_widget("Goog_Reviews_Widget");'));

/*-------------------------------- Menu --------------------------------*/
/*function grw_setting_menu() {
     add_submenu_page(
         'options-general.php',
         'Google Reviews Widget',
         'Google Reviews Widget',
         'moderate_comments',
         'grw',
         'grw_setting'
     );
}
add_action('admin_menu', 'grw_setting_menu', 10);
*/
function grw_setting() {
    include_once(dirname(__FILE__) . '/grw-setting.php');
}

function grw_review_list() {
    include_once(dirname(__FILE__) . '/grw-reviews-listing.php');
}

function grw_review_edit() {
    include_once(dirname(__FILE__) . '/grw-reviews-edit.php');
}

/*-------------------------------- Links --------------------------------*/
function grw_plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=grw') . '">'.grw_i('Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'grw_plugin_action_links', 10, 2);



/*-------------------------------- Request --------------------------------*/
function grw_request_handler() {
    global $grw_api;
    global $wpdb;

    if (!empty($_GET['cf_action'])) {

        if (!empty($_GET['key'])) {
            $google_api_key = $_GET['key'];
        } else {
            $google_api_key = get_option('grw_google_api_key');
        }
        if (!$google_api_key) {
            $google_api_key = grw_google_api_key();
        }

        switch ($_GET['cf_action']) {
            case 'grw_google_api_key':
                if (current_user_can('manage_options')) {
                    if (isset($_POST['_textsearch_wpnonce']) === false) {
                        $error = grw_i('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.');
                        $response = compact('error');
                    } else {
                        check_admin_referer('grw-wpnonce_grw_textsearch', '_textsearch_wpnonce');
                        update_option('grw_google_api_key', $_POST['key']);
                        $status = 'success';
                        $response = compact('status');
                    }
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
            case 'textsearch':
                if (current_user_can('manage_options')) {
                    if (isset($_GET['_textsearch_wpnonce']) === false) {
                        $error = grw_i('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.');
                        $response = compact('error');
                    } else {
                        check_admin_referer('grw-wpnonce_grw_textsearch', '_textsearch_wpnonce');
                        $response = $grw_api->textsearch(array(
                            'query' => $_GET['query'],
                            'key' => $google_api_key,
                        ));
                    }
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
            case 'details':
                if (current_user_can('manage_options')) {
                    if (isset($_GET['_textsearch_wpnonce']) === false) {
                        $error = grw_i('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.');
                        $response = compact('error');
                    } else {
                        check_admin_referer('grw-wpnonce_grw_textsearch', '_textsearch_wpnonce');
                        $response = $grw_api->details(array(
                            'placeid' => $_GET['placeid'],
                            'key' => $google_api_key,
                        ));
                    }
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
            case 'save':
                if (current_user_can('manage_options')) {
                    if (isset($_POST['_textsearch_wpnonce']) === false) {
                        $error = grw_i('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.');
                        $response = compact('error');
                    } else {
                        check_admin_referer('grw-wpnonce_grw_textsearch', '_textsearch_wpnonce');
                        $result = $grw_api->details(array(
                            'placeid' => $_POST['placeid'],
                            'key' => $google_api_key,
                        ));
                        if ($result['place']) {
                            grw_save_reviews($result['place']);
                            $status = 'success';
                        } else {
                            $status = 'failed';
                        }
                        $response = compact('status');
                    }
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
            case 'save_json':
                if (current_user_can('manage_options')) {
                    if (isset($_POST['_textsearch_wpnonce']) === false) {
                        $error = grw_i('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.');
                        $response = compact('error');
                    } else {
                        check_admin_referer('grw-wpnonce_grw_textsearch', '_textsearch_wpnonce');

                        $place_json = stripslashes($_POST['place']);
                        $reviews_json = stripslashes($_POST['reviews']);
                        $place = grw_json_decode($place_json);
                        $reviews = grw_json_decode($reviews_json);
                        $place_array = get_object_vars($place);
                        $place_array['reviews'] = array();
                        foreach ($reviews as $review) {
                            array_push($place_array['reviews'], get_object_vars($review));
                        }

                        if ($place_array && strlen($place_array['place_id']) > 0) {
                            grw_save_reviews($place_array);
                            $status = 'success';
                        } else {
                            $status = 'failed';
                        }
                        $response = compact('status');
                    }
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
        }
    }
}
add_action('init', 'grw_request_handler');

function grw_save_reviews($place, $min_filter = 0) {
    global $wpdb;

    $google_place_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "grp_google_place WHERE place_id = %s", $place['place_id']));
    if ($google_place_id) {
        $wpdb->update($wpdb->prefix . 'grp_google_place', array('rating' => $place['rating']), array('ID' => $google_place_id));
    } else {
        $wpdb->insert($wpdb->prefix . 'grp_google_place', array(
            'place_id' => $place['place_id'],
            'name' => $place['name'],
            'photo' => $place['photo'],
            'icon' => $place['icon'],
            'address' => $place['formatted_address'],
            'rating' => $place['rating'],
            'url' => $place['url'],
            'website' => $place['website']
        ));
        $google_place_id = $wpdb->insert_id;
    }

    if ($place['reviews']) {
        $reviews = $place['reviews'];
        foreach ($reviews as $review) {
            if ($min_filter > 0 && $min_filter > $review['rating']) {
                continue;
            }
            if (strlen($review['author_url']) < 1) {
                continue;
            }
            $hash = sha1($place['place_id'] . $review['author_url']);
            $google_review_hash = $wpdb->get_var($wpdb->prepare("SELECT hash FROM " . $wpdb->prefix . "grp_google_review WHERE hash = %s", $hash));
            if (!$google_review_hash) {
                $wpdb->insert($wpdb->prefix . 'grp_google_review', array(
                    'google_place_id' => $google_place_id,
                    'hash' => $hash,
                    'rating' => $review['rating'],
                    'text' => $review['text'],
                    'time' => $review['time'],
                    'language' => $review['language'],
                    'author_name' => $review['author_name'],
                    'author_url' => $review['author_url'],
                    'profile_photo_url' => $review['profile_photo_url']
                ));
            }
        }
    }
}

function grw_lang_init() {
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain('grw', false, basename( dirname( __FILE__ ) ) . '/languages');
}
add_action('plugins_loaded', 'grw_lang_init');

/*-------------------------------- Helpers --------------------------------*/
function grw_enabled() {
    global $id, $post;

    $active = get_option('grw_active');
    if (empty($active) || $active === '0') { return false; }
    return true;
}

function grw_does_need_update() {
    $version = (string)get_option('grw_version');
    if (empty($version)) {
        $version = '0';
    }
    if (version_compare($version, '1.0', '<')) {
        return true;
    }
    return false;
}

function grw_i($text, $params=null) {
    if (!is_array($params)) {
        $params = func_get_args();
        $params = array_slice($params, 1);
    }
    return vsprintf(__($text, 'grw'), $params);
}

if (!function_exists('esc_html')) {
function esc_html( $text ) {
    $safe_text = wp_check_invalid_utf8( $text );
    $safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
    return apply_filters( 'esc_html', $safe_text, $text );
}
}

if (!function_exists('esc_attr')) {
function esc_attr( $text ) {
    $safe_text = wp_check_invalid_utf8( $text );
    $safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
    return apply_filters( 'attribute_escape', $safe_text, $text );
}
}

/**
 * JSON ENCODE for PHP < 5.2.0
 * Checks if json_encode is not available and defines json_encode
 * to use php_json_encode in its stead
 * Works on iteratable objects as well - stdClass is iteratable, so all WP objects are gonna be iteratable
 */
if(!function_exists('cf_json_encode')) {
    function cf_json_encode($data) {

        // json_encode is sending an application/x-javascript header on Joyent servers
        // for some unknown reason.
        return cfjson_encode($data);
    }

    function cfjson_encode_string($str) {
        if(is_bool($str)) {
            return $str ? 'true' : 'false';
        }

        return str_replace(
            array(
                '\\'
                , '"'
                //, '/'
                , "\n"
                , "\r"
            )
            , array(
                '\\\\'
                , '\"'
                //, '\/'
                , '\n'
                , '\r'
            )
            , $str
        );
    }

    function cfjson_encode($arr) {
        $json_str = '';
        if (is_array($arr)) {
            $pure_array = true;
            $array_length = count($arr);
            for ( $i = 0; $i < $array_length ; $i++) {
                if (!isset($arr[$i])) {
                    $pure_array = false;
                    break;
                }
            }
            if ($pure_array) {
                $json_str = '[';
                $temp = array();
                for ($i=0; $i < $array_length; $i++) {
                    $temp[] = sprintf("%s", cfjson_encode($arr[$i]));
                }
                $json_str .= implode(',', $temp);
                $json_str .="]";
            }
            else {
                $json_str = '{';
                $temp = array();
                foreach ($arr as $key => $value) {
                    $temp[] = sprintf("\"%s\":%s", $key, cfjson_encode($value));
                }
                $json_str .= implode(',', $temp);
                $json_str .= '}';
            }
        }
        else if (is_object($arr)) {
            $json_str = '{';
            $temp = array();
            foreach ($arr as $k => $v) {
                $temp[] = '"'.$k.'":'.cfjson_encode($v);
            }
            $json_str .= implode(',', $temp);
            $json_str .= '}';
        }
        else if (is_string($arr)) {
            $json_str = '"'. cfjson_encode_string($arr) . '"';
        }
        else if (is_numeric($arr)) {
            $json_str = $arr;
        }
        else if (is_bool($arr)) {
            $json_str = $arr ? 'true' : 'false';
        }
        else {
            $json_str = '"'. cfjson_encode_string($arr) . '"';
        }
        return $json_str;
    }
}
?>