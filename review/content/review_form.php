<?php 
$review_data = (array)get_option( 'wp_social_seo_review_tab' );
$dn = new wpsocial_DotNotation( $review_data ); 
?>
				<div class="metabox-holder content-container"> 
					<div class="postbox-container wps-postbox-container">    
						<div class="meta-box-sortables ui-sortable">        
							<div class="postbox">
								<div class="handlediv" title="Click to toggle"><br/></div>
								<h3 class="hndle"><span>General Settings</span></h3>
								<div class="inside">
									<?php global $wpdb;
	                                    ?>
	                                    <div class='review-setup'>
	                                    	<form action='' method='post'>
	                                    		<div class="social-form">
	                                    			<div class="form-left">
	                                    				<div class="form-group">
	                                    					<label>Would you like us to email each client after an order to ask for a review ?<br>(WooCommerce only)</label>
	                                    					<select name="review[review_enabled]">
	                                    						<option value="">choose option</option>
	                                    						<option <?php echo selected($dn->get('review_enabled'), 'YES') ?> value="YES">YES</option>
	                                    						<option <?php echo selected($dn->get('review_enabled'), 'NO') ?> value="NO">NO</option>
	                                    					</select>
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Full Name</label>
	                                    					<input type='text' name='review[full_name]' value="<?php echo $dn->get( 'full_name' ); ?>" />
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Phone Number</label>
	                                    					<input type='text' name='review[phone_num]' value="<?php echo $dn->get( 'phone_num' ); ?>" />
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Email</label>
	                                    					<input type='text' name='review[Email]' value="<?php echo $dn->get( 'Email' ); ?>" />
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Header Logo URL</label>
	                                    					<input type='text' name='review[logo_header]' value="<?php echo $dn->get( 'logo_header' ); ?>" />
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Footer Logo URL</label>
	                                    					<input type='text' name='review[logo_footer]' value="<?php echo $dn->get( 'logo_footer' ); ?>" />
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Message</label>
	                                    					<textarea name='review[message]' rows='10' cols='80'><?php echo $dn->get( 'message' ); ?></textarea>
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<input type='hidden' name='review_message' value='1'><input type='submit' value='Submit'>
	                                    				</div>

	                                    			</div>

	                                    			<div class="form-right">
	                                    				<div class="form-group">

	                                    					<label>Company Name</label>
	                                    					<input type='text' name='review[company_name]' value="<?php echo $dn->get( 'company_name' ); ?>" />
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Subject</label>
	                                    					<input type='text' name='review[subject]' value="<?php echo $dn->get( 'subject' ); ?>" />
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Review us at</label>
	                                    					<input class="one-third" type='text' name='review[review_link_1]' value="<?php echo $dn->get( 'review_link_1' ); ?>" />
			                                    			<input class="one-third" type='text' name='review[review_link_2]' value="<?php echo $dn->get( 'review_link_2' ); ?> " />
			                                    			<input class="one-third" type='text' name='review[review_link_3]' value="<?php echo $dn->get( 'review_link_3' ); ?>" />
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Footer Text Top</label>
	                                    					<textarea name='review[footer_text_top]' rows='8' cols='80'><?php echo $dn->get( 'footer_text_top' ); ?></textarea>
	                                    				</div>
	                                    				<div class="form-group">
	                                    					<label>Footer Text</label>
	                                    					<textarea name='review[footertext]' rows='8' cols='80'><?php echo $dn->get( 'footertext' ); ?></textarea>
	                                    				</div>
	                                    			</div>

	                                    		</div>

	            							</form>
	        							</div>
								 </div>
							</div>
						</div>
					</div>
				</div>
