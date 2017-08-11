<?php

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    //add_action('admin_menu', 'review_plugin_setup_menu');

   /* function review_plugin_setup_menu() {
        add_menu_page('Review Plugin Page', 'Review Plugin Setup', 'manage_options', 'review-plugin', 'review_init');
    }*/
 
    function review_init() {
    	global $wpdb;
    	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $query = "select * from `" . $wpdb->prefix . "review_user_profile`";
    	$result = $wpdb->get_row($query);
//    
    	$html = "<div class='review-setup'><form action='' method='post'><input type='hidden' name='review_user_profile_id' value='";
    	if (isset($result->id)) {
    		$html .= $result->id;
    	}
    	$html .= "'><div class='form-input'><label>Full Name</label><input type='text' name='full_name' value='";
    	if (isset($result->full_name)) {
    		$html .= $result->full_name;
    	}
    	$html .= "'></div><div class='form-input'><label>Company Name</label><input type='text' name='company_name' value='";
    	if (isset($result->company_name)) {
    		$html .= $result->company_name;
    	}
    	$html .="'></div><div class='form-input'><label>Subject</label><textarea name='subject' rows='2' cols='80'>";
    	$html .=$result->subject;
    	$html .="</textarea></div><div class='form-input'><label>Message</label><textarea name='message' rows='10' cols='80'>";
    	$html .=$result->message_body;
    	$html .="</textarea></div>";
    	$html .="<div class='form-input'><label>Review us at</label><input type='text' name='review_link_1' value='";
    	if (isset($result->review_link_1)) {
    		$html .= $result->review_link_1;
    	}
    	$html .="'><input type='text' name='review_link_2' value='";
    	if (isset($result->review_link_2)) {
    		$html .= $result->review_link_2;
    	}
    	$html .="'><input type='text' name='review_link_3' value='";
    	if (isset($result->review_link_3)) {
    		$html .= $result->review_link_3;
    	}
    	$html .="'></div>";
    	$html .="<div class='form-input'><input type='hidden' name='review_message' value='1'><input type='submit' value='Submit'></div></form></div>";

    	echo $html;
    }

    if ( isset( $_POST['review'] ) && $_POST['review'] !== '') {
        update_option( 'wp_social_seo_review_tab', $_POST['review'] );
    }

    

    add_action('template_redirect', 'send_email_after_purchase');
    function send_email_after_purchase() {
    	global $wp;
    	global $wpdb;
    	if (is_checkout() && !empty($wp->query_vars['order-received'])) {
            $order_id = absint($wp->query_vars['order-received']);

            $review_data = (array)get_option( 'wp_social_seo_review_tab' );
            $dn = new wpsocial_DotNotation( $review_data ); 
            if ($dn->get('review_enabled') != 'YES'){
                return false;
            }


    		$order = new WC_Order($order_id);
    		//print_r($order); exit();
    		$customer = get_userdata($order->customer_user);
    		$customer_email = $customer->data->user_email;
    		$customer_name = $customer->data->display_name;

            $order_items = current($order->get_items());
            $review_options = (array)get_option( 'wp_social_seo_review_tab' );
    		$to = $customer_email;
            $pro_title = get_the_title( $order_items['product_id'] );
    		$subject = isset( $review_options['subject'] ) ? $review_options['subject'] : esc_html__('What would you tell others about ', 'wp-social-seo' ).$pro_title ;

    		$headers = "Reply-To:  no-reply\r\n";
    		$headers .= "MIME-Version: 1.0\r\n";

    		$headers = "Content-Type: text/html; charset=ISO-8859-1\r\n";
    		$content = '';
    		ob_start();
    		include WPSOCIALSEO_PATH . '/templates/email_template.php';
    		$content = ob_get_clean();
    		$message = $content;

    		if ( ! $message )  {
    			return;
    		}
    		
//$to = 'admin@octocs.com';
    		$mail_status = wp_mail($to, $subject, $message, $headers );


    		if ($mail_status) {

                $res = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix. "review_user_emails WHERE order_id = %s", $order_id ) );

                $data_toupdate = array( 'custom_name' => $customer_name, 'email_sent_on' => date('Y-m-d h:i:s'), 'order_id' => $order->id );
                if ( $res ) {
                    $wpdb->update( $wpdb->prefix.'review_user_emails', $data_toupdate, array( 'order_id' => $order_id ) );

                } else {
                    $wpdb->insert( $wpdb->prefix.'review_user_emails', $data_toupdate );
                }

    		}

    	}
    }



}

// run the install scripts upon plugin activation
