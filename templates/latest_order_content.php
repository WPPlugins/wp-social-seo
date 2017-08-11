<?php 
wp_enqueue_style('carouselcss', plugins_url('../css/jquery.bxslider.css', __FILE__));
wp_enqueue_script('jquery');
wp_enqueue_script('custom_carousel', plugins_url('/../fb-reviews-widget/static/js/jquery.bxslider.js', __FILE__));
 ?>
<div class="bxslider-slider-latestorder">
<?php 
foreach ( $orders as $customer_order ) {
	$order = new WC_Order();
	$order->populate( $customer_order );
	
	$status     = get_term_by( 'slug', $order->status, 'shop_order_status' );
	$item_count = $order->get_item_count();
	$order_url = $order->get_view_order_url();
	$order_number = $order->get_order_number();

	?>
	<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $customer_order->ID ), 'single-post-thumbnail' );?>

	<div class="latest_order">
		<div>
			<p><img src="<?php echo $image[0]; ?>"></p>	
			<p><?php echo $customer_order->post_title; ?></p>
			<p>Total iterms order <?php  echo $item_count; ?></p>
			Ordered on <?php echo date( 'Y-m-d', strtotime( $order->order_date ) ); ?>
			<?php $add_to_cart =  do_shortcode('[add_to_cart_url id="'+$customer_order->ID+'"]'); ?>
			<p><a href="<?php echo $add_to_cart; ?>" target="_blank">Buy Now</a></p>
		</div>
		
	</div>
<?php	
}	
?>
</div>
<script>
jQuery(document).ready(function () {           
                jQuery('.bxslider-slider-latestorder').bxSlider({
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
            
