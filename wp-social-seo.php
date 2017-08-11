<?php
/**
 * Plugin Name: Reviews Widget - Facebook, Google, WooCommerce, Reviews.io
 * Plugin URI: https://campaigns.io
 * Description: Reviews.io (Reviews.co.uk), WooCommerce, Facebook, Google Local reviews in one easy plugin
 * Version: 6.5.11
 * Author: Jody Nesbitt, pigeonhut
 * Author URI: https://optimisation.io/
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * Credit to richplugins.com for the intial idea and concept and base code.
 */

if ( ! defined( 'WPSOCIALSEO_PATH' ) ) {
    define( 'WPSOCIALSEO_PATH', plugin_dir_path(  __FILE__ ) );
}
if ( ! defined( 'WPSOCIALSEO_URL' ) ) {
    define( 'WPSOCIALSEO_URL', plugin_dir_url(  __FILE__ ) );
}
if( ! defined('WPSOCIALSEO_MIN_PHP_VERSION')) {
    define('WPSOCIALSEO_MIN_PHP_VERSION', '5.5');
}





if (!class_exists('Wps_Review_List_Table')) {
    require_once( plugin_dir_path(__FILE__) . 'class/class-wps-review-list-table.php' );
}
if (!class_exists('NMRichReviewsAdminHelper')) {
    require_once(plugin_dir_path(__FILE__) . 'class/admin-view-helper-functions.php');
}
if (!class_exists('reviews')) {
    require_once( plugin_dir_path(__FILE__) . 'class/class-reviews.php' );
}
if (!class_exists('fbpost')) {
    require_once( plugin_dir_path(__FILE__) . 'class/class-fb-post.php' );
}

if (!class_exists('wpseo_admin_notice')) {
    require_once( plugin_dir_path(__FILE__) . 'class/class-admin-notice.php' );
}




//print_r(  plugin_dir_path(__FILE__) . 'fb-reviews-widget/fbrev-widget.php'  ); exit();
if (!function_exists('fbrev_setting_menu')) {
    require_once( plugin_dir_path(__FILE__) . 'fb-reviews-widget/fbrev.php' );
}
if (!function_exists('grw_init_widget')) {
    require_once( plugin_dir_path(__FILE__) . 'widget-google-reviews/grw.php' );
}
if (!function_exists('send_email_after_purchase')) {
    require_once( plugin_dir_path(__FILE__) . 'review/review.php' );
}
if (!class_exists('wpsocial_DotNotation')) {
    require_once( plugin_dir_path(__FILE__) . 'class/DotNotation.php' );
}
if (!function_exists('social_seo_review_widget')) {
    require_once( plugin_dir_path(__FILE__) . 'richsnippet-review-widget/richsnippet-review-widget.php' );
}
/*if (!function_exists('wpsocial_widg_content')) {
    require_once( plugin_dir_path(__FILE__) . 'templates/widget_content.php' );
}*/

/*-------------------------------- Widget --------------------------------*/
function wpsocial_review_widget() {
    if (!class_exists('wpsocial_widget' ) ) {
        require 'class/wpsocial_widget.php';
    }
}

add_action('widgets_init', 'wpsocial_review_widget');

//add_action('widgets_init', create_function('', 'register_widget("wpsocial_widget");'));


add_action('widgets_init', 'wps_load_widget');

add_action('admin_menu', 'wps_admin_init');
add_action('admin_post_submit-wnp-settings', 'wpsSaveSettings');
add_action('admin_post_submit-wps-company', 'wpsSaveCompany');
add_action('admin_post_submit-facebook-review', 'wpsFacebookReview');
add_action('admin_post_submit-rich-snippets-review', 'wpsSaveRichSnippets');
add_action('admin_post_submit-color-picker', 'saveSocialSeoColorPicker');
add_action('admin_post_submit-save-reviewio-snippet', 'saveReviewsioSnippet');

add_action('admin_post_submit-stop-randomizer_text', 'wpsSaveStopRandomizer');
add_action('admin_post_submit-', 'wpsSaveStopRandomizer');

add_action('init','wp_social_seo_custom_post_type',0);

add_shortcode('facebook-review-slider', 'bartag_func');
add_shortcode('wp-social-seo', 'display_social_seo_slider');
add_shortcode('wps-rich-snippets', 'display_rich_snippets');
add_shortcode('wps-rich-snippets-all', 'display_all_rich_snippets');
add_shortcode('wps-random-content', 'get_random_posts_content'); // [wps-random-content cat="category_name"]
add_shortcode('wps-social-seo-tag', 'display_social_seo_content');//[wps-social-seo-tag tag="tag_name"]
add_shortcode('wps-social-latest-order', 'display_social_latest_order');

add_action('wp_ajax_update_fb_tag','updatefbtag');
add_action('wp_ajax_noprev_update_fb_tag', 'updatefbtag');

add_action('wp_ajax_update_google_tag','updategoogletag');
add_action('wp_ajax_noprev_update_google_tag', 'updategoogletag');


add_action('init', 'wpsocialseo_checkVersion');


function display_social_latest_order()
{
    if ( class_exists( 'WooCommerce' ) ) {
      // Last 30 days
      $after_date = date( 'Y-m-d', strtotime('-30 days') );
      $args = array(
          'post_type' => 'shop_order',
          'post_status' => 'publish',
          'posts_per_page' => -1, // or -1 for all
          'tax_query' => array(
              array(
                  'taxonomy' => 'shop_order_status',
                  'field' => 'slug',
                  'terms' => array('completed')
              ),
          ),
      );

      $args['date_query'] = array(
          'after' => $after_date, //'2012-04-01',
          'inclusive' => true,
      );
      $orders = get_posts($args);
      ob_start();
      include( WPSOCIALSEO_PATH . '/templates/latest_order_content.php');
      $var= ob_get_contents();
      ob_end_clean();
      return $var;


    }
}

function updatefbtag()
{
    global $wpdb;
    $id =  sanitize_text_field($_POST['id']);
    $tag = sanitize_text_field($_POST['tag']);
    $wpdb->update($wpdb->prefix."fbrev_page_review", array('tag'=>$tag), array('id'=>$id));
    $return['type'] = 'success';
    echo json_encode($return);die;
}
function updategoogletag()
{
    global $wpdb;
    $id =  sanitize_text_field($_POST['id']);
    $tag = sanitize_text_field($_POST['tag']);
    $wpdb->update($wpdb->prefix."grp_google_review", array('tag'=>$tag), array('id'=>$id));
    $return['type'] = 'success';
    echo json_encode($return);die;
}


//checking php version
function wpsocialseo_checkVersion()
{
    // Check if PHP is too old
    if (version_compare(PHP_VERSION, WPSOCIALSEO_MIN_PHP_VERSION, '<')) {
        // Display notice
        add_action('admin_notices','wpsocialseo_phpVersionError');
        return false;
    }
}
function wpsocialseo_phpVersionError()
{
    $data = get_plugin_data(__FILE__);
    echo '<div class="error"><p><strong>';
    printf(
        'Error: %3$s requires PHP version %1$s or greater.<br/>' .
        'Your installed PHP version: %2$s',
        WPSOCIALSEO_MIN_PHP_VERSION,
        PHP_VERSION,
        $data['Name']
    );
    echo '</strong></p></div>';
}


/*--------------------------------fbrev Activation --------------------------------*/
function fbrev_activation() {
    global $wpdb;

    if (fbrev_does_need_update()) {
        fbrev_install();
    }

    $wpdb->query("CREATE TABLE " . $wpdb->prefix . "fbrev_page (".
                 "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
                 "page_id VARCHAR(80) NOT NULL,".
                 "name VARCHAR(255) NOT NULL,".
                 "cover TEXT NULL,".
                 "PRIMARY KEY (`id`),".
                 "UNIQUE INDEX fbrev_page_id (`page_id`)".
                 ");");

    $wpdb->query("CREATE TABLE " . $wpdb->prefix . "fbrev_page_review (".
                 "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
                 "page_id BIGINT(20) UNSIGNED NOT NULL,".
                 "rating INTEGER NOT NULL,".
                 "text VARCHAR(10000),".
                 "time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,".
                 "author_id VARCHAR(20),".
                 "author_name VARCHAR(255),".
                 "tag varchar(255) DEFAULT NULL,".
                 "deleted tinyint(1) DEFAULT '0',".
                 "PRIMARY KEY (`id`),".
                 //"UNIQUE INDEX grp_google_review_hash (`id`),".
                 "INDEX fbrev_page_id (`page_id`)".
                 ");");
}

register_activation_hook(__FILE__, 'fbrev_activation');


/*--------------------------------grp Database --------------------------------*/
function grw_activation() {
        grw_install();
}
register_activation_hook(__FILE__, 'grw_activation');

function grw_install($allow_db_install=true) {
    global $wpdb, $userdata;

    $version = (string)get_option('grw_version');
    if (!$version) {
        $version = '0';
    }

    if ($allow_db_install) {
        grw_install_db($version);
    }

    if (version_compare($version, GRW_VERSION, '=')) {
        return;
    }

    add_option('grw_active', '1');
    add_option('grw_google_api_key', '');
    update_option('grw_version', GRW_VERSION);
}

function grw_install_db() {
    global $wpdb;

    $wpdb->query("CREATE TABLE " . $wpdb->prefix . "grp_google_place (".
                 "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
                 "place_id VARCHAR(80) NOT NULL,".
                 "name VARCHAR(255) NOT NULL,".
                 "photo VARCHAR(255),".
                 "icon VARCHAR(255),".
                 "address VARCHAR(255),".
                 "rating DOUBLE PRECISION,".
                 "url VARCHAR(255),".
                 "website VARCHAR(255),".
                 "updated BIGINT(20),".
                 "PRIMARY KEY (`id`),".
                 "UNIQUE INDEX grp_place_id (`place_id`)".
                 ");");

    $wpdb->query("CREATE TABLE " . $wpdb->prefix . "grp_google_review (".
                 "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
                 "google_place_id BIGINT(20) UNSIGNED NOT NULL,".
                 "hash VARCHAR(40) NOT NULL,".
                 "rating INTEGER NOT NULL,".
                 "text VARCHAR(10000),".
                 "time INTEGER NOT NULL,".
                 "language VARCHAR(2),".
                 "author_name VARCHAR(255),".
                 "author_url VARCHAR(255),".
                 "profile_photo_url VARCHAR(255),".
                 "tag varchar(255) DEFAULT NULL,".
                 "deleted tinyint(1) DEFAULT '0',".
                 "PRIMARY KEY (`id`),".
                 "UNIQUE INDEX grp_google_review_hash (`hash`),".
                 "INDEX grp_google_place_id (`google_place_id`)".
                 ");");
}

function grw_reset_db() {
    global $wpdb;

    $wpdb->query("DROP TABLE " . $wpdb->prefix . "grp_google_place;");
    $wpdb->query("DROP TABLE " . $wpdb->prefix . "grp_google_review;");
}
function fbrev_reset_db() {
    global $wpdb;

    $wpdb->query("DROP TABLE " . $wpdb->prefix . "fbrev_page;");
    $wpdb->query("DROP TABLE " . $wpdb->prefix . "fbrev_page_review;");
}
// register_deactivation_hook(__FILE__, 'fbrev_reset_db');
//register_deactivation_hook(__FILE__, 'grw_reset_db');
/*--------------------------------Review Database --------------------------------*/

register_activation_hook(__FILE__, 'review_plugin_install');



    function review_plugin_install() {


        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $table1 = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "review_user_profile`
        (
        id int(11) NOT NULL auto_increment,
        full_name  varchar(255),
        company_name varchar(255),
        subject  longtext,
        message_body longtext,
        review_link_1 varchar(255),
        review_link_2 varchar(255),
        review_link_3 varchar(255),
        PRIMARY KEY  (`id`)
        );
        ";

        dbDelta($table1);

        $table2 = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "review_user_emails`
        (
        id int(11) NOT NULL auto_increment,
        customer_name  varchar(255),
        customer_email varchar(255),
        email_sent_on datetime,
        order_id int,
        PRIMARY KEY  (`id`)
        );
        ";

        dbDelta($table2);
    }

function wpsSaveStopRandomizer()
{
    if ($_POST['stop_randomizer_text'] == 1) {
       update_option('social_seo_stop_randomizer', 1);
    } else {
        update_option('social_seo_stop_randomizer', 0);
    }
}

function wp_social_seo_custom_post_type()
{

    // $permalinks = get_option( 'elm_randomizer_permalinks' );

    // Default values
    // $custom_post_type_slug = 'randomizer';
    // $taxonomy_slug = 'randomizer-category';


    // Register randomizer custom post
    $labels = array(
        'name'               => __( 'Items' ),
        'singular_name'      => __( 'Item' ),
        'menu_name'          => __( 'Content for SEO' ),
        'name_admin_bar'     => __( 'Items' ),
        'add_new'            => __( 'Add New' ),
        'add_new_item'       => __( 'Add New Item' ),
        'new_item'           => __( 'New Item' ),
        'edit_item'          => __( 'Edit Item' ),
        'view_item'          => __( 'View Item' ),
        'all_items'          => __( 'All Items' ),
        'search_items'       => __( 'Search Item' ),
        'parent_item_colon'  => __( 'Parent Item:' ),
        'not_found'          => __( 'No item found.' ),
        'not_found_in_trash' => __( 'No item found in Trash.' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,

        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'social_seo_random_texts' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 100,
        'publicly_queryable' => false,
        'public'=>false,
        'has_archive' => false,
        'query_var' => false,
        'supports'           => array( 'title', 'editor', 'thumbnail' )
    );

    register_post_type( 'social_random_texts', apply_filters( 'social_seo_custom_post_args', $args ) );

    // Register randomizer categories
    $labels = array(
        'name'              => __( 'Categories' ),
        'singular_name'     => __( 'Category' ),
        'search_items'      => __( 'Search Categories' ),
        'all_items'         => __( 'All Categories' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Categories' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'social_seo_randomizer_category' ),
    );

    register_taxonomy( 'randomizer_category', array( 'social_random_texts' ), $args );

}


add_action('restrict_manage_posts', 'wp_social_filter_post_type_by_taxonomy');
function wp_social_filter_post_type_by_taxonomy() {
    global $typenow;
    $post_type = 'social_random_texts'; // change to your post type
    $taxonomy  = 'randomizer_category'; // change to your taxonomy
    if ($typenow == $post_type) {
        $selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        $info_taxonomy = get_taxonomy($taxonomy);
        wp_dropdown_categories(array(
            'show_option_all' => __("Show All {$info_taxonomy->label}"),
            'taxonomy'        => $taxonomy,
            'name'            => $taxonomy,
            'orderby'         => 'name',
            'selected'        => $selected,
            'show_count'      => true,
            'hide_empty'      => true,
        ));
    };
}


add_filter('parse_query', 'wp_social_convert_id_to_term_in_query');
function wp_social_convert_id_to_term_in_query($query) {
    global $pagenow;
    $post_type = 'social_random_texts'; // change to your post type
    $taxonomy  = 'randomizer_category'; // change to your taxonomy
    $q_vars    = &$query->query_vars;
    if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
        $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
        $q_vars[$taxonomy] = $term->slug;
    }
}



function wps_load_widget() {
    register_widget('reviews');
    register_widget('fbpost');
}

function wps_admin_init() {
    global $wpdb;
    $sql = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "rich_snippets_review" . "` (
            `id` bigint(20) unsigned NOT NULL auto_increment,
            `item_name` varchar(255) default NULL,
            `reviewer_name` varchar(255) default NULL,
            `date_reviewed` varchar(255) default NULL,
            `summary` TEXT DEFAULT NULL,
            `description` TEXT DEFAULT NULL,
            `rating` int(10) NOT NULL,
            `status` int(10) DEFAULT 1,
            `dateCreated` timestamp NOT NULL,
            PRIMARY KEY (`id`))ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    $wpdb->query($sql);

    $sql1 = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "random_content_record" . "` (
            `id` bigint(20) unsigned NOT NULL auto_increment,
            `content_id` int(11) default NULL,
            `page` varchar(255) default NULL,
            `category` varchar(255) default NULL,
            PRIMARY KEY (`id`))ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    $wpdb->query($sql1);

    $table_name = $wpdb->prefix . 'rich_snippets_review';
    $tableNameArray = array();
    foreach ($wpdb->get_col("DESC " . $table_name, 0) as $column_name) {
        $tableNameArray[] = $column_name;
    }
    if (!in_array('url', $tableNameArray)) {
        $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "rich_snippets_review" . "` ADD url TEXT NOT NULL AFTER `description`");
    }
    if (!in_array('pageid', $tableNameArray)) {
        $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "rich_snippets_review" . "` ADD pageid int(10) NOT NULL AFTER `url`");
    }
    add_menu_page(__('WP Social', 'wps'), __('WP Social', 'wps'), 'manage_options', 'wps-social-profile', 'wpscallWebNicePlc', '', 100);
    //add_submenu_page('', __('Your company', 'wps'), __('Your company', 'wps'), 'manage_options', 'wps-manage-your-company', 'wpsmanageCompany');
    add_submenu_page('', __('Social seo', 'wps'), __('Social seo', 'wps'), 'manage_options', 'wps-manage-social-seo', 'wpsmanageSocialSeo');
    add_submenu_page('', __('Shortcodes', 'wps'), __('Shortcodes', 'wps'), 'manage_options', 'wps-shortcodes', 'wpsShortcodes');
    add_submenu_page('', __('Facebook review', 'wps'), __('Facebook review', 'wps'), 'manage_options', 'wps-facebook-review', 'wpsmanageFacebookReview');

    add_submenu_page('', __('Facebook review list', 'wps'), __('Facebook review list', 'wps'), 'manage_options', 'wps-facebook-review-list', 'wpsmanageFacebookReviewList');

    add_submenu_page('', __('Facebook review list edit', 'wps'), __('Facebook review list edit', 'wps'), 'manage_options', 'wps-facebook-review-edit', 'wpsmanageFacebookReviewEdit');

    add_submenu_page('', __('Facebook review list status', 'wps'), __('Facebook review list status', 'wps'), 'manage_options', 'wps-facebook-review-status', 'wpsmanageFacebookReviewStatus');

    add_submenu_page('', __('Rich snippets review', 'wps'), __('Rich snippets review', 'wps'), 'manage_options', 'wps-rich-snippets-review', 'wpsmanageRichSnippets');
    add_submenu_page('', __('Rich snippets review', 'wps'), __('Rich snippets review', 'wps'), 'manage_options', 'wps-add-rich-snippets-review', 'wpsmanageAddRichSnippets');
    add_submenu_page('', __('Rich snippets review', 'wps'), __('Rich snippets review', 'wps'), 'manage_options', 'wps-delete-snipepts-review', 'wpsmanageDeleteRichSnippets');
    add_submenu_page('', __('Add a review', 'wps'), __('Add a review', 'wps'), 'manage_options', 'wps-add-review', 'wpsreview');

    add_submenu_page('', __('Feeds', 'wps'), __('Feeds', 'wps'), 'manage_options', 'wps-google-review', 'wpsFeeds');

    add_submenu_page('', __('Feeds list', 'wps'), __('Feeds list', 'wps'), 'manage_options', 'wps-google-review-list', 'wpsFeedsList');

    add_submenu_page('', __('Feeds list Edit', 'wps'), __('Feeds list Edit', 'wps'), 'manage_options', 'wps-google-review-edit', 'wpsFeedEdit');

    add_submenu_page('', __('Feeds list Status', 'wps'), __('Feeds list Status', 'wps'), 'manage_options', 'wps-google-review-status', 'wpsFeedStatus');

    add_submenu_page('', __('Custom Text', 'wps'), __('Custom Text', 'wps'), 'manage_options', 'wps-custom-text', 'wpsCustomText');
    add_submenu_page('', __('Reviews.io', 'wps'), __('Reviews.io', 'wps'), 'manage_options', 'wps-reviewio-snippets', 'wpsReviewIoSnippet');
}

function wps_load_custom_wp_admin_style() {
    wp_enqueue_style('wpsadminstyle', plugins_url('css/wps-admin-style.css', __FILE__));
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-form');
    wp_register_style('colorpickcss', plugins_url('css/colpick.css', __FILE__), array(), '20120208', 'all');
    wp_enqueue_script('colorpickjs', plugins_url('js/colpick.js', __FILE__), array(), '1.0.0', true);
    wp_enqueue_style('colorpickcss');
}

add_action('admin_enqueue_scripts', 'wps_load_custom_wp_admin_style');
add_action('admin_init', 'load_color_picker');

function load_color_picker()
{
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
}
$my_plugin_tabs = array(
    'wps-social-profile' => 'Your Details',
    'wps-manage-social-seo' => 'Social Profiles',
    'wps-reviewio-snippets' => 'Reviews.io',
    'wps-facebook-review' => 'Facebook',
    'wps-google-review' => 'Google',
    'wps-custom-text' => 'SEO Content',
    'wps-rich-snippets-review' => 'Add Review',
    'wps-add-review' => 'WooCommerce Reviews',
    'wps-shortcodes' => 'Shortcodes',
);

function wpscallWebNicePlc() {
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    $get_option_details = unserialize(get_option('wnp_your_company'));
    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);
    ?>

    <script type="text/javascript">

        function wpsValidate() {
            var usernameValue = jQuery('select[name=type]').fieldValue();
            var urlValue = jQuery('input[name=url]').fieldValue();
            var nameValue = jQuery('input[name=name]').fieldValue();
            var telephone = jQuery('input[name=telephone]').fieldValue();
            var logourlValue = jQuery('input[name=logo-url]').fieldValue();
            // usernameValue and passwordValue are arrays but we can do simple
            // "not" tests to see if the arrays are empty
            if (!usernameValue[0]) {
                alert('Please enter a value for the Type');
                return false;
            }
            if (!nameValue[0]) {
                alert('Please enter a value for the Name');
                return false;
            }
            if (!urlValue[0]) {
                alert('Please enter a value for the Url');
                return false;
            }
            if (!logourlValue[0]) {
                alert('Please enter a value for the Logo url');
                return false;
            }
            if (!telephone[0]) {
                alert('Please enter telephone number');
                return false;
            }
            return true;
        }

        jQuery(document).ready(function () {
            jQuery("body").addClass("wps-admin-page")
            // binds form submission and fields to the validation engine
            jQuery('#companyID').ajaxForm({
                beforeSubmit: wpsValidate,
                success: function (data) {
                    jQuery('.success').show();
                }
            });
            jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
            });
        });
    </script>
        <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Company Information');
                ?>
                    <form id="companyID" method="post" action="<?php echo get_admin_url() ?>admin-post.php">
                        <fieldset>
                            <input type='hidden' name='action' value='submit-wps-company' />
                            <div class="alert-box success" style="display:none;"><span>Success : </span>Your company settings has been saved successfully</div>

                            <div class="social-form">
                                <div class="form-left">

                                    <div class="form-group">


                                        <label>Type</label>

                                        <select class="validate[required] text-input" id="type" name="type">

                                            <?php

                                            $org = '';

                                            $personal = '';

                                            if ($get_option_details['type'] == 'Organization')

                                                $org = 'selected="selected"';

                                            if ($get_option_details['type'] == 'Personal')

                                                $personal = 'selected="selected"';

                                            ?>

                                            <option value="Organization" <?php echo $org; ?> >Organization</option>

                                            <option value="Personal" <?php echo $personal; ?>>Personal</option>

                                        </select>

                                    </div>

                                    <div class="form-group">

                                        <label>URL</label>

                                        <input type="text" class="validate[required] text-input" id="url" name="url" value="<?php echo $get_option_details['url']; ?>" />

                                    </div>

                                    <div class="form-group">
                                        <label>Telephone</label>
                                        <input type="text" class="validate[required] text-input" id="telephone" name="telephone" value="<?php echo $get_option_details['telephone']; ?>" />

                                    </div>

                                    <div class="form-group">
                                        <label>Area Served</label>
                                        <input type="text" class="text-input" id="area_served" name="area_served" value="<?php echo $get_option_details['area_served']; ?>" />

                                    </div>

                                    <div class="form-group">
                                        <input class="button-primary" type="submit" value="Update" name="submit" />
                                    </div>

                                </div>

                                <div class="form-right">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="validate[required] text-input" id="name" name="name" value="<?php echo $get_option_details['name']; ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label>Logo URL</label>

                                        <input type="text" class="validate[required] text-input" id="logo-url" name="logo-url" value="<?php echo $get_option_details['logo-url']; ?>" />

                                    </div>

                                    <div class="form-group">

                                        <label>Contact Type</label>

                                        <select class="validate[required] text-input" id="type" name="contact_type">

                                            <option value="">Select contact type</option>

                                            <?php

                                            $contact_types = array('customer support', 'technical support', 'billing support', 'bill payment', 'sales', 'reservations', 'credit card support', 'emergency', 'baggage tracking', 'roadside assistance', 'package tracking');

                                            foreach ($contact_types as $contact_type) {

                                                if ($get_option_details['contact_type'] == $contact_type) {

                                                    $selected_contact_type = 'selected="selected"';

                                                } else {

                                                    $selected_contact_type = '';

                                                }
                                                echo '<option value="' . $contact_type . '" ' . $selected_contact_type . '>' . ucfirst($contact_type) . '</option>';
                                            }

                                            ?>

                                        </select>

                                    </div>

                                    <div class="form-group">

                                        <label>Available Languages :</label>

                                        <input type="text" id="avail_language" name="avail_language" value="<?php echo $get_option_details['avail_language']; ?>" />

                                    </div>

                                </div>
                            </div>

                        </fieldset>
                    </form>

                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                ?>
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                 echo '<div class="about-box">';
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                  echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
            </div>
        <?php displayRight(); ?>
    </div>
    <?php
}

function wpsShortcodes() {
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    $get_option_details = unserialize(get_option('wnp_your_company'));
    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);
    ?>
            <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Shortcodes');
                ?>
                    <div class="inner-left">
                        <div class="shortcode-box">
                            <h4>Reiview Shortcodes</h4>
                            <div class="shortcode-list">
                                <p>This is a shortcode [shortcode_here]</p>
                            </div>
                        </div>

                    </div>
                    <div class="inner-right">
                        <div class="shortcode-box">
                            <h4>Content Shortcodes</h4>
                            <div class="shortcode-list">
                                <p>This is a shortcode [shortcode_here]</p>
                            </div>
                        </div>
                    </div>

                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                ?>
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                 echo '<div class="about-box">';
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                  echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
            </div>
        <?php displayRight(); ?>
    </div>
    <?php
}


function render_rr_show_content() {
    $output = '<p><strong>WP Social SEO</strong> gives you the ability to quick add your Social Profiles in a compliant way so that it shows up in a google search.</p>
               <p>Specify your social profiles to Google <a href="https://developers.google.com/webmasters/structured-data/customize/social-profiles" target="_blank">https://developers.google.com/webmasters/structured-data/customize/social-profiles</a></p>
               <p>Use mark-up on your official website to add your social profile information to the Google Knowledge panel in some searches. Knowledge panels can prominently display your social profile information.</p>
               <p>Our other free plugins can be found at <a href="https://profiles.wordpress.org/pigeonhut#content-plugins/" target="_blank">https://profiles.wordpress.org/pigeonhut#content-plugins/</a> </p>
               <p>To see more about us as a company, visit <a href="https://campaigns.io" target="_blank">https://campaigns.io</a></p>
               <p>Proudly made in Belfast, Northern Ireland.</p>';
    echo $output;
}

function wpsmanageSocialSeo() {
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    $get_option_details = unserialize(get_option('wnp_social_settings'));
    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery("body").addClass("wps-admin-page")
            // binds form submission and fields to the validation engine
            jQuery('#settingsID').ajaxForm({
                success: function (data) {
                    jQuery('.success').show();
                }
            });
            jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
            });
        });
    </script>
        <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Social Profile Settings');

                ?>
                    <form id="settingsID" method="post" action="<?php echo get_admin_url() ?>admin-post.php">
                        <fieldset>
                            <input type='hidden' name='action' value='submit-wnp-settings' />
                            <input type='hidden' name='id' value='<?php echo $getId ?>' />
                            <input type='hidden' name='paged' value='<?php echo $_GET['paged']; ?>' />
                                <div class="alert-box success" style="display:none;"><span>Success : </span>Social profile settings has been saved successfully</div>

                              <div class="social-form">

                                  <div class="form-left">
                                      <div class="form-group">

                                          <label>Facebook</label>

                                          <input type="text" class="validate[required] text-input" id="facebook" name="facebook" value="<?php echo $get_option_details['facebook']; ?>" />

                                      </div>
                                      <div class="form-group">

                                          <label>Google+</label>

                                          <input type="text" class="text-input" id="googleplus" name="googleplus" value="<?php echo $get_option_details['googleplus']; ?>" />
                                      </div>
                                      <div class="form-group">
                                          <label>YouTube</label>
                                          <input type="text" id="youtube" name="youtube" value="<?php echo $get_option_details['youtube']; ?>" />
                                      </div>

                                      <div class="form-group">
                                          <input class="button-primary" type="submit" value="Update" name="submit" />
                                      </div>

                                  </div>

                                  <div class="form-right">
                                      <div class="form-group">
                                          <label>Twitter</label>
                                          <input type="text" id="twitter" name="twitter" value="<?php echo $get_option_details['twitter']; ?>" />

                                      </div>
                                      <div class="form-group">

                                          <label>Instagram</label>

                                          <input type="text" id="instagram" name="instagram" value="<?php echo $get_option_details['instagram']; ?>" />

                                      </div>
                                      <div class="form-group">

                                          <label>LinkedIn</label>

                                          <input type="text" id="linkedin" name="linkedin" value="<?php echo $get_option_details['linkedin']; ?>" />
                                      </div>

                                  </div>



                              </div>
                        </fieldset>
                    </form>

                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                NMRichReviewsAdminHelper::render_container_open('content-container');
                echo '<div class="about-box">';
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
            </div>
            <?php displayRightSocialSeo(); ?>
        </div>

    </div>

    <?php
}

function wpsmanageFacebookReviewStatus()
{
    global $wpdb;
    $id = $_GET['fbrev'];
    $sql = $wpdb->prepare("select * from ".$wpdb->prefix."fbrev_page_review where id=%s",$id);
    $res = $wpdb->get_row($sql, ARRAY_A);
    if($res['deleted'] == 1){
        $array = array();
        $array['deleted'] = 0;
        $wpdb->update($wpdb->prefix."fbrev_page_review", $array, array('id'=>$id));

    }else{
        $array = array();
        $array['deleted'] = 1;
        $wpdb->update($wpdb->prefix."fbrev_page_review", $array, array('id'=>$id));
    }
    new wpseo_admin_notice( "Facebook review status changed" );
    if ($_GET['paged'] != '') {
        wp_redirect(admin_url('admin.php?page=wps-facebook-review-list&paged="' . $_GET['paged'] . '"'));
        exit;
    }
    wp_redirect(admin_url('admin.php?page=wps-facebook-review-list'));
}

function wpsmanageFacebookReviewEdit()
{
        echo '<div class="wrap wp-social-seo">';
        echo '<h1></h1>';
        global $social_main_title;
        echo $social_main_title;
        $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
        wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
        wp_enqueue_style('wp-social-css');
        $get_option_details = unserialize(get_option('wnp_facebook_reviews'));
        $otpr = '';

        global $my_plugin_tabs;
        echo admin_tabs($my_plugin_tabs);
        ?>
          <div id="poststuff" class="metabox-holder ppw-settings">
                <div class="left-side">
                    <?php
                    NMRichReviewsAdminHelper::render_container_open('content-container');
                    NMRichReviewsAdminHelper::render_postbox_open('Facebook Reviews Edit');
                    ?>
                    <?php fbrev_review_edit();
                     ?>
                    <?php
                    NMRichReviewsAdminHelper::render_postbox_close();
                    NMRichReviewsAdminHelper::render_container_close();
                    NMRichReviewsAdminHelper::render_container_open('content-container');
                    echo "<div class='about-box'>";
                    NMRichReviewsAdminHelper::render_postbox_open('About');
                    render_rr_show_content();
                    NMRichReviewsAdminHelper::render_postbox_close();
                    echo "</div>";
                    NMRichReviewsAdminHelper::render_container_close();
                    ?>
                    <!--            </div>           -->
                </div>
                <?php displayRightFacebookReviews(); ?>
            </div>
        </div>
        <?php
    }



function wpsmanageFacebookReviewList() {
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    $get_option_details = unserialize(get_option('wnp_facebook_reviews'));
    $otpr = '';

    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs, 'wps-facebook-review-list');
    ?>
      <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Facebook Reviews');
                ?>
                <?php fbrev_review_listing();
                    $a = new Fbreview_List_Table();

                    $a->prepare_items();
                    $a->display();
                    $a->quickedit();
                 ?>
                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                NMRichReviewsAdminHelper::render_container_open('content-container');
                echo "<div class='about-box'>";
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
                <!--            </div>           -->
            </div>
            <?php displayRightFacebookReviews(); ?>
        </div>
    </div>
    <?php
}


function wpsmanageFacebookReview() {
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    $get_option_details = unserialize(get_option('wnp_facebook_reviews'));
    $otpr = '';

    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery("body").addClass("wps-admin-page")
            // binds form submission and fields to the validation engine
            jQuery('#facebookReview').ajaxForm({
                success: function (data) {
                    jQuery('.success').show();
                }
            });
            jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
            });
            jQuery('#btnAdd').click(function () {
                var num = jQuery('.clonedInput').length, // Checks to see how many "duplicatable" input fields we currently have
                        newNum = new Number(num + 1), // The numeric ID of the new input field being added, increasing by 1 each time
                        newElem = jQuery('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('fast'); // create the new element via clone(), and manipulate it's ID using newNum value
                // First name - text
                newElem.find('.label_fn').attr('for', 'ID' + newNum + '_reviewer-name');
                newElem.find('.input_fn').attr('id', 'ID' + newNum + '_reviewer-name').attr('name', 'reviewer-name[]').val('');

                // Last name - text
                newElem.find('.label_ln').attr('for', 'ID' + newNum + '_post_id');
                newElem.find('.input_ln').attr('id', 'ID' + newNum + '_post_id').attr('name', 'post_id[]').val('');

                // Insert the new element after the last "duplicatable" input field
                jQuery('#entry' + num).after(newElem);
                jQuery('#ID' + newNum + '_reviewer-name').focus();

                // Enable the "remove" button. This only shows once you have a duplicated section.
                jQuery('#btnDel').attr('disabled', false);

                // Right now you can only add 4 sections, for a total of 5. Change '5' below to the max number of sections you want to allow.
                //if (newNum == 5)
                //jQuery('#btnAdd').attr('disabled', true).prop('value', "You've reached the limit"); // value here updates the text in the 'add' button when the limit is reached
            });
            jQuery('#btnDel').click(function () {
                // Confirmation dialog box. Works on all desktop browsers and iPhone.
                //                if (confirm("Are you sure you wish to remove this section? This cannot be undone."))
                //                {
                var num = jQuery('.clonedInput').length;
                // how many "duplicatable" input fields we currently have
                jQuery('#entry' + num).slideUp('fast', function () {
                    jQuery(this).remove();
                    // if only one element remains, disable the "remove" button
                    if (num - 1 === 1)
                        jQuery('#btnDel').attr('disabled', true);
                    // enable the "add" button
                    jQuery('#btnAdd').attr('disabled', false).prop('value', "add section");
                });
                //}
                return false; // Removes the last section you added
            });
            // Enable the "add" button
            jQuery('#btnAdd').attr('disabled', false);
    <?php if (empty($get_option_details)) { ?>
                // Disable the "remove" button
                jQuery('#btnDel').attr('disabled', true);
    <?php } ?>
        });
    </script>
        <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
            <h2><a class="button" href="<?php echo admin_url() ?>admin.php?page=wps-facebook-review-list">List Facebook Reviews</a></h2>
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Facebook Reviews');
                ?>
                <?php fbrev_setting(); ?>
                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                NMRichReviewsAdminHelper::render_container_open('content-container');
                echo "<div class='about-box'>";
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
                <!--            </div>           -->
            </div>
            <?php displayRightFacebookReviews(); ?>
        </div>
    </div>

    <?php
}

function wpsmanageAddRichSnippets() {
    session_start();
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    global $wpdb;
    if ($_REQUEST['action'] == 'edit' && $_REQUEST['review'] != '') {
        $getItemname = '';
        $getReviewername = '';
        $getDate = '';
        $getSummary = '';
        $getDescription = '';
        $getStatus = '';
        $getRating = '';
        $getDetails = $wpdb->get_row('SELECT * FROM  ' . $wpdb->prefix . 'rich_snippets_review WHERE id=' . $_REQUEST['review']);
        if ($getDetails != NULL) {
            $getId = $getDetails->id;
            $getItemname = $getDetails->item_name;
            $getReviewername = $getDetails->reviewer_name;
            $getDate = $getDetails->date_reviewed;
            $getSummary = $getDetails->summary;
            $getDescription = $getDetails->description;
            $getStatus = $getDetails->status;
            $getRating = $getDetails->rating;
            $getUrl = $getDetails->url;
            $getPageId = $getDetails->pageid;
        }
    }
    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery("body").addClass("wps-admin-page")
            // binds form submission and fields to the validation engine
            jQuery('#reviewID').ajaxForm({
                beforeSubmit: wpsValidate,
                success: function (data) {
                    jQuery('.success').show();
                }
            });
            jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
            });
        });
        function wpsValidate() {
            //var itemValue = jQuery('input[name=item-name]').fieldValue();
            var reviewerValue = jQuery('input[name=reviewer-name]').fieldValue();
            var dateValue = jQuery('input[name=date-reviewed]').fieldValue();
            var summary = jQuery('input[name=summary]').fieldValue();
            var descriptionValue = jQuery('textarea[name=description]').fieldValue();
            var ratingValue = jQuery('select[name=rating]').fieldValue();
            // usernameValue and passwordValue are arrays but we can do simple
            // "not" tests to see if the arrays are empty
           /* if (!itemValue[0]) {
                alert('Please enter a title in review of field');
                return false;
            }*/
            if (!reviewerValue[0]) {
                alert('Please enter reviewer name');
                return false;
            }
            if (!dateValue[0]) {
                alert('Please enter date');
                return false;
            }
            //            if (!summary[0]) {
            //                alert('Please enter summary');
            //                return false;
            //            }
            if (!descriptionValue[0]) {
                alert('Please enter description');
                return false;
            }
            if (!ratingValue[0]) {
                alert('Please enter rating');
                return false;
            }
            return true;
        }
    </script>
        <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Rich snippets reviews');
                ?>
                    <form id="reviewID" method="post" action="<?php echo get_admin_url() ?>admin-post.php">
                        <fieldset>
                            <input type='hidden' name='action' value='submit-rich-snippets-review' />
                            <input type='hidden' name='id' value='<?php echo $getId ?>' />
                            <input type='hidden' name='paged' value='<?php echo $_GET['paged']; ?>' />

                            <div class="social-form">
                                <div class="form-left">
                                    <div class="form-group">
                                        <label>Review of</label>
                                        <select id="item-name" name="item-name">
                                            <option value=''>Select Id</option>
                                            <?php
                                                 $product = get_posts_array('product');
                                          /*       print_r($product); exit();
                                            foreach ($product as $product) {
                                                $pChecked = '';
                                                if ($product == $product) {
                                                    $pChecked = 'selected="selected"';
                                                }
                                                $option = '<option ' . $pChecked . ' value="' . $product . '">';
                                                $option .= $product;
                                                $option .= '</option>';
                                                echo $option;
                                            }*/
                                            foreach ($product  as $key => $value) {
                                               //print_r($value); exit();
                                               $pChecked = '';?>

                                               <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                            <?php }?>

                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Reviewer name</label>
                                        <input type="text" id="reviewer-name" name="reviewer-name" value="<?php echo $getReviewername; ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label>Date reviewed</label>
                                        <input type="text"  id="date-reviewed" name="date-reviewed" value="<?php echo $getDate; ?>" />
                                    </div>

                                    <div class="form-group">
                                        <label>Rating</label>
                                        <!-- <input type="text" id="rating" name="rating" value="<?php echo $getRating; ?>" /> -->
                                            <select id="rating" name="rating">
                                                <option value=''>Select rating</option>
                                                <?php
                                                $ratings = array(1, 2, 3, 4, 5);
                                                foreach ($ratings as $rating) {
                                                    $pChecked = '';
                                                    if ($getRating == $rating) {
                                                        $pChecked = 'selected="selected"';
                                                    }
                                                    $option = '<option ' . $pChecked . ' value="' . $rating . '">';
                                                    $option .= $rating;
                                                    $option .= '</option>';
                                                    echo $option;
                                                }
                                                ?>
                                            </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Page id</label>
                                        <select id="pageid" name="pageid">
                                                <option value=''>Select Page</option>
                                                <?php
                                                $pages = get_pages();
                                                foreach ($pages as $page) {
                                                    $pChecked = '';
                                                    if ($getPageId == $page->ID) {
                                                        $pChecked = 'selected="selected"';
                                                    }
                                                    $option = '<option ' . $pChecked . ' value="' . $page->ID . '">';
                                                    $option .= $page->post_title;
                                                    $option .= '</option>';
                                                    echo $option;
                                                }
                                                ?>
                                            </select>
                                    </div>

                                    <div class="form-group">
                                        <input class="button-primary" type="submit" value="Submit" name="submit" />
                                    </div>
                                </div>

                                <div class="form-right">


                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea type="text" id="description" rows="8" name="description"><?php echo stripcslashes($getDescription); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>URL</label>
                                        <input type="text" id="url" name="url" value="<?php echo $getUrl; ?>" />
                                    </div>


                                </div>
                            </div>

                        </fieldset>
                    </form>

                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                NMRichReviewsAdminHelper::render_container_open('content-container');
                echo "<div class='about-box'>";
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
            </div>
            <?php displayRightRichSnippets(); ?>
        </div>
    </div>
    <?php
}

function wpsmanageDeleteRichSnippets() {

    session_start();
    global $wpdb;
    $wpdb->delete($wpdb->prefix . "rich_snippets_review", array('id' => $_GET['review']));
    if ($wpdb->rows_affected > 0) {
        $_SESSION['area_status'] = 'deletesuccess';
    } else {
        $_SESSION['area_status'] = 'deletefailed';
    }
    if ($_GET['paged'] != '') {
        wp_redirect(admin_url('admin.php?page=wps-rich-snippets-review&paged="' . $_GET['paged'] . '"'));
        exit;
    }
    wp_redirect(admin_url('admin.php?page=wps-rich-snippets-review'));
}
$social_main_title = '<h1 class="wp_social_header">Make your reviews work for your business</h1>';

function wpsmanageRichSnippets() {
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    session_start();
   global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery("body").addClass("wps-admin-page")
            jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
            });
        });
    </script>
        <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
             <h2><a class="button" href="<?php echo admin_url() ?>admin.php?page=wps-add-rich-snippets-review">Add New Rich Snippets Review</a></h2>
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Rich snippets reviews');
                ?>
                    <form id="review" name="review" method="post" action="">
                        <?php
                        if ($_REQUEST['action'] == 'delete') {
                            $del = $_REQUEST['review'];
                            if ($del != '') {
                                $idsToDelete = implode($del, ',');
                                global $wpdb;
                                $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "rich_snippets_review WHERE id IN ($idsToDelete)"));
                                if ($wpdb->rows_affected > 0) {
                                    $_SESSION['area_status'] = 'deletesuccess';
                                    wp_redirect(admin_url('admin.php?page=wps-rich-snippets-review&paged="' . $_GET['paged'] . '"'));
                                    exit;
                                }
                            } else {
                                $_SESSION['area_status'] = 'deletefailed';
                                if ($_GET['paged'] != '') {
                                    wp_redirect(admin_url('admin.php?page=wps-rich-snippets-review&paged="' . $_GET['paged'] . '"'));
                                } else {
                                    wp_redirect(admin_url('admin.php?page=wps-rich-snippets-review'));
                                }
                            }
                        }
                        $myListTable = new Wps_Review_List_Table();
                        $myListTable->prepare_items();
                        $myListTable->display();
                        ?>
                    </form>

                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                NMRichReviewsAdminHelper::render_container_open('content-container');
                echo "<div class='about-box'>";
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
            </div>
            <?php displayRightRichSnippets(); ?>
        </div>
    </div>
    <?php
}

function wpsSaveRichSnippets() {
    session_start();
    global $wpdb;
    if (isset($_POST['submit'])) {
        $insertArray = array();
        $insertArray['item_name'] = $_POST['item-name'];
        $insertArray['reviewer_name'] = $_POST['reviewer-name'];
        $insertArray['date_reviewed'] = $_POST['date-reviewed'];
        $insertArray['summary'] = $_POST['summary'];
        $insertArray['description'] = addslashes($_POST['description']);
        $insertArray['rating'] = $_POST['rating'];
        $insertArray['url'] = $_POST['url'];
        $insertArray['pageid'] = $_POST['pageid'];
        if ($_POST['id'] != '') {
            $wpdb->update($wpdb->prefix . "rich_snippets_review", $insertArray, array('id' => $_POST['id']), array('%s', '%s'), array('%d'));
            //if ($wpdb->insert_id > 0) {
            $_SESSION['area_status'] = 'updated';
//            } else {
//                $_SESSION['area_status'] = 'failed';
//            }
        } else {
            $wpdb->insert($wpdb->prefix . "rich_snippets_review", $insertArray, array('%s', '%s'));
            //echo $wpdb->last_query;exit;
            if ($wpdb->insert_id > 0) {
                $_SESSION['area_status'] = 'success';
            } else {
                $_SESSION['area_status'] = 'failed';
            }
        }
    }
}

function wpsFacebookReview() {
    session_start();
    global $wpdb;
    if (isset($_POST['submit'])) {
        $insertArray = array();
        $insertArray['name'] = $_POST['reviewer-name'];
        $insertArray['id'] = $_POST['post_id'];
        $insertArray['review_content'] = $_POST['review_content'];
        $insertArray['review_width'] = $_POST['review_width'];

        $serialize_array = serialize($insertArray);
        update_option('wnp_facebook_reviews', $serialize_array);
        $_SESSION['area_status'] = 'updated';
    }
}

function admin_tabs($tabs, $current = NULL) {
    if (is_null($current)) {
        if (isset($_GET['page'])) {
            $current = $_GET['page'];
        }
    }
    $content = '';
    $content .= '<div class="nav-tab-wrapper social-nav">';
    foreach ($tabs as $location => $tabname) {
        if (preg_match('/'.$location.'/i', $current)) {
            $class = ' nav-tab-active';
        }else {
            $class = '';
        }
        $content .= '<a class="nav-tab' . $class . '" href="?page=' . $location . '">' . $tabname . '</a>';
    }
    $content .= '</div>';
    return $content;
}

function wpsSaveSettings() {
    session_start();
    global $wpdb;
    if (isset($_POST['submit'])) {
        $insertArray = array();
        if ($_POST['facebook'] != '')
            $insertArray['facebook'] = sanitize_text_field($_POST['facebook']);
        if ($_POST['twitter'] != '')
            $insertArray['twitter'] = sanitize_text_field($_POST['twitter']);
        if ($_POST['googleplus'] != '')
            $insertArray['googleplus'] = sanitize_text_field($_POST['googleplus']);
        if ($_POST['instagram'] != '')
            $insertArray['instagram'] = sanitize_text_field($_POST['instagram']);
        if ($_POST['youtube'] != '')
            $insertArray['youtube'] = sanitize_text_field($_POST['youtube']);
        if ($_POST['linkedin'] != '')
            $insertArray['linkedin'] = sanitize_text_field($_POST['linkedin']);
        if ($_POST['myspace'] != '')
            $insertArray['myspace'] = sanitize_text_field($_POST['myspace']);
        if (!empty($insertArray)) {
            $serialize_array = serialize($insertArray);
            update_option('wnp_social_settings', $serialize_array);
            $_SESSION['area_status'] = 'updated';
        }
        // wp_redirect(admin_url('admin.php?page=web-nine-plc'));
    }
}

function wpsSaveCompany() {
    session_start();
    global $wpdb;
    if (isset($_POST['submit'])) {
        $insertArray = array();
        if ($_POST['type'] != '')
            $insertArray['type'] = sanitize_text_field($_POST['type']);
        if ($_POST['name'] != '')
            $insertArray['name'] = sanitize_text_field($_POST['name']);
        if ($_POST['url'] != '')
            $insertArray['url'] = esc_url($_POST['url']);
        if ($_POST['logo-url'] != '')
            $insertArray['logo-url'] = esc_url($_POST['logo-url']);
        if ($_POST['telephone'] != '')
            $insertArray['telephone'] = sanitize_text_field($_POST['telephone']);
//        if ($_POST['other_telephone'] != '')
//            $insertArray['other_telephone'] = sanitize_text_field($_POST['other_telephone']);
        if ($_POST['contact_type'] != '')
            $insertArray['contact_type'] = sanitize_text_field($_POST['contact_type']);
        if ($_POST['area_served'] != '')
            $insertArray['area_served'] = sanitize_text_field($_POST['area_served']);
//        if ($_POST['contact_option'] != '' && !empty($_POST['contact_option']))
//            $insertArray['contact_option'] = sanitize_text_field(implode(',', $_POST['contact_option']));
        if ($_POST['avail_language'] != '')
            $insertArray['avail_language'] = sanitize_text_field($_POST['avail_language']);
        if (!empty($insertArray)) {
            $serialize_array = serialize($insertArray);
            update_option('wnp_your_company', $serialize_array);
            $_SESSION['area_status'] = 'updated';
        }
        // wp_redirect(admin_url('admin.php?page=web-nine-plc'));
    }
}

add_action('wp_footer', 'wps_buffer_end');

function wps_buffer_end() {
    //return;
    if(get_option('wnp_social_settings') && is_string(get_option('wnp_social_settings'))){
        $get_option_details = unserialize(get_option('wnp_social_settings'));
    }


    if(get_option('wnp_your_company') && is_string(get_option('wnp_your_company'))) {
      $get_company_option_details = unserialize(get_option('wnp_your_company'));
    }




    $display_social = '';
    if (isset($get_option_details['facebook']))
        $display_social .= '"' . $get_option_details['facebook'] . '",';
    if (isset($get_option_details['twitter']))
        $display_social .= '"' . $get_option_details['twitter'] . '",';
    if (isset($get_option_details['googleplus']))
        $display_social .= '"' . $get_option_details['googleplus'] . '",';
    if (isset($get_option_details['instagram']))
        $display_social .= '"' . $get_option_details['instagram'] . '",';
    if (isset($get_option_details['youtube']))
        $display_social .= '"' . $get_option_details['youtube'] . '",';
    if (isset($get_option_details['linkedin']))
        $display_social .= '"' . $get_option_details['linkedin'] . '",';
    if (isset($get_option_details['myspace']))
        $display_social .= '"' . $get_option_details['myspace'] . '",';
    $display_social = rtrim($display_social, ",");

    $displayOut = '';
    if (isset($get_company_option_details['telephone'])) {
        $expl_telephone = explode(',', $get_company_option_details['telephone']);
        if (count($expl_telephone) == 1) {
            $displayOut .='"telephone" : "' . $get_company_option_details['telephone'] . '",';
        } else {
            $parts = explode(',', $get_company_option_details['telephone']);
            $displayOut .='"telephone" : ["' . join('", "', $parts) . '"],';
        }
    }

    if (isset($get_company_option_details['contact_type']))
        $displayOut .='"contactType" : "' . $get_company_option_details['contact_type'] . '",';
    if (isset($get_company_option_details['contact_option']) && !empty($get_company_option_details['contact_option'])) {
        $expl_contact_option = explode(',', $get_company_option_details['contact_option']);
        if (count($expl_contact_option) == 1) {
            $displayOut .='"contactOption" : "' . $get_company_option_details['contact_option'] . '",';
        } else {
            $parts = explode(',', $get_company_option_details['contact_option']);
            $displayOut .='"contactOption" : ["' . join('", "', $parts) . '"],';
        }
    }

    if (isset($get_company_option_details['area_served']) && !empty($get_company_option_details['area_served'])) {
        $expl_area_served = explode(',', $get_company_option_details['area_served']);
        if (count($expl_area_served) == 1) {
            $displayOut .='"areaServed" : "' . $get_company_option_details['area_served'] . '",';
        } else {
            //echo $get_company_option_details['area_served'];exit;
            $parts = explode(',', $get_company_option_details['area_served']);
            $displayOut .='"areaServed" : ["' . join('", "', $parts) . '"],';
        }
    }

    if (isset($get_company_option_details['avail_language'])) {
        $expl_avail_language = explode(',', $get_company_option_details['avail_language']);
        if (count($expl_avail_language) == 1) {
            $displayOut .='"availableLanguage" : "' . $get_company_option_details['avail_language'] . '"';
        } else {
            $parts = explode(',', $get_company_option_details['avail_language']);
            $displayOut .='"availableLanguage" : ["' . join('", "', $parts) . '"]';
        }
    }

    $displayOut = rtrim($displayOut, ",");
    if(isset($get_company_option_details['logo-url']) && !preg_match('/http/i', $get_company_option_details['logo-url'])) {
        $get_company_option_details['logo-url'] = $get_company_option_details['url']. $get_company_option_details['logo-url'];
    }

if(isset($get_company_option_details)) {
        echo '<script type="application/ld+json">
    { "@context" : "http://schema.org",
      "@type" : "' . $get_company_option_details['type'] . '",
      "name" : "' . stripslashes($get_company_option_details['name']) . '",
      "url" : "' . $get_company_option_details['url'] . '",
      "logo": "' . $get_company_option_details['logo-url'] . '",
      "sameAs" : [' . $display_social . '],
          "contactPoint" : [
        { "@type" : "ContactPoint",
          ' . $displayOut . '
        } ]
    }
    </script>
    ';
}



}

add_filter('widget_text', 'do_shortcode');

function bartag_func($atts) {
    wp_enqueue_style('carouselcss', plugins_url('../css/jquery.bxslider.css', __FILE__));
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery_carousel', plugins_url('js/jquery.bxslider.js', __FILE__));
    echo "<link href='". plugins_url('../css/jquery.bxslider.css', __FILE__)."' rel='stylesheet' type='text/css'>";
    echo '<script src="'.plugins_url('js/jquery.bxslider.js', __FILE__).'"></script>';
    $get_option_details = unserialize(get_option('wnp_facebook_reviews'));
    $names = $get_option_details['name'];
    $i = 1;
    $render = '';
    $render.='<div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.7&appId=1268357783206283";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, \'script\', \'facebook-jssdk\'));</script>';
    // $render .= '<div id="fb-root"></div><script>(function(d, s, id) {  var js, fjs = d.getElementsByTagName(s)[0];  if (d.getElementById(id)) return;  js = d.createElement(s); js.id = id;  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";  fjs.parentNode.insertBefore(js, fjs);}(document, \'script\', \'facebook-jssdk\'));</script>';
    $render .='<script>jQuery(document).ready(function () {
        jQuery(\'.bxslider-fb\').bxSlider({
        pager :false,
        auto:true,
        mode:\'fade\',
        speed: 3000,
        pause:10000,
        controls:false,
        wrapperClass: \'bx-wrapper-new\',
        autoHover:true,
        adaptiveHeight:true
        });
        });</script>
    <ul class="bxslider-fb">';
    foreach ($names as $name) {
        $render .='<li><div class="fb-post" data-href="'.$get_option_details['id'][$i - 1].'" data-width="'.$get_option_details['review_width'][$i-1].'" data-show-text="true">';
        if($get_option_details['review_content']){
            $render.='<blockquote cite="'.$get_option_details['id'][$i - 1].'" class="fb-xfbml-parse-ignore"><p>'.$get_option_details['review_content'][$i-1].'</p>Posted by <a href="#" role="button">'.$get_option_details['name'][$i-1].'</a></blockquote>';
        }else{
            $render.='<div class = "fb-xfbml-parse-ignore"></div>';
        }
        $render.='</div>';
        $render.='</li>';
        $i++;
    }
    $render .=' </ul>';
    return $render;
}


function display_social_seo_slider($atts=[] )
{

    $res = array();

    if (isset($atts['rotate_random']) && $atts['rotate_random']) {
      $rotate_random = $atts['rotate_random'];
    }else{
        $rotate_random = 5;
    }

    if (isset($atts['facebook_review']) && $atts['facebook_review'] == 'true') {
      $res = fbrev_array( $res, $rotate_random );
    }

    if (isset($atts['google_review']) && $atts['google_review'] == 'true') {
      $res = grw_array( $res, $rotate_random );
    }
    if(isset($atts['style_dark']) && $atts['style_dark'] == 'true') {
        $dark_color = true;
    }
        ob_start();
       include( WPSOCIALSEO_PATH . '/templates/widget_content.php');
       $var= ob_get_contents();
       ob_end_clean();
       return $var;

}

function display_social_seo_content($atts=[])
{
    if (isset($atts['facebook_review']) && $atts['facebook_review'] == 'true') {
      if (isset($atts['tag']) && $atts['tag']) {
        $res = fbrev_array_bytag( $res, $atts['tag'] );
      } else {
        $res = fbrev_array_bytag( $res, '' );
      }

    }else if (isset($atts['google_review']) && $atts['google_review'] == 'true') {
        if( isset($atts['tag']) && $atts['tag']) {
            $res = grw_array_bytag( $res, $atts['tag'] );
        } else {
            $res = grw_array_bytag( $res, '' );
        }

    }
       ob_start();
       include( WPSOCIALSEO_PATH . '/templates/social_seo_widget_content.php');
       $var= ob_get_contents();
       ob_end_clean();
       return $var;
}




function display_rich_snippets() {
    global $wpdb;
    $picker1 = '#CCCCCC';
    $picker2 = '#FFF000';
    $picker3 = '#FFFFFF';
    $picker4 = '#000000';
    $picker5 = '#000000';
    $picker6 = '#000000';

    $get_option_details = unserialize(get_option('social_seo_options_picker'));
    if (!empty($get_option_details)) {
        if (isset($get_option_details['picker1']) && $get_option_details['picker1'] != '')
            $picker1 = $get_option_details['picker1'];
        if (isset($get_option_details['picker2']) && $get_option_details['picker2'] != '')
            $picker2 = $get_option_details['picker2'];
        if (isset($get_option_details['picker3']) && $get_option_details['picker3'] != '')
            $picker3 = $get_option_details['picker3'];
        if (isset($get_option_details['picker4']) && $get_option_details['picker4'] != '')
            $picker4 = $get_option_details['picker4'];
          if (isset($get_option_details['picker5']) && $get_option_details['picker5'] != '')
            $picker5 = $get_option_details['picker5'];
          if (isset($get_option_details['picker6']) && $get_option_details['picker6'] != '')
            $picker6 = $get_option_details['picker6'];


    } else {
        $picker1 = '#CCCCCC';
        $picker2 = '#FFF000';
        $picker3 = '#FFFFFF';
        $picker4 = '#000000';
        $picker5 = '#000000';
        $picker6 = '#000000';

    }
    ?>
       <style>
        .gnrl-class{
            padding: 0px 0px 10px 0px;
            display:block;
            line-height: 20px;
        }
        .gnrl-new-class{
            display:block;
            line-height: 20px;
            float:right;
        }
        .top-class{
            background: none repeat scroll 0 0 <?php echo $picker2; ?>;
            border-radius: 5px;
            color: #000 !important;
            margin-bottom: 5px;
            /*            margin-top: 30px;*/
            padding: 10px;
        }
        .bottom-class {
            background: none repeat scroll 0 0 <?php echo $picker3; ?>;
            border-radius: 5px;
            color: #000;
            display: inline-block;
            float: right;
            font-style: italic;
            font-weight: normal;
            padding: 5px 10px;
            text-align: right;
        }
        .testimonial{
            background: none repeat scroll 0 0 <?php echo $picker1; ?>;
            display:inline-block;
            border-radius:5px;
            padding: 10px;
            width: 100%;
        }
        .display-reviews{
            list-style:none;
        }
        .display-reviews li{
            margin: 0px 0px 10px 0px;
        }
        .listing-reviews{
            width:100%
        }
    </style>
    <script>
        var ratingUrl = "<?php echo plugins_url(); ?>/wp-social-seo/";
    </script>
    <?php
    wp_enqueue_style('carouselcss', plugins_url('../css/jquery.bxslider.css', __FILE__));
    wp_enqueue_style('ratingcss', plugins_url('js/jRating.jquery.css', __FILE__));
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery_carousel', plugins_url('js/jquery.bxslider.js', __FILE__));
    wp_enqueue_script('jquery_rating', plugins_url('js/jRating.jquery.js', __FILE__));
    $Lists = $wpdb->get_results('SELECT * FROM  ' . $wpdb->prefix . 'rich_snippets_review WHERE pageid='.get_the_ID().' ORDER BY rand()');
    $greview = $wpdb->get_row("select avg(rating) as avgrating, min(rating) as worstrating, count(*) as totalreviews from ".$wpdb->prefix."grp_google_review", ARRAY_A);
    //get f review
    $freview = $wpdb->get_row("select avg(rating) as avgrating, min(rating) as worstrating, count(*) as totalreviews from ".$wpdb->prefix."fbrev_page_review", ARRAY_A);
    //get m review
    $mreview = $wpdb->get_row("select avg(rating) as avgrating, min(rating) as worstrating, count(*) as totalreviews from ".$wpdb->prefix."rich_snippets_review", ARRAY_A);

    $count_review = 0;
    $avgrating = 0;
    $avgworstrating = 0;
    $total_reviews = 0;
    if($greview['avgrating']){
      $count_review++;
      $avgrating+= $greview['avgrating'];
      $avgworstrating+=$greview['worstrating'];
      $total_reviews+=$greview['totalreviews'];
    }
    if($freview['avgrating']){
      $count_review++;
      $avgrating+= $freview['avgrating'];
      $avgworstrating+=$freview['worstrating'];
      $total_reviews+=$freview['totalreviews'];
    }
    if($mreview['avgrating']){
      $count_review++;
      $avgrating+= $mreview['avgrating'];
      $avgworstrating+=$mreview['worstrating'];
      $total_reviews+=$mreview['totalreviews'];
    }
    if( $avgrating > 0) {
      $avgrating = number_format(($avgrating)/$count_review,2);
      $avgworstrating = number_format(($avgworstrating)/$count_review,2);
    } else {
      $avgrating = 0;
      $avgworstrating = 0;
    }
    $get_company_option_details = unserialize(get_option('wnp_your_company'));
    if (!empty($Lists)) {
        //echo $wpdb->last_query;
        $i = 0;
        $newi = 1;
        $display = '';

        $display .='<script>jQuery(document).ready(function () {
        jQuery(\'.bxslider-reviews\').bxSlider({
        pager :false,
        auto:true,
        mode:\'fade\',
        speed: 1000,
        pause:4000,
        controls:false,
        autoHover:true,
        adaptiveHeight: true
        });
        jQuery(\'.basic\').jRating({
      isDisabled : true
    });
        });</script>
        <div itemscope itemtype="http://schema.org/Organization>
        <meta itemprop="name" content="'.$get_company_option_details['name'].'"/>
        <div itemscope itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating">
        <div class="avgrating" style="text-align: center;">
            <div class="basic" data-average = "' . $avgrating . '" ></div>
            <div class="average_rating_display">"'.$avgrating.'" Average &nbsp; "'.$total_reviews.'" Reviews</div>
            <div class="avgrating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" style="display:none;">
             <span itemprop="ratingValue" content="'.$avgrating.'"></span>
              <span itemprop="bestRating" content="5"></span>
              <span itemprop="worstRating" content="'.$avgworstrating.'"></span>
              <span itemprop="reviewCount" content="'.$total_reviews.'"></span>
              <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing">';
                if(isset($Lists[0]->item_name) && $Lists[0]->item_name):
                $display.=' <span itemprop="name">'.$Lists[0]->item_name.'</span>';
                else:
                 $display.='<span itemprop="name">'.get_bloginfo('name').'</span>';
                endif;
               $display.='</div>
            </div>

        </div>
                    <ul class="bxslider-reviews">';
        foreach ($Lists as $key1=>$List) {
            $display .='
            <li>
            <div class = "hms-testimonial-container-new" itemprop="reviewRating" itemscope itemtype="http://schema.org/Review">';

            $display.='<div class = "testimonial">
            <div class = "gnrl-new-class" itemprop="reviewRating" itemscope="" itemtype="http://schema.org/Rating"><span itemprop="ratingValue" style="display:none;">' . $List->rating . '</span><div class = "basic" data-average = "' . $List->rating . '" data-id = "pn-display-rich-snippets-' . $newi . '"></div></div>
            <div class = "top-class">
            <div class = "gnrl-class" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name">' . stripcslashes($List->item_name) . '</span></div>
            <div class = "gnrl-class" itemprop = "description">' . preg_replace('/\\\\/', '', $List->description) . '</div>
            </div>
            <div class = "bottom-class">
            <div class = "gnrl-new-class" itemprop="author" itemscope="" itemtype="http://schema.org/Person">Reviewed by <i><a href = "' . $List->url . '" target = "_blank"><span itemprop="name">' . stripcslashes($List->reviewer_name) . '</span></a></i> on <i>' . $List->date_reviewed . '</i></div>

            </div>
            </div>
            </div>
            </li>';
            $newi++;
        }
        $display .=' </ul > ';
        $display.='</div>';
        $display.='</div>';
        return $display;
    } else {
        return '';
    }
    ?>
    <?php
}

function displayRight() {
    ?>
    <div class="right-side">
        <?php
        NMRichReviewsAdminHelper::render_container_open('content-container-right');
        echo '<div class="info-box">';
        NMRichReviewsAdminHelper::render_postbox_open('Information');
        render_rr_information();
        NMRichReviewsAdminHelper::render_postbox_close();
         echo '</div>';
        NMRichReviewsAdminHelper::render_container_close();
        NMRichReviewsAdminHelper::render_container_open('content-container-right');
          echo '<div class="do-box">';
        NMRichReviewsAdminHelper::render_postbox_open('What We Do');
        render_rr_what_we_do();
        NMRichReviewsAdminHelper::render_postbox_close();
         echo '</div>';
        NMRichReviewsAdminHelper::render_container_close();
        ?>
    </div>
    <?php
}

function displayRightSocialSeo() {
    ?>
    <div class="right-side">
        <?php
        NMRichReviewsAdminHelper::render_container_open('content-container-right');
          echo '<div class="info-box">';
        NMRichReviewsAdminHelper::render_postbox_open('Information');
        render_rr_information_social_seo();
        NMRichReviewsAdminHelper::render_postbox_close();
        echo "</div>";
        NMRichReviewsAdminHelper::render_container_close();
        NMRichReviewsAdminHelper::render_container_open('content-container-right');
         echo '<div class="do-box">';
        NMRichReviewsAdminHelper::render_postbox_open('What We Do');
        render_rr_what_we_do();
        NMRichReviewsAdminHelper::render_postbox_close();
        echo "</div>";
        NMRichReviewsAdminHelper::render_container_close();
        ?>
    </div>
    <?php
}

function displayRightFacebookReviews() {
    ?>
    <div class="right-side">
        <?php
        NMRichReviewsAdminHelper::render_container_open('content-container-right');
           echo "<div class='info-box'>";
        NMRichReviewsAdminHelper::render_postbox_open('ShortCodes');

        render_rr_information_facebook_reviews();
        NMRichReviewsAdminHelper::render_postbox_close();
        echo "</div>";
        NMRichReviewsAdminHelper::render_container_close();
        NMRichReviewsAdminHelper::render_container_open('content-container-right');
          echo "<div class='do-box'>";
        NMRichReviewsAdminHelper::render_postbox_open('What We Do');
        render_rr_what_we_do();
        NMRichReviewsAdminHelper::render_postbox_close();
        echo "</div>";
        NMRichReviewsAdminHelper::render_container_close();
        ?>
    </div>
    <?php
}

function displayRightRichSnippets() {
    ?>
    <div class="right-side">
        <?php
        NMRichReviewsAdminHelper::render_container_open('content-container-right');
        echo "<div class='info-box'>";
        NMRichReviewsAdminHelper::render_postbox_open('ShortCodes');
        render_rr_information_rich_snippets();
        NMRichReviewsAdminHelper::render_postbox_close();
         echo "</div>";
        NMRichReviewsAdminHelper::render_container_close();

        NMRichReviewsAdminHelper::render_container_open('content-container-right');
        echo "<div class='color-box'>";
        NMRichReviewsAdminHelper::render_postbox_open('Color picker settings');
        render_rr_color_picker_settings();
        NMRichReviewsAdminHelper::render_postbox_close();
        echo "</div>";
        NMRichReviewsAdminHelper::render_container_close();

        NMRichReviewsAdminHelper::render_container_open('content-container-right');
        echo "<div class='do-box'>";
        NMRichReviewsAdminHelper::render_postbox_open('WordPress Performance');
        render_rr_what_we_do();
        NMRichReviewsAdminHelper::render_postbox_close();
        echo "</div>";
        NMRichReviewsAdminHelper::render_container_close();
        ?>
    </div>
    <?php
}

function render_rr_information() {
   $output = '<span class="green-box">Test your Data using <a target="_blank" href="https://developers.google.com/webmasters/structured-data/testing-tool/" class="g-link">Google\'s Structured Data Testing Tool </a></span>';

   $output .= '<span class="blue-box">Countries may be specified concisely using just their standard ISO-3166 two-letter code, for example US, CA, MX</span>';

   $output .='<span class="blue-box">Optional details about the language spoken. Languages may be specified by their common English name. If omitted, the language defaults to English, for example French, English</span>';
    echo $output;
}

function render_rr_what_we_do() {
    $output = '<a href="https://wordpress.org/plugins/cache-performance/" target="_blank"><img src="https://res.cloudinary.com/wp-images/image/upload/v1495092337/banner-772x250_o1dmrc.png" /></a>';
    $output .='<span class="orange-box">Want to see how else we can help your business, we have a range of <a href="https://profiles.wordpress.org/pigeonhut#content-plugins" target="_blank">Free Performance plugins</a> in the WordPress repository as we believe that by giving back to the community we help to create a better product for everyone to use.</span>';
    echo $output;
}

function render_rr_information_social_seo() {
    $output = '<span class="green-box"><a target="_blank" href="#" class="g-link">Your Social Profiles</a></span>';

    $output .= '<span class="blue-box">Please add the links to your Social Media profiles that you wish to associate with this domain, which will then get added to your sites SERP to be displayed in Google Searches.</span>';

    $output .='<span class="blue-box">View more info via Google <a href="https://developers.google.com/structured-data/customize/social-profiles" class="g-link" target="_blank">Googles Description</a></span>';
    echo $output;
}

function render_rr_information_facebook_reviews() {
 $output = '<span class="blue-box">If you wish to display <strong>Facebook reviews</strong> on your Website, Connect to Facebook, select the relevant FB Page and Submit the settings to update</span>';

  $output.= '<span class="green-box">On a page or post, use the following shortcodes<br>
  For Reviews.io reviews - <strong>[reviews-widget type="socialseo_reviewio_widget"  tag="" height="" num_reviews="3"]<br>
  For Facebook and Google Reviews - [wp-social-seo rotate_random="3" factbook_review="true" google_review="true"]</strong><br>
  to display reviews on your site or select the individual settings with our WP Social SEO Reviews widget .</span>';

    $output .='<span class="blue-box">If you are using Visual Composer, remember to MAP the shortcodes first and then use the VC provided shortcode.</span>';
    echo $output;
}

function render_rr_information_rich_snippets() {
   $output = '<span class="green-box">You can use the following shortcode <strong>[wps-rich-snippets]</strong> to display your reviews on your site or our WP SOCIAL SEO widget.<br>
   Please use manuaal reviews wisely and stay within Googles guidelines. Do not create your own reviews because this will lead to a Google penalty</span>';

   $output .= '<span class="blue-box"><a href="https://developers.google.com/structured-data/rich-snippets/" target="_blank">Google’s Rich Snippets</a> allow your visitors to add reviews to your website that will show up in the SERPs.  For more info, visit the Google page.</span>';
    echo $output;
}

function render_rr_color_picker_settings() {
    session_start();
    global $wpdb;
    $picker1 = '#CCCCCC';
    $picker2 = '#FFF000';
    $picker3 = '#FFFFFF';
    $picker4 = '#000000';
    $picker5 = '#000000';
    $picker6 = '#000000';

    $call_back_admin_email = '';

    $get_option_details = unserialize(get_option('social_seo_options_picker'));
    if (!empty($get_option_details)) {
        if (isset($get_option_details['picker1']) && $get_option_details['picker1'] != '')
            $picker1 = $get_option_details['picker1'];
        if (isset($get_option_details['picker2']) && $get_option_details['picker2'] != '')
            $picker2 = $get_option_details['picker2'];
        if (isset($get_option_details['picker3']) && $get_option_details['picker3'] != '')
            $picker3 = $get_option_details['picker3'];
        if (isset($get_option_details['picker4']) && $get_option_details['picker4'] != '')
            $picker4 = $get_option_details['picker4'];
        if (isset($get_option_details['picker5']) && $get_option_details['picker5'] != '')
            $picker5 = $get_option_details['picker5'];
        if (isset($get_option_details['picker6']) && $get_option_details['picker6'] != '')
            $picker6 = $get_option_details['picker6'];

    } else {
        $picker1 = '#CCCCCC';
        $picker2 = '#FFF000';
        $picker3 = '#FFFFFF';
        $picker4 = '#000000';
        $picker5 = '#000000';
        $picker6 = '#000000';

    }
    _socialStatusMessage('Color picker settings');
    if ($dropdown == 1) {
        $checked = 'checked="checked"';
    } else {
        $checked = '';
    }
    $output = '   <div>
                    <form id="color_picker_form" name="color_picker_form" method="post" action="' . get_admin_url() . 'admin-post.php" onsubmit="return validate();">
                        <fieldset>
                            <input type=\'hidden\' name=\'action\' value=\'submit-color-picker\' />
                            <table width="600px" cellpadding="0" cellspacing="0" class="form-table">
                                <tr>
                                    <td>Background box: </td>
                                    <td><input readonly type="text" id="picker1" name="picker1" style="border-color:' . $picker1 . '" value="' . $picker1 . '"></input></td>
                                </tr>
                                <tr>
                                    <td>Review text background: </td>
                                    <td><input readonly type="text" id="picker2" name="picker2" style="border-color:' . $picker2 . '" value="' . $picker2 . '"></input></td>
                                </tr>
                                <tr>
                                    <td>Reviews Name Background: </td>
                                    <td><input readonly type="text" id="picker3" name="picker3" style="border-color:' . $picker3 . '" value="' . $picker3 . '"></input></td>
                                </tr>
                                <tr>
                                    <td>Reveiw Text color: </td>
                                    <td><input readonly type="text" id="picker5" name="picker5" style="border-color:' . $picker5 . '" value="' . $picker5 . '"></input></td>
                                </tr>
                                <tr>
                                    <td>Reviews Name color: </td>
                                    <td><input readonly type="text" id="picker6" name="picker6" style="border-color:' . $picker6 . '" value="' . $picker6 . '"></input></td>
                                </tr>

                                <tr>
                                    <td colspan="2"><input class="button-primary" type="submit" id="submit_form_settings" name="submit_form_settings" value="Update"></input></td>
                                </tr>
                            </table>
                        </fieldset>
                    </form>   </div>
    <script>
        function validate() {
            var picker1 = jQuery(\'#picker1\').val();
            var picker2 = jQuery(\'#picker2\').val();
            var picker3 = jQuery(\'#picker3\').val();
            var picker4 = jQuery(\'#picker4\').val();
            var picker5 = jQuery(\'#picker5\').val();
            var picker6 = jQuery(\'#picker6\').val();

            var call_back_admin_email = jQuery(\'#call_back_admin_email\').val();
            if (picker1 == \'\' || picker2 == \'\' || picker3 == \'\' || picker4 == \'\' || picker5 == \'\' || picker6 == \'\') {
                alert(\'Please fill all the required fields\');
                return false;
            }
            return true;
        }
        jQuery(document).ready(function () {
            jQuery(\'#picker1,#picker2,#picker3,#picker4,#picker5,#picker6\').colpick({
                layout: \'hex\',
                submit: 0,
                color: \'3289c7\',
                colorScheme: \'dark\',
                onChange: function (hsb, hex, rgb, el, bySetColor) {
                    jQuery(el).css(\'border-color\', \'#\' + hex);
                    // Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
                    if (!bySetColor)
                        jQuery(el).val(\'#\' + hex);
                }
            }).keyup(function () {
                jQuery(this).colpickSetColor(this.value);
            });
        });
    </script>';
    echo $output;
}

function wpsreview() {
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    $get_option_details = unserialize(get_option('wnp_your_company'));
    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);
    ?>

    <script>
        jQuery(document).ready(function () {
            jQuery("body").addClass("wps-admin-page")
            // binds form submission and fields to the validation engine

            jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
            });
        });
    </script>
            <div id="poststuff" class="metabox-holder ppw-settings">

                <div class="full-side">
               <?php require_once( plugin_dir_path(__FILE__) . 'review/content/review_form.php' ); ?>
            </div>
           </div>
    <?php
}

function wpsFeeds() {
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    $get_option_details = unserialize(get_option('wnp_your_company'));
    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);
    ?>

    <script>
        jQuery(document).ready(function () {
            jQuery("body").addClass("wps-admin-page")
            // binds form submission and fields to the validation engine

            jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
            });

        });
    </script>

        <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
            <h2><a class="button" href="<?php echo admin_url() ?>admin.php?page=wps-google-review-list">List Google Reviews</a></h2>
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Setup Google Reviews');
                ?>
                <?php grw_setting(); ?>
                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                NMRichReviewsAdminHelper::render_container_open('content-container');
                echo "<div class='about-box'>";
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
                <!--            </div>           -->
            </div>
                 <?php displayRightFacebookReviews(); ?>
        </div>
    </div>

    <?php
}

function wpsFeedsList()
{
     echo '<div class="wrap wp-social-seo">';
     echo '<h1></h1>';
     global $social_main_title;
     echo $social_main_title;
     $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
     wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
     wp_enqueue_style('wp-social-css');
     $get_option_details = unserialize(get_option('wnp_your_company'));
     global $my_plugin_tabs;
     echo admin_tabs($my_plugin_tabs,'wps-google-review-list');
     ?>

     <script>
         jQuery(document).ready(function () {
             jQuery("body").addClass("wps-admin-page")
             // binds form submission and fields to the validation engine

             jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                 return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
             });

         });
     </script>

         <div id="poststuff" class="metabox-holder ppw-settings">
             <div class="left-side">
                 <?php
                 NMRichReviewsAdminHelper::render_container_open('content-container');
                 NMRichReviewsAdminHelper::render_postbox_open('Google Reviews List');
                 ?>
                 <?php grw_review_list();
                 $a = new Grwreview_List_Table();
                 $a->prepare_items();
                 $a->display();
                 $a->quickedit();

                 ?>
                 <?php
                 NMRichReviewsAdminHelper::render_postbox_close();
                 NMRichReviewsAdminHelper::render_container_close();
                 NMRichReviewsAdminHelper::render_container_open('content-container');
                 echo "<div class='about-box'>";
                 NMRichReviewsAdminHelper::render_postbox_open('About');
                 render_rr_show_content();
                 NMRichReviewsAdminHelper::render_postbox_close();
                 echo "</div>";
                 NMRichReviewsAdminHelper::render_container_close();
                 ?>
                 <!--            </div>           -->
             </div>
                  <?php displayRightFacebookReviews(); ?>
         </div>
     </div>

     <?php
 }

 function wpsFeedStatus()
 {
    global $wpdb;
    $id = $_GET['grwrev'];
    $sql = $wpdb->prepare("select *  from ".$wpdb->prefix."grp_google_review where id= %s",$id);
    $res = $wpdb->get_row($sql, ARRAY_A);
    if ($res['deleted'] == 1) {
        $wpdb->update($wpdb->prefix."grp_google_review", array('deleted'=>0), array('id'=>$id));
    } else {
        $wpdb->update($wpdb->prefix."grp_google_review", array('deleted'=>1), array('id'=>$id));
    }
    new wpseo_admin_notice( "Google review staus changed." );
    if ($_GET['paged'] != '') {
        wp_redirect(admin_url('admin.php?page=wps-google-review-list&paged="' . $_GET['paged'] . '"'));
        exit;
    }
    wp_redirect(admin_url('admin.php?page=wps-google-review-list'));
 }

function wpsFeedEdit()
{
     echo '<div class="wrap wp-social-seo">';
     echo '<h1></h1>';
     global $social_main_title;
     echo $social_main_title;
     $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
     wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
     wp_enqueue_style('wp-social-css');
     $get_option_details = unserialize(get_option('wnp_your_company'));
     global $my_plugin_tabs;
     echo admin_tabs($my_plugin_tabs);
     ?>

     <script>
         jQuery(document).ready(function () {
             jQuery("body").addClass("wps-admin-page")
             // binds form submission and fields to the validation engine

             jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                 return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
             });

         });
     </script>

         <div id="poststuff" class="metabox-holder ppw-settings">
             <div class="left-side">
                 <?php
                 NMRichReviewsAdminHelper::render_container_open('content-container');
                 NMRichReviewsAdminHelper::render_postbox_open('Google Review Edit');
                 ?>
                 <?php grw_review_edit();
                 ?>
                 <?php
                 NMRichReviewsAdminHelper::render_postbox_close();
                 NMRichReviewsAdminHelper::render_container_close();
                 NMRichReviewsAdminHelper::render_container_open('content-container');
                 echo "<div class='about-box'>";
                 NMRichReviewsAdminHelper::render_postbox_open('About');
                 render_rr_show_content();
                 NMRichReviewsAdminHelper::render_postbox_close();
                 echo "</div>";
                 NMRichReviewsAdminHelper::render_container_close();
                 ?>
                 <!--            </div>           -->
             </div>
                  <?php displayRightFacebookReviews(); ?>
         </div>
     </div>

     <?php
 }
function wpsReviewIoSnippet()
{
    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    $get_option_details = unserialize(get_option('wnp_your_company'));
    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);

        $settings = unserialize(get_option('social_seo_reviewsio_snippet'));

    ?>

        <script>
            jQuery(document).ready(function () {
                jQuery("body").addClass("wps-admin-page")
                jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                    return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
                });
            });
        </script>
        <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Reviews.io');
                ?>


                   <form id="frm_random" action="<?php echo get_admin_url() ?>admin-post.php" method="post">
                       <fieldset>

                           <div class="alert-box success" style="display:none;"><span>Success : </span>Review.io settings saved</div>



                           <div class="social-form">
                               <div class="form-left">
                                   <div class="form-group">

                                       <label for="store_domain">Your Reviews.io account name</label>

                                       <input type="text" name="store_domain" id="store_domain" value="<?php echo $settings['store_domain']; ?>">
                                       <input type='hidden' name='action' value='submit-save-reviewio-snippet' />

                                   </div>
                                   <div class="form-group">
                                       <input type="checkbox" <?php checked('1', $settings['names']); ?> name="names" id="names" value="1">
                                       <label for="names" class="for_checkbox">Show Reviewers name</label>
                                   </div>
                                   <div class="form-group">
                                       <label for="primary_color"><?php _e('Stars Color');?></label>
                                       <input class=" my-color-picker" id="primary_color" name="primary_color" type="text" value="<?php echo $settings['primary_color']; ?>" />
                                   </div>
                                   <div class="form-group">
                                       <label for="background_color"><?php _e('Focus Color');?></label>
                                       <input class="my-color-picker" id="background_color" name="background_color" type="text" value="<?php echo $settings['background_color']; ?>" />
                                   </div>
                                   <div class="form-group">
                                       <input class="button-primary" type="submit" value="Update" name="submit">
                                   </div>

                               </div>


                               <div class="form-right">
                                   <div class="form-group">

                                       <input type="checkbox" name="footer" id="footer" <?php checked('1', $settings['footer']); ?> value="1">
                                       <label for="footer" class="for_checkbox">Show Reviews.io badge</label>

                                   </div>






                                   <div class="form-group">





                                       <input type="checkbox" <?php checked('1', $settings['dates']); ?>  name="dates" id="dates" value="1">
                                       <label for="dates" class="for_checkbox">Show Review Date</label>

                                   </div>
                                   <div class="form-group">









                                       <label for="text_color"><?php _e('Text Color');?></label>

                                       <input class="my-color-picker" id="text_color" name="text_color" type="text" value="<?php echo $settings['text_color']; ?>" />

                                   </div>
                                   <div class="form-group">






                                       <label for="header_color"><?php _e('Main Widget Color');?></label>

                                       <input class="my-color-picker" id="header_color" name="header_color" type="text" value="<?php echo $settings['header_color']; ?>" />
                                   </div>

                               </div>





                           </div>
                       </fieldset>




                   </form>

                <script type='text/javascript'>
                           jQuery(document).ready(function($) {
                               $('.my-color-picker').wpColorPicker();
                           });
                </script>


                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                ?>
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                echo "<div class='about-box'>";
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
            </div>
              <?php displayRight(); ?>
        </div>
    </div>

<?php

}


function wpsCustomText() {

    echo '<div class="wrap wp-social-seo">';
    echo '<h1></h1>';
    global $social_main_title;
    echo $social_main_title;
    $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
    wp_register_style('wp-social-css', $pluginDirectory . 'css/wp-social-seo.css');
    wp_enqueue_style('wp-social-css');
    $get_option_details = unserialize(get_option('wnp_your_company'));
    global $my_plugin_tabs;
    echo admin_tabs($my_plugin_tabs);
    ?>

    <script>
        jQuery(document).ready(function () {

            jQuery("body").addClass("wps-admin-page")
            jQuery('#frm_random').submit(function(event){
                event.preventDefault();
                jQuery.post(jQuery('#frm_random').attr('action'),jQuery('#frm_random').serialize(),function(data){
                    jQuery('.success').show();
                })
            })

            jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
            });
        });
    </script>
        <div id="poststuff" class="metabox-holder ppw-settings">
            <div class="left-side">
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                NMRichReviewsAdminHelper::render_postbox_open('Custom Text');
                ?>
                <p>Ever wanted to embed some custom text messages or images in your sidebar and have it rotate among your testimonials or reviews ?  Now you can.<br>
                Add this short code to randomize custom texts <b>[wps-random-content cat="category_slug"]</b> where category_slug is the slug of category.</p>

                    <?php
                        $randomizer = get_option('social_seo_stop_randomizer');
                     ?>

                    <form id="frm_random" action="<?php echo get_admin_url() ?>admin-post.php" method="post">
                        <fieldset>
                            <div class="alert-box success" style="display:none;"><span>Success : </span>Text randomizer settings saved</div>
                            <div class="social-form">
                                <div class="form-left">
                                    <div class="form-group">
                                        <input type='hidden' name='action' value='submit-stop-randomizer_text' />
                                    </div>

                                    <div class="form-group">
                                        <input type="checkbox" name="stop_randomizer_text" value="1" <?php  if($randomizer == 1)echo 'checked="checked"'; ?>>
                                        <label for="stop_randomizer" class="for_checkbox">Stop custom text randomiser</label>
                                    </div>

                                    <div class="form-group">
                                        <a href="/wp-admin/edit.php?post_type=social_random_texts">Add Random Text</a>
                                    </div>

                                    <div class="form-group">
                                        <input class="button-primary" type="submit" value="Update" name="submit">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                <?php
                NMRichReviewsAdminHelper::render_postbox_close();
                NMRichReviewsAdminHelper::render_container_close();
                ?>
                <?php
                NMRichReviewsAdminHelper::render_container_open('content-container');
                echo "<div class='about-box'>";
                NMRichReviewsAdminHelper::render_postbox_open('About');
                render_rr_show_content();
                NMRichReviewsAdminHelper::render_postbox_close();
                echo "</div>";
                NMRichReviewsAdminHelper::render_container_close();
                ?>
            </div>
        <?php displayRight(); ?>
        </div>
    </div>
    <?php
}

function _socialStatusMessage($string) {
    if ($_SESSION['area_status'] == 'success') {
        unset($_SESSION['area_status']);
        ?>
        <div class="alert-box success"><span>Success : </span>New <?php echo $string; ?> has been added successfully</div>
        <?php
    } else if ($_SESSION['area_status'] == 'failed') {
        unset($_SESSION['area_status']);
        ?>
        <div class="alert-box errormes"><span>Error : </span>Problem in creating new <?php echo $string; ?>.</div>
        <?php
    } else if ($_SESSION['area_status'] == 'updated') {
        unset($_SESSION['area_status']);
        ?>
        <div class="alert-box success"><span>Success : </span><?php echo $string; ?> has been updated successfully.</div>
        <?php
    } else if ($_SESSION['area_status'] == 'deletesuccess') {
        unset($_SESSION['area_status']);
        ?>
        <div class="alert-box success"><span>Success : </span><?php echo $string; ?> has been deleted successfully.</div>
        <?php
    } else if ($_SESSION['area_status'] == 'deletefailed') {
        unset($_SESSION['area_status']);
        ?>
        <div class="alert-box errormes"><span>Error : </span>Problem in deleting <?php echo $string; ?>.</div>
        <?php
    } else if ($_SESSION['area_status'] == 'invalid_file') {
        unset($_SESSION['area_status']);
        ?>
        <div class="alert-box errormes"><span>Error : </span><?php echo $string; ?> should be a PHP file.</div>
        <?php
    }
}

function saveSocialSeoColorPicker() {
    session_start();
    global $wpdb;
    if (isset($_POST['submit_form_settings'])) {
        if (isset($_POST['picker1']))
            $insertArray['picker1'] = $_POST['picker1'];
        if (isset($_POST['picker2']))
            $insertArray['picker2'] = $_POST['picker2'];
        if (isset($_POST['picker3']))
            $insertArray['picker3'] = $_POST['picker3'];
        if (isset($_POST['picker4']))
            $insertArray['picker4'] = $_POST['picker4'];
        if (isset($_POST['picker5']))
            $insertArray['picker5'] = $_POST['picker5'];
        if (isset($_POST['picker6']))
            $insertArray['picker6'] = $_POST['picker6'];

        $serialize_array = serialize($insertArray);
        update_option('social_seo_options_picker', $serialize_array);
        $_SESSION['area_status'] = 'updated';
        wp_redirect(admin_url('admin.php?page=wps-rich-snippets-review'));
    }
    wp_redirect(admin_url('admin.php?page=wps-rich-snippets-review'));
}

function saveReviewsioSnippet()
{
    session_start();
    global $wpdb;
    if (isset($_POST['submit'])) {
        if (isset($_POST['store_domain']))
            $insertArray['store_domain'] = $_POST['store_domain'];
        if (isset($_POST['header_color']))
            $insertArray['header_color'] = $_POST['header_color'];
        if (isset($_POST['text_color']))
            $insertArray['text_color'] = $_POST['text_color'];
        if (isset($_POST['background_color']))
            $insertArray['background_color'] = $_POST['background_color'];
        if (isset($_POST['primary_color']))
            $insertArray['primary_color'] = $_POST['primary_color'];
        if (isset($_POST['names']))
            $insertArray['names'] = $_POST['names'];
        if (isset($_POST['footer']))
            $insertArray['footer'] = $_POST['footer'];
        if (isset($_POST['dates']))
            $insertArray['dates'] = $_POST['dates'];


        $serialize_array = serialize($insertArray);
        update_option('social_seo_reviewsio_snippet', $serialize_array);
        $_SESSION['review_status'] = 'updated';
        wp_redirect(admin_url('admin.php?page=wps-reviewio-snippets'));
    }
    wp_redirect(admin_url('admin.php?page=wps-reviewio-snippets'));
}


function get_random_posts_content($atts)
{
    global $wpdb;
    $serverUrl = explode('?',$_SERVER['REQUEST_URI']);
    $serverUrl = $serverUrl[0];
    $serverUrl = $_SERVER['HTTP_HOST'].$serverUrl;
    $rcontents = $wpdb->get_row("select * from ".$wpdb->prefix."random_content_record where page='".$serverUrl."' and category='".$atts['cat']."'",ARRAY_A);
    $is_stop_random = get_option('social_seo_stop_randomizer');

    if($rcontents && $is_stop_random == 1){
        $post = get_post( $rcontents['content_id'] );

        return $post->post_content;
    }
    $args = array(
        'post_type' => 'social_random_texts',
        /*'tax_query' => array(
            array(
                'taxonomy' => 'randomizer_category',
                'field'    => 'slug',
                'terms'    => $atts['cat'],
            ),
        ),*/
    );

    if ( isset($atts['cat'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'randomizer_category',
                'field'    => 'slug',
                'terms'    => $atts['cat'],
            ),
        );
    }

    $loop = new WP_Query( $args );
    $postarray = array();
    $postarrayId = array();
    if ($loop->posts) {
            foreach($loop->posts as $posts){
               $postarray[] = $posts->post_content;
               $postarrayId[] = $posts->ID;
            }

     $array = array_rand($postarray, 1);
     $serverUrl = explode('?',$_SERVER['REQUEST_URI']);
     $serverUrl = $serverUrl[0];
     $serverUrl = $_SERVER['HTTP_HOST'].$serverUrl;
     $rcontents = $wpdb->get_row("select * from ".$wpdb->prefix."random_content_record where page='".$serverUrl."' and category='".$atts['cat']."'",ARRAY_A);

     if($rcontents){
        //update
        $wpdb->update( $wpdb->prefix.'random_content_record', array('page'=>$serverUrl,'content_id'=>$postarrayId[$array], 'category' => $atts['cat']), array('id' => $rcontents['id']));
     }else{
        //insert
        $wpdb->insert($wpdb->prefix."random_content_record", array('page'=>$serverUrl,'content_id'=>$postarrayId[$array], 'category' => $atts['cat']));
     }

    return $postarray[$array];
    } else{
        return;
    }
}

function display_all_rich_snippets() {
    session_start();
    global $wpdb;
    $get_option_details = unserialize(get_option('social_seo_options_picker'));
    if (!empty($get_option_details)) {
        if (isset($get_option_details['picker1']) && $get_option_details['picker1'] != '')
            $picker1 = $get_option_details['picker1'];
        if (isset($get_option_details['picker2']) && $get_option_details['picker2'] != '')
            $picker2 = $get_option_details['picker2'];
        if (isset($get_option_details['picker3']) && $get_option_details['picker3'] != '')
            $picker3 = $get_option_details['picker3'];
    } else {
        $picker1 = '#CCCCCC';
        $picker2 = '#FFF000';
        $picker3 = '#FFFFFF';
    }
    wp_enqueue_style('ratingcss', plugins_url('js/jRating.jquery.css', __FILE__));
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery_rating', plugins_url('js/jRating.jquery.js', __FILE__));
    ?>
    <style>
        .gnrl-class-all{
            padding: 0px 0px 10px 0px;
            display:block;
            line-height: 20px;
        }
        .gnrl-new-class-all{
            display:block;
            line-height: 20px;
            float:right;
        }
        .top-class-all{
            background: none repeat scroll 0 0 <?php echo $picker2; ?>;
            border-radius: 5px;
            color: #000 !important;
            margin-bottom: 5px;
            /*            margin-top: 30px;*/
            padding: 10px;
        }
        .bottom-class-all {
            background: none repeat scroll 0 0 <?php echo $picker3; ?>;
            border-radius: 5px;
            color: #000;
            display: inline-block;
            float: right;
            font-style: italic;
            font-weight: normal;
            padding: 5px 10px;
            text-align: right;
        }
        .testimonial-all{
            background: none repeat scroll 0 0 <?php echo $picker1; ?>;
            display:inline-block;
            border-radius:5px;
            padding: 10px;
            width: 100%;
        }
        .display-all-reviews-all{
            list-style:none;
        }
        .display-all-reviews-all li{
            margin: 0px 0px 10px 0px;
        }
        .listing-all-reviews-all{
            width:100%
        }
    </style>
    <script>
        var ratingUrl = "<?php echo plugins_url(); ?>/wp-social-seo/";
    </script>

    <?php
    //get g review
    $greview = $wpdb->get_row("select avg(rating) as avgrating, min(rating) as worstrating, count(*) as totalreviews from ".$wpdb->prefix."grp_google_review", ARRAY_A);
    //get f review
    $freview = $wpdb->get_row("select avg(rating) as avgrating, min(rating) as worstrating, count(*) as totalreviews from ".$wpdb->prefix."fbrev_page_review", ARRAY_A);
    //get m review
    $mreview = $wpdb->get_row("select avg(rating) as avgrating, min(rating) as worstrating, count(*) as totalreviews from ".$wpdb->prefix."rich_snippets_review", ARRAY_A);

    $count_review = 0;
    $avgrating = 0;
    $avgworstrating = 0;
    $total_reviews = 0;
    if($greview['avgrating']){
      $count_review++;
      $avgrating+= $greview['avgrating'];
      $avgworstrating+=$greview['worstrating'];
      $total_reviews+=$greview['total_reviews'];
    }
    if($freview['avgrating']){
      $count_review++;
      $avgrating+= $freview['avgrating'];
      $avgworstrating+=$freview['worstrating'];
      $total_reviews+=$freview['total_reviews'];
    }
    if($mreview['avgrating']){
      $count_review++;
      $avgrating+= $mreview['avgrating'];
      $avgworstrating+=$mreview['worstrating'];
      $total_reviews+=$mreview['total_reviews'];
    }
    if( $avgrating > 0) {
      $avgrating = number_format(($avgrating)/$count_review,2);
      $avgworstrating = number_format(($avgworstrating)/$count_review,2);
    } else {
      $avgrating = 0;
      $avgworstrating = 0;
    }

    $Lists = $wpdb->get_results('SELECT * FROM  ' . $wpdb->prefix . 'rich_snippets_review ORDER BY rand()');
    $get_company_option_details = unserialize(get_option('wnp_your_company'));
    if (!empty($Lists)) {
        $i = 0;
        $newi = 1;
        $display = '';
        $display .= '<div id="fb-root"></div>
        <script>(function(d, s, id) {  var js, fjs = d.getElementsByTagName(s)[0];  if (d.getElementById(id)) return;  js = d.createElement(s); js.id = id;  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";  fjs.parentNode.insertBefore(js, fjs);}(document, \'script\', \'facebook-jssdk\'));</script>';
        $display .='<script>jQuery(document).ready(function () {
        jQuery(\'.basic\').jRating({
      isDisabled : true
    });
        });</script>
        <div itemscope itemtype="http://schema.org/Organization">
        <meta itemprop="name" content="'.$get_company_option_details['name'].'"/>
        <div class="avgrating" style="text-align: center;">
            <div class="basic" data-average = "' . $avgrating . '" ></div>
              <div class="average_rating_display">"'.$avgrating.'" Average &nbsp; "'.$total_reviews.'" Reviews</div>
            <div class="avgrating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" style="display:none;">
              <span itemprop="ratingValue" content="'.$avgrating.'"/>
              <span itemprop="bestRating"  content="5"></span>
              <span itemprop="worstRating" content="'.$avgworstrating.'"></span>
              <span itemprop="reviewCount" content="'.$total_reviews.'"></span>
              <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing">';
                if(isset($Lists[0]->item_name) && $Lists[0]->item_name):
                $display.=' <span itemprop="name">'.$Lists[0]->item_name.'</span>';
                else:
                 $display.='<span itemprop="name">'.get_bloginfo('name').'</span>';
                endif;
               $display.='</div>
              </div>
          </div>
        <div class="listing-all-reviews-all"><ul class="display-all-reviews-all">';
        foreach ($Lists as $key1=>$List) {
            $display .='
            <li>
            <div class = "hms-testimonial-container-all" itemprop="reviewRating" itemscope itemtype="http://schema.org/Review">';

            $display.='<div class = "testimonial-all">

             <div class = "gnrl-new-class-all" itemprop="reviewRating" itemscope="" itemtype="http://schema.org/Rating"> <span itemprop="ratingValue" style="display:none;">' . $List->rating . '</span>
             <div class = "basic" data-average = "' . $List->rating . '" data-id = "pn-display-all-rich-snippets-' . $newi . '">

             </div>
             </div>


            <div class = "top-class-all">
            <div class = "gnrl-class-all" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name">' . stripcslashes($List->item_name) . '</span></div>
            <div class = "gnrl-class-all" itemprop = "description">' . preg_replace('/\\\\/', '', $List->description) . '</div>
            </div>
            <div class = "bottom-class-all">

            <div class = "gnrl-new-class-all" itemprop="author" itemscope="" itemtype="http://schema.org/Person">Reviewed by <i><a href = "' . $List->url . '" target = "_blank"><span itemprop="name">' . stripcslashes($List->reviewer_name) . '</span></a></i> on <i>' . $List->date_reviewed . '</i>

            </div>

            </div>
            </div>
            </div>
            </li>';
            $newi++;
        }
        $display .=' </ul > </div>';
        $display.='</div>';
        return $display;
    }
}

function get_posts_array( $post_type = 'post', $flip = false )
{
    global $wpdb;

    $res = $wpdb->get_results( "SELECT `ID`, `post_title` FROM `" .$wpdb->prefix. "posts` WHERE `post_type` = '$post_type' AND `post_status` = 'publish' ", ARRAY_A );

    $return = array();
    foreach( $res as $k => $r) {
        if( $flip ) {
            if( isset( $return[social_sh_set($r, 'post_title')] ) ) $return[social_sh_set($r, 'post_title').$k] = social_sh_set($r, 'ID');
            else $return[social_sh_set($r, 'post_title')] = social_sh_set( $r, 'ID' );
        }
        else $return[social_sh_set($r, 'ID')] = social_sh_set($r, 'post_title');
    }

    return $return;
}
function social_sh_set( $var, $key, $def = '' )
{
    if( !$var ) return false;

    if( is_object( $var ) && isset( $var->$key ) ) return $var->$key;
    elseif( is_array( $var ) && isset( $var[$key] ) ) return $var[$key];
    elseif( $def ) return $def;
    else return false;
}


function fbrev_array( $res ,$limit) {
    global $wpdb;

    $fb_options = get_option( 'wp_social_seo_facebook_tab' );

    $fdn = new wpsocial_DotNotation( $fb_options );
 //print_r($fdn ); exit('asd');
    foreach ($fb_options as $variable => $value) {
        ${$variable} = $fdn->get( $variable );
    }

    if ( ! $page_id ) {
        return $res;
    }


    $fb_reviews = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."fbrev_page_review WHERE page_id = %s and `text` is not null and `deleted` != '1' order by rand() limit ".$limit, $page_id ) );
    //print_r($fb_reviews ); exit();

    if ( $fb_reviews )
    foreach ($fb_reviews  as  $fb_review ) {

        $res[] = array(
                'type'          => 'fb',
                'page_image'    => 'https://graph.facebook.com/'.$page_id.'/picture',
                'page_name'     => $page_name,
                'cover'     => $page_access_token,
                'page_link'     => 'https://fb.com/' . $page_id,
                'name'          => $fb_review->author_name,
                'image'         => 'https://graph.facebook.com/'. $fb_review->author_id.'/picture',
                'rating'        => $fb_review->rating,
                'time'          => $fb_review->time,
                'text'          => $fb_review->text,
            );
    }

    return $res;
}

function fbrev_array_bytag( $res, $tag) {
    global $wpdb;

    $fb_options = get_option( 'wp_social_seo_facebook_tab' );

    $fdn = new wpsocial_DotNotation( $fb_options );
 //print_r($fdn ); exit('asd');
    foreach ($fb_options as $variable => $value) {
        ${$variable} = $fdn->get( $variable );
    }

    if ( ! $page_id ) {
        return $res;
    }


    $fb_review = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."fbrev_page_review WHERE page_id = %s and `text` is not null and `tag`=%s and `deleted` != '1' order by rand() limit 1", $page_id, $tag ) );


    if ( $fb_review )
        $res[] = array(
                'type'          => 'fb',
                'page_image'    => 'https://graph.facebook.com/'.$page_id.'/picture',
                'page_name'     => $page_name,
                'cover'     => $page_access_token,
                'page_link'     => 'https://fb.com/' . $page_id,
                'name'          => $fb_review->author_name,
                'image'         => 'https://graph.facebook.com/'. $fb_review->author_id.'/picture',
                'rating'        => $fb_review->rating,
                'time'          => $fb_review->time,
                'text'          => $fb_review->text,
            );
    return $res;
}

function grw_array( $res ,$limit) {
    global $wpdb;

    $google_options = get_option( 'wp_social_seo_google_tab' );

    $fdn = new wpsocial_DotNotation( $google_options );

    foreach ($google_options as $variable => $value) {
        ${$variable} = $fdn->get( $variable );
    }

    if ( ! $place_id ) {
        return $res;
    }

    $place = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "grp_google_place WHERE place_id = %s", $place_id));
    $reviews = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "grp_google_review WHERE google_place_id = %d and `text` is not null and `deleted` != '1' order by rand() limit ".$limit, $place->id));


    if ( $reviews )
    foreach ($reviews  as  $review ) {

        $res[] = array(
                'type'          => 'google',
                'page_image'    => esc_url( $place->icon ),
                'page_name'     => $place->name,
                'page_link'     => $place->url,
                'name'          => (strlen($review->author_name) > 0) ? $review->author_name : grw_i('Google User'),
                'image'         => $review->profile_photo_url,
                'rating'        => $review->rating,
                'time'          => date( 'Y-m-d h:i:s', $review->time ),
                'text'          => $review->text,
            );
    }

    return $res;
}

function grw_array_bytag( $res ,$tag) {
    global $wpdb;

    $google_options = get_option( 'wp_social_seo_google_tab' );

    $fdn = new wpsocial_DotNotation( $google_options );

    foreach ($google_options as $variable => $value) {
        ${$variable} = $fdn->get( $variable );
    }

    if ( ! $place_id ) {
        return $res;
    }

    $place = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "grp_google_place WHERE place_id = %s", $place_id));
    $review = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "grp_google_review WHERE google_place_id = %d and `text` is not null and `tag`=%s and `deleted` != '1' order by rand() limit 1", $place->id, $tag));


    if ( $review )
        $res[] = array(
                'type'          => 'google',
                'page_image'    => esc_url( $place->icon ),
                'page_name'     => $place->name,
                'page_link'     => $place->url,
                'name'          => (strlen($review->author_name) > 0) ? $review->author_name : grw_i('Google User'),
                'image'         => $review->profile_photo_url,
                'rating'        => $review->rating,
                'time'          => date( 'Y-m-d h:i:s', $review->time ),
                'text'          => $review->text,
            );
    return $res;
}

?>
