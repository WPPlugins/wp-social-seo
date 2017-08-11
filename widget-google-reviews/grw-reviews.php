<?php

add_action('admin_enqueue_scripts', array($this, 'grw_widget_scripts'));
wp_register_script('grp_time_js', plugins_url('/static/js/grp-time.js', __FILE__));
wp_enqueue_script('grp_time_js', plugins_url('/static/js/grp-time.js', __FILE__));
wp_register_style('grp_widget_css', plugins_url('/static/css/grp-widget.css', __FILE__));
wp_enqueue_style('grp_widget_css', plugins_url('/static/css/grp-widget.css', __FILE__));

$google_options = get_option( 'wp_social_seo_google_tab' );

$fdn = new wpsocial_DotNotation( $google_options );

foreach ($google_options as $variable => $value) {
    ${$variable} = $fdn->get( $variable );
}


if ($place_id) {
    if ($view_mode == 'badge') { ?>
        <style>
           #<?php echo $this->id; ?>
           {
                margin: 0;
                padding: 0;
                border: none;
            }
        </style>
    <?php
        }
    } else { ?>
         <div class="grp-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
             ?php echo grw_i('Please check that this widget <b>Google Reviews</b> has a Google Place ID set.'); ?>
        </div>
    <?php }


include_once(dirname(__FILE__) . '/grw-reviews-helper.php');

$place = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "grp_google_place WHERE place_id = %s", $place_id));
$reviews = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "grp_google_review WHERE google_place_id = %d", $place->id));

$rating = 0;
if ($place->rating > 0) {
    $rating = $place->rating;
} else if (count($reviews) > 0) {
    foreach ($reviews as $review) {
        $rating = $rating + $review->rating;
    }
    $rating = round($rating / count($reviews), 1);
}
$rating = number_format((float)$rating, 1, '.', '');
?>

<?php if ($view_mode != 'list') { ?>

<div class="wp-gr wpac">
    <div class="wp-google-badge<?php if ($view_mode == 'badge') { ?> wp-google-badge-fixed<?php } ?>" onclick="grw_next(this).style.display='block'">
        <div class="wp-google-border"></div>
        <div class="wp-google-badge-btn">

            <div class="wp-google-badge-score">
                <div><?php echo grw_i('Google Rating'); ?></div>
                <span class="wp-google-rating"><?php echo $rating; ?></span>
                <span class="wp-google-stars"><?php grw_stars($rating); ?></span>
            </div>
        </div>
    </div>
    <div class="wp-google-form" style="display:none">
        <div class="wp-google-head">
            <div class="wp-google-head-inner">
                <?php grw_place($rating, $place, $reviews, $dark_theme, false); ?>
            </div>
            <button class="wp-google-close" type="button" onclick="this.parentNode.parentNode.style.display='none'">×</button>
        </div>
        <div class="wp-google-body"></div>
        <div class="wp-google-content">
            <div class="wp-google-content-inner">
                <?php grw_place_reviews($place, $reviews, $place_id, $text_size); ?>
            </div>
        </div>
        <div class="wp-google-footer">
            <img src="https://res.cloudinary.com/dhnesdsyd/image/upload/c_scale,h_50/v1491071985/google_rating_logo_36_zaajo5.png" alt="powered by Google">
        </div>
    </div>
</div>

<?php } else { ?>

<div class="wp-gr wpac">
    <div class="wp-google-list<?php if ($dark_theme) { ?> wp-dark<?php } ?>">
        <div class="wp-google-place">
            <?php grw_place($rating, $place, $reviews, $dark_theme); ?>
        </div>
        <div class="wp-google-content-inner">
            <?php grw_place_reviews($place, $reviews, $place_id, $text_size); ?>
        </div>
    </div>
</div>
<?php } ?>
