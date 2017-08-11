<?php $review_options = (array)get_option( 'wp_social_seo_review_tab' );
$product_id = isset( $order_items['product_id'] ) ? $order_items['product_id'] : 0;
$dn = new wpsocial_DotNotation( $review_options );

if ( ! $product_id ) return; ?>

<div>
	<div class="m_-4644357665824657478ui-sortable" id="m_-4644357665824657478sort_them">
		<table border="0" cellpadding="0" cellspacing="0" align="center" class="m_-4644357665824657478full" style="width:100%">
			<tbody>
				<tr>
					<td bgcolor="#ffffff" valign="top" width="100%" style="background-color:#ffffff">

						<table class="m_-4644357665824657478mobile" align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px">
							<tbody>
								<tr>
									<td width="100%">
										<table class="m_-4644357665824657478full" align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%">
											<tbody>
												<tr>
													<td height="10" width="100%"></td>
												</tr>
												<tr>
													<td valign="middle" width="100%" class="m_-4644357665824657478logo">
														<table style="border-collapse:collapse;width:120px" class="m_-4644357665824657478fullCenter" align="left" border="0" cellpadding="0" cellspacing="0">
															<tbody>
																<tr>
																	<td class="m_-4644357665824657478fullCenter" align="center" height="65" valign="middle" width="100%">
																		<a href="">
																			<img class="m_-4644357665824657478hover CToWUd" src="<?php echo $dn->get('logo_header'); ?>" border="0" alt="goHenry" width="120">
																		</a>
																	</td>
																</tr>
															</tbody>
														</table>
														<table style="border-collapse:collapse;width:330px" class="m_-4644357665824657478fullCenter" align="right" border="0" cellpadding="0" cellspacing="0">
															<tbody>
																<tr>
																	<td>
																		<table style="font-size:13px;border-collapse:collapse;width:290px" class="m_-4644357665824657478fullCenter" align="right" border="0" cellpadding="0" cellspacing="0">
																			<tbody>
																				<tr>
																					<td>
																						<table style="font-size:13px;border-collapse:collapse;width:290px" align="center" border="0" cellpadding="0" cellspacing="0" class="m_-4644357665824657478fullCenter">
																							<tbody>
																								<tr>
																								</tr>
																							</tbody>
																						</table>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
												<tr>
													<td height="10" width="100%"></td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-4644357665824657478full" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
		<tbody>
			<tr>
				<td width="100%">
					<table class="m_-4644357665824657478mobile" align="center" border="0" width="600" cellpadding="0" cellspacing="0">
						<tbody>
							<tr>
								<td width="100%">
									<table class="m_-4644357665824657478fullCenter" align="center" border="0" width="600" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td height="40" width="100%">
													
												</td>
											</tr>
											<tr>
												<td width="100%">
													<table class="m_-4644357665824657478fullCenter" align="center" border="0" width="600" cellpadding="0" cellspacing="0">
														<tbody>
															<tr>
																<td class="m_-4644357665824657478fullCenter" align="center" width="100%"></td>
															</tr>
															<tr>
																<td height="20" width="100%"></td>
															</tr>
															<tr>
																<td align="center" width="100%" style="letter-spacing:1px">
																	<span style="font-size:17px;color:rgb(94,104,122);text-align:center;font-family:Helvetica,Arial,sans-serif;line-height:26px;font-weight:normal;vertical-align:top">
																		<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal">
																			<strong><?php echo $dn->get( 'subject' ); ?></strong> 
																		</span>
																	</span>
																</td>
															</tr>
															<tr>
																<td height="30" width="100%"></td>
															</tr>

														</tbody>
													</table>
														<table bgcolor="#eaf6f6" width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;background-color:rgb(234,246,246)" class="m_-4644357665824657478full">
															<tbody>
																<tr>
																	<td width="600" align="center">
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="15" height="1"></td>
																					<td width="2" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="15" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="14" height="1"></td>
																					<td width="4" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="14" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="13" height="1"></td>
																					<td width="6" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="13" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="12" height="1"></td>
																					<td width="8" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="12" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="11" height="1"></td>
																					<td width="10" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="11" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="10" height="1"></td>
																					<td width="12" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="10" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="9" height="1"></td>
																					<td width="14" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="9" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="8" height="1"></td>
																					<td width="16" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="8" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="7" height="1"></td>
																					<td width="18" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="7" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="6" height="1"></td>
																					<td width="20" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="6" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="5" height="1"></td>
																					<td width="22" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="5" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="4" height="1"></td>
																					<td width="24" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="4" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="3" height="1"></td>
																					<td width="26" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="3" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="2" height="1"></td>
																					<td width="28" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="2" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="32" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eaf6f6" style="background-color:rgb(234,246,246)">
																			<tbody>
																				<tr>
																					<td width="1" height="1"></td>
																					<td width="30" height="1" bgcolor="#ffffff" style="background-color:rgb(255,255,255)">
																					</td>
																					<td width="1" height="1"></td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
															</tbody>
														</table>

														<table class="m_-4644357665824657478fullCenter" style="background-color:rgb(255,255,255)" align="center" border="0" width="600" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
															<tbody>
																<tr>
																	<td width="100%">
																		<table style="border-collapse:collapse" class="m_-4644357665824657478full" align="left" bgcolor="" border="0" width="100%" cellpadding="0" cellspacing="0">
																			<tbody>
																				<tr>
																					<td height="40" width="100%"></td>
																				</tr>
																			</tbody>
																		</table>

																		<table style="border-collapse:collapse" class="m_-4644357665824657478fullCenter" align="center" border="0" width="100%" cellpadding="0" cellspacing="0">
																			<tbody>
																				<tr>
																					<td width="600" align="center">
																						<table class="m_-4644357665824657478mobile" align="center" border="0" width="70%" cellpadding="0" cellspacing="0">
																							<tbody>
																								<tr>
																									<td width="100%" height="20"></td>
																								</tr>
																							</tbody>
																						</table>
																						<table style="border-collapse:collapse" class="m_-4644357665824657478fullCenter" align="center" border="0" width="100%" cellpadding="0" cellspacing="0">
																							<tbody>
																								<tr>
																									<td width="600" align="center">
																										<table class="m_-4644357665824657478mobile" align="center" border="0" width="70%" cellpadding="0" cellspacing="0">
																											<tbody>
																												<tr>
																													<td class="m_-4644357665824657478fullCenter" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:rgb(68,68,68);line-height:22px;text-align:center" width="100%">
																														<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal">Hi <?php echo $customer_name; ?>,
																														</span>
																													</td>
																												</tr>
																												<tr>
																													<td width="100%" height="20">
																													</td>
																												</tr>
																													<tr>
																														<td class="m_-4644357665824657478fullCenter" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:rgb(68,68,68);line-height:22px;text-align:center" width="100%">
																															<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal">
																																<?php echo $dn->get( 'message' ); ?>
																															</span>
																														</td>
																													</tr>
																													<tr>
																														<td width="40%" height="15"></td>
																													</tr>
																														<tr>
																															<td class="m_-4644357665824657478fullCenter" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:rgb(68,68,68);line-height:22px;text-align:center" width="100%">
																																<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal"><a href="<?php echo get_permalink( $product_id ); ?>" target="_blank"><img height="30" src="<?php echo WPSOCIALSEO_URL.'/images/5.png'; ?>" class="CToWUd"></a></span>
																															</td>
																														</tr>
																														<tr>
																															<td width="100%" height="15"></td>
																														</tr>
																														<tr>
																															<td class="m_-4644357665824657478fullCenter" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:rgb(68,68,68);line-height:22px;text-align:center" width="100%">
																																<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal"><a href="<?php echo get_permalink( $product_id ); ?>" target="_blank" ><img height="30" src="<?php echo WPSOCIALSEO_URL.'/images/4.png'; ?>" class="CToWUd"></a></span></td>
																														</tr>
																														<tr>
																															<td width="100%" height="15"></td>
																														</tr>
																														<tr>
																															<td class="m_-4644357665824657478fullCenter" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:rgb(68,68,68);line-height:22px;text-align:center" width="100%">
																																<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal">
																																	<a href="<?php echo get_permalink( $product_id ); ?>" target="_blank" >
																																		<img height="30" src="<?php echo WPSOCIALSEO_URL.'/images/3.png'; ?>" class="CToWUd">
																																	</a>
																																</span>
																															</td>
																														</tr>
																														<tr>
																															<td width="100%" height="15"></td>
																														</tr>
																														<tr>
																															<td class="m_-4644357665824657478fullCenter" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:rgb(68,68,68);line-height:22px;text-align:center" width="100%">
																																<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal">
																																	<a href="<?php echo get_permalink( $product_id ); ?>" target="_blank" ><img height="30" src="<?php echo WPSOCIALSEO_URL.'/images/2.png'; ?>" class="CToWUd">
																																	</a>
																																</span>
																															</td>
																														</tr>
																														<tr>
																															<td width="100%" height="15"></td>
																														</tr>
																														<tr>
																															<td class="m_-4644357665824657478fullCenter" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:rgb(68,68,68);line-height:22px;text-align:center" width="100%">
																																<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal">
																																	<a href="<?php echo get_permalink( $product_id ); ?>" target="_blank" >
																																		<img height="30" src="<?php echo WPSOCIALSEO_URL.'/images/1.png'; ?>" class="CToWUd">
																																	</a>
																																</span>
																															</td>
																														</tr>
																														<tr>
																															<td width="100%" height="20"></td>
																														</tr>
																														<tr>
																															<td width="100%" height="20"></td>
																														</tr>
																														<tr>
																															<td class="m_-4644357665824657478fullCenter" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:rgb(68,68,68);line-height:22px;text-align:center" width="100%">
																																<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal">Kind regards,<br>
																																	<?php echo $dn->get('full_name' ); ?>
																																</span>
																															</td>
																														</tr>
																														<tr>
																															<td width="100%" height="10"></td>
																														</tr>
																														<tr>
																															<td class="m_-4644357665824657478fullCenter" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:rgb(68,68,68);line-height:22px;text-align:center" width="100%">
																																<span style="font-family:'proxima_nova_regular',Helvetica;font-weight:normal">
																																	<img class="CToWUd" src="<?php echo $dn->get('logo_footer' ); ?>" height="100">
																																	<br>
																																	<br>
																																	<a href="mailto:<?php echo $dn->get('Email' ); ?>" title="Get in touch" style="text-decoration:none;color:#11abaa" target="_blank">
																																		<?php echo $dn->get('Email' ); ?>
																																	</a>
																																	<br>
																																		<?php echo $dn->get('phone_num' ); ?>
																																	<br>
																																	<?php echo $dn->get('footer_text_top' ); ?>
																																	<br>
																																	<br>
																																		<?php echo $dn->get('footertext' ); ?>
																																	</a>
																																</span>
																															</td>	
																														</tr>
																													</tbody>
																												</table>
																												<table style="border-collapse:collapse" class="m_-4644357665824657478full" align="left" bgcolor="" border="0" width="100%" cellpadding="0" cellspacing="0">
																													<tbody>
																														<tr>
																															<td height="45" width="100%">
																																
																															</td>
																														</tr>
																													</tbody>
																												</table>
																										</td>
																									</tr>
																							</tbody>
																						</table>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																		<table style="border-collapse:collapse" class="m_-4644357665824657478full" align="left" bgcolor="" border="0" width="100%" cellpadding="0" cellspacing="0">
																			<tbody>
																				<tr>
																					<td height="60" width="100%"></td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
														</tbody>
														</table>
													</td>
												</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-4644357665824657478full">
						<tbody>
							<tr>
								<td bgcolor="#11abaa" valign="top" width="100%" style="background-color:rgb(17,171,170)">
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>		
		</tbody>
	</table>
</div>