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

use WP_Filesystem_Direct;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement work with WordPress filesystem.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Helper {

	/**
	 * The one true Helper.
	 *
	 * @var Helper
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Helper instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

	}

	/**
	 * Create Coronar cache folder for json data files.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	public function create_cache_folder() {

		/** Create /wp-content/uploads/coronar/ folder. */
		wp_mkdir_p( trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' );

	}

	/**
	 * Remove all coronar files.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 **/
	public function remove_coronar_files() {

		/** Remove /wp-content/uploads/coronar/ folder. */
		$dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar';
		$this->remove_directory( $dir );

	}

	/**
	 * Remove directory with all contents.
	 *
	 * @param $dir - Directory path to remove.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function remove_directory( $dir ) {

		require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
		require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
		$fileSystemDirect = new WP_Filesystem_Direct( false );
		$fileSystemDirect->rmdir( $dir, true );

	}

	/**
	 * Get remote contents.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param  string $url  The URL we're getting our data from.
	 * @return false|string The contents of the remote URL, or false if we can't get it.
	 **/
	public function get_remote( $url ) {

		$args = [
			'timeout'    => 30,
			'user-agent' => 'coronar-user-agent',
		];

		$response = wp_remote_get( $url, $args );

		if ( is_array( $response ) ) {
			return $response['body'];
		}

		/** Error while downloading remote file. */
		return false;

	}

	/**
	 * Write content to the destination file.
	 *
	 * @param $destination - The destination path.
	 * @param $content - The content to write in file.
	 *
	 * @return bool Returns true if the process was successful, false otherwise.
	 * @access public
	 * @since 1.0.0
	 **/
	public function write_file( $destination, $content ) {

		/** Content for file is empty. */
		if ( ! $content ) { return false; }

		/** Build the path. */
		$path = wp_normalize_path( $destination );

		/** Define constants if undefined. */
		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
		}

		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
		}

		/** Try to put the contents in the file. */
		global $wp_filesystem;

		/** @noinspection PhpUndefinedMethodInspection */
		$wp_filesystem->mkdir( dirname( $path ), FS_CHMOD_DIR ); // Create folder, just in case.

		/** @noinspection PhpUndefinedMethodInspection */
		$result = $wp_filesystem->put_contents( $path, $content, FS_CHMOD_FILE );

		/** We can't write file.  */
		if ( ! $result ) { return false; }

		return $result;

	}

	/**
	 * Initializes WordPress filesystem.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @return object WP_Filesystem
	 **/
	public static function init_filesystem() {

		$credentials = [];

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		$method = defined( 'FS_METHOD' ) ? FS_METHOD : false;

		/** FTP */
		if ( 'ftpext' === $method ) {

			/** If defined, set credentials, else set to NULL. */
			$credentials['hostname'] = defined( 'FTP_HOST' ) ? preg_replace( '|\w+://|', '', FTP_HOST ) : null;
			$credentials['username'] = defined( 'FTP_USER' ) ? FTP_USER : null;
			$credentials['password'] = defined( 'FTP_PASS' ) ? FTP_PASS : null;

			/** FTP port. */
			if ( strpos( $credentials['hostname'], ':' ) && null !== $credentials['hostname'] ) {
				list( $credentials['hostname'], $credentials['port'] ) = explode( ':', $credentials['hostname'], 2 );
				if ( ! is_numeric( $credentials['port'] ) ) {
					unset( $credentials['port'] );
				}
			} else {
				unset( $credentials['port'] );
			}

			/** Connection type. */
			if ( ( defined( 'FTP_SSL' ) && FTP_SSL ) && 'ftpext' === $method ) {
				$credentials['connection_type'] = 'ftps';
			} elseif ( ! array_filter( $credentials ) ) {
				$credentials['connection_type'] = null;
			} else {
				$credentials['connection_type'] = 'ftp';
			}
		}

		/** The WordPress filesystem. */
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {

			/** @noinspection PhpIncludeInspection */
			require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem( $credentials );

		}

		return $wp_filesystem;

	}

	/**
	 * Send Action to our remote host.
	 *
	 * @param $action - Action to execute on remote host.
	 * @param $plugin - Plugin slug.
	 * @param $version - Plugin version.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 **/
	public function send_action( $action, $plugin, $version ) {

		$domain = parse_url( site_url(), PHP_URL_HOST );
		$admin = base64_encode( get_option( 'admin_email' ) );
		$pid = get_option( 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );

		$ch = curl_init();

		$url = 'https://upd.merkulov.design/wp-content/plugins/mdp-purchase-validator/src/Merkulove/PurchaseValidator/Validate.php?';
		$url .= 'action=' . $action . '&'; // Action.
		$url .= 'plugin=' . $plugin . '&'; // Plugin Name.
		$url .= 'domain=' . $domain . '&'; // Domain Name.
		$url .= 'version=' . $version . '&'; // Plugin version.
		$url .= 'pid=' . $pid . '&'; // Purchase Code.
		$url .= 'admin_e=' . $admin;

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		curl_exec( $ch );

	}

	/**
	 * Parser function to get formatted headers with response code.
	 *
	 * @param $headers - HTTP response headers.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 **/
	public function parse_headers( $headers ) {
		$head = [];
		foreach( $headers as $k => $v ) {
			$t = explode( ':', $v, 2 );
			if ( isset( $t[1] ) ) {
				$head[ trim($t[0]) ] = trim( $t[1] );
			} else {
				$head[] = $v;
				if ( preg_match( "#HTTP/[0-9.]+\s+([0-9]+)#",$v, $out ) ) {
					$head['response_code'] = intval($out[1]);
				}
			}
		}

		return $head;
	}

	/**
	 * Main Helper Instance.
	 *
	 * Insures that only one instance of Helper exists in memory at any one time.
	 *
	 * @static
	 * @return Helper
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Helper ) ) {
			self::$instance = new Helper;
		}

		return self::$instance;
	}

} // End Class Helper.
