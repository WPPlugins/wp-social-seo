<?php

if ( isset( $_POST['grwrevlist'] ) && $_POST['grwrevlist'] !== '') {

      global $wpdb;
      $post = $_POST['grwrevlist'];
      $array = array();
      $array['google_place_id'] = sanitize_text_field($post['google_place_id']);
      $array['rating'] = sanitize_text_field($post['rating']);
      $array['text'] = sanitize_text_field($post['text']);
      $array['time'] = sanitize_text_field($post['time']);
      $array['author_url'] = sanitize_text_field($post['author_url']);
      $array['author_name'] = sanitize_text_field($post['author_name']);
      $array['tag'] = sanitize_text_field($post['tag']);
      $wpdb->update($wpdb->prefix."grp_google_review", $array, array('id'=>$post['id']));
      if ($_GET['paged'] != '') {
          wp_redirect(admin_url('admin.php?page=wps-google-review-list&paged="' . $_GET['paged'] . '"'));
          exit;
      }
      wp_redirect(admin_url('admin.php?page=wps-google-review-list'));
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

?>

<?php 
global $wpdb;
  $sql = $wpdb->prepare("select * from ".$wpdb->prefix."grp_google_review where id=%s",$_GET['grwrev']);
  $result = $wpdb->get_row($sql, ARRAY_A);
?>

<div class="fbrev-setting container-fluid">

    <div class="tab-content">

        <div role="tabpanel" class="tab-pane active" id="setting">

            <!-- Enable/disable Facebook Reviews Widget toggle -->
            <form method="POST" action="" enctype="multipart/form-data">

                <fieldset>
                           <div class="social-form">

                               <input type='hidden' name='action' value='submit-wps-company' />
                               <div class="alert-box success" style="display:none;"><span>Success : </span>Facebook review updated succesfully</div>
                               <div class="form-left">
                                   <div class="form-group">
                                       <label>Page Id</label>
                                       <input type="text" name="grwrevlist[google_place_id]" value="<?php echo $result['google_place_id']; ?>">
                                   </div>
                                   <div class="form-group">
                                       <label>Rating</label>
                                       <input type="text" name="grwrevlist[rating]" value="<?php echo $result['rating']; ?>">
                                   </div>

                                   <div class="form-group">
                                       <label>Text</label>
                                       <textarea name="grwrevlist[text]" cols="5" rows=5><?php echo $result['text']; ?></textarea>
                                       
                                   </div>



                                   <div class="form-group">
                                       <input class="button-primary" type="submit" value="Update" name="submit" />
                                   </div>
                               </div>

                               <div class="form-right">
                                   <div class="form-group">
                                       <label>Time</label>
                                       <input type="text" name="grwrevlist[time]" value="<?php echo $result['time']; ?>">
                                    </div>
                               </div>
                               <div class="form-right">
                                   <div class="form-group">
                                       <label>Author Id</label>
                                       <input type="text" name="grwrevlist[author_url]" value="<?php echo $result['author_url']; ?>">
                                    </div>
                               </div>

                               <div class="form-right">
                                   <div class="form-group">
                                       <label>Author Name</label>
                                       <input type="text" name="grwrevlist[author_name]" value="<?php echo $result['author_name']; ?>">
                                       <input type="hidden" name="grwrevlist[id]" value="<?php echo $result['id']; ?>">
                                    </div>
                               </div> 
                               <div class="form-right">
                                   <div class="form-group">
                                       <label>Tag</label>
                                       <input type="text" name="grwrevlist[tag]" value="<?php echo $result['tag']; ?>">
                                       
                                    </div>
                               </div>
                           </div>
                        </fieldset>
            </form>
        </div>

    </div>
</div>

