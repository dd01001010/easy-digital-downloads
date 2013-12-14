<?php
/**
 * View Order Details
 *
 * @package     EDD
 * @subpackage  Admin/Payments
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.6
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * View Order Details Page
 *
 * @since 1.6
 * @return void
*/
if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
	wp_die( __( 'Payment ID not supplied. Please try again', 'edd' ), __( 'Error', 'edd' ) );
}

// Setup the variables
$payment_id   = absint( $_GET['id'] );
$item         = get_post( $payment_id );
$payment_meta = edd_get_payment_meta( $payment_id );
$cart_items   = edd_get_payment_meta_cart_details( $payment_id );
$user_info    = edd_get_payment_meta_user_info( $payment_id );
$user_id      = edd_get_payment_user_id( $payment_id );
$payment_date = strtotime( $item->post_date );
?>
<div class="wrap">
	<h2><?php printf( __( 'Payment #%d', 'edd' ), $payment_id ); ?></h2>
	<?php do_action( 'edd_view_order_details_before' ); ?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<?php do_action( 'edd_view_order_details_sidebar_before' ); ?>
					<div id="edd-order-totals" class="postbox">
						<h3 class="hndle">
							<span><?php _e( 'Order Totals', 'edd' ); ?></span>&nbsp;&ndash;&nbsp;
							<a href="" class="edd-payment-edit edd-edit-toggles" title="<?php esc_attr_e( 'Edit Totals', 'edd' ); ?>"><?php _e( 'Edit Totals', 'edd' ); ?></a>
							<a href="" class="edd-payment-edit edd-edit-toggles" style="display:none;" title="<?php esc_attr_e( 'Cancel Edit', 'edd' ); ?>"><?php _e( 'Cancel Edit', 'edd' ); ?></a>
						</h3>
						<div class="inside">
							<div class="edd-order-totals-box edd-admin-box">
								<form id="edd-order-totals-form" method="post">
									<?php do_action( 'edd_view_order_details_totals_before', $payment_id ); ?>
									<div class="edd-order-discounts edd-admin-box-inside">
										<p>
											<span class="label"><?php _e( 'Discount Code', 'edd' ); ?></span>&nbsp;
											<span class="right"><?php if ( isset( $user_info['discount'] ) && $user_info['discount'] !== 'none' ) { echo '<code>' . $user_info['discount'] . '</code>'; } else { _e( 'None', 'edd' ); } ?></span>
										</p>
									</div>
									<?php if ( edd_use_taxes() ) : ?>
									<div class="edd-order-taxes edd-admin-box-inside">
										<p>
											<span class="label"><?php _e( 'Tax', 'edd' ); ?></span>&nbsp;
											<span class="right edd-edit-toggles"><?php echo edd_currency_filter( edd_format_amount( edd_get_payment_tax( $payment_id ) ) ); ?></span>
											<input name="edd-payment-tax" style="display:none;" type="number" class="small-text right edd-edit-toggles" value="<?php echo esc_attr( edd_get_payment_tax( $payment_id ) ); ?>"/>
										</p>
									</div>
									<?php endif; ?>
									<?php
									$fees = edd_get_payment_fees( $payment_id );
									if ( ! empty( $fees ) ) : ?>
									<div class="edd-order-fees edd-admin-box-inside">
										<p class="strong"><?php _e( 'Fees', 'edd' ); ?></p>
										<ul class="edd-payment-fees">
											<?php foreach( $fees as $fee ) : ?>
											<li><span class="fee-label"><?php echo $fee['label'] . ':</span> ' . '<span class="right">' . edd_currency_filter( $fee['amount'] ); ?></span></li>
											<?php endforeach; ?>
										</ul>
									</div>
									<?php endif; ?>
									<div class="edd-order-payment edd-admin-box-inside">
										<p>
											<span class="label"><?php _e( 'Total Price', 'edd' ); ?></span>&nbsp;
											<span class="right edd-edit-toggles"><?php echo edd_currency_filter( edd_format_amount( edd_get_payment_amount( $payment_id ) ) ); ?></span>
											<input name="edd-payment-total" style="display:none;" type="number" class="small-text right edd-edit-toggles" value="<?php echo esc_attr( edd_get_payment_amount( $payment_id ) ); ?>"/>
										</p>
									</div>
									<div class="edd-order-resend-email edd-admin-box-inside edd-edit-toggles">
										<p>
											<span class="label"><?php _e( 'Payment Receipt', 'edd' ); ?></span>&nbsp;
											<a href="<?php echo add_query_arg( array( 'edd-action' => 'email_links', 'purchase_id' => $payment_id ) ); ?>" class="right button-secondary"><?php _e( 'Resend', 'edd' ); ?></a>
										</p>
									</div>
									<div class="edd-order-update edd-admin-box-inside edd-edit-toggles" style="display:none;">
										<p>
											<span class="label"><?php _e( 'Update Payment', 'edd' ); ?></span>&nbsp;
											<input type="hidden" name="edd_payment_id" value="<?php echo esc_attr( $payment_id ); ?>"/>
											<input type="hidden" name="edd_action" value="update_payment_totals"/>
											<?php wp_nonce_field( 'edd_update_payment_totals_nonce' ); ?>
											<input type="submit" class="button-primary right" value="<?php _e( 'Update', 'edd' ); ?>"/>
										</p>
									</div>
									<?php do_action( 'edd_view_order_details_totals_after', $payment_id ); ?>
								</form><!-- /#edd-order-totals-form -->
							</div><!-- /.edd-order-totals-box -->
						</div><!-- /.inside -->
					</div><!-- /#edd-order-totals -->

					<div id="edd-payment-notes" class="postbox">
						<h3 class="hndle"><span><?php _e( 'Payment Notes', 'edd' ); ?></span></h3>
						<div class="inside">
							<?php
							$notes = edd_get_payment_notes( $payment_id );
							if ( ! empty( $notes ) ) :
								foreach ( $notes as $note ) :
									if ( ! empty( $note->user_id ) ) {
										$user = get_userdata( $note->user_id );
										$user = $user->display_name;
									} else {
										$user = __( 'EDD Bot', 'edd' );
									}
									?>
									<div class="edd-payment-note">
										<p>
											<strong><?php echo $user; ?></strong> <em><?php echo $note->comment_date; ?></em><br/>
											<?php echo $note->comment_content; ?>
										</p>
									</div>
									<?php
								endforeach;
							else :
								echo '<p>'. __( 'No payment notes', 'edd' ) . '</p>';
							endif;
							?>
							<form id="edd-payment-notes-form" method="post">
								<textarea name="edd-payment-note" id="edd-payment-note" class="large-text"></textarea>
								<input type="hidden" name="edd_action" value="add_payment_note"/>
								<input type="hidden" name="edd_payment_id" value="<?php echo esc_attr( $payment_id ); ?>"/>
								<input type="submit" class="button-secondary" value="<?php _e( 'Add Note', 'edd' ); ?>"/>
							</form>

						</div><!-- /.inside -->
					</div><!-- /#edd-payment-notes -->

					<?php do_action( 'edd_view_order_details_sidebar_after', $payment_id ); ?>
				</div><!-- /#side-sortables -->
			</div><!-- /#postbox-container-1 -->

			<div id="postbox-container-2" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<?php do_action( 'edd_view_order_details_main_before' ); ?>
					<div id="edd-order-data" class="postbox">
						<h3 class="hndle">
							<span><?php _e( 'Order Details', 'edd' ); ?></span>&nbsp;&ndash;&nbsp;
							<a href="" class="edd-payment-edit edd-edit-toggles" title="<?php esc_attr_e( 'Edit Order Details', 'edd' ); ?>"><?php _e( 'Edit Order Details', 'edd' ); ?></a>
							<a href="" class="edd-payment-edit edd-edit-toggles" style="display:none;" title="<?php esc_attr_e( 'Cancel Edit', 'edd' ); ?>"><?php _e( 'Cancel Edit', 'edd' ); ?></a>
						</h3>
						<div class="inside">
							<form id="edd-payment-details-form" method="post">
								<div class="column-container">
									<div class="order-data-column">
										<h4><?php _e( 'General Details', 'edd' ); ?></h4>
										<p class="data">
											<span><?php _e( 'Status:', 'edd' ); ?></span>&nbsp;
											<span class="edd-edit-toggles"><?php echo edd_get_payment_status( $item, true ) ?></span>
										</p>
										<p class="data">
											<span><?php _e( 'Date:', 'edd' ); ?></span>&nbsp;
											<span class="edd-edit-toggles"><?php echo date_i18n( get_option( 'date_format' ), $payment_date ) ?></span>
										</p>
										<p class="data">
											<span><?php _e( 'Time:', 'edd' ); ?></span>&nbsp;
											<span class="edd-edit-toggles"><?php echo date_i18n( get_option( 'time_format' ), $payment_date ); ?></span>
										</p>
									</div>

									<div class="order-data-column">
										<h4><?php _e( 'Buyer\'s Personal Details', 'edd' ); ?></h4>
										<p class="data">
											<span><?php _e( 'Name:', 'edd' ); ?></span>&nbsp;
											<span class="edd-edit-toggles"><?php echo $user_info['first_name'] . ' ' . $user_info['last_name']; ?></span>
											<input type="text" name="edd-payment-user-name" value="<?php esc_attr_e( $user_info['first_name'] . ' ' . $user_info['last_name'] ); ?>" class="edd-edit-toggles medium-text" style="display:none;"/>
										</p>
										<p class="data">
											<span class="edd-edit-toggles"><?php _e( 'User Status:', 'edd' ); ?></span>&nbsp;
											<span class="edd-edit-toggles"><?php echo $user_id > 0 ? __( 'Registered User', 'edd' ) : __( 'Guest', 'edd' ); ?></span>
										</p>
										<?php if( $user_id > 0 ) : ?>
											<p class="data">
												<span><?php _e( 'User ID:', 'edd' ); ?></span>&nbsp;
												<span class="edd-edit-toggles"><?php echo $user_id; ?></span>
												<input type="number" step="1" min="0" name="edd-payment-user-id" value="<?php esc_attr_e( $user_id ); ?>" class="edd-edit-toggles small-text" style="display:none;"/>
											</p>
										<?php endif; ?>
										<p class="data">
											<span><?php _e( 'Email:', 'edd' ); ?></span>&nbsp;
											<a href="mailto:<?php echo esc_attr( edd_get_payment_user_email( $payment_id ) ); ?>" class="edd-edit-toggles"><?php echo edd_get_payment_user_email( $payment_id ); ?></a>
											<input type="email" name="edd-payment-user-email" value="<?php esc_attr_e( edd_get_payment_user_email( $payment_id ) ); ?>" class="edd-edit-toggles medium-text" style="display:none;"/>
										</p>
										<p class="data">
											<span><?php _e( 'IP:', 'edd' ); ?></span>&nbsp;
											<span class="edd-edit-toggles"><?php echo edd_get_payment_user_ip( $payment_id ); ?></span>
										</p>
										<ul><?php do_action( 'edd_payment_personal_details_list', $payment_meta, $user_info ); ?></ul>
									</div>

									<div class="order-data-column">
										<h4><?php _e( 'Payment Details', 'edd' ); ?></h4>
										<?php
										$gateway = edd_get_payment_gateway( $payment_id );
										if ( $gateway ) { ?>
										<p class="data">
											<span><?php _e( 'Gateway:', 'edd' ); ?></span> <?php echo edd_get_gateway_admin_label( $gateway ); ?>
										</p>
										<?php } ?>
										<p class="data data-payment-key">
											<span><?php _e( 'Key:', 'edd' ); ?></span>&nbsp;
											<span class="edd-edit-toggles"><?php echo $payment_meta['key']; ?></span>
										</p>
									</div>

									<?php if( ! empty( $user_info['address'] ) ) { ?>
										<div class="order-data-column" id="edd-order-address">

											<h4><span><?php _e( 'Billing Address', 'edd' ); ?></span></h4>
											<div class="order-data-address">
												<p class="data">
													<span class="order-data-address-line"><?php echo _x( 'Line 1:', 'First address line', 'edd' ); ?></span>&nbsp;
													<span class="edd-edit-toggles"><?php echo $user_info['address']['line1']; ?></span><br/>
													<span class="order-data-address-line"><?php echo _x( 'Line 2:', 'Second address line', 'edd' ); ?></span>&nbsp;
													<span class="edd-edit-toggles"><?php echo $user_info['address']['line2']; ?></span><br/>
													<span class="order-data-address-line"><?php echo _x( 'City:', 'First address line', 'edd' ); ?></span>&nbsp;
													<span class="edd-edit-toggles"><?php echo $user_info['address']['city']; ?></span><br/>
													<span class="order-data-address-line"><?php echo _x( 'State / Province:', 'First address line', 'edd' ); ?></span>&nbsp;
													<span class="edd-edit-toggles"><?php echo $user_info['address']['state']; ?></span><br/>
													<span class="order-data-address-line"><?php echo _x( 'Zip / Postal Code:', 'First address line', 'edd' ); ?></span>&nbsp;
													<span class="edd-edit-toggles"><?php echo $user_info['address']['zip']; ?></span><br/>
													<span class="order-data-address-line"><?php echo _x( 'Country:', 'First address line', 'edd' ); ?></span>&nbsp;
													<span class="edd-edit-toggles"><?php echo $user_info['address']['country']; ?></span><br/>
												</p>
											</div>
										</div><!-- /#edd-order-address -->
									<?php } ?>

									<?php do_action( 'edd_payment_view_details', $payment_id ); ?>

									<div id="edd-payment-details-form-submit" class="edd-edit-toggles" style="display:none;">
										<input type="hidden" name="edd_payment_id" value="<?php echo esc_attr( $payment_id ); ?>"/>
										<input type="hidden" name="edd_action" value="update_payment_details"/>
										<?php wp_nonce_field( 'edd_update_payment_details_nonce' ); ?>
										<input type="submit" class="button-primary right" value="<?php _e( 'Update', 'edd' ); ?>"/>
									</div><!-- /#edd-payment-details-form-submit -->
								</div><!-- /.column-container -->
							</form>
						</div><!-- /.inside -->
					</div><!-- /#edd-order-data -->

					<div id="edd-purchased-files" class="postbox">
						<h3 class="hndle"><?php printf( __( 'Purchased %s', 'edd' ), edd_get_label_plural() ); ?> - <a href="<?php echo add_query_arg( 'action', 'edit' ); ?>"><?php _e( 'Edit Files', 'edd' ); ?></a></h3>
						<div class="inside">
							<table class="wp-list-table widefat fixed" cellspacing="0">
								<tbody id="the-list">
									<?php
									if ( $cart_items ) :
										$i = 0;
										foreach ( $cart_items as $key => $cart_item ) :
											// Item ID is checked if isset due to the near-1.0 cart data
											$item_id = isset( $cart_item['id']    ) ? $cart_item['id']    : $cart_item;
											$price   = isset( $cart_item['price'] ) ? $cart_item['price'] : false;
											if( ! $price ) {
												// This function is only used on payments with near 1.0 cart data structure
												$price = edd_get_download_final_price( $item_id, $user_info, null );
											}
											?>
											<tr class="<?php if ( $i % 2 == 0 ) { echo 'alternate'; } ?>">
												<td class="name column-name">
													<?php
													echo '<a href="' . admin_url( 'post.php?post=' . $item_id . '&action=edit' ) . '">' . get_the_title( $item_id ) . '</a>';

													if ( isset( $cart_items[ $key ]['item_number'] ) ) {
														$price_options = $cart_items[ $key ]['item_number']['options'];

														if ( isset( $price_options['price_id'] ) ) {
															echo ' - ' . edd_get_price_option_name( $item_id, $price_options['price_id'], $payment_id );
														}
													}
													?>
												</td>
												<?php if( edd_item_quantities_enabled() ) : ?>
												<td class="quantity column-quantity">
													<?php echo __( 'Quantity:', 'edd' ) . '&nbsp;' . $cart_item['quantity']; ?>
												</td>
												<?php endif; ?>
												<td class="price column-price">
													<?php echo edd_currency_filter( edd_format_amount( $price ) ); ?>
												</td>
											</tr>
											<?php
											$i++;
										endforeach;
									endif;
									?>
								</tbody>
							</table>
						</div><!-- /.inside -->
					</div><!-- /#edd-purchased-files -->
					<?php do_action( 'edd_view_order_details_main_after', $payment_id ); ?>
				</div><!-- /#normal-sortables -->
			</div><!-- #postbox-container-2 -->
		</div><!-- /#post-body -->
	</div><!-- /#post-stuff -->
	<?php do_action( 'edd_view_order_details_after' ); ?>
</div><!-- /.wrap -->
