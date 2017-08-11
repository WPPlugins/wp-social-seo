<?php

 function fbrev_get_cover_photo( $page_id, $access_token ) {
    $remote_get = wp_remote_get( 'https://graph.facebook.com/'.$page_id.'?fields=cover&access_token='.$access_token );
    $remote_body = wp_remote_retrieve_body( $remote_get );

    $json_decode = json_decode( $remote_body );

    if ( $json_decode && isset( $json_decode->cover ) ) {

        $url = $json_decode->cover->source;

    } else {
        $url =    WPSOCIALSEO_URL.'/images/test.jpg';
    }

    return $url;
}
function fbrev_save_page_and_reviews() {
    global $fbrev_api, $wpdb; 


    $posted_Fb_rev = $_POST['fbrev'];
    $page_id = isset( $posted_Fb_rev['page_id'] ) ? $posted_Fb_rev['page_id'] : '';
    $page_name = isset( $posted_Fb_rev['page_name'] ) ? $posted_Fb_rev['page_name'] : '';
    $page_access_token = isset( $posted_Fb_rev['page_access_token'] ) ? $posted_Fb_rev['page_access_token'] : '';

    // $cover_photo = fbrev_get_cover_photo( $page_id, $page_access_token );
    if ($_FILES['background_image']['tmp_name']) {
        $attach_id = media_handle_upload('background_image', 0 );
    }
    if($attach_id){
       $attachment = $wpdb->get_row("select * from ".$wpdb->prefix."posts where ID='".$attach_id."'", ARRAY_A); 
       $cover_photo = $attachment['guid'];
    }else {
        $cover_photo = '';
    }

    $reviews = '';
    //print_r($reviews); exit();
    if ( $page_access_token ) {
        $reviews = $fbrev_api->reviews($page_id, array('access_token' => $page_access_token, 'limit' => 25));
    }


    if ( $reviews ) {

        $page_exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix. "fbrev_page WHERE page_id = %s", $page_id ) );


        if ( ! $page_exists ) {
            $wpdb->insert( $wpdb->prefix.'fbrev_page', array( 'page_id' => $page_id, 'name' => $page_name, 'cover' => $cover_photo ));
        } else {
            $wpdb->update( $wpdb->prefix.'fbrev_page', array( 'page_id' => $page_id, 'name' => $page_name, 'cover' => $cover_photo ), array( 'page_id' => $page_id ));
        }

        if ( is_array( $reviews ) && ! isset( $reviews['error'] ) ) {

            foreach ($reviews as $key => $value) {
                
                if ( is_object( $value ) ) {
                    $value = (array)$value;
                }
                if ( ! is_array( $value ) ) {
                    continue;
                }
                //print_r($value);exit;
                $value = json_decode( json_encode( $value ), true );
                $dn = new wpsocial_DotNotation( $value );    
                $review_id = $dn->get( 'reviewer.id' );
                $review_name = $dn->get( 'reviewer.name' );


                $res  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix. "fbrev_page_review WHERE author_id = %s", $review_id ) );

                if ( ! $res ) {
                    $data = array( 'page_id' => $page_id, 'rating' => $dn->get( 'rating' ), 'text' => $dn->get('review_text'), 'time' => date( 'Y-m-d h:i:s', strtotime( $dn->get('created_time' )) ), 'author_id' => $review_id, 'author_name' => $review_name );
                    
                    $wpdb->insert( $wpdb->prefix.'fbrev_page_review', $data );
                }
            }
        }

    }
}

function fbrev_page($page_id, $page_name, $rating, $reviews, $show_powered = true) {
    ?>
    <div class="mehak my">
    <div class="wp-facebook-left">
        <img src="https://graph.facebook.com/<?php echo $page_id; ?>/picture" alt="<?php echo $page_name; ?>">
    </div>
    <div class="wp-facebook-right">
        <div class="wp-facebook-name">
            <?php echo fbrev_anchor('https://fb.com/' . $page_id, '', '<span>' . $page_name . '</span>', true, true); ?>
        </div>
        <div>
            <span class="wp-facebook-rating"><?php echo $rating; ?></span>
            <span class="wp-facebook-stars"><?php fbrev_stars($rating); ?></span>
        </div>
        <?php if ($show_powered) { ?>
        <div class="wp-facebook-powered">powered by <span>Facebook</span></div>
        <?php } ?>
    </div>
    </div>
    <?php
}

function fbrev_page_reviews($page_id, $page_name, $rating, $reviews, $show_powered = true) {
    ?>
    <div class="wp-facebook-reviews">

    <?php
    if (count($reviews) > 0) {
        foreach ($reviews as $review) {
print_r($reviews); exit();
         ?>
        <div class="wp-facebook-review">
            <div class="wp-facebook-left">
                <img src="https://graph.facebook.com/<?php echo $review->author_id; ?>/picture" alt="<?php echo $review->author_name; ?>" onerror="if(this.src!='<?php echo FBREV_AVATAR; ?>')this.src='<?php echo FBREV_AVATAR; ?>';">
            </div>
            <div class="wp-facebook-right">
                <?php fbrev_anchor('https://www.facebook.com/app_scoped_user_id/' . $review->author_id, 'wp-facebook-name', $review->author_name, true, true); ?>
                <div class="wp-facebook-time" data-time="<?php echo $review->time; ?>"><?php echo human_time_diff( strtotime( $review->time ) ) . esc_html__( ' ago', 'wp-social-seo' ); ?></div>
                <div class="wp-facebook-feedback">
                    <span class="wp-facebook-stars"><?php echo fbrev_stars($review->rating); ?></span>
                    <span class="wp-facebook-text"><?php echo fbrev_trim_text($review->text, 0); ?></span>
                </div>
            </div>
        </div>
        <?php
        }
    }
    ?>
    </div>
    <?php /*$seeAllReviews = fbrev_i('See All Reviews'); fbrev_anchor('https://fb.com/' . $page_id, 'wp-facebook-url', $seeAllReviews, true, true);*/
}
function fbrev_page_reviews_data( $page_id ) {

    global $wpdb;

    $page_info = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."fbrev_page WHERE  page_id = '$page_id'" );
  //print_r($page_info); exit();

    $reviews = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."fbrev_page_review WHERE  page_id = '$page_id'" );

 //print_r($page_access_token); exit();
    if ( ! $reviews ) {
        return;
    }

    foreach ( $reviews as $review ) :
    ?>

    <div class="wp-facebook-reviews">
            <div class="wp-facebook-review">
                <div class="wp-facebook-left">
                    <img src="https://graph.facebook.com/<?php echo $review->author_id; ?>/picture" alt="<?php echo $review->author_name; ?>" onerror="if(this.src!='<?php echo FBREV_AVATAR; ?>')this.src='<?php echo FBREV_AVATAR; ?>';">

                </div>

                <div class="wp-facebook-right clearfix">
                    <?php fbrev_anchor('https://www.facebook.com/app_scoped_user_id/' . $review->author_id, 'wp-facebook-name', $review->author_name, true, true); ?>
                    <div class="wp-facebook-time" data-time="<?php echo $review->time; ?>"><?php echo human_time_diff( strtotime( $review->time ) ) . esc_html__( ' ago', 'wp-social-seo' ); ?></div>
                    <div class="wp-facebook-feedback">
                        <span class="wp-facebook-stars"><?php echo fbrev_stars($review->rating); ?></span>
                     
                    </div>
                    <span class="wp-facebook-text"><?php echo fbrev_trim_text($review->text, 0); ?></span>
                </div>
               
            </div>
             <div class="icon-fb"><a href="#"><i class="fa fa-facebook-square"></i></a></div>
            <div class="author clearfix">                   
                    <div class="facebook-cover clearfix">
                        <img src="<?php echo esc_url( $page_info->cover ); ?>" height="60px" width="1250px">
                        <div class="upper-content">
                            <img src="https://graph.facebook.com/<?php echo $page_id; ?>/picture" alt="<?php echo $page_name; ?>">
                            <div class="name-author">
                                <?php echo fbrev_anchor('https://fb.com/' . $page_id, '', '<span>' . $page_info->name . '</span>', true, true); ?>
                            </div>
                       
                        </div>
                   </div> 
               
            </div>

    </div>

    <?php endforeach; /*$seeAllReviews = fbrev_i('See All Reviews'); fbrev_anchor('https://fb.com/' . $page_id, 'wp-facebook-url', $seeAllReviews, true, true);*/
}



function fbrev_rstrpos($haystack, $needle, $offset) {
    $size = strlen ($haystack);
    $pos = strpos (strrev($haystack), $needle, $size - $offset);

    if ($pos === false)
        return false;

    return $size - $pos;
}

function fbrev_trim_text($text, $size) {
    if ($size > 0 && strlen($text) > $size) {
        $visible_text = $text;
        $invisible_text = '';
        $idx = fbrev_rstrpos($text, ' ', $size);
        if ($idx < 1) {
            $idx = $size;
        }
        if ($idx > 0) {
            $visible_text = substr($text, 0, $idx);
            $invisible_text = substr($text, $idx, strlen($text));
        }
        echo $visible_text;
        if (strlen($invisible_text) > 0) {
            ?><span class="wp-more"><?php echo $invisible_text; ?></span><span class="wp-more-toggle" onclick="this.previousSibling.className='';this.textContent='';"><?php echo fbrev_i('read more'); ?></span><?php
        }
    } else {
        echo $text;
    }
}

function fbrev_anchor($url, $class, $text, $open_link, $nofollow_link) {
    ?><a href="<?php echo $url; ?>" class="<?php echo $class; ?>" <?php if ($open_link) { ?>target="_blank"<?php } ?> <?php if ($nofollow_link) { ?>rel="nofollow"<?php } ?>><?php echo $text; ?></a><?php
}


/*function fbrev_get_cover_photo( $page_id, $access_token ) {
    $remote_get = wp_remote_get( 'https://graph.facebook.com/'.$page_id.'?fields=cover&access_token='.$access_token );
    $remote_body = wp_remote_retrieve_body( $remote_get );

    $json_decode = json_decode( $remote_body );

    if ( $json_decode && isset( $json_decode->cover ) ) {

        $url = $json_decode->cover->source;

    } else {
        $url =    WPSOCIALSEO_URL.'/images/test.jpg';
    }

    return $url;
}*/

function fbrev_stars($rating) {
    ?><span class="wp-stars"><?php
    foreach (array(1,2,3,4,5) as $val) {
        $score = $rating - $val;
        if ($score >= 0) {
            ?>
            <span class="wp-star">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="16" height="16" viewBox="0 0 1792 1792">
                    <path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#4080ff"></path>
                </svg>
            </span>
            <?php
        } else if ($score > -1 && $score < 0) {
            ?>
            <span class="wp-star">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="16" height="16" viewBox="0 0 1792 1792">
                    <path d="M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z" fill="#4080ff"></path>
                </svg>
            </span>
            <?php
        } else {
            ?>
            <span class="wp-star">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="16" height="16" viewBox="0 0 1792 1792"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#ccc"></path></svg>
            </span>
            <?php
        }
    }
    ?></span><?php
} 
