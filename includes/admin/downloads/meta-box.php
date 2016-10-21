<?php
/**
 * Meta Box
 *
 * @package     EDD\ExternalProducts\Downloads\Meta_Box
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Render the external products row in the download configuration metabox
 *
 * @since       1.1.0
 * @param       int $post_id The ID of this download
 * @return      void
 */
function edd_external_products_metabox_row( $post_id = 0 ) {
	$url   = get_post_meta( $post_id, '_edd_external_product_url', true );
	$label = get_post_meta( $post_id, '_edd_external_product_button', true );
	$label = ( $label ? $label : __( 'Get it free', 'edd-external-products' ) );

	echo '<p><strong>' . __( 'External URL:', 'edd-external-products' ) . '</strong></p>';
	echo '<label for="edd_external_product_url">';
	echo '<input type="text" name="_edd_external_product_url" id="edd_external_product_url" value="' . esc_attr( $url ) . '" style="width: 50%" placeholder="http://" /> ';
	echo '</label>';

	echo '<p><strong>' . __( 'Button Label:', 'edd-external-products' ) . '</strong></p>';
	echo '<label for="edd_external_product_button">';
	echo '<input type="text" name="_edd_external_product_button" id="edd_external_product_button" value="' . esc_attr( $label ) . '" /> ';
	echo '</label>';
}
add_action( 'edd_meta_box_fields', 'edd_external_products_metabox_row', 20 );


/**
 * Add our fields to the saved fields
 *
 * @since       1.1.0
 * @param       array $fields The current fields EDD is saving
 * @return      array The updated fields to save
 */
function edd_external_products_save_fields( $fields ) {
	$extra_fields = array(
		'_edd_external_product_url',
		'_edd_external_product_button'
	);

	return array_merge( $fields, $extra_fields );
}
add_filter( 'edd_metabox_fields_save', 'edd_external_products_save_fields' );
