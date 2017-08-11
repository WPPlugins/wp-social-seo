<?php

if ( isset( $_POST['fbrev'] ) && $_POST['fbrev'] !== '') {
    update_option( 'wp_social_seo_facebook_tab', $_POST['fbrev'] );
     include_once(dirname(__FILE__) . '/fbrev-reviews-helper.php');
    fbrev_save_page_and_reviews();


}



wp_enqueue_script('jquery');
wp_register_script('fbrev_bootstrap_js', plugins_url('/static/js/bootstrap.min.js', __FILE__));
wp_enqueue_script('fbrev_custom_wpac', plugins_url('/static/js/wpac.js', __FILE__));
wp_enqueue_script('fbrev_custom_js', plugins_url('/static/js/fb_custom.js', __FILE__));

wp_register_style('fbrev_bootstrap_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));
wp_enqueue_style('fbrev_bootstrap_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));
wp_register_style('fbrev_setting_css', plugins_url('/static/css/fbrev-setting.css', __FILE__));
wp_enqueue_style('fbrev_setting_css', plugins_url('/static/css/fbrev-setting.css', __FILE__));

$fbrev_enabled = get_option('fbrev_active') == '1'; ?>

<?php error_reporting(EP_ALL);

$facebook_data = (array)get_option( 'wp_social_seo_facebook_tab' );
$dn = new wpsocial_DotNotation( $facebook_data );

global $wpdb;
$fbpage = $wpdb->get_row("select * from ".$wpdb->prefix."fbrev_page where page_id='".$dn->get( 'page_id' )."'", ARRAY_A);


?>

<div class="fbrev-setting container-fluid">

    <div class="tab-content">

        <div role="tabpanel" class="tab-pane active" id="setting">

            <!-- Enable/disable Facebook Reviews Widget toggle -->
            <form method="POST" action="" enctype="multipart/form-data">

                <fieldset>
                           <div class="social-form">

                               <input type='hidden' name='action' value='submit-wps-company' />
                               <div class="alert-box success" style="display:none;"><span>Success : </span>Your company settings has been saved successfully</div>
                               <div class="form-left">
                                   <div class="form-group">
                                       <label>Choose Page</label>
                                       <button onclick="return fbrev_facebook(this);" class="fb-btn">Connect to Facebook</button>
                                       <div class="fbrev-pages"></div>

                                   </div>

                                   <div class="form-group">
                                       <label>FB Page ID</label>
                                       <input id="widget-fbrev_widget-page_id" name="fbrev[page_id]" value="<?php echo $dn->get( 'page_id' ); ?>" placeholder="Page ID" type="text" readonly="">
                                   </div>
                                   <div class="form-group">
                                       <label>Upload Background Image:</label>
                                      <input type="file" name="background_image" value="">
                                      <?php echo $fbpage['cover']; ?>

                                   </div>
                                   <div class="form-group">
                                       <label>Type of listing</label>
                                       <select id="widget-fbrev_widget-3-view_mode" name="fbrev[view_mode]">
                                           <option value="list" <?php selected( 'list', $dn->get( 'view_mode' ) ); ?>>Review list</option>
                                           <option value="badge" <?php selected( 'badge', $dn->get( 'view_mode', 'badge' ) ); ?>>Facebook Badge</option>
                                       </select>
                                   </div>

                                   <div class="form-group">
                                       <input class="button-primary" type="submit" value="Update" name="submit" />
                                   </div>
                               </div>

                               <div class="form-right">
                                   <div class="form-group">
                                       <label>Selected Page</label>
                                       <input id="widget-fbrev_widget-page_name" name="fbrev[page_name]" value="<?php echo $dn->get( 'page_name' ); ?>" placeholder="Page Name"  type="text" readonly="">
                                       <input id="widget-fbrev_widget-page_access_token" name="fbrev[page_access_token]" value="<?php echo $dn->get( 'page_access_token' ); ?>" placeholder="Access token" readonly="" type="hidden">

                                   </div>


                                   <div class="form-group">
                                       <input id="widget-fbrev_widget-3-dark_theme" name="fbrev[dark_theme]" value="1" type="checkbox" <?php checked( 1, $dn->get( 'dark_theme' ) ); ?>>
                                       <label class="for_checkbox">Enable Theme for Dark background</label>
                                   </div>


                                   <div class="form-group">
                                       <label>Update/refresh reviews every</label>
                                       <select id="widget-fbrev_widget-3-cache" name="fbrev[cache]">
                                           <option value="1" <?php selected( 1, $dn->get( 'cache' ) ); ?>>1 Hour</option>
                                           <option value="3" <?php selected( 3, $dn->get( 'cache' ) ); ?>>3 Hours</option>
                                           <option value="6" <?php selected( 6, $dn->get( 'cache' ) ); ?>>6 Hours</option>
                                           <option value="12" <?php selected( 12, $dn->get( 'cache' ) ); ?>>12 Hours</option>
                                           <option value="24"  <?php selected( 24, $dn->get( 'cache', 24 ) ); ?>>1 Day</option>
                                           <option value="48" <?php selected( 48, $dn->get( 'cache' ) ); ?>>2 Days</option>
                                           <option value="168" <?php selected( 168, $dn->get( 'cache' ) ); ?>>1 Week</option>
                                       </select>
                                   </div>
                               </div>
                           </div>
                        </fieldset>
            </form>
        </div>

    </div>
</div>
<style type="text/css">

    .fbrev-page-photo{
    border-radius: 50%;
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.12), 0 2px 4px rgba(0, 0, 0, 0.24);
    height: 32px;
    vertical-align: middle;
    width: 32px;
}
</style>
