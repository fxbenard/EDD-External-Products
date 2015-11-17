<?php
/**
 * Plugin Name:     Easy Digital Downloads - External Products
 * Plugin URI:      https://wordpress.org/plugins/easy-digital-downloads-external-products
 * Description:     Adds a robust third-party product system to Easy Digital Downloads
 * Version:         1.0.0
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     edd-external-products
 *
 * @package         EDD\ExternalProducts
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 * @copyright       Copyright (c) 2014, Daniel J Griffiths
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( ! class_exists( 'EDD_External_Products' ) ) {


	/**
	 * Main EDD_External_Products class
	 *
	 * @since       1.0.0
	 */
	class EDD_External_Products {


		/**
		 * @var         EDD_External_Products $instance The one true EDD_External_Products
		 * @since       1.0.0
		 */
		private static $instance;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      self::$instance The one true EDD_External_Products
		 */
		public static function instance() {
			if( ! self::$instance ) {
				self::$instance = new EDD_External_Products();
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function setup_constants() {
			// Plugin version
			define( 'EDD_EXTERNAL_PRODUCTS_VER', '1.0.0' );

			// Plugin path
			define( 'EDD_EXTERNAL_PRODUCTS_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'EDD_EXTERNAL_PRODUCTS_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function hooks() {
			// Add settings to downloads config metabox
			add_action( 'edd_meta_box_fields', array( $this, 'metabox_row' ), 20 );

			// Add our settings to saved fields
			add_filter( 'edd_metabox_fields_save', array( $this, 'save_fields' ) );

			// Disable the add_to_cart action
			add_action( 'edd_pre_add_to_cart', array( $this, 'pre_add_to_cart' ) );

			// Override purchase buttons
			add_action( 'edd_purchase_download_form', array( $this, 'product_links' ), 10, 2 );
		}


		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_conditional_emails_lang_dir', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), '' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'edd-external-products', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/edd-external-products/' . $mofile;

			if( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-external-products/ folder
				load_textdomain( 'edd-external-products', $mofile_global );
			} elseif( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-external-products/languages/ folder
				load_textdomain( 'edd-external-products', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-external-products', false, $lang_dir );
			}
		}


		/**
		 * Render the external products row in the download configuration metabox
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       int $post_id The ID of this download
		 * @return      void
		 */
		public function metabox_row( $post_id = 0 ) {
			$url    = get_post_meta( $post_id, '_edd_external_product_url', true );
			$label  = get_post_meta( $post_id, '_edd_external_product_button', true );
			$label  = ( $label ? $label : __( 'Get it free', 'edd-external-products' ) );

			echo '<p><strong>' . __( 'External URL:', 'edd-external-products' ) . '</strong></p>';
			echo '<label for="edd_external_product_url">';
			echo '<input type="text" name="_edd_external_product_url" id="edd_external_product_url" value="' . esc_attr( $url ) . '" style="width: 50%" placeholder="http://" /> ';
			echo '</label>';

			echo '<p><strong>' . __( 'Button Label:', 'edd-external-products' ) . '</strong></p>';
			echo '<label for="edd_external_product_button">';
			echo '<input type="text" name="_edd_external_product_button" id="edd_external_product_button" value="' . esc_attr( $label ) . '" /> ';
			echo '</label>';
		}


		/**
		 * Add our fields to the saved fields
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       array $fields The current fields EDD is saving
		 * @return      array The updated fields to save
		 */
		public function save_fields( $fields ) {
			$extra_fields = array(
				'_edd_external_product_url',
				'_edd_external_product_button'
			);

			return array_merge( $fields, $extra_fields );
		}


		/**
		 * Disable the add_to_cart action
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       int $download_id The ID of this download
		 * @return      void
		 */
		public function pre_add_to_cart( $download_id = 0 ) {
			$url = get_post_meta( $download_id, '_edd_external_product_url', true );

			if( $url ) {
				wp_die( sprintf( __( 'This %1$s can only be purchased from %2$s!', 'edd-external-products' ), strtolower( edd_get_label_singular() ), esc_url( $url ) ), '', array( 'back_link' => true ) );
			}
		}


		/**
		 * Override product links
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       string $purchase_form The current form markup
		 * @param       array $args The purchase form args
		 * @return      string $purchase_form The updated form markup
		 */
		public function product_links( $purchase_form, $args ) {
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
	}
}


/**
 * The main function responsible for returning the one true EDD_External_Products
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      EDD_External_Products The one true EDD_External_Products
 */
function edd_external_products() {
	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		if( ! class_exists( 'EDD_Extension_Activation' ) ) {
			require_once 'includes/libraries/class.extension-activation.php';
		}

		$activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();

		return EDD_External_Products::instance();
	} else {
		return EDD_External_Products::instance();
	}
}
add_action( 'plugins_loaded', 'edd_external_products' );
