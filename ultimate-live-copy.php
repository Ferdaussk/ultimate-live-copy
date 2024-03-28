<?php
/**
 * Plugin Name: Ultimate Live Copy
 * Description: Offers a collection of advanced and unique Elementor widgets and extensions to create stunning websites.
 * Plugin URI:  https://bestwpdeveloper.com/ultimate-live-copy
 * Version:     1.0
 * Author:      Best WP Developer
 * Author URI:  https://bestwpdeveloper.com/
 * Text Domain: ultimate-live-copy
 * Elementor tested up to: 3.13.4
 * Elementor Pro tested up to: 3.13.2
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function ultlc_elementor() {
	return \Elementor\Plugin::instance();
}
define("ultlc_THE_PLUGIN_FILE", __FILE__);
require_once ( plugin_dir_path(ultlc_THE_PLUGIN_FILE) ) . '/includes/requires-check.php';
final class ultlcElementorPlugiN {
	const VERSION = '1.0';
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
	const MINIMUM_PHP_VERSION = '7.0';
	public function __construct() {
		add_action( 'ultlc_init', array( $this, 'ultlc_loaded_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'ultlc_init' ) );
	}

	public function ultlc_loaded_textdomain() {
		load_plugin_textdomain( 'ultimate-live-copy', false, basename(dirname(__FILE__)).'/languages' );
	}

	public function ultlc_init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', 'ultlc_register_required_plugins');
			return;
		}
		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'ultlc_admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'ultlc_admin_notice_minimum_php_version' ) );
			return;
		}
		require_once( 'basic-live-copy.php' );
	}

	public function ultlc_admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'ultimate-live-copy' ),
			'<strong>' . esc_html__( 'Elementor Extention', 'ultimate-live-copy' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'ultimate-live-copy' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('%1$s', 'ultimate-live-copy') . '</p></div>', $message );
	}

	public function ultlc_admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'ultimate-live-copy' ),
			'<strong>' . esc_html__( 'Elementor Extention', 'ultimate-live-copy' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'ultimate-live-copy' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('%1$s', 'ultimate-live-copy') . '</p></div>', $message );
	}
}

// Instantiate ultimate-live-copy.
new ultlcElementorPlugiN();
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );