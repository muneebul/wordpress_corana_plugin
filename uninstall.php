<?php
/**
 * COVID19 Coronavirus Visual Dashboard
 * Exclusively on Envato Market: https://1.envato.market/coronar
 *
 * @encoding        UTF-8
 * @version         1.0.7
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Envato License https://1.envato.market/KYbje
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove;

/** Include plugin autoloader for additional classes. */
require __DIR__ . '/src/autoload.php';

use Merkulove\Coronar\Helper;

/** Exit if uninstall.php is not called by WordPress. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement Uninstall of Coronar plugin.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Uninstall {

	/**
	 * The one true Uninstall.
	 *
	 * @var Uninstall
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Uninstall instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Get Uninstall mode. */
		$uninstall_mode = $this->get_uninstall_mode();

		/** Send uninstall Action to our host. */
		Helper::get_instance()->send_action( 'uninstall', 'coronar', '1.0.7' );

		/** Remove Plugin and Settings. */
		if ( 'plugin+settings' === $uninstall_mode ) {

			/** Remove Plugin Settings. */
			$this->remove_settings();

			/** Remove Plugin with Settings and Audio files. */
		} elseif ( 'plugin+settings+data' === $uninstall_mode ) {

			/** Remove Plugin Settings. */
			$this->remove_settings();

			/** Remove Plugin Audio files. */
			Helper::get_instance()->remove_coronar_files();

		}

	}

	/**
	 * Return uninstall mode.
	 * plugin - Will remove the plugin only. Settings and Audio files will be saved. Used when updating the plugin.
	 * plugin+settings - Will remove the plugin and settings. Audio files will be saved. As a result, all settings will be set to default values. Like after the first installation.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function get_uninstall_mode() {

		$uninstall_settings = get_option( 'mdp_coronar_uninstall_settings' );

		if( isset( $uninstall_settings['mdp_coronar_uninstall_settings'] ) AND $uninstall_settings['mdp_coronar_uninstall_settings'] ) { // Default value.
			$uninstall_settings = [
				'delete_plugin' => 'plugin'
			];
		}

		return $uninstall_settings['delete_plugin'];

	}

	/**
	 * Delete Plugin Options.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function remove_settings() {

		$settings = [
			'mdp_coronar_envato_id',
			'mdp_coronar_settings',
			'mdp_coronar_style_settings',
			'mdp_coronar_map_settings',
			'mdp_coronar_css_settings',
			'mdp_coronar_assignments_settings',
			'mdp_coronar_uninstall_settings',
		];

		foreach ( $settings as $key ) {

			if ( is_multisite() ) { // For Multisite.
				if ( get_site_option( $key ) ) {
					delete_site_option( $key );
				}
			} else {
				if ( get_option( $key ) ) {
					delete_option( $key );
				}
			}
		}
	}

	/**
	 * Main Uninstall Instance.
	 *
	 * Insures that only one instance of Uninstall exists in memory at any one time.
	 *
	 * @static
	 * @return Uninstall
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Uninstall ) ) {
			self::$instance = new Uninstall;
		}

		return self::$instance;
	}

}

/** Runs on Uninstall of Coronar plugin. */
Uninstall::get_instance();
