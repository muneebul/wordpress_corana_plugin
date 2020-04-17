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
 * SINGLETON: Class used to debug plugin.
 *
 * @since 1.0.7
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Debug {

	/**
	 * The one true Debug.
	 *
	 * @var Debug
	 * @since 1.0.7
	 **/
	private static $instance;

	/**
	 * Sets up a new Debug instance.
	 *
	 * @since 1.0.7
	 * @access private
	 **/
	private function __construct() {

	}

	/**
	 * Show PHP script ( part of code ) resource usage.
	 *
	 * @param array $rus - Associative array containing the data returned from the getrusage() call.
	 *
	 * @since  1.0.7
	 * @access public
	 **/
	public function resource_usage( $rus ) {

		$ru = getrusage();

		esc_html_e( 'This process used ', 'coronar' );
		esc_html_e( $this->resource_usage_time( $ru, $rus, 'utime' ) );
		esc_html_e( 'ms for its computations.', 'coronar' );
		?><br><?php
		esc_html_e( 'It spent ', 'coronar' );
		esc_html_e( $this->resource_usage_time( $ru, $rus, 'stime' ) );
		esc_html_e( 'ms in system calls.', 'coronar' );
		?><br><?php

	}

	/**
	 * Return value of
	 *
	 * @param array $ru  - At the End Associative array containing the data returned from the getrusage() call.
	 * @param array $rus - Start Associative array containing the data returned from the getrusage() call.
	 *
	 * @param       $index
	 *
	 * @since  1.0.7
	 * @access public
	 * @return float|int
	 **/
	private function resource_usage_time( $ru, $rus, $index ) {

		return ( $ru["ru_$index.tv_sec"]*1000 + intval( $ru["ru_$index.tv_usec"]/1000 ) )
		       -  ( $rus["ru_$index.tv_sec"]*1000 + intval( $rus["ru_$index.tv_usec"]/1000 ) );

	}

	/**
	 * Main Debug Instance.
	 *
	 * Insures that only one instance of Debug exists in memory at any one time.
	 *
	 * @static
	 * @return Debug
	 * @since 1.0.7
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Debug ) ) {

			self::$instance = new Debug;

		}

		return self::$instance;

	}

} // End Class Debug.
