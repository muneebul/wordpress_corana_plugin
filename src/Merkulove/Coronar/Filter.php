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
 * SINGLETON: Class used to filter records for shortcode uses.
 *
 * @since 1.0.4
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Filter {

	/**
	 * The one true Filter.
	 *
	 * @var Filter
	 * @since 1.0.4
	 **/
	private static $instance;

	/**
	 * Sets up a new Filter instance.
	 *
	 * @since 1.0.4
	 * @access public
	 **/
	private function __construct() {

	}

	/**
	 * Filter Summary Object if we have some filters.
	 *
	 * @param string $filter    - Filter name from shortcode params.
	 * @param object $summary   - Object to filter.
	 *
	 * @since  1.0.4
	 * @access public
	 *
	 * @return object {
	 *     @type array [
	 *         @type object {
	 *             @type string Country
	 *             @type string Slug
	 *             @type int    NewConfirmed
	 *             @type int    TotalConfirmed
	 *             @type int    NewDeaths
	 *             @type int    TotalDeaths
	 *             @type int    NewRecovered
	 *             @type int    TotalRecovered
	 *         }
	 *     ]
	 * }
	 **/
	public function apply_filters( $filter, $summary ) {

		/** Number records to show and filter name. */
		list( $top, $filter_name ) = $this->split_filter_name( $filter );

		/** Return filtered object. Top N countries by Field (ex.: NewConfirmed ) cases. */
		return $this->top_records_by_filter( $top, $summary, $filter_name );

	}

	public function apply_filters_province( $filter, $cases ) {

		/** Number records to show and filter name. */
		list( $top, $filter_name ) = $this->split_filter_name( $filter );

		/** Return filtered object. Top N countries by Field (ex.: NewConfirmed ) cases. */
		return $this->top_provinces_by_filter( $top, $cases, $filter_name );

	}

	/**
	 * Return filtered object. Top N countries by Field (ex.: NewConfirmed ) cases.
	 *
	 * @param int       $top        - Number of records to show.
	 * @param object    $summary    - Object to filter.
	 * @param string    $filter_by  - Filed name to filter by.
	 *
	 * @since  1.0.4
	 * @access private
	 *
	 * @return object {
	 *     @type array [
	 *         @type object {
	 *             @type string Country
	 *             @type string Slug
	 *             @type int    NewConfirmed
	 *             @type int    TotalConfirmed
	 *             @type int    NewDeaths
	 *             @type int    TotalDeaths
	 *             @type int    NewRecovered
	 *             @type int    TotalRecovered
	 *         }
	 *     ]
	 * }
	 **/
	private function top_records_by_filter( $top, $summary, $filter_by ) {

		/** Build auxiliary array. */
		$f_records = [];
		foreach ( $summary->Countries as $country ) {

			/** If we filter by non existing field, return unfiltered. */
			if ( ! property_exists( $country, $filter_by ) ) { return $summary; }

			$f_records[$country->Country] = $country->{$filter_by};

		}

		/** Sort from big value to small. */
		arsort( $f_records );

		/** Get top $top elements . */
		$f_records = array_slice( $f_records, 0, $top );

		/** Convert object to array. */
		$summary = json_decode( json_encode( $summary ), true );

		/** Remove all countries not in $f_records. */
		foreach ( $summary['Countries'] as $key => $country ) {

			$founded = false;
			foreach ( $f_records as $f_key => $f_country ) {

				/** Country found, go to next record. */
				if ( $f_key === $country['Country'] ) {
					$founded = true;
					break;
				}

			}

			/** Remove country data if it's filtered. */
			if ( ! $founded ) {

				unset( $summary['Countries'][$key] );

			}

		}

		/** Convert array to object. */
		$summary = json_decode( json_encode( $summary ), false );

		return $summary;

	}

	private function top_provinces_by_filter( $top, $cases, $filter_by ) {

		/** Build auxiliary array. */
		$f_provinces = [];

		foreach ( $cases as $province ) {

			/** If we filter by non existing field, return unfiltered. */
			if ( ! property_exists( $province, $filter_by ) ) { return $cases; }

			$f_provinces[$province->Province] = $province->{$filter_by};

		}

		/** Sort from big value to small. */
		arsort( $f_provinces );

		/** Get top $top elements . */
		$f_provinces = array_slice( $f_provinces, 0, $top );

		/** Convert object to array. */
		$cases = json_decode( json_encode( $cases ), true );

		/** Remove all Provinces not in $f_provinces. */
		foreach ( $cases as $key => $province ) {

			$founded = false;
			foreach ( $f_provinces as $f_key => $f_country ) {

				/** Country found, go to next record. */
				if ( $f_key === $province['Province'] ) {
					$founded = true;
					break;
				}

			}

			/** Remove country data if it's filtered. */
			if ( ! $founded ) {

				unset( $cases[$key] );

			}

		}

		/** Convert array to object. */
		$cases = json_decode( json_encode( $cases ), false );

		return $cases;

	}

	/**
	 * Split filter from shortcode params to number and filter name.
	 *
	 * @param string $filter - Filter name from shortcode params.
	 *
	 * @since  1.0.4
	 * @access public
	 *
	 * @return array {
	 *      @type int       $top            - number records to filter.
	 *      @type string    $filter_name    - Name of filter.
	 * }
	 **/
	public function split_filter_name( $filter ) {

		/** Remove Spaces. */
		$filter = trim( $filter );

		/** Remove 'Top' from start. */
		$filter = substr( $filter, 3 );

		/** Get Top number from string. */
		$top = intval( preg_replace( '/[^0-9]/', '', $filter ) );

		/** Get filter name from string. */
		$filter_name = str_replace( $top, '', $filter );

		/** Sample [15, 'TotalDeaths'] */
		return [$top, $filter_name];

	}

	/**
	 * Main Filter Instance.
	 *
	 * Insures that only one instance of Filter exists in memory at any one time.
	 *
	 * @static
	 * @return Filter
	 * @since 1.0.4
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Filter ) ) {

			self::$instance = new Filter;

		}

		return self::$instance;

	}

} // End Class Filter.
