<?php
/**
 * Plugin Name:     Easy Digital Downloads - External Products
 * Plugin URI:      https://wordpress.org/plugins/easy-digital-downloads-external-products
 * Description:     Adds a robust third-party product system to Easy Digital Downloads
 * Version:         1.1.0
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
				self::$instance->includes();
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
			define( 'EDD_EXTERNAL_PRODUCTS_VER', '1.1.0' );

			// Plugin path
			define( 'EDD_EXTERNAL_PRODUCTS_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'EDD_EXTERNAL_PRODUCTS_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Include required files
		 *
		 * @access      private
		 * @since       1.1.0
		 * @return      void
		 */
		private function includes() {
			require_once EDD_EXTERNAL_PRODUCTS_DIR . 'includes/template-overrides.php';

			if( is_admin() ) {
				require_once EDD_EXTERNAL_PRODUCTS_DIR . 'includes/admin/downloads/meta-box.php';
			}
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
		if( ! class_exists( 'S214_EDD_Activation' ) ) {
			require_once 'includes/libraries/class.s214-edd-activation.php';
		}

		$activation = new S214_EDD_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();

		return EDD_External_Products::instance();
	} else {
		return EDD_External_Products::instance();
	}
}
add_action( 'plugins_loaded', 'edd_external_products' );
