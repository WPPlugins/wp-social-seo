<?php
class socialseo_reviewio_widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'socialseo_reviewio_widget',
            __('Review.io Rich Snippets', 'socialseo_reviewio_widget_domain'),
            array('description' => __('Rich snippets', 'socialseo_reviewio_widget_domain'))
        );

    }
    

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        $time = time();
        // Rich Snippet Code
            $settings = get_option('social_seo_reviewsio_snippet');
            if ($settings) {
                $settings = unserialize(get_option('social_seo_reviewsio_snippet'));
            }
            $settings['store_domain'] = isset($settings['store_domain'])?$settings['store_domain']:'';
            $settings['primary_color'] = isset($settings['primary_color'])?$settings['primary_color']:'';
            $settings['text_color'] = isset($settings['text_color'])?$settings['text_color']:'';
            $settings['background_color'] = isset($settings['background_color'])?$settings['background_color']:'';
            $settings['header_color'] = isset($settings['header_color'])?$settings['header_color']:'';
            $settings['footer'] = isset($settings['footer'])?$settings['footer']:'0';
            $settings['names'] = isset($settings['names'])?$settings['names']:'0';
            $settings['dates'] = isset($settings['dates'])?$settings['dates']:'0';
           ?> 
        
           <?php  if ( ! wp_script_is( 'jquery', 'enqueued' )):?>
           <!-- Reviews.co.uk Rich Snippet Code -->
           <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
           <script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
       <?php endif; ?>
           <div id='snippetWidget<?php echo $time;?>'></div>
           
           <script>

           jQuery.get('https://widget.reviews.co.uk/rich-snippet-reviews/widget?store=<?php echo $settings['store_domain']; ?>&primaryClr=<?php echo urlencode($settings['primary_color']); ?>&textClr=<?php echo urlencode($settings['text_color']); ?>&bgClr=<?php echo urlencode($settings['background_color']); ?>&height=600&headClr=<?php echo urlencode($settings['header_color']); ?>&header=&headingSize=20px&numReviews=<?php echo $instance['num_reviews']; ?>&names=<?php echo $settings['names']; ?>&dates=<?php echo $settings['dates']; ?>&footer=<?php echo $settings['footer']; ?>&tag=<?php echo $instance['tag']; ?>', function(r){
               jQuery('#snippetWidget'+'<?php echo $time;?>').html(r);
           });
           </script>

           <?php
            
          
        echo $args['after_widget'];
    }

// Widget Backend
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = '';
        }

        
        if (isset($instance['height'])) {
            $height = $instance['height'];
        } else {
            $height = '';
        }

        
        if (isset($instance['num_reviews'])) {
            $num_reviews = $instance['num_reviews'];
        } else {
            $num_reviews = '';
        }

        if (isset($instance['tag'])) {
            $tag = $instance['tag'];
        } else {
            $tag = '';
        }

        if (isset($instance['num_reviews'])) {
            $num_reviews = $instance['num_reviews'];
        } else {
            $num_reviews = '';
        }
        if (isset($instance['cache_time'])) {
            $cache_time = $instance['cache_time'];
        } else {
            $cache_time = '';
        }
        ?>
 <div id="review_snippet_widget">
 <p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:');?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
</p>


<p>
<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:');?></label>
<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo esc_attr($height); ?>" />
</p>


<p>
<label for="<?php echo $this->get_field_id('num_reviews'); ?>"><?php _e('No of reviews to show:');?></label>
<input class="widefat" id="<?php echo $this->get_field_id('num_reviews'); ?>" name="<?php echo $this->get_field_name('num_reviews'); ?>" type="text" value="<?php echo esc_attr($num_reviews); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag:');?></label>
<input class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="text" value="<?php echo esc_attr($tag); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('cache_time'); ?>"><?php _e('Cache Length (seconds):');?></label>
<input class="widefat" id="<?php echo $this->get_field_id('cache_time'); ?>" name="<?php echo $this->get_field_name('cache_time'); ?>" type="text" value="<?php echo esc_attr($cache_time); ?>" />
</p>
</div>


<?php
}

    public function update($new_instance, $old_instance)
    {
        $instance          = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['height'] = (!empty($new_instance['height'])) ? strip_tags($new_instance['height']) : '';
        $instance['num_reviews'] = (!empty($new_instance['num_reviews'])) ? strip_tags($new_instance['num_reviews']) : '';
        $instance['tag'] = (!empty($new_instance['tag'])) ? strip_tags($new_instance['tag']) : '';
        $instance['cache_time'] = (!empty($new_instance['cache_time'])) ? strip_tags($new_instance['cache_time']) : '';
        return $instance;
    }
}
function social_seo_review_widget()
{
    register_widget('socialseo_reviewio_widget');
}
add_shortcode( 'reviews-widget', 'reviews_widget');
function reviews_widget( $atts ) {

// Configure defaults and extract the attributes into variables
extract( shortcode_atts( 
    array( 
        'type'  => '',
        'title' => '',
        'tag' => '',
        'height' => '',
        'num_reviews'=>'',
        'cache_time'=>''

    ), 
    $atts 
));

$args = array(
    'before_widget' => '<div class="box widget">',
    'after_widget'  => '</div>',
    'before_title'  => '<div class="widget-title">',
    'after_title'   => '</div>',
);

ob_start();
the_widget( $type, $atts, $args ); 
$output = ob_get_clean();

return $output;
}


add_action('widgets_init', 'social_seo_review_widget');

