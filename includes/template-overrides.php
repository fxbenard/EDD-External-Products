<?php
/**
 * Template overrides
 *
 * @package     EDD\ExternalProducts\Template_Overrides
 * @since       1.1.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Disable the add_to_cart action
 *
 * @since       1.1.0
 * @param       int $download_id The ID of this download
 * @return      void
 */
function edd_external_products_pre_add_to_cart( $download_id = 0 ) {
	$url = get_post_meta( $download_id, '_edd_external_product_url', true );

	if( $url ) {
		wp_die( sprintf( __( 'This %1$s can only be purchased from %2$s!', 'edd-external-products' ), strtolower( edd_get_label_singular() ), esc_url( $url ) ), '', array( 'back_link' => true ) );
	}
}
add_action( 'edd_pre_add_to_cart', 'edd_external_products_pre_add_to_cart' );


/**
 * Override product links
 *
 * @since       1.1.0
 * @param       string $purchase_form The current form markup
 * @param       array $args The purchase form args
 * @return      string $purchase_form The updated form markup
 */
function edd_external_products_product_links( $purchase_form, $args ) {
	if( $url = get_post_meta( $args['download_id'], '_edd_external_product_url', true ) ) {
		$label = get_post_meta( $args['download_id'], '_edd_external_product_button', true );

		$purchase_form  = '<div class="edd_download_purchase_form">';
		$purchase_form .= '<div class="edd_purchase_submit_wrapper">';

		$purchase_form .= sprintf(
			'<a class="%1$s" href="%2$s">%3$s</a>',
			implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) ),
			esc_url( $url ),
			esc_attr( $label )
		);

		$purchase_form .= '</div>';
		$purchase_form .= '</div>';
	}

	return $purchase_form;
}
add_action( 'edd_purchase_download_form', 'edd_external_products_product_links', 10, 2 );
