<?php

        wp_enqueue_style('carouselcss', plugins_url('../css/jquery.bxslider.css', __FILE__));
        wp_enqueue_script('jquery');
        //wp_enqueue_script('custom_carousel', plugins_url('js/jquery.bxslider.js', __FILE__));
   
        wp_enqueue_script('custom_carousel', plugins_url('/../fb-reviews-widget/static/js/jquery.bxslider.js', __FILE__));
        require_once(plugin_dir_path(__FILE__) . '/../fb-reviews-widget/fbrev-reviews-helper.php');
        require_once(plugin_dir_path(__FILE__) . '/../widget-google-reviews/grw-reviews-helper.php');
        wp_enqueue_style('fbrev_widget_icon', plugins_url('/fonts/font-awesome.css', __FILE__));

global $wpdb;
 $fbpage = $wpdb->get_row("select * from ".$wpdb->prefix."fbrev_page", ARRAY_A);
 //get g review
 $greview = $wpdb->get_row("select avg(rating) as avgrating, min(rating) as worstrating, count(*) as totalreviews  from ".$wpdb->prefix."grp_google_review", ARRAY_A);
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
  

    if($rotate_random) {
        shuffle( $res ); 
    }

    
    if($rotate_number) {
        $res = array_slice($res, 0, $rotate_number);  

    }
    ?>
    
    <div></div>
    <?php $get_company_option_details = unserialize(get_option('wnp_your_company')); ?>
    <div itemscope  itemtype="http://schema.org/Organization">
    <meta itemprop="name" content="<?php echo $get_company_option_details['name']; ?>"/>
    <div class="avgrating" style="text-align: center;">
         <span class="wp-facebook-stars"><?php echo fbrev_stars( $avgrating ); ?></span>
         <div class="average_rating_display"><?php echo $avgrating; ?> Average &nbsp;<?php echo $total_reviews; ?> Reviews</div>
         
         <div class="avgrating" itemscope itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating" style="display:none">
               <span itemprop="ratingValue" content="<?php echo $avgrating; ?>"></span>
               <span itemprop="bestRating" content="5"></span>
               <span itemprop="worstRating" content="<?php echo $avgworstrating; ?>"></span>
               <span itemprop="reviewCount" content="<?php echo $total_reviews; ?>"></span>
               <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing">
                 <?php if(isset($res[0]['page_name']) && $res[0]['page_name']): ?>
                 <span itemprop="name" style="display: none;"><?php echo $res[0]['page_name']; ?></span>
                 <?php else: ?>
                  <span itemprop="name" style="display:none;"><?php echo get_bloginfo('name'); ?></span>
                 <?php endif; ?>
                </div>        
         </div>
         
    </div>

    <div class="bxslider-wrap">
    <div class="bxslider-reviews-fb">

  <?php 
  $counter = 0;
     foreach ($res  as $key1=>$re) { 
      

  $ndn = new wpsocial_DotNotation( $re );
    
    $page_image = $ndn->get( 'page_image' );
    
    $page_name = $ndn->get( 'page_name' );
 
    $img = $ndn->get( 'image' );
    $page_link = $ndn->get( 'page_link' );
    $rev_type = $ndn->get( 'type' );
    if($rev_type == 'google') : $stars='grw_stars'; else: $stars ='fbrev_stars'; endif;
    if($rev_type == 'fb') : $icon='fa-facebook-square'; elseif($rev_type == 'google'): $icon ='fa-google';  else: $icon = 'fa-shield';endif;
    $name = $ndn->get('name');

    // $cover_photo = fbrev_get_cover_photo( $page_id, $page_access_token );

    ?>

        <div class="wp-facebook-rew wpsocial-<?php echo esc_attr( $rev_type ); ?> <?php if(isset($dark_color) && $dark_color){?> wp_dark <?php }?> widget wpsocial_review_Widget" itemprop="Review" itemscope itemtype="http://schema.org/Review">
            
            <div class="wp-facebook-review">
                <div class="wp-facebook-left">
                    <img src="<?php echo esc_url($img); ?>">

                </div>

                <div class="wp-facebook-right clearfix">
                    <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing">
                    <?php if(isset($pagename) && $page_name): ?>
                    <span style="display:none" itemprop="name"><?php echo $page_name; ?></span>
                    <?php else: ?>
                        <span style="display:none" itemprop="name"><?php echo get_bloginfo('name'); ?></span>
                     <?php endif; ?>   
                     </div>
                     <span class="" itemprop="author"><?php echo $name; ?></span>
                      <div class="icon-fb"><a href="<?php echo esc_url($page_link); ?>" target="_blank"><i class="fa <?php echo $icon; ?>"></i></a></div>

                    <div class="wp-facebook-feedback">
                        <span class="wp-facebook-stars" itemprop="reviewRating"><?php echo $stars( $ndn->get( 'rating' ) ); ?></span>
                      
                     
                    </div>
                </div>
              
            </div>
             <span class="wp-facebook-text" itemprop="description"><?php echo $ndn->get( 'text' ) ; ?></span>
            <?php   if($rev_type == 'fb'){ ?>
                <div class="image_cover">
                    <div class="fb-page" data-href="<?php echo esc_url(str_replace('fb','facebook',$page_link)); ?>" itemprop="URL"  data-tabs="false" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false"><blockquote cite="<?php echo esc_url(str_replace('fb','facebook',$page_link)); ?>" class="fb-xfbml-parse-ignore"><a href="<?php echo esc_url(str_replace('fb','facebook',$page_link)); ?>"><?php echo ($page_name); ?></a></blockquote></div>    
                </div>
            <?php }?>    

        </div>
<?php } 

?>
</div>
<style>
    .image-review {
        min-height: 71px;
    }
    .image-fb img {
    background: #eee none repeat scroll 0 0;
    border: 1px solid #ccc;
    border-radius: 50%;
    height: 67px;
    margin-right: 10px;
    padding: 5px;
    width: 67px;
}
.rating-review > a {
    display: block;
}
.wp-facebook-text.name {
    color: #222;
    font-family: inherit;
    font-weight: 500;
    margin-bottom: 6px;
}
.wp-facebook-text {
    display: block;
}
.wp-facebook-right.clearfix > span {
    display: inline-block;
    width: 50%;
}
.wpsocial-custom .image-fb {
    display: none;
}
.image-fb {
    float: left;
}
.wp-facebook-rew {
    border: 1px solid #eee;
    min-height: 300px;
    padding: 10px;
}
.rating-review > a {
    color: #222 !important;
}
.wp-facebook-left {
    float: left;
    margin-right: 10px;
}
.wp-facebook-left {
    float: left;
    margin-right: 10px;
}
.wp_dark .wp-facebook-right.clearfix > span, .wp_dark .wp-facebook-text, .wp_dark .image > a {
    color: #fff;
}
.wp-facebook-rew.wpsocial-fb {
    position: relative;
}
.widget.wpsocial_review_Widget .bx-wrapper {
    overflow-x: hidden;
}
.fa-facebook-square:before {
  content: "\f230";
}
.fa-facebook-square {
  font-family: 'fontawesome';
}
.icon-fb > a {
    font-size: 20px;
}
.wpsocial-fb .icon-fb > a, .wpsocial-custom .icon-fb > a {
    color: rgb(60, 91, 155) !important;
}
.wpsocial-google .icon-fb > a {
    color: orange !important;
}
#sidebar .widget, #sidebar .widget a {
    color: #888;
}
.icon-fb {
    float: right;
}
.wp-facebook-left > img {
    border-radius: 50%;
    height: 70px;
    width: 70px;
}
.wp-facebook-right.clearfix > span {
    color: #222;
    font-size: 14px !important;
    font-weight: bold;
    letter-spacing: 0.2px;
    line-height: 27px !important;
}
.wp-facebook-text {
    color: #222;
    font-family: "Open Sans",Helvetica,Arial,sans-serif;
    margin-bottom: 11px;
    margin-top: 11px;
}
.image_cover > img {
    height: 100%;
    width: 100%;
}
.image_cover {
    position: relative;
}

.cover-upper {
    bottom: 23px;
    display: inline-flex;
    left: 10px;
    position: absolute;
}
.cover-upper > a {
    color: #fff !important;
    font-size: 16px !important;
    font-weight: bold;
}
.page_name > a {
    color: #fff;
    font-size: 20px;
}
.cover-upper img {
    border: 5px solid;
    border-radius: 5px;
    margin-right: 20px;
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

</div> 
</div>
<script>
    
    jQuery(document).ready(function () {           
                jQuery('.bxslider-reviews-fb').bxSlider({
                pager :false,
                auto:true,
                mode:'horizontal',
                speed: 5000,
                pause: 20000,
                controls:false,
                autoHover:true,
                adaptiveHeight: true,
                autoHover:true,
                }); 
            });
    </script>  
            

