<?php
/**
 * Plugin Name: Coronar
 * Plugin URI: https://1.envato.market/mdpcoronar
 * Description: COVID19 Coronavirus Visual Dashboard
 * Author: Merkulove
 * Version: 1.0.7
 * Author URI: https://1.envato.market/cc-merkulove
 * Requires PHP: 5.6
 * Requires at least: 3.0
 * Tested up to: 5.4
 **/

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

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/** Include plugin autoloader for additional classes. */
require __DIR__ . '/src/autoload.php';

use Merkulove\Coronar\PluginUpdater;
use Merkulove\Coronar\Helper;
use Merkulove\Coronar\PluginHelper;
use Merkulove\Coronar\Settings;
use Merkulove\Coronar\Shortcodes;
use Merkulove\Coronar\EnvatoItem;

/**
 * SINGLETON: Core class used to implement a Coronar plugin.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since 1.0.0
 */
final class Coronar {

	/**
	 * Plugin version.
	 *
	 * @string version
	 * @since 1.0.0
	 **/
	public static $version = '';

	/**
	 * Use minified libraries if SCRIPT_DEBUG is turned off.
	 *
	 * @since 1.0.0
	 **/
	public static $suffix = '';

	/**
	 * URL (with trailing slash) to plugin folder.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $url = '';

	/**
	 * PATH to plugin folder.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $path = '';

	/**
	 * Plugin base name.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $basename = '';

	/**
	 * Plugin admin menu base.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $menu_base;

	/**
	 * The one true Coronar.
	 *
	 * @var Coronar
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Initialize main variables. */
		$this->initialization();

		/** Define admin hooks. */
		$this->admin_hooks();

		/** Define public hooks. */
		$this->public_hooks();

		/** Define hooks that runs on both the front-end as well as the dashboard. */
		$this->both_hooks();

	}

	/**
	 * Return plugin version.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 **/
	public function get_version() {
		return self::$version;
	}

	/**
	 * Define hooks that runs on both the front-end as well as the dashboard.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	private function both_hooks() {

		/** Load translation. */
		add_action( 'plugins_loaded', [$this, 'load_textdomain'] );

		/** Adds all the necessary shortcodes. */
		Shortcodes::get_instance();

	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	private function admin_hooks() {

		/** Work only in backend. */
		if ( ! is_admin() ) { return; }

		/** Add plugin settings page. */
		Settings::get_instance()->add_settings_page();

		/** Create Coronar cache folder '/wp-content/uploads/coronar/' for json data files. */
		Helper::get_instance()->create_cache_folder();

		/** Load JS and CSS for Backend Area. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ], 100 ); // CSS.
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ], 100 ); // JS.

		/** Remove "Thank you for creating with WordPress" and WP version only from plugin settings page. */
		add_action( 'admin_enqueue_scripts', [$this, 'remove_wp_copyrights'] );

		/** Remove all "third-party" notices from plugin settings page. */
		add_action( 'in_admin_header', [$this, 'remove_all_notices'], 1000 );

		/** Clear Cache. */
		if ( defined('DOING_AJAX') ) {
			add_action( 'wp_ajax_clear_cache', [$this, 'clear_cache' ] );
		}

	}

	/**
	 * Clear coronar cache.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 **/
	public function clear_cache() {

		/** Check nonce for security. */
		check_ajax_referer( 'coronar_clear_cache', 'nonce' );

		/** Do we need to do a full reset? */
		if ( empty( $_POST['doClear'] ) ) {
			wp_die( 'Wrong parameter value.' );
		}

		/** Remove /wp-content/uploads/coronar/ folder. */
		$dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar';
		Helper::get_instance()->remove_directory( $dir );

		/** Return JSON result. */
		echo json_encode( true );

		/** Exit. */
		wp_die();

	}

	/**
	 * Remove all other notices.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function remove_all_notices() {

		/** Work only on plugin settings page. */
		$screen = get_current_screen();
		if ( $screen->base !== self::$menu_base ) { return; }

		/** Remove other notices. */
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

	}

	/**
	 * Remove "Thank you for creating with WordPress" and WP version only from plugin settings page.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	public function remove_wp_copyrights() {

		/** Remove "Thank you for creating with WordPress" and WP version from plugin settings page. */
		$screen = get_current_screen(); // Get current screen.

		/** Coronar Settings Page. */
		if ( $screen->base === self::$menu_base ) {
			add_filter( 'admin_footer_text', '__return_empty_string', 11 );
			add_filter( 'update_footer', '__return_empty_string', 11 );
		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	private function public_hooks() {

		/** Work only on frontend. */
		if ( is_admin() ) { return; }

		/** Load CSS for Frontend Area. */
		add_action( 'wp_enqueue_scripts', [$this, 'styles'] ); // CSS.

		/** Load JavaScript for Frontend Area. */
		add_action( 'wp_enqueue_scripts', [$this, 'scripts'] ); // JS.

	}

	/**
	 * Initialize main variables.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function initialization() {

		/** Plugin version. */
		if ( ! function_exists('get_plugin_data') ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$plugin_data = get_plugin_data( __FILE__ );
		self::$version = $plugin_data['Version'];

		/** Gets the plugin URL (with trailing slash). */
		self::$url = plugin_dir_url( __FILE__ );

		/** Gets the plugin PATH. */
		self::$path = plugin_dir_path( __FILE__ );

		/** Use minified libraries if SCRIPT_DEBUG is turned off. */
		self::$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		/** Set plugin basename. */
		self::$basename = plugin_basename( __FILE__ );

		/** Plugin admin menu base. */
		self::$menu_base = 'toplevel_page_mdp_coronar_settings';

		/** Initialize plugin settings. */
		Settings::get_instance();

		/** Initialize PluginHelper. */
		PluginHelper::get_instance();

		/** Plugin update mechanism enable only if plugin have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( (int)$plugin_id > 0 ) {
			PluginUpdater::get_instance();
		}

		/** Create /wp-content/uploads/coronar/ folder for audio files. */
		wp_mkdir_p( trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' );

	}

	/**
	 * Add CSS for the public-facing side of the site.
	 *
	 * @return void
	 * @since 1.0.0
	 **/
	public function styles() {

		/** Frontend CSS for shortcodes. */
		wp_register_style( 'dataTables', Coronar::$url . 'css/jquery.dataTables' . Coronar::$suffix . '.css', [], Coronar::$version );

		if ( 'on' === Settings::get_instance()->options['responsive_table'] ) {

			wp_register_style( 'dataTables-rowReorder', Coronar::$url . 'css/rowReorder.dataTables' . Coronar::$suffix . '.css', [], Coronar::$version );
			wp_register_style( 'dataTables-responsive', Coronar::$url . 'css/responsive.dataTables' . Coronar::$suffix . '.css', [], Coronar::$version );

		}


		wp_register_style( 'chartist', Coronar::$url . 'css/chartist' . Coronar::$suffix . '.css', [], Coronar::$version );
		wp_register_style( 'mdp-coronar', self::$url . 'css/coronar' . self::$suffix . '.css', [], self::$version );

		$inline_css = $this->get_inline_css();

		/** Add custom CSS. */
		wp_add_inline_style( 'mdp-coronar', $inline_css . Settings::get_instance()->options['custom_css'] );

	}

	/**
	 * Return inline CSS for coronar.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 **/
	private function get_inline_css() {

		/** Get Plugin Settings. */
		$options = Settings::get_instance()->options;

		/** Prepare variables. */
		$accent_color = $options['accent_color'];
		$bg_color = $options['bg_color'];
		$shadow = 'none';
		if ( 'on' === $options['shadow'] ) {
			$shadow = '0 4px 1px -6px rgba(0,0,0,0.02), 0 1px 10px 0 rgba(0,0,0,0.11)';
		}
		$margin = $options['margin'];
		$padding = $options['padding'];

		$half_padding = $options['padding']/4;

		$border_radius = $options['border_radius'];
		$font_size = $options['font_size'];
		$flag_size = $options['flag_size'];

		$confirmed_color = $options['confirmed_color'];
		$deaths_color = $options['deaths_color'];
		$recovered_color = $options['recovered_color'];

		// language=CSS
		/** @noinspection CssUnusedSymbol */
		return "
			.mdp-coronar-table-box table {
				border-spacing: 0 {$margin}px;
				font-size: {$font_size};
			}
			
			.mdp-coronar-table-box table.dataTable td {
				padding: {$padding}px 0;
				background-color: {$bg_color};
			}
			
			.mdp-coronar-table-box tbody tr {
				border-radius: {$border_radius}px;
				box-shadow: {$shadow};
				background-color: {$bg_color};
			}
						
			.mdp-coronar-table-box tbody tr td:first-child {
				border-radius: {$border_radius}px 0 0 {$border_radius}px;				
				padding-left: {$padding}px;
			}
			
			.mdp-coronar-table-box tbody tr td:last-child {
				border-radius: 0 {$border_radius}px {$border_radius}px 0;
				padding-right: {$padding}px;
			}
			
			.mdp-coronar-table-box tbody tr.child td{
				border-radius: {$border_radius}px;
			}
			
			.mdp-coronar-table-box table.dataTable td.mdp-coronar-flag {
				padding: {$padding}px;
				min-width: {$flag_size}px;
				max-width: {$flag_size}px;
				width: {$flag_size}px;
			}
						
			.mdp-coronar-table-box table.dataTable th {
				padding: 0 {$padding}px 0 {$padding}px;
				background-color: {$bg_color};
				color: {$accent_color};
			}
			
			.mdp-coronar-table-box tbody td.mdp-coronar-flag img {
				width: {$flag_size}px;
            	height: {$flag_size}px;
            	min-width: {$flag_size}px;
            	min-height: {$flag_size}px;
			}
				
			.mdp-coronar-table-box table.dataTable td ul li span {
				color: {$accent_color};
			}
					
			.mdp-coronar-table-box .dataTables_filter input {
				border-radius: {$border_radius}px;
			}
						
			.mdp-coronar-table-box table.dataTable td.dataTables_empty {
				border-radius: {$border_radius}px;
			}
			
			.mdp-coronar-table-box table.dtr-inline tbody tr {
				background-color: {$bg_color};
			}
						
			.mdp-coronar-table-box table.dtr-inline tbody tr td.mdp-coronar-last-visible {
				border-radius: 0 {$border_radius}px {$border_radius}px 0;
			}			
			.mdp-coronar-table-box table.dtr-inline tbody td li {
				padding: {$half_padding}px 0;
			}
			
			.mdp-coronar-summary-date-box {
				font-size: {$font_size};
			}
			
			.mdp-coronar-summary-tbl .ct-golden-section {
				height: {$flag_size}px;
			}
			
			.mdp-coronar-summary-relative-date-box {
				font-size: {$font_size};
			}
			
			.mdp-coronar-country {
				color: {$accent_color};
			}
			
			.mdp-coronar-confirmed-total,
			.mdp-coronar-confirmed-new {
				color: {$confirmed_color};
			}
			
			.mdp-coronar-deaths-new,
			.mdp-coronar-deaths-total {
				color: {$deaths_color};
			}
			
			.mdp-coronar-recovered-new,
			.mdp-coronar-recovered-total {
				color: {$recovered_color};
			}
			
			.mdp-coronar-bottom {
				font-size: {$font_size};
			}

            .mdp-coronar-country-card {
            	margin: {$margin}px;
                background-color: {$bg_color};
				box-shadow: {$shadow}; 
				border-radius: {$border_radius}px;
				font-size: {$font_size};
            }
            
            .mdp-coronar-country-card .mdp-coronar-amount {
            	font-size: calc( {$flag_size}px * .75);
            	line-height: {$flag_size}px;
            	height: {$flag_size}px;
            }
            
            .mdp-coronar-country-card > div{
            	padding: {$padding}px 0 0 {$padding}px;           
            }
            
            .mdp-coronar-country-card span.mdp-coronar-card-label {
            	margin-top: calc( {$padding}px / 2 );
            	color: {$accent_color};
            }
            
            .mdp-coronar-country-card .mdp-coronar-flag-country span {
            	color: {$accent_color};
            }
            
            .mdp-coronar-country-card .mdp-coronar-stats > div {
            	padding: 0 {$padding}px {$padding}px 0;
            }
            
            .mdp-coronar-country-card .mdp-coronar-flag-country {
            	padding-bottom: {$padding}px;
            }
            
			.mdp-coronar-country-card .mdp-coronar-flag {
				min-width: {$flag_size}px;
				max-width: {$flag_size}px;
			}
				
            .mdp-coronar-country-card .mdp-coronar-flag img {
            	width: {$flag_size}px;
            	height: {$flag_size}px;
            	min-width: {$flag_size}px;
            	min-height: {$flag_size}px;
            }
            
            .mdp-coronar-summary-tbl .mdp-coronar-chart-confirmed .ct-point,
            .mdp-coronar-summary-tbl .mdp-coronar-chart-confirmed .ct-line,
            .mdp-coronar-cards-box .mdp-coronar-chart-confirmed .ct-point,
            .mdp-coronar-cards-box .mdp-coronar-chart-confirmed .ct-line {
            	stroke: {$confirmed_color};
            }
            
            .mdp-coronar-cards-box .mdp-coronar-chart-confirmed .ct-area {
            	fill: {$confirmed_color};
            }
            
            .mdp-coronar-summary-tbl .mdp-coronar-chart-deaths .ct-point,
            .mdp-coronar-summary-tbl .mdp-coronar-chart-deaths .ct-line,
            .mdp-coronar-cards-box .mdp-coronar-chart-deaths .ct-point,
            .mdp-coronar-cards-box .mdp-coronar-chart-deaths .ct-line {
            	stroke: {$deaths_color};
            }

			.mdp-coronar-cards-box .mdp-coronar-chart-deaths .ct-area {
            	fill: {$deaths_color};
            }
            
            .mdp-coronar-summary-tbl .mdp-coronar-chart-recovered .ct-point,
            .mdp-coronar-summary-tbl .mdp-coronar-chart-recovered .ct-line,
            .mdp-coronar-cards-box .mdp-coronar-chart-recovered .ct-point,
            .mdp-coronar-cards-box .mdp-coronar-chart-recovered .ct-line {
            	stroke: {$recovered_color};
            }
            
            .mdp-coronar-cards-box .mdp-coronar-chart-recovered .ct-area {
            	fill: {$recovered_color};
            }
            
            .google-visualization-tooltip {
            	border-radius: {$border_radius}px;
            	padding: {$padding}px;
            	color: {$accent_color};
            	background: {$bg_color};
            }            	
             
        ";

	}

	/**
	 * Add JavaScript for the public-facing side of the site.
	 *
	 * @return void
	 * @since 1.0.0
	 **/
	public function scripts() {

		/** Frontend JS for shortcodes. */
		wp_register_script( 'dataTables', Coronar::$url . 'js/jquery.dataTables' . Coronar::$suffix . '.js', ['jquery'], Coronar::$version, true );

		if ( 'on' === Settings::get_instance()->options['responsive_table'] ) {

			wp_register_script( 'dataTables-rowReorder', Coronar::$url . 'js/dataTables.rowReorder' . Coronar::$suffix . '.js', ['jquery', 'dataTables'], Coronar::$version, true );
			wp_register_script( 'dataTables-responsive', Coronar::$url . 'js/dataTables.responsive' . Coronar::$suffix . '.js', ['jquery', 'dataTables'], Coronar::$version, true );

		}

		wp_register_script( 'chartist', Coronar::$url . 'js/chartist' . Coronar::$suffix . '.js', [], Coronar::$version, true );

		wp_register_script( 'geo-chart', 'https://www.gstatic.com/charts/loader.js', [], Coronar::$version, true );
		wp_register_script( 'mdp-map-chart', Coronar::$url . 'js/map-chart' . Coronar::$suffix . '.js', ['jquery', 'geo-chart'], Coronar::$version, true );

		/** Pass variables to JS. */
		wp_localize_script( 'mdp-map-chart', 'mdpCoronarMap', [
			'mapsApiKey' => Settings::get_instance()->options['api_key']
		] );

		wp_register_script( 'mdp-coronar', self::$url . 'js/coronar' . self::$suffix . '.js', ['jquery', 'dataTables'], self::$version, true );

		/** Pass variables to JS. */
		wp_localize_script( 'mdp-coronar', 'mdpCoronar', [
			'showSearch'        => ( 'on' !== Settings::get_instance()->options['show_search'] ) ? 'false' : 'true',
			'responsiveTable'   => ( 'on' !== Settings::get_instance()->options['responsive_table'] ) ? 'false' : 'true',
		] );

	}

	/**
	 * Loads the Coronar translated strings.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function load_textdomain() {

		load_plugin_textdomain( 'coronar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Add CSS for admin area.
	 *
	 * @return void
	 * @since 1.0.0
	 **/
	public function admin_styles() {

		/** Get current screen to add styles on specific pages. */
		$screen = get_current_screen();

		/** Coronar Settings Page. */
		if ( self::$menu_base === $screen->base ) {
			wp_enqueue_style( 'merkulov-ui', self::$url . 'css/merkulov-ui' . self::$suffix . '.css', [], self::$version );
			wp_enqueue_style( 'mdp-coronar-admin', self::$url . 'css/admin' . self::$suffix . '.css', [], self::$version );

			/** Coronar popup on update. */
		} elseif ( 'plugin-install' === $screen->base ) {

			/** Styles only for our plugin. */
			if ( isset( $_GET['plugin'] ) AND $_GET['plugin'] === 'coronar' ) {
				wp_enqueue_style( 'mdp-coronar-plugin-install', self::$url . 'css/plugin-install' . self::$suffix . '.css', [], self::$version );
			}

		}

	}

	/**
	 * Add class to body in admin area.
	 *
	 * @param string $classes - Space-separated list of CSS classes.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function add_admin_class( $classes ) {

		return $classes . ' mdc-disable ';

	}

	/**
	 * Add JS for admin area.
	 *
	 * @return void
	 * @since 1.0.0
	 **/
	public function admin_scripts() {

		/** Get current screen to add scripts on specific pages. */
		$screen = get_current_screen();

		/** Coronar Settings Page. */
		if ( $screen->base !== self::$menu_base ) { return; }

		wp_enqueue_script( 'merkulov-ui', self::$url . 'js/merkulov-ui' . self::$suffix . '.js', [], self::$version, true );
		wp_enqueue_script( 'mdp-coronar-admin', self::$url . 'js/admin' . self::$suffix . '.js', [ 'jquery' ], self::$version, true );

		wp_localize_script('mdp-coronar-admin', 'mdpCoronar', [
			'ajaxURL' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('coronar_clear_cache'),
		] );

	}

	/**
	 * Run when the plugin is activated.
	 *
	 * @static
	 * @since 1.0.0
	 **/
	public static function on_activation() {

		/** Security checks. */
		if ( ! current_user_can( 'activate_plugins' ) ) { return; }

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );

		/** Send install Action to our host. */
		Helper::get_instance()->send_action( 'install', 'coronar', self::$version );

	}

	/**
	 * Main Coronar Instance.
	 *
	 * Insures that only one instance of Coronar exists in memory at any one time.
	 *
	 * @static
	 * @return Coronar
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Coronar ) ) {
			self::$instance = new Coronar;
		}

		return self::$instance;
	}

} // End Class Coronar.

/** Run when the plugin is activated. */
register_activation_hook( __FILE__, ['Merkulove\Coronar', 'on_activation'] );

/** Run Coronar class. */
Coronar::get_instance();
