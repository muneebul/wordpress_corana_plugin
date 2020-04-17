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

namespace Merkulove\Coronar;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class contain information about the envato item.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class EnvatoItem {

	/**
	 * The one true EnvatoItem.
	 *
	 * @var EnvatoItem
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Return CodeCanyon Item ID.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 **/
	public function get_url() {

		return 'https://1.envato.market/mdpcoronar';

	}

	/**
	 * Return CodeCanyon Item ID.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 **/
	public function get_id() {

		/** Do we have stored Envato ID? */
		$item_id = get_option( 'mdp_coronar_envato_id' );

		/** If we have stored Envato ID, return it. */
		if ( $item_id ) {
			return $item_id;
		}

		/** Else get id from our server. */
		$item_id = $this->get_remote_plugin_id();

		/** Store local option if this is real item ID. */
		if ( (int)$item_id > 0 ) {
			update_option( 'mdp_coronar_envato_id', $item_id );
		}

		return (string)$item_id;
	}

	/**
	 * Return CodeCanyon Plugin ID from out server.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function get_remote_plugin_id() {
        return;
		/** Get Plugin name. */
		if ( ! function_exists('get_plugin_data') ) {
			/** @noinspection PhpIncludeInspection */
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/coronar/coronar.php' );
		$plugin_name = $plugin_data['Name'];

		/** Build URL. */
		$url = 'https://upd.merkulov.design/wp-content/plugins/mdp-purchase-validator/src/Merkulove/PurchaseValidator/GetMyId.php';
		$url .= '?plugin_name=' . urlencode( $plugin_name );

		/** Suppress warning, if file not exist. */
		$context = stream_context_create( ['http' => ['ignore_errors' => true] ] );
		$plugin_id = file_get_contents( $url, false, $context );

		/** We don't have plugin ID. */
		if ( false === $plugin_id ) { return '0'; }

		$plugin_id = json_decode( $plugin_id );

		/** Wrong JSON. */
		if ( null === $plugin_id ) { return '0'; }

		return $plugin_id;
	}

	/**
	 * Main EnvatoItem Instance.
	 *
	 * Insures that only one instance of EnvatoItem exists in memory at any one time.
	 *
	 * @static
	 * @return EnvatoItem
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EnvatoItem ) ) {
			self::$instance = new EnvatoItem;
		}

		return self::$instance;
	}

} // End Class EnvatoItem.
