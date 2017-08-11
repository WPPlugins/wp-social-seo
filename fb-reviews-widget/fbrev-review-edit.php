<?php

if ( isset( $_POST['fbrevlist'] ) && $_POST['fbrevlist'] !== '') {
      global $wpdb;
      $post = $_POST['fbrevlist'];
      $array = array();
      $array['page_id'] = sanitize_text_field($post['page_id']);
      $array['rating'] = sanitize_text_field($post['rating']);
      $array['text'] = sanitize_text_field($post['text']);
      $array['time'] = sanitize_text_field($post['time']);
      $array['author_id'] = sanitize_text_field($post['author_id']);
      $array['author_name'] = sanitize_text_field($post['author_name']);
      $array['tag'] = sanitize_text_field($post['tag']);
      $wpdb->update($wpdb->prefix."fbrev_page_review", $array, array('id'=>$post['id']));
      if ($_GET['paged'] != '') {
          wp_redirect(admin_url('admin.php?page=wps-facebook-review-list&paged="' . $_GET['paged'] . '"'));
          exit;
      }
      wp_redirect(admin_url('admin.php?page=wps-facebook-review-list'));
      
}     



wp_enqueue_script('jquery');
wp_register_script('fbrev_bootstrap_js', plugins_url('/static/js/bootstrap.min.js', __FILE__));
wp_enqueue_script('fbrev_custom_wpac', plugins_url('/static/js/wpac.js', __FILE__));
wp_enqueue_script('fbrev_custom_js', plugins_url('/static/js/fb_custom.js', __FILE__));

wp_register_style('fbrev_bootstrap_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));
wp_enqueue_style('fbrev_bootstrap_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));
wp_register_style('fbrev_setting_css', plugins_url('/static/css/fbrev-setting.css', __FILE__));
wp_enqueue_style('fbrev_setting_css', plugins_url('/static/css/fbrev-setting.css', __FILE__));

?>

<?php 
global $wpdb;
  $sql = $wpdb->prepare("select * from ".$wpdb->prefix."fbrev_page_review where id=%s",$_GET['fbrev']);
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
                                       <input type="text" name="fbrevlist[page_id]" value="<?php echo $result['page_id']; ?>">
                                   </div>
                                   <div class="form-group">
                                       <label>Rating</label>
                                       <input type="text" name="fbrevlist[rating]" value="<?php echo $result['rating']; ?>">
                                   </div>

                                   <div class="form-group">
                                       <label>Text</label>
                                       <textarea name="fbrevlist[text]" cols="5" rows=5><?php echo $result['text']; ?></textarea>
                                       
                                   </div>



                                   <div class="form-group">
                                       <input class="button-primary" type="submit" value="Update" name="submit" />
                                   </div>
                               </div>

                               <div class="form-right">
                                   <div class="form-group">
                                       <label>Time</label>
                                       <input type="text" name="fbrevlist[time]" value="<?php echo $result['time']; ?>">
                                    </div>
                               </div>
                               <div class="form-right">
                                   <div class="form-group">
                                       <label>Author Id</label>
                                       <input type="text" name="fbrevlist[author_id]" value="<?php echo $result['author_id']; ?>">
                                    </div>
                               </div>

                               <div class="form-right">
                                   <div class="form-group">
                                       <label>Author Name</label>
                                       <input type="text" name="fbrevlist[author_name]" value="<?php echo $result['author_name']; ?>">
                                       <input type="hidden" name="fbrevlist[id]" value="<?php echo $result['id']; ?>">
                                    </div>
                               </div> 
                               <div class="form-right">
                                   <div class="form-group">
                                       <label>Tag</label>
                                       <input type="text" name="fbrevlist[tag]" value="<?php echo $result['tag']; ?>">
                                       
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
