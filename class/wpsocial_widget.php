<?php


/**
 * Facebook Reviews Widget
 *
 * @description: The Facebook Reviews Widget
 * @since      : 1.0
 */
?>
<?php
class wpsocial_review_Widget extends WP_Widget {

    public $options;

    public $widget_fields = array(
        'title'             => '',
        'number'             => '',
        'google_review'     => '',
        'facebook_review'   => '',
        'manuall_review'    => '',
        'style_dark'    => '',
        'rotate_random'    => '',
    );

    public function __construct() {
        parent::__construct(
            'wpsocial_reviews_widget', // Base ID
            'WP Social SEO Reviews', // Name
            array(
                'classname'   => 'wpsocial_review_Widget',
                'description' => fbrev_i('Display WP Social SEO Widget on your site..', 'fbrev')
            )
        );


    }



    function widget($args, $instance) {
        global $wpdb;
        global $fbrev_api;
        extract( $args);
        $dn = new wpsocial_DotNotation( $instance );
        echo $before_widget;
        if ($dn->get('title')) { ?><h2 class="fbrev-widget-title widget-title"><?php echo $dn->get('title'); ?></h2><?php }

        $res = array();
        if ($dn->get('rotate_random')) {

          $rotate_random = $dn->get('rotate_random');

        }else{
            $rotate_random = 5;
        }

        if ( $dn->get( 'facebook_review' ) ) {
            //include( WPSOCIALSEO_PATH . '/fb-reviews-widget/fbrev-reviews.php');
            $res = $this->fbrev_array( $res, $rotate_random );
        }

        if ( $dn->get( 'google_review' ) ) {
            //include( WPSOCIALSEO_PATH . '/widget-google-reviews/grw-reviews.php');
            $res = $this->grw_array( $res ,$rotate_random);
        }

        if ( $dn->get( 'manuall_review' ) ) {
            //echo display_rich_snippets();
            $res = $this->custom_array( $res );
        }

        if ($dn->get('style_dark')) {

          $dark_color = $dn->get('style_dark');

        }
        
        if ($dn->get('number')) {

          $rotate_number = $dn->get('number');
          //print_r( $rotate_number); exit('asd');

        }

        //echo $rotate_number;exit;
         include( WPSOCIALSEO_PATH . '/templates/widget_content.php');

        echo $after_widget;

    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        foreach ($this->widget_fields as $field => $value) {
            $instance[$field] = strip_tags(stripslashes($new_instance[$field]));
        }
        return $instance;
    }

    function form($instance) {
        global $wp_version;
        foreach ($this->widget_fields as $field => $value) {
            ${$field} = !isset($instance[$field]) ? $value : esc_attr($instance[$field]);
        } ?>

        <style>
            .fbrev-sidebar-widget.widget-styling {
                margin: 10px 0;
            }

            .fbrev-sidebar-widget.widget-styling > label {
                display: block;
                margin-bottom: 10px;
            }
        </style>

        <div class="fbrev-sidebar-widget widget-styling">
            <label>
                <?php esc_html_e( 'Title :', 'wp-social-seo' ); ?>
                <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" placeholder="<?php echo fbrev_i('Widget title'); ?>" />
            </label>
			<label>
                <?php esc_html_e( 'Rotate Number :', 'wp-social-seo' ); ?>
                <input type="text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $number; ?>" placeholder="<?php echo fbrev_i('Rotate Number'); ?>" />
            </label>
             <label>
                <input id="<?php echo $this->get_field_id('rotate_random'); ?>" name="<?php echo $this->get_field_name('rotate_random'); ?>" type="checkbox" value="1" <?php checked('1', $rotate_random); ?> />
                    <?php esc_html_e( 'Rotate Random :', 'wp-social-seo' ); ?>
            </label>
            <label>
                <input id="<?php echo $this->get_field_id('google_review'); ?>" name="<?php echo $this->get_field_name('google_review'); ?>" type="checkbox" value="1" <?php checked('1', $google_review); ?> />
                    <?php esc_html_e( 'Google Review', 'wp-social-seo' ); ?>
            </label>

            <label>
                <input id="<?php echo $this->get_field_id('facebook_review'); ?>" name="<?php echo $this->get_field_name('facebook_review'); ?>" type="checkbox" value="1" <?php checked('1', $facebook_review); ?> />
                        <?php esc_html_e( 'Facebook Review', 'wp-social-seo' ); ?>
            </label>
            <label>
                <input id="<?php echo $this->get_field_id('manuall_review'); ?>" name="<?php echo $this->get_field_name('manuall_review'); ?>" type="checkbox" value="1" <?php checked('1', $manuall_review); ?> />
                    <?php esc_html_e( 'Manual', 'wp-social-seo' ); ?>
            </label>
            <label>
                <input id="<?php echo $this->get_field_id('style_dark'); ?>" name="<?php echo $this->get_field_name('style_dark'); ?>" type="checkbox" value="1" <?php checked('1', $style_dark); ?> />
                    <?php esc_html_e( 'Select if Backround Is Dark.', 'wp-social-seo' ); ?>
            </label>

        </div>

        <?php
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


        $fb_reviews = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."fbrev_page_review WHERE page_id = %s and `text` is not null order by rand() limit ".$limit, $page_id ) );
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
        $reviews = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "grp_google_review WHERE google_place_id = %d and `text` is not null order by rand() limit ".$limit, $place->id));


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

    function custom_array( $res ) {
        global $wpdb;

        $reviews = $wpdb->get_results('SELECT * FROM  ' . $wpdb->prefix . 'rich_snippets_review');

        if ( $reviews )
        foreach ($reviews  as  $review ) {

            $prod_name = is_numeric( $review->item_name ) ? '<a href="' . get_permalink( $review->item_name ) . '">' . get_the_title( $review->item_name ) . '</a>' : $review->item_name;

            $res[] = array(
                    'type'          => 'custom',
                    'page_image'    => get_the_post_thumbnail( $review->item_name ),
                    'page_name'     => get_the_title( $review->item_name ),
                    'page_link'     => get_permalink( $review->item_name ),
                    'name'          => $review->reviewer_name,
                    'image'         => get_avatar_url( 1 ),
                    'rating'        => $review->rating,
                    'time'          => $review->dateCreated,
                    'text'          => $review->description,
                );
        }

        return $res;
    }
}


register_widget("wpsocial_review_Widget");
?>
