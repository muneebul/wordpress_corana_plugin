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
 * SINGLETON: Class used to implement work with covid19api.com.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class COVID {

	/**
	 * The one true COVID.
	 *
	 * @var COVID
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new COVID instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() { }

	/**
	 * Get Summary of new and total cases per country.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function get_summary() {

		/** Read Summary data from local JSON. */
		$summary = $this->read_summary();

		/** Return false on error. */
		if ( ! $summary ) { return false; }

		/** Fix "Korea, South" to Korea (South). */
		$summary = $this->fix_korea_south( $summary );

		$summary = json_decode( $summary );

		/** Double check. */
		if ( ! is_object( $summary ) ) {
			$summary = json_decode( $summary );
		}

		return $summary;

	}

	/**
	 * Return Provinces of USA.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array|false
	 **/
	public function get_usa_provinces() {

		$countries = $this->read_countries();

		/** Search USA slug. */
		foreach ( $countries as $country ) {

			if ( 'United States of America' === $country->Country ) {

				$usa_provinces = $country->Provinces;

				/** Remove "US" */
				if ( ( $key = array_search( 'United States of America', $usa_provinces ) ) !== false ) {
					unset( $usa_provinces[$key] );
				}

				/** Remove "Recovered" */
				if ( ( $key = array_search( 'Recovered', $usa_provinces ) ) !== false ) {
					unset( $usa_provinces[$key] );
				}

				return $usa_provinces;
				break;
			}

		}

		return false;

	}

	/**
	 * Return data for all provinces of country.
	 *
	 * @param $country_slug
	 * @param $status
	 *
	 * @since  1.0.4
	 * @access public
	 *
	 * @return object
	 **/
	public function get_live_country_status( $country_slug, $status ) {

		/** Can we read data from the cache or do we need to update the data? */
		$cache_option_name = 'mdp_coronar_' . $country_slug . '_' . $status . '_cache';
		$update_cache = get_transient( $cache_option_name );

		/** Download data from remote host. */
		if ( false === $update_cache ) {

			/** Download and cache data from https://api.covid19api.com/live/country/{country}/status/{status} */
			$this->download_live_cases( $country_slug, $status );

			/** Refresh cache time. */
			$this->refresh_cache_time( $cache_option_name );

		}

		/** Path to cases local JSON. */
		$cases_file = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' . DIRECTORY_SEPARATOR . $status . '_' . $country_slug . '_live.json';

		/** Download if file not found. */
		if ( ! file_exists( $cases_file ) ) {

			/** Download and cache data from https://api.covid19api.com/live/country/{country}/status/{status} */
			$this->download_live_cases( $country_slug, $status );

			/** Refresh cache time. */
			$this->refresh_cache_time( $cache_option_name );

		}

		/** Read cases data. */
		$cases = file_get_contents( $cases_file );

		$cases = json_decode( $cases );

		return $cases;

	}

	/**
	 * Get Confirmed cases By Country From First Recorded Case.
	 *
	 * @param string $country_slug - Country slug.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array|false
	 **/
	public function get_confirmed( $country_slug ) {

		$confirmed = $this->read_cases( $country_slug, 'confirmed' );

		/** Return false on error. */
		if ( ! $confirmed ) { return false; }

		return json_decode( $confirmed );

	}

	/**
	 * Get Deaths cases By Country From First Recorded Case.
	 *
	 * @param string $country_slug - Country slug.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array|false
	 **/
	public function get_deaths( $country_slug ) {

		$deaths = $this->read_cases( $country_slug, 'deaths' );

		/** Return false on error. */
		if ( ! $deaths ) { return false; }

		return json_decode( $deaths );

	}

	/**
	 * Get Recovered cases By Country From First Recorded Case.
	 *
	 * @param string $country_slug - Country slug.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array|false
	 **/
	public function get_recovered( $country_slug ) {

		$recovered = $this->read_cases( $country_slug, 'recovered' );

		/** Return false on error. */
		if ( ! $recovered ) { return false; }

		return json_decode( $recovered );

	}

	/**
	 * Read Cases By Country From First Recorded Case data from local JSON.
	 *
	 * @param string $country_slug
	 * @param string $case
	 *
	 * @since  1.0.0
	 * @access public
	 * @return mixed
	 **/
	private function read_cases( $country_slug, $case ) {

		/** Can we read data from the cache or do we need to update the data? */
		$cache_option_name = 'mdp_coronar_' . $case . '_' . $country_slug . '_cache';
		$update_cache = get_transient( $cache_option_name );

		/** Download data from remote host. */
		if ( false === $update_cache ) {

			/** Download and cache data from https://api.covid19api.com/total/dayone/country/{country}/status/{status} */
			$this->download_cases( $country_slug, $case );

			/** Refresh cache time. */
			$this->refresh_cache_time( $cache_option_name );

		}

		/** Path to Countries local JSON. */
		$countries_file = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' . DIRECTORY_SEPARATOR . $case . '_' . $country_slug . '.json';

		/** Download if file not found. */
		if ( ! file_exists( $countries_file ) ) {

			/** Download and cache data from https://api.covid19api.com/total/dayone/country/{country}/status/{status} */
			$this->download_cases( $country_slug, $case );

			/** Refresh cache time. */
			$this->refresh_cache_time( $cache_option_name );

		}

		/** Read cases data. */
		return file_get_contents( $countries_file );

	}

	/**
	 * Get country slug.
	 *
	 * @param $country_name
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 **/
	public function get_country_slug( $country_name ) {

		$countries = $this->read_countries();

		/** Search country slug. */
		foreach ( $countries as $country ) {

			if ( $country->Country === $country_name ) {

				return $country->Slug;

				break;

			}

		}

		/** Nothing found. */
		return '';

	}

	/**
	 * Read Countries data from local JSON.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function read_countries() {

		/** Can we read data from the cache or do we need to update the data? */
		$cache_option_name = 'mdp_coronar_countries_cache';
		$update_cache = get_transient( $cache_option_name );

		/** Download data from remote host. */
		if ( false === $update_cache ) {

			/** Download and cache Countries data from https://api.covid19api.com/countries */
			$this->download_countries();

			/** Refresh cache time. */
			$this->refresh_cache_time( $cache_option_name );

		}

		/** Path to Countries local JSON. */
		$countries_file = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' . DIRECTORY_SEPARATOR . 'countries.json';

		/** Download if file not found. */
		if ( ! file_exists( $countries_file ) ) {

			/** Download and cache Countries data from https://api.covid19api.com/countries */
			$this->download_countries();

			/** Refresh cache time. */
			$this->refresh_cache_time( $cache_option_name );

		}

		/** Read the countries data. */
		$countries = file_get_contents( $countries_file );
		$countries = json_decode( $countries );

		/** Clear data. */
		foreach ( $countries as $key => &$country ) {

			$country->Country = trim( $country->Country );

			if ( empty( $country->Country ) ) {
				unset( $countries[$key] );
			}

			if ( 'Others' === $country->Country ) {
				unset( $countries[$key] );
			}

		}

		return $countries;

	}

	/**
	 * Refresh cache time.
	 *
	 * @param string $option_name
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	private function refresh_cache_time( $option_name ) {

		/** Refresh cache time. */
		set_transient( $option_name, '1', ( Settings::get_instance()->options['cache_time'] * 60 ) );

	}

	/**
	 * Fix "Korea, South" to Korea (South).
	 *
	 * @param string $summary
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 **/
	private function fix_korea_south( $summary ) {

		return str_replace( 'Korea, South', 'Korea (South)', $summary );

	}

	/**
	 * Read Summary data from local JSON.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function read_summary() {

		/** Can we read data from the cache or do we need to update the data? */
		$cache_option_name = 'mdp_coronar_summary_cache';
		$update_cache = get_transient( $cache_option_name );

		/** Download data from remote host. */
		if ( false === $update_cache ) {

			/** Download and cache Summary data from https://api.covid19api.com/summary. */
			$this->download_summary();

			/** Refresh cache time. */
			$this->refresh_cache_time( $cache_option_name );

		}

		/** Path to Summary local JSON. */
		$summary_file = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' . DIRECTORY_SEPARATOR . 'summary.json';

		/** Return Error if file not found. */
		if ( ! file_exists( $summary_file ) ) {

			/** Download and cache Summary data from https://api.covid19api.com/summary. */
			$this->download_summary();

			/** Refresh cache time. */
			$this->refresh_cache_time( $cache_option_name );

		}

		/** Read the summary data. */
		return file_get_contents( $summary_file );

	}

	/**
	 * Download and cache data from https://api.covid19api.com/live/country/{country}/status/{status}
	 *
	 * @param $country_slug
	 * @param $status
	 *
	 * @since  1.0.0
	 * @access public
	 * @return bool
	 **/
	private function download_live_cases( $country_slug, $status ) {

		/** Returns all cases by case type for a country from the first recorded case with the latest record being the live count. */
		$cases = Helper::get_instance()->get_remote( 'https://api.covid19api.com/live/country/' . $country_slug . '/status/' . $status );

		/** Error. */
		if ( ! $cases ) { return false; }

		/** Path to Countries local JSON. */
		$cases_file = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' . DIRECTORY_SEPARATOR . $status . '_' . $country_slug . '_live.json';

		/** Instantiate the WordPress filesystem. */
		Helper::init_filesystem();

		/** Write to file. */
		return Helper::get_instance()->write_file( $cases_file, $cases );

	}

	/**
	 * Download and cache data from https://api.covid19api.com/total/dayone/country/{country}/status/{status}
	 *
	 * @param $country_slug
	 * @param $case
	 *
	 * @since  1.0.0
	 * @access public
	 * @return bool
	 **/
	private function download_cases( $country_slug, $case ) {

		/** Returns all countries and associated provinces. The country_slug variable is used for country specific data. */
		$cases = Helper::get_instance()->get_remote( 'https://api.covid19api.com/total/dayone/country/' . $country_slug . '/status/' . $case );

		/** Error. */
		if ( ! $cases ) { return false; }

		/** Path to Countries local JSON. */
		$cases_file = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' . DIRECTORY_SEPARATOR . $case . '_' . $country_slug . '.json';

		/** Instantiate the WordPress filesystem. */
		Helper::init_filesystem();

		/** Write to file. */
		return Helper::get_instance()->write_file( $cases_file, $cases );

	}

	/**
	 * Download and cache Countries data from https://api.covid19api.com/countries.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function download_countries() {

		/** Returns all countries and associated provinces. The country_slug variable is used for country specific data. */
		$countries = Helper::get_instance()->get_remote( 'https://api.covid19api.com/countries' );

		/** Error. */
		if ( ! $countries ) { return false; }

		/** Path to Countries local JSON. */
		$countries_file = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' . DIRECTORY_SEPARATOR . 'countries.json';

		/** Instantiate the WordPress filesystem. */
		Helper::init_filesystem();

		/** Write to file. */
		return Helper::get_instance()->write_file( $countries_file, $countries );

	}

	/**
	 * Download and cache Summary data from https://api.covid19api.com/summary.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function download_summary() {

		/** A summary of new and total cases per country. */
		$summary = Helper::get_instance()->get_remote( 'https://api.covid19api.com/summary' );

		/** Error. */
		if ( ! $summary ) { return false; }

		/** Path to Summary local JSON. */
		$summary_file = trailingslashit( wp_upload_dir()['basedir'] ) . 'coronar' . DIRECTORY_SEPARATOR . 'summary.json';

		/** Instantiate the WordPress filesystem. */
		Helper::init_filesystem();

		/** Write to file. */
		return Helper::get_instance()->write_file( $summary_file, $summary );

	}

	/**
	 * Main COVID Instance.
	 *
	 * Insures that only one instance of COVID exists in memory at any one time.
	 *
	 * @static
	 * @return COVID
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof COVID ) ) {
			self::$instance = new COVID;
		}

		return self::$instance;
	}

} // End Class COVID.
