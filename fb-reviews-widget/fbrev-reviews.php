<?php global $wpdb;

add_action('admin_enqueue_scripts', array($this, 'fbrev_widget_scripts'));
wp_enqueue_style('carouselcss', plugins_url('/static/css/jquery.bxslider.css', __FILE__));
wp_register_script('fbrev_wpac_time_js', plugins_url('/static/js/wpac-time.js', __FILE__));
wp_enqueue_script('fbrev_wpac_time_js', plugins_url('/static/js/wpac-time.js', __FILE__));
wp_register_style('fbrev_widget_css', plugins_url('/static/css/facebook-review.css', __FILE__));
wp_enqueue_style('fbrev_widget_css', plugins_url('/static/css/facebook-review.css', __FILE__));
wp_enqueue_style('fbrev_widget_icon', plugins_url('/static/css/font-awesome.css', __FILE__));

?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
 <script>
            var ratingUrl = "<?php echo plugins_url(); ?>/wp-social-seo/";
</script>
<?php
        //wp_enqueue_style('carouselcss', plugins_url('../css/jquery.bxslider.css', __FILE__));
        wp_enqueue_script('jquery');
        wp_enqueue_script('custom_carousel', plugins_url('/static/js/jquery.bxslider.js', __FILE__));
?>

<?php
$fb_options = get_option( 'wp_social_seo_facebook_tab' );

$fdn = new wpsocial_DotNotation( $fb_options ); 

foreach ($fb_options as $variable => $value) {
    ${$variable} = $fdn->get( $variable );
}

//print_r($fb_options); exit();
if (empty($page_id)) { ?>
    <div class="fbrev-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
        <?php echo fbrev_i('Please check that this widget <b>Facebook Reviews</b> has a connected Facebook.'); ?>
    </div> <?php
    return false;
}

$reviews             = false;//get_transient('fbrev_widget_api_' . $page_id);
$widget_options      = get_transient('fbrev_widget_options_' . $page_id);
$serialized_instance = serialize($instance);

if (true) {
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
    $fbpage = $wpdb->get_row("select * from ".$wpdb->prefix."fbrev_page", ARRAY_A);
    
    $reviews = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."fbrev_page_review WHERE page_id = %s", $page_id ) );
    //$reviews = $fbrev_api->reviews($page_id, array('access_token' => $fdn->get('page_access_token'), 'limit' => 25));
    //set_transient('fbrev_widget_api_' . $page_id, $reviews, $expiration);
    //set_transient('fbrev_widget_options_' . $page_id, $serialized_instance, $expiration);
}

//print_r($reviews);exit;
include_once(dirname(__FILE__) . '/fbrev-reviews-helper.php');




$rating = 0;
if (count($reviews) > 0 && ! isset( $reviews['error'] ) ) {
    foreach ($reviews as $review) {
        $rating = $rating + $review->rating;
    }
    $rating = round($rating / count($reviews), 1);
    $rating = number_format((float)$rating, 1, '.', '');
}
?>




    <section id="wpsocial_reviews_widget-4" class="widget wpsocial_review_Widget">
        <style>        
           .bxslider-reviews-fb .wp-facebook-left {
                float: left;
                margin-right: 11px;
            }
            .bxslider-reviews-fb .wp-facebook-name {
                color: #1d2921;
                font-family: Helvetica, Arial, sans-serif;
                font-size: 14px;
                font-weight: bold;
            }

            .bxslider-reviews-fb .wp-facebook-time {
                color: #90949c;
                font-size: 12px;
            }
            .bxslider-reviews-fb .wp-facebook-text {
                color: #1d2129 ;
                font-family: Helvetica,Arial,sans-serif;
                font-size: 14px;
            }
            .wp-gray-custom .wp-facebook-text {
                color: #ececec !important;
            }
            .wp-gray-custom .wp-facebook-name {
                color: #ececec !important;
            }
            .wp-gray-custom .wp-facebook-time {
                color: #ececec !important;
            }
            .author .wp-facebook-right {
                position: relative;
            }
           /* .bxslider-reviews-fb .upper-content {
                bottom: 0;
                position: absolute;
                z-index: 999;
            }*/
           .bxslider-reviews-fb .upper-content > img {
                background: #fff none repeat scroll 0 0 !important;
                display: inline-block !important;
                height: 60px;
                padding: 7px;
                width: 60px !important;
            }
            .bxslider-reviews-fb .name {
                color: #fff;
                display: block;
                float: none !important;
                font-size: 15px;
                font-weight: bold;
                margin-bottom: 10px;
                margin-left: 10px;
            }
            .bxslider-reviews-fb .name-author {
            float: right;
            }
            .bxslider-reviews-fb .cutom-fb-reviews .upper-content > img {
                float: left;
                margin-right: 10px;
            }
            .bxslider-reviews-fb .name-author > a {
                color: #fff;
                display: list-item;
                font-size: 18px;
                font-weight: bold;
            }
            .bxslider-reviews-fb .wp-facebook-place {
                padding: 5px;
            }
            .bxslider-reviews-fb .fa-facebook {
                font-family: fontawesome;
            }
            .bxslider-reviews-fb .icon-fb > a {
                font-size: 25px;
            }
            .bxslider-reviews-fb .wp-facebook-review {
                float: left;
            }
            .bxslider-reviews-fb .icon-fb > a:hover {
                color: blue;
            }
            .bxslider-reviews-fb .icon-fb > a {
                font-size: 25px;
            }
            .bxslider-reviews-fb .icon-fb {
                float: right;
                position: absolute;
                right: 9px;
                top: 9px;
            }
            .bxslider-reviews-fb .wp-facebook-reviews {
                border: 1px solid #eee;
                box-shadow: 0 7px 6px #888888;
                padding: 10px;
            }
            .bxslider-reviews-fb .wp-facebook-text {
                    min-height: 100px !important;
                }
            .wp-facebook-review {
                margin-bottom: 6px;
            }
            .icon-fb > a {
                color: blue !important;
            }
            .name-author > a {
                color: #fff !important;
            }
            .upper-content {
                bottom: 0;
                position: absolute;
            }
            .facebook-cover {
                position: relative;
            }
            .facebook-cover img {
                box-shadow: 1px 7px 5px 2px #888888;
            }

            .wpsocial_review_Widget .wp-facebook-left > img {
                border-radius: 0;
                height: 40px;
                width: 40px;
            }
            .wpsocial_review_Widget .fb-page blockquote a {
              color: hsl(0, 0%, 100%);
                display: block;
                font-size: 13px;
                line-height: 17px;
                margin-left: 109px;
                margin-top: 31px;
                position: relative;
                z-index: 9;
            }
            .wpsocial_review_Widget .fb-page blockquote {
             background: hsla(0, 0%, 0%, 0) url("<?php echo $fbpage['cover']; ?>") no-repeat scroll 0 0;
                border: 1px solid hsl(0, 0%, 87%);
                border-radius: 1px;
                box-shadow: 0 0 0 1px hsla(0, 0%, 0%, 0.1) inset, 0 1px 1px hsla(0, 0%, 0%, 0.05);
                color: hsl(0, 0%, 100%);
                font-size: 18px;
                height: 176px;
                line-height: 25px;
                margin: 0 auto;
                padding: 10px 15px;
                position: relative;
            }
            .wpsocial_review_Widget .wp-facebook-right.clearfix > span {
                color: hsl(221, 44%, 41%);
            }
            .wpsocial_review_Widget .fb-page blockquote::after {
             background: hsla(0, 0%, 0%, 0.3) none repeat scroll 0 0;
                content: "";
                display: block;
                height: 61%;
                left: 0;
                position: absolute;
                top: 0;
                width: 100%;
              
            }    

        </style> 
        <script>jQuery(document).ready(function () {           
        jQuery('.bxslider-reviews-fb').bxSlider({
        pager :false,
        auto:true,
        mode:'fade',
        speed: 10000,
        pause: 400,
        controls:false,
        autoHover:true
        }); 
   
        });</script>  

        <?php //print_r($view_mode); exit();
         //$class = if($view_mode=='badge') : 'bxslider-reviews-fb'; else: 'test'; 
        //print_r($view_mode); exit();
        if($view_mode=='badge') :
        ?>

            <div class="bxslider-reviews-fb <?php if ( ! $dark_theme) { ?> wp-gray-custom<?php } ?>">
                <?php fbrev_page_reviews_data($page_id); ?>

            </div> 

        <?php else: ?>
        <div class="wp-fbrev wpac">
            <div class="wp-facebook-list<?php if ( ! $dark_theme) { ?> wp-dark<?php } ?>">
                <div class="wp-facebook-place">
                    <?php fbrev_page($page_id, $page_name, $rating, $reviews ); ?>
                </div>
                <div class="wp-facebook-content-inner">
                    <?php fbrev_page_reviews($page_id, $page_name, $rating, $reviews); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </section>  