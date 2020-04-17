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

use DateTime;
use DateTimeZone;
use Exception;
use Merkulove\Coronar;
use stdClass;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement shortcodes.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Shortcodes {

	/**
	 * The one true Shortcodes.
	 *
	 * @var Shortcodes
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Shortcodes instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Initializes plugin shortcodes. */
		add_action( 'init', [$this, 'shortcodes_init'] );

	}

	/**
	 * Initializes shortcodes.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 **/
	public function shortcodes_init() {

		/** Add plugin shortcode [coronar]. Works everywhere on site. */
		add_shortcode( 'coronar', [ $this, 'coronar_shortcode' ] );

		/** Add plugin shortcode [coronar-card]. Works everywhere on site. */
		add_shortcode( 'coronar-card', [ $this, 'coronar_card_shortcode' ] );

		/** Add plugin shortcode [coronar-date]. Works everywhere on site. */
		add_shortcode( 'coronar-date', [ $this, 'coronar_date_shortcode' ] );

		/** Add plugin shortcode [coronar-relative-date]. Works everywhere on site. */
		add_shortcode( 'coronar-relative-date', [ $this, 'coronar_relative_date_shortcode' ] );

		/** Add plugin shortcode [coronar-usa]. Works everywhere on site. */
		add_shortcode( 'coronar-usa', [ $this, 'coronar_usa_shortcode' ] );

		/** Add plugin shortcode [coronar-map]. Works everywhere on site. */
		add_shortcode( 'coronar-map', [ $this, 'coronar_map_shortcode' ] );

		/** Add plugin shortcode [coronar-total]. Works everywhere on site. */
		add_shortcode( 'coronar-total', [ $this, 'coronar_total_shortcode' ] );

	}

	/**
	 * Add Coronar shortcode [coronar-relative-date].
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 **/
	public function coronar_relative_date_shortcode() {

		/** Unique id for current shortcode. */
		$id = $this->get_shortcode_id();

		/** Enqueue styles and scripts only if shortcode used on this page. */
		$this->enqueue();

		ob_start();
		?>
		<!-- Start Coronar WordPress Plugin -->
		<div id="<?php esc_attr_e( $id ); ?>" class="mdp-coronar-summary-relative-date-box">
            <span class="dashicons dashicons-clock"></span>
			<?php $this->render_summary_relative_date(); ?>
		</div>
		<!-- End Coronar WordPress Plugin -->
		<?php

		return ob_get_clean();

	}

	/**
	 * Add Coronar shortcode [coronar-date].
	 *
	 * @throws Exception
	 * @since  1.0.0
	 * @access public
	 *         *@return string
	 */
	public function coronar_date_shortcode() {

		/** Unique id for current shortcode. */
		$id = $this->get_shortcode_id();

		/** Enqueue styles and scripts only if shortcode used on this page. */
		$this->enqueue();

		ob_start();
		?>
        <!-- Start Coronar WordPress Plugin -->
        <div id="<?php esc_attr_e( $id ); ?>" class="mdp-coronar-summary-date-box">
            <span class="dashicons dashicons-clock"></span>
            <?php $this->render_summary_date(); ?>
        </div>
        <!-- End Coronar WordPress Plugin -->
		<?php

		return ob_get_clean();

    }

	/**
	 * Add Coronar Total shortcode [coronar-total].
	 *
	 * @param $atts
	 *
	 * @since  1.0.5
	 * @access public
	 *
	 * @return string
	 **/
	public function coronar_total_shortcode( $atts ) {

		/** Filter shortcode attributes. */
		$atts = shortcode_atts( [
			'columns'   => '',
			'labels'    => 'on',
		], $atts );

		/** Unique id for current shortcode. */
		$id = $this->get_shortcode_id( [] );

		/** Enqueue styles and scripts only if shortcode used on this page. */
		$this->enqueue();

		ob_start();
		?>
        <!-- Start Coronar WordPress Plugin -->
        <div id="<?php esc_attr_e( $id ); ?>" class="mdp-coronar-cards-box mdp-coronar-total">
			<?php $this->render_total_view( $atts ); ?>
        </div>
        <!-- End Coronar WordPress Plugin -->
		<?php

		return ob_get_clean();

	}

	/**
	 * Add Coronar shortcode [coronar-card].
	 *
	 * @param array $atts - Shortcodes attributes.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 **/
	public function coronar_card_shortcode( $atts ) {

		/** Filter shortcode attributes. */
		$atts = shortcode_atts( [
			'countries' => '',
			'columns'   => '',
			'labels'    => 'on',
			'filter'    => '',
		], $atts );

		/** Unique id for current shortcode. */
		$id = $this->get_shortcode_id( $atts );

		/** Enqueue styles and scripts only if shortcode used on this page. */
		$this->enqueue();

		ob_start();
		?>
        <!-- Start Coronar WordPress Plugin -->
        <div id="<?php esc_attr_e( $id ); ?>" class="mdp-coronar-cards-box">
			<?php $this->render_cards_view( $atts ); ?>
        </div>
        <!-- End Coronar WordPress Plugin -->
		<?php

		return ob_get_clean();

	}

	/**
	 * Add Coronar USA shortcode [coronar-usa].
	 *
	 * @param array $atts - Shortcodes attributes.
	 *
	 * @return string
	 * @since 1.0.3
	 * @access public
	 **/
	public function coronar_usa_shortcode( $atts ) {

		/** Filter shortcode attributes. */
		$atts = shortcode_atts( [
            'provinces' => '',
			'columns'   => '',
			'labels'    => 'on',
            'filter'    => '',
		], $atts );

		/** Unique id for current shortcode. */
		$id = $this->get_shortcode_id( $atts );

		/** Enqueue styles and scripts only if shortcode used on this page. */
		$this->enqueue();

		ob_start();
		?>
		<!-- Start Coronar WordPress Plugin -->
		<div id="<?php esc_attr_e( $id ); ?>" class="mdp-coronar-table-box mdp-coronar-usa-box">
			<?php $this->render_usa_table_view( $atts ); ?>
		</div>
		<!-- End Coronar WordPress Plugin -->
		<?php

		return ob_get_clean();

	}

	/**
	 * Add Coronar shortcode [coronar-map].
	 *
	 * @param array $atts - Shortcodes attributes.
	 *
	 * @return string
	 * @since 1.0.4
	 * @access public
	 **/
	public function coronar_map_shortcode( $atts ) {

	    /** Do we have API Key? */
	    if ( empty( Settings::get_instance()->options['api_key'] ) ) {

		    ob_start();

            ?>
            <p>
                <?php esc_html_e( 'To use GeoChart you will need to', 'coronar' ); ?>
                <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" rel="noopener">
                    <?php esc_html_e( 'Get an API Key', 'coronar' ); ?>
                </a>
                <?php esc_html_e( ' for your project.' ); ?>
            </p>
            <?php

		    return ob_get_clean();

        }

		/** Filter shortcode attributes. */
		$atts = shortcode_atts( [
			'value'   => 'TotalConfirmed',
            'region'  => '',
		], $atts );

		/** Unique id for current shortcode. */
		$id = $this->get_shortcode_id( $atts );

		/** Enqueue styles and scripts only if shortcode used on this page. */
		$this->enqueue();
		wp_enqueue_script( 'geo-chart' ); // https://developers.google.com/chart/interactive/docs/gallery/geochart
		wp_enqueue_script( 'mdp-map-chart' );

		/** Get Summary Object.  */
		$summary = COVID::get_instance()->get_summary();

		/** Filter Summary Object if we have some.  */
		$summary = $this->filter( $summary, $atts );

		/** Prepare data for JS. */
		$js_arr = [];
		$js_arr[] = ['Country', 'Value', ['role' => 'tooltip', 'p' => ['html' => true]]];

		$label_confirmed = Settings::get_instance()->options['label_confirmed_total'];
		$label_deaths = Settings::get_instance()->options['label_deaths_total'];
		$label_recovered = Settings::get_instance()->options['label_recovered_total'];

		foreach ( $summary->Countries as $country ) {

            $tooltip = "{$label_confirmed} <strong>{$country->TotalConfirmed}</strong><br>
            {$label_deaths} <strong>{$country->TotalDeaths}</strong><br>
            {$label_recovered} <strong>{$country->TotalRecovered}</strong>";

			$js_arr[] = [$country->Country, $country->{$atts['value']}, $tooltip];
		}

		/** Prepare map colors. */
		$color_2 = $this->get_map_color( $atts['value'] );
		$color_1 = $this->change_brightness( $color_2, 0.9 );


		ob_start();
		?>
        <!-- Start Coronar WordPress Plugin -->
        <div
                id="<?php esc_attr_e( $id ); ?>"
                class="mdp-coronar-map-box"
                data-color-1="<?php esc_attr_e( $color_1 ); ?>"
                data-color-2="<?php esc_attr_e( $color_2 ); ?>"
                data-land-color="<?php esc_attr_e( $this->convert_to_hex( Settings::get_instance()->options['land_color'] ) ); ?>"
                data-water-color="<?php esc_attr_e( $this->convert_to_hex( Settings::get_instance()->options['water_color'] ) ); ?>"
                data-summary='<?php esc_attr_e( json_encode( $js_arr ) ); ?>'
                data-region="<?php esc_attr_e( $this->get_region_code( $atts['region'] ) ); ?>"
        ></div>
        <!-- End Coronar WordPress Plugin -->
		<?php

		return ob_get_clean();

	}

	/**
	 * Increases or decreases the brightness of a color by a percentage of the current brightness.
	 *
	 * @param   string $hex_color - Supported formats: `#FFF`, `#FFFFFF`, `FFF`, `FFFFFF`
	 * @param   float  $percent   - A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
	 *
	 * @since   1.0.5
     *
	 * @return  string
	 **/
    private function change_brightness( $hex_color, $percent ) {

	    $hex_color = ltrim( $hex_color, '#' );

		/** Shorthand. */
		if ( strlen( $hex_color ) == 3 ) {

			$hex_color = $hex_color[0] . $hex_color[0] . $hex_color[1] . $hex_color[1] . $hex_color[2] . $hex_color[2];

		}

	    $hex_color = array_map( 'hexdec', str_split( $hex_color, 2 ) );

		foreach ( $hex_color as &$color ) {

			$adjustableLimit = $percent < 0 ? $color : 255 - $color;
			$adjustAmount = ceil( $adjustableLimit * $percent );

			$color = str_pad( dechex( $color + $adjustAmount ), 2, '0', STR_PAD_LEFT );

		}

		return '#' . implode( $hex_color );

	}

	private function get_region_code( $region_name ) {

	    $codes = [
            'Africa'    => '002',
            'Europe'    => '150',
            'Americas'  => '019',
            'Asia'      => '142',
            'Oceania'   => '009'
        ];

	    if ( array_key_exists( $region_name, $codes ) ) {
	        return $codes[$region_name];
        }

	    return '';

    }

	private function get_map_color( $value ) {

		/** Default we use TotalConfirmed color. */
	    $color = Settings::get_instance()->options['confirmed_color'];

	    if ( 'TotalDeaths' === $value ) {

		    $color = Settings::get_instance()->options['deaths_color'];

	    } elseif ( 'TotalRecovered' === $value ) {

		    $color = Settings::get_instance()->options['recovered_color'];

	    }

	    /** Convert to Hex. */
		$color = $this->convert_to_hex( $color );

        return $color;

    }

    private function convert_to_hex( $color ) {

	    /** Convert to Hex. */
	    if ( strpos( $color, 'rgba' ) !== false ) {

		    $color = $this->rgba2rgb( $color );

		    $color = $this->rgb2hex( $color );

	    }

        return $color;

    }

	private function rgba2rgb( $rgba ) {

		sscanf( $rgba, 'rgba(%d, %d, %d, %f)', $r, $g, $b, $a );

		return ( 'rgb(' . $r . ', ' . $g . ', ' . $b . ');' );

	}

	function rgb2hex( $color ) {

	    $hex = "#";

	    if ( ! is_array( $color ) ) {

	        $color = explode(',', $color );

	        $color[0] = str_replace( 'rgb', '', $color[0] );
			$color[0] = str_replace( '(', '', $color[0] );
			$color[2] = str_replace( ')', '', $color[2] );

		}

		$hex .= str_pad( dechex( $color[0] ), 2, "0", STR_PAD_LEFT );
		$hex .= str_pad( dechex( $color[1] ), 2, "0", STR_PAD_LEFT );
		$hex .= str_pad( dechex( $color[2] ), 2, "0", STR_PAD_LEFT );

		return $hex; // returns the hex value including the number sign (#)

	}

	/**
	 * Add Coronar shortcode [coronar].
	 *
	 * @param array $atts - Shortcodes attributes.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 **/
	public function coronar_shortcode( $atts ) {

		/** Filter shortcode attributes. */
		$atts = shortcode_atts( [
			'countries' => '',
            'columns'   => '',
            'labels'    => 'on',
			'filter'    => '',
		], $atts );

		/** Unique id for current shortcode. */
		$id = $this->get_shortcode_id( $atts );

		/** Enqueue styles and scripts only if shortcode used on this page. */
		$this->enqueue();

		ob_start();
		?>
		<!-- Start Coronar WordPress Plugin -->
		<div id="<?php esc_attr_e( $id ); ?>" class="mdp-coronar-table-box">
            <?php $this->render_table_view( $atts ); ?>
		</div>
		<!-- End Coronar WordPress Plugin -->
		<?php

		return ob_get_clean();

	}

	/**
	 * Render Summary table Date.
	 *
	 * @throws Exception
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
	private function render_summary_date() {

		/** Get Summary Object.  */
		$summary = COVID::get_instance()->get_summary();

		/** Date time format from wp settings. */
		$wp_format = get_option('date_format') . ' ' . get_option('time_format');

        /** We do not need such accuracy. */
        list( $date_time, $micro_time ) = explode( '.', $summary->Date );

		$date = DateTime::createFromFormat( 'Y-m-d\TH:i:s', $date_time  );
		$date->setTimeZone( new DateTimeZone( wp_timezone_string() ) );

		esc_html_e( $date->format( $wp_format ) );

    }

	/**
	 * Render Summary table Relative Date.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
	private function render_summary_relative_date() {

		/** Get Summary Object.  */
		$summary = COVID::get_instance()->get_summary();

		$date = DateTime::createFromFormat( 'Y-m-d\TH:i:sZ', $summary->Date );
		$date->setTimeZone( new DateTimeZone( wp_timezone_string() ) );

		/** Relative date: Today, Yesterday, Days Ago, Weeks Ago */
		?><span class="mdp-coronar-relative-date"><?php
		$relative_date = $this->relative_date( $date->format( 'Y-m-d H:i:s' ), false );
		esc_html_e( $relative_date );
		?></span> <?php

		/** Relative time: Seconds Ago, Minutes Ago, Hours Ago */
		?><span class="mdp-coronar-relative-time"><?php
		$relative_time =  $this->relative_time( $date,  get_option('time_format')  );
		esc_html_e( $relative_time );
		?></span><?php

	}

	/**
	 * Return time in relative format (Seconds Ago/Minutes Ago/Hours Ago).
	 *
	 * @param DateTime $date
	 * @param string $current_timeformat
	 * @param bool $display_ago_only
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 **/
	private function relative_time( $date, $current_timeformat, $display_ago_only = false ) {

		$current_time = current_time( 'timestamp' );
		$date_today_time = gmdate( 'j-n-Y H:i:s', $current_time );
		$post_date_time = mysql2date( 'j-n-Y H:i:s', $date->format( 'Y-m-d H:i:s' ), false );
		$date_today = gmdate( 'j-n-Y', $current_time );
		$post_date = mysql2date( 'j-n-Y', $date->format( 'Y-m-d H:i:s' ), false );
		$time_diff = ( strtotime( $date_today_time ) - strtotime( $post_date_time ) );
		$format_ago = '';

		if ( $post_date == $date_today ) {

			if ( $time_diff < 60 ) {

				$format_ago = sprintf( _n( '%s second ago', '%s seconds ago', $time_diff, 'coronar' ), number_format_i18n( $time_diff ) );

			} elseif ( $time_diff < 3600 ) {

				$format_ago = sprintf( _n( '%s minute ago', '%s minutes ago', intval( $time_diff/60 ), 'coronar' ), number_format_i18n( intval( $time_diff/60 ) ) );

			} elseif ( $time_diff < 86400 ) {

				$format_ago = sprintf( _n( '%s hour ago', '%s hours ago', intval( $time_diff/3600 ), 'coronar' ), number_format_i18n( intval( $time_diff/3600 ) ) );

			}

			if ( $display_ago_only ) {

				return $format_ago;

			} else {

				return $date->format( $current_timeformat ) . ' (' . $format_ago . ')';

			}

		} else {

			return $date->format( $current_timeformat );

		}

	}

	/**
	 * Return date in relative format (Today/Yesterday/Days Ago/Weeks Ago).
	 *
	 * @param string $date
	 * @param bool $display_ago_only
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	private function relative_date( $date,  $display_ago_only = false ) {

        $the_date = mysql2date( get_option( 'date_format' ), $date );


		if ( gmdate('Y', current_time('timestamp')) != mysql2date('Y', $date, false ) ) {

			$output = $the_date;

		} else {

			$day_diff = ( gmdate( 'z', current_time( 'timestamp' ) ) - mysql2date( 'z', $date, false ) );

			if ( $day_diff < 0 ) { $day_diff = 32; }

			if ( $day_diff == 0 ) {

				$output = esc_html__('Today', 'coronar' );

			} elseif ( $day_diff == 1 ) {

				$output = __('Yesterday', 'coronar' );

			} elseif ( $day_diff < 7 ) {

				if ( $display_ago_only ) {

					$output = sprintf( _n( '%s day ago', '%s days ago', $day_diff, 'coronar' ), number_format_i18n( $day_diff ) );

				} else {

					$output = $the_date . ' (' . sprintf( _n('%s day ago', '%s days ago', $day_diff, 'coronar' ), number_format_i18n( $day_diff ) ) . ')';
				}

			} elseif ( $day_diff < 31 ) {

				if ( $display_ago_only ) {

					$output = sprintf( _n( '%s week ago', '%s weeks ago', ceil( $day_diff/7 ), 'coronar'), number_format_i18n( ceil( $day_diff/7 ) ) );

				} else {

					$output = $the_date . ' (' . sprintf( _n( '%s week ago', '%s weeks ago', ceil( $day_diff/7 ), 'coronar' ), number_format_i18n( ceil( $day_diff/7 ) ) ) . ')';

				}

			} else {

				$output = $the_date;

			}
		}

        return $output;

	}

	/**
	 * Render Total card.
	 *
	 * @param $atts
	 *
	 * @since  1.0.5
	 * @access public
	 *
	 * @return void
	 **/
	private function render_total_view( $atts ) {

		/** Prepare array of columns to show. */
		$columns = $this->prepare_columns( $atts['columns'] );

		/** Get Summary Object.  */
		$summary = COVID::get_instance()->get_summary();

		$NewConfirmed = 0;
		$TotalConfirmed = 0;
		$NewDeaths = 0;
		$TotalDeaths = 0;
		$NewRecovered = 0;
		$TotalRecovered = 0;

		foreach ( $summary->Countries as  $country ) {

			$NewConfirmed += intval( $country->NewConfirmed );
			$TotalConfirmed += intval( $country->TotalConfirmed );
			$NewDeaths += intval( $country->NewDeaths );
			$TotalDeaths += intval( $country->TotalDeaths );
			$NewRecovered += intval( $country->NewRecovered );
			$TotalRecovered += intval( $country->TotalRecovered );

		}

		$this->render_total_card( $columns, $atts, $NewConfirmed, $TotalConfirmed, $NewDeaths, $TotalDeaths, $NewRecovered, $TotalRecovered );

	}

	/** Render Total Card
	 * @param $columns
	 * @param $atts
	 * @param $NewConfirmed
	 * @param $TotalConfirmed
	 * @param $NewDeaths
	 * @param $TotalDeaths
	 * @param $NewRecovered
	 * @param $TotalRecovered
	 */
	private function render_total_card( $columns, $atts, $NewConfirmed, $TotalConfirmed, $NewDeaths, $TotalDeaths, $NewRecovered, $TotalRecovered ) {

		?>
        <div class="mdp-coronar-country-card">

            <?php if ( in_array( 'Flag', $columns) ) : ?>
            <div class="mdp-coronar-flag-country">
                <span class="mdp-coronar-flag"><?php $this->render_country_flag( 'total' ); ?></span>
            </div>
            <?php endif; ?>

            <div class="mdp-coronar-stats">

			<?php foreach ( $columns as $column ) : ?>

				<?php if ( 'New Confirmed' === $column ) : ?>
                    <div class="mdp-new-confirmed">
                        <span class="mdp-coronar-confirmed-new mdp-coronar-amount"><?php $this->add_plus( $NewConfirmed ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_confirmed_new'] , 'coronar' ); endif; ?></span>
                    </div>
				<?php endif; ?>

				<?php if ( 'Total Confirmed' === $column ) : ?>
                    <div class="mdp-total-confirmed">
                        <span class="mdp-coronar-confirmed-new mdp-coronar-amount"><?php esc_html_e( $TotalConfirmed ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_confirmed_total'] , 'coronar' ); endif; ?></span>
                    </div>
				<?php endif; ?>

				<?php if ( 'New Deaths' === $column ) : ?>
                    <div class="mdp-new-deaths">
                        <span class="mdp-coronar-deaths-new mdp-coronar-amount"><?php $this->add_plus( $NewDeaths ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_deaths_new'] , 'coronar' ); endif; ?></span>
                    </div>
				<?php endif; ?>

				<?php if ( 'Total Deaths' === $column ) : ?>
                    <div class="mdp-total-deaths">
                        <span class="mdp-coronar-deaths-total mdp-coronar-amount"><?php esc_html_e( $TotalDeaths ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_deaths_total'] , 'coronar' ); endif; ?></span>
                    </div>
				<?php endif; ?>

				<?php if ( 'New Recovered' === $column ) : ?>
                    <div class="mdp-new-recovered">
                        <span class="mdp-coronar-recovered-new mdp-coronar-amount"><?php $this->add_plus( $NewRecovered ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_recovered_new'] , 'coronar' ); endif; ?></span>
                    </div>
				<?php endif; ?>

				<?php if ( 'Total Recovered' === $column ) : ?>
                    <div class="mdp-total-recovered">
                        <span class="mdp-coronar-recovered-total mdp-coronar-amount"><?php esc_html_e( $TotalRecovered ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_recovered_total'] , 'coronar' ); endif; ?></span>
                    </div>
				<?php endif; ?>

			<?php endforeach; ?>

            </div>

        </div>
		<?php

	}

	/**
	 * Render contries cards.
	 *
	 * @param array $atts - Shortcode attributes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
	private function render_cards_view( $atts ) {

		/** Prepare array of countries names. */
		$countries = $this->prepare_countries( $atts['countries'] );

        /** Prepare array of columns to show. */
		$columns = $this->prepare_columns( $atts['columns'] );

		/** Get Summary Object.  */
		$summary = COVID::get_instance()->get_summary();

		/** Filter Summary Object if we have some filters. */
		$summary = $this->filter( $summary, $atts );

		foreach ( $summary->Countries as  $country ) {

		    if ( is_array( $countries ) ) { // Show only selected countries.

			    if ( in_array( $country->Country, $countries ) ) {

				    $this->render_card( $country, $columns, $atts );

			    }

            } else { // Show all countries.

			    $this->render_card( $country, $columns, $atts );

            }

		}

    }

	/**
	 * Filter Summary Object if we have some filters.
	 *
	 * @param object    $summary    - Object to filter.
	 * @param array     $atts       - Shortcode attributes.
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
    private function filter( $summary, $atts ) {

        /** Do nothing if we haven't any filter. */
	    if ( empty( $atts['filter'] ) ) { return $summary; }

	    /** Apply Filter */
	    $summary = Filter::get_instance()->apply_filters( $atts['filter'], $summary );

	    return $summary;

    }

	private function filter_province( $cases, $atts ) {

		/** Do nothing if we haven't any filter. */
		if ( empty( $atts['filter'] ) ) { return $cases; }

		/** Apply Filter */
		$cases = Filter::get_instance()->apply_filters_province( $atts['filter'], $cases );

		return $cases;

	}

	/**
     * Render Country Card
     *
	 * @param $country
	 * @param $columns
	 * @param $atts
	 */
    private function render_card( $country, $columns, $atts ) {

	    ?>
        <div class="mdp-coronar-country-card <?php esc_attr_e( sanitize_title( $country->Country, '', 'save' ) ); ?>">

	        <?php if ( in_array( 'Flag', $columns) and in_array( 'Country', $columns) ) { //Layout for flag AND country ?>

            <div class="mdp-coronar-flag-country">
                <span class="mdp-coronar-flag"><?php $this->render_country_flag( $country->Country ); ?></span>
                <span class="mdp-coronar-country mdp-coronar-card-label"><?php esc_html_e( $country->Country ); ?></span>
            </div>

	        <?php } elseif ( in_array( 'Flag', $columns) or in_array( 'Country', $columns) ) { //Layout for flag OR country ?>

            <div class="mdp-coronar-flag-country">

                <?php if ( in_array( 'Flag', $columns) ) : ?><span class="mdp-coronar-flag"><?php $this->render_country_flag( $country->Country ); ?></span><?php endif; ?>
	            <?php if ( in_array( 'Country', $columns) ) : ?><span class="mdp-coronar-amount"><?php esc_html_e( $country->Country ); ?></span><?php endif; ?>

            </div>

	        <?php } ?>

            <div class="mdp-coronar-stats">

		    <?php foreach ( $columns as $column ) : ?>

			    <?php if ( 'New Confirmed' === $column ) : ?>
                    <div class="mdp-new-confirmed">
                        <span class="mdp-coronar-confirmed-new mdp-coronar-amount"><?php $this->add_plus( $country->NewConfirmed ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_confirmed_new'] , 'coronar' ); endif; ?></span>
                    </div>
			    <?php endif; ?>

			    <?php if ( 'Total Confirmed' === $column ) : ?>
                    <div class="mdp-total-confirmed">
                        <span class="mdp-coronar-confirmed-new mdp-coronar-amount"><?php esc_html_e( $country->TotalConfirmed ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_confirmed_total'] , 'coronar' ); endif; ?></span>
                    </div>
			    <?php endif; ?>

			    <?php if ( 'New Deaths' === $column ) : ?>
                    <div class="mdp-new-deaths">
                        <span class="mdp-coronar-deaths-new mdp-coronar-amount"><?php $this->add_plus( $country->NewDeaths ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_deaths_new'] , 'coronar' ); endif; ?></span>
                    </div>
			    <?php endif; ?>

			    <?php if ( 'Total Deaths' === $column ) : ?>
                    <div class="mdp-total-deaths">
                        <span class="mdp-coronar-deaths-total mdp-coronar-amount"><?php esc_html_e( $country->TotalDeaths ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_deaths_total'] , 'coronar' ); endif; ?></span>
                    </div>
			    <?php endif; ?>

			    <?php if ( 'New Recovered' === $column ) : ?>
                    <div class="mdp-new-recovered">
                        <span class="mdp-coronar-recovered-new mdp-coronar-amount"><?php $this->add_plus( $country->NewRecovered ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_recovered_new'] , 'coronar' ); endif; ?></span>
                    </div>
			    <?php endif; ?>

			    <?php if ( 'Total Recovered' === $column ) : ?>
                    <div class="mdp-total-recovered">
                        <span class="mdp-coronar-recovered-total mdp-coronar-amount"><?php esc_html_e( $country->TotalRecovered ); ?></span>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_recovered_total'] , 'coronar' ); endif; ?></span>
                    </div>
			    <?php endif; ?>

			    <?php if ( 'Chart Confirmed' === $column ) : ?>
                    <div class="mdp-coronar-chart-confirmed">
					    <?php $this->render_chart_confirmed( $country->Country ); ?>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_chart_confirmed'] , 'coronar' ); endif; ?></span>
                    </div>
			    <?php endif; ?>

			    <?php if ( 'Chart Deaths' === $column ) : ?>
                    <div class="mdp-coronar-chart-deaths">
					    <?php $this->render_chart_deaths( $country->Country ); ?>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_chart_deaths'] , 'coronar' ); endif; ?></span>
                    </div>
			    <?php endif; ?>

			    <?php if ( 'Chart Recovered' === $column ) : ?>
                    <div class="mdp-coronar-chart-recovered">
                        <?php $this->render_chart_recovered( $country->Country ); ?>
                        <span class="mdp-coronar-card-label"><?php if ( $atts['labels'] === 'on' ) : esc_html_e( Settings::get_instance()->options['label_chart_recovered'] , 'coronar' ); endif; ?></span>
                    </div>
			    <?php endif; ?>

		    <?php endforeach; ?>

            </div>

        </div>
	    <?php

    }

	private function prepare_usa_columns( $columns ) {

		/** Show default columns if columns empty. */
		if ( empty( $columns ) ) {
			$columns = 'Province, Total Confirmed, Total Deaths, Total Recovered';
		}

		/** Convert columns string to array. */
		$columns = explode( ',', $columns );
		$columns = array_map( 'trim', $columns );

		return $columns;

	}

    private function prepare_columns( $columns ) {

	    /** Show default columns if columns empty. */
	    if ( empty( $columns ) ) {
		    $columns = 'Flag, Country, New Confirmed, Total Confirmed, New Deaths, Total Deaths, New Recovered, Total Recovered';
	    }

	    /** Convert columns string to array. */
	    $columns = explode( ',', $columns );
	    $columns = array_map( 'trim', $columns );

	    return $columns;

    }

	private function prepare_provinces( $provinces ) {

		/** Convert provinces string to array. */
		$provinces = explode( ',', $provinces );
		$provinces = array_map( 'trim', $provinces );

		/** Clean out empty values. */
		foreach ( $provinces as $key => $province ) {

			if ( empty( $province ) ) {

				unset( $provinces[$key] );

			}

		}

		/** Show all provinces if empty.  */
		if ( empty( $provinces ) ) {

			$provinces = [];
			foreach ( COVID::get_instance()->get_usa_provinces() as $province ) {
				$provinces[] = $province;
			}

		}

		return $provinces;

	}

    private function prepare_countries( $countries ) {

		/** Convert countries string to array. */
	    $countries = explode( ',', $countries );
	    $countries = array_map( 'trim', $countries );

	    /** Clean out empty values. */
	    foreach ( $countries as $key => $country ) {

		    if ( empty( $country ) ) {
			    unset($countries[$key]);
		    }

	    }

	    /** Show all Countries if empty.  */
	    if ( empty( $countries ) ) {

            $countries = [];
            foreach ( COVID::get_instance()->read_countries() as $country ) {
	            $countries[] = $country->Country;
            }

        }

	    return $countries;

    }

	/**
	 * Render Summary table for Single country.
	 *
	 * @param array $atts - Shortcode attributes.
	 *
	 * @since  1.0.3
	 * @access public
	 * @return void
	 **/
	private function render_usa_table_view( $atts ) {

        /** USA country slug. */
		$country_slug = 'united-states';

		/** Prepare array of provinces names. */
		$provinces = $this->prepare_provinces( $atts['provinces'] );

		/** Prepare array of columns to show. */
		$columns = $this->prepare_usa_columns( $atts['columns'] );

		?>
        <table class="mdp-coronar-summary-tbl display responsive">

            <thead <?php if ( $atts['labels'] === 'off' ) : ?>style="display: none"<?php endif; ?> >
	            <tr>
					<?php foreach ( $columns as $column ) : ?>

						<?php if ( 'Province' === $column ) : ?>
	                        <th class="mdp-coronar-province"><?php esc_html_e( Settings::get_instance()->options['label_province'] , 'coronar' ); ?></th>
						<?php endif; ?>

						<?php if ( 'Total Confirmed' === $column ) : ?>
	                        <th class="mdp-coronar-confirmed-total-th"><?php esc_html_e( Settings::get_instance()->options['label_confirmed_total'] , 'coronar' ); ?></th>
						<?php endif; ?>

						<?php if ( 'Total Deaths' === $column ) : ?>
	                        <th class="mdp-coronar-deaths-total-th"><?php esc_html_e( Settings::get_instance()->options['label_deaths_total'] , 'coronar' ); ?></th>
						<?php endif; ?>

						<?php if ( 'Total Recovered' === $column ) : ?>
	                        <th class="mdp-coronar-recovered-total-th"><?php esc_html_e( Settings::get_instance()->options['label_recovered_total'] , 'coronar' ); ?></th>
						<?php endif; ?>

					<?php endforeach; ?>
	            </tr>
            </thead>
            <tbody>
			<?php

			$cases_by_province = $this->combine_cases_by_province( $country_slug );

			/** Filter cases by province Object if we have some. */
			$cases_by_province = $this->filter_province( $cases_by_province, $atts );

			foreach ( $cases_by_province as $province ) {

				if ( is_array( $provinces ) ) { // Show only selected provinces.

					if ( in_array( $province->Province, $provinces ) ) {

						$this->render_province_tr( $province, $columns );

					}

				} else { // Show all provinces.

					$this->render_province_tr( $province, $columns );

				}

			}
			?>
            </tbody>
        </table>
		<?php

    }

	private function render_province_tr( $province, $columns ) {

		?>
        <tr>
			<?php foreach ( $columns as $column ) : ?>

				<?php if ( 'Province' === $column ) : ?>
                    <td class="mdp-coronar-province"><?php esc_html_e( $province->Province ); ?></td>
				<?php endif; ?>

				<?php if ( 'Total Confirmed' === $column ) : ?>
                    <td class="mdp-coronar-confirmed-total"><?php esc_html_e( $province->TotalConfirmed ); ?></td>
				<?php endif; ?>

				<?php if ( 'Total Deaths' === $column ) : ?>
                    <td class="mdp-coronar-deaths-total"><?php esc_html_e( $province->TotalDeaths ); ?></td>
				<?php endif; ?>

				<?php if ( 'Total Recovered' === $column ) : ?>
                    <td class="mdp-coronar-recovered-total"><?php esc_html_e( $province->TotalRecovered ); ?></td>
				<?php endif; ?>

			<?php endforeach; ?>
        </tr>
		<?php

    }

	/**
	 * Return array of objects cases by province .
	 *
	 * @param string $country_slug
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 **/
    private function combine_cases_by_province( $country_slug ) {

	    $confirmed = COVID::get_instance()->get_live_country_status( $country_slug, 'confirmed' );
	    $deaths = COVID::get_instance()->get_live_country_status( $country_slug, 'deaths' );
	    $recovered = COVID::get_instance()->get_live_country_status( $country_slug, 'recovered' );

	    $res = [];

	    /** Get USA Provinces. */
	    $provinces = COVID::get_instance()->get_usa_provinces();

	    /** Get latest data for each province. */
	    foreach ( $provinces as $province ) {

		    $province_obj = new stdClass;
		    $province_obj->Province = $province;
		    $province_obj->TotalConfirmed = $this->get_latest_value_by_province( $confirmed, $province );

		    /** Skip empty states. */
		    if ( empty( $province_obj->TotalConfirmed ) ) { continue; }

		    $province_obj->TotalDeaths = $this->get_latest_value_by_province( $deaths, $province );
		    $province_obj->TotalRecovered = $this->get_latest_value_by_province( $recovered, $province );

		    $res[] = $province_obj;

        }

	    return $res;

    }

    private function get_latest_value_by_province( $cases, $province ) {

	    $count = '';
	    foreach ( $cases as $case ) {

		    if ( $province === $case->Province ) {
			    $count = $case->Cases;
		    }

	    }

	    return $count;

    }

	/**
	 * Render Summary table.
	 * Summary of new and total cases per country.
	 *
	 * @param array $atts - Shortcode attributes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
	private function render_table_view( $atts ) {

		/** Prepare array of countries names. */
		$countries = $this->prepare_countries( $atts['countries'] );

		/** Prepare array of columns to show. */
		$columns = $this->prepare_columns( $atts['columns'] );

		/** Get Summary Object.  */
		$summary = COVID::get_instance()->get_summary();

		/** Filter Summary Object if we have some.  */
		$summary = $this->filter( $summary, $atts );

		/** We use this to sort the column when the filter was applied. */
		list( $top, $filter_name ) = Filter::get_instance()->split_filter_name( $atts['filter'] );

	    ?>
        <table class="mdp-coronar-summary-tbl display responsive">

            <thead <?php if ( $atts['labels'] === 'off' ) : ?>style="display: none"<?php endif; ?> >
                <tr>
                    <?php foreach ( $columns as $column ) : ?>

                        <?php if ( 'Flag' === $column ) : ?>
                            <th class="mdp-coronar-flag"><?php esc_html_e( Settings::get_instance()->options['label_flag'] , 'coronar' ); ?></th>
                        <?php endif; ?>

                        <?php if ( 'Country' === $column ) : ?>
                            <th class="mdp-coronar-country"><?php esc_html_e( Settings::get_instance()->options['label_country'] , 'coronar' ); ?></th>
                        <?php endif; ?>

                        <?php if ( 'Total Confirmed' === $column ) : ?>
                            <th class="mdp-coronar-confirmed-total-th <?php if ( 'TotalConfirmed' === $filter_name ) : ?>mdp-coronar-filter-order<?php endif; ?>"><?php esc_html_e( Settings::get_instance()->options['label_confirmed_total'] , 'coronar' ); ?></th>
                        <?php endif; ?>

	                    <?php if ( 'New Confirmed' === $column ) : ?>
                            <th class="mdp-coronar-confirmed-new-th <?php if ( 'NewConfirmed' === $filter_name ) : ?>mdp-coronar-filter-order<?php endif; ?>"><?php esc_html_e( Settings::get_instance()->options['label_confirmed_new'] , 'coronar' ); ?></th>
	                    <?php endif; ?>

                        <?php if ( 'Total Deaths' === $column ) : ?>
                            <th class="mdp-coronar-deaths-total-th <?php if ( 'TotalDeaths' === $filter_name ) : ?>mdp-coronar-filter-order<?php endif; ?>"><?php esc_html_e( Settings::get_instance()->options['label_deaths_total'] , 'coronar' ); ?></th>
                        <?php endif; ?>

	                    <?php if ( 'New Deaths' === $column ) : ?>
                            <th class="mdp-coronar-deaths-new-th <?php if ( 'NewDeaths' === $filter_name ) : ?>mdp-coronar-filter-order<?php endif; ?>"><?php esc_html_e( Settings::get_instance()->options['label_deaths_new'] , 'coronar' ); ?></th>
	                    <?php endif; ?>

                        <?php if ( 'Total Recovered' === $column ) : ?>
                            <th class="mdp-coronar-recovered-total-th <?php if ( 'TotalRecovered' === $filter_name ) : ?>mdp-coronar-filter-order<?php endif; ?>"><?php esc_html_e( Settings::get_instance()->options['label_recovered_total'] , 'coronar' ); ?></th>
                        <?php endif; ?>

	                    <?php if ( 'New Recovered' === $column ) : ?>
                            <th class="mdp-coronar-recovered-new-th <?php if ( 'NewRecovered' === $filter_name ) : ?>mdp-coronar-filter-order<?php endif; ?>"><?php esc_html_e( Settings::get_instance()->options['label_recovered_new'] , 'coronar' ); ?></th>
	                    <?php endif; ?>

                        <?php if ( 'Chart Confirmed' === $column ) : ?>
                            <th class="mdp-coronar-charts-th"><?php esc_html_e( Settings::get_instance()->options['label_chart_confirmed'] , 'coronar' ); ?></th>
                        <?php endif; ?>

                        <?php if ( 'Chart Deaths' === $column ) : ?>
                            <th class="mdp-coronar-charts-th"><?php esc_html_e( Settings::get_instance()->options['label_chart_deaths'] , 'coronar' ); ?></th>
                        <?php endif; ?>

                        <?php if ( 'Chart Recovered' === $column ) : ?>
                            <th class="mdp-coronar-charts-th"><?php esc_html_e( Settings::get_instance()->options['label_chart_recovered'] , 'coronar' ); ?></th>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ( $summary->Countries as  $country ) {

	                if ( is_array( $countries ) ) { // Show only selected countries.

		                if ( in_array( $country->Country, $countries ) ) {

			                $this->render_tr( $country, $columns );

		                }

	                } else { // Show all countries.

		                $this->render_tr( $country, $columns );

	                }

                }
                ?>
            </tbody>
        </table>
        <?php

    }

    private function render_tr( $country, $columns ) {
	    ?>
        <tr>
		    <?php foreach ( $columns as $column ) : ?>

			    <?php if ( 'Flag' === $column ) : ?>
                    <td class="mdp-coronar-flag"><?php $this->render_country_flag( $country->Country ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'Country' === $column ) : ?>
                    <td class="mdp-coronar-country"><?php esc_html_e( $country->Country ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'Total Confirmed' === $column ) : ?>
                    <td class="mdp-coronar-confirmed-total"><?php esc_html_e( $country->TotalConfirmed ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'New Confirmed' === $column ) : ?>
                    <td class="mdp-coronar-confirmed-new"><?php $this->add_plus( $country->NewConfirmed ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'Total Deaths' === $column ) : ?>
                    <td class="mdp-coronar-deaths-total"><?php esc_html_e( $country->TotalDeaths ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'New Deaths' === $column ) : ?>
                    <td class="mdp-coronar-deaths-new"><?php $this->add_plus( $country->NewDeaths ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'Total Recovered' === $column ) : ?>
                    <td class="mdp-coronar-recovered-total"><?php esc_html_e( $country->TotalRecovered ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'New Recovered' === $column ) : ?>
                    <td class="mdp-coronar-recovered-new"><?php $this->add_plus( $country->NewRecovered ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'Chart Confirmed' === $column ) : ?>
                    <td class="mdp-coronar-chart-confirmed"><?php $this->render_chart_confirmed( $country->Country ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'Chart Deaths' === $column ) : ?>
                    <td class="mdp-coronar-chart-deaths"><?php $this->render_chart_deaths( $country->Country ); ?></td>
			    <?php endif; ?>

			    <?php if ( 'Chart Recovered' === $column ) : ?>
                    <td class="mdp-coronar-chart-recovered"><?php $this->render_chart_recovered( $country->Country ); ?></td>
			    <?php endif; ?>

		    <?php endforeach; ?>
        </tr>
        <?php
    }

	/**
	 * Render value, add plus to positive values.
	 *
	 * @param $value
	 *
	 * @since  1.0.3
	 * @access public
	 * @return void
	 **/
    private function add_plus( $value ) {

	    /** Show '+' only for positive values. */
	    if ( intval( $value ) > 0 ) { esc_html_e( '+', 'coronar' ); }

	    esc_html_e( $value );

    }

	/**
	 * Render recovered cases on chart by country.
	 *
	 * @param $country
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
	private function render_chart_recovered( $country ) {

		/** Get country slug. */
		$country_slug = COVID::get_instance()->get_country_slug( $country );
		if ( empty( $country_slug ) ) { return; }

		/** Get Recovered cases By Country From First Recorded Case. */
		$recovered = COVID::get_instance()->get_recovered( $country_slug );
		if ( ! is_array( $recovered ) ) { return; }

		/** Render line chart. */
		$this->render_chart( $country_slug, 'recovered', $recovered );

	}

	/**
	 * Render deaths cases on chart by country.
	 *
	 * @param $country
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
	private function render_chart_deaths( $country ) {

		/** Get country slug. */
		$country_slug = COVID::get_instance()->get_country_slug( $country );
		if ( empty( $country_slug ) ) { return; }

		/** Get Deaths cases By Country From First Recorded Case. */
		$deaths = COVID::get_instance()->get_deaths( $country_slug );
		if ( ! is_array( $deaths ) ) { return; }

		/** Render line chart. */
		$this->render_chart( $country_slug, 'deaths', $deaths );

	}

	/**
	 * Render confirmed cases on chart by country.
	 *
	 * @param      $country
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
    private function render_chart_confirmed( $country ) {

	    /** Get country slug. */
	    $country_slug = COVID::get_instance()->get_country_slug( $country );
	    if ( empty( $country_slug ) ) { return; }

	    /** Get Confirmed cases By Country From First Recorded Case. */
	    $confirmed = COVID::get_instance()->get_confirmed( $country_slug );
	    if ( ! is_array( $confirmed ) ) { return; }

	    /** Render line chart. */
        $this->render_chart( $country_slug, 'confirmed', $confirmed );

    }

	/**
	 * Render line chart.
	 *
	 * @param      $country_slug
	 * @param      $case_type
	 * @param      $cases_data
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
	private function render_chart( $country_slug, $case_type, $cases_data ) {

		/** Enqueue Chartist.js CSS and JavaScript only if we use Charts on this page. */
		wp_enqueue_style( 'chartist' );
		wp_enqueue_script( 'chartist' );

		/** Unique id of chart. */
		static $call_count = 0; // $call_count will be initialized on the first time call.
		$call_count ++; // $call_count will be incremented each time the method gets called.
		$class_id = 'mdp-coronar-chart-' . str_replace( ['*', ',', '(', ')'], '', $country_slug ) . '-' . $case_type . '-' . $call_count;

		/** Prepare data arrays. */
		$labels = [];
		$series = [];
		$count = 0;
		foreach ( $cases_data as $day ) {

			if ( ! $count ) {
				$labels[] = $day->Date;
			} else {
				$labels[] = $count;
			}

			$series[] = $day->Cases;

			$count++;
		}

		$labels = json_encode( $labels );
		$series = json_encode( $series );

		?>
        <div class="ct-chart ct-golden-section <?php esc_attr_e( $class_id ); ?>"></div>
        <!--suppress JSDuplicatedDeclaration -->
		<script>
            "use strict";
            document.addEventListener( 'DOMContentLoaded', function() {
                // Initialize a Line chart in the container with the ID chart1
                new Chartist.Line('.<?php esc_attr_e( $class_id ); ?>', {
                    labels: [<?php echo $labels; ?> ],
                    series: [<?php echo $series; ?>]
                }, {
                    lineSmooth: Chartist.Interpolation.simple({divisor: 2}),
                    fullWidth: true,
                    chartPadding: 1,
                    low: 0,
                    showPoint: false,
                    responsive: true,
                    width: <?php echo 4*Settings::get_instance()->options['flag_size'] ?>,
                    height: <?php echo Settings::get_instance()->options['flag_size'] ?>,

                    showArea: false,
                    showLabel: false,
                    axisX: {
                        showGrid: false,
                        showLabel: false,
                        offset: 0
                    },
                    axisY: {
                        showGrid: false,
                        showLabel: false,
                        offset: 0
                    },
                } );
            } );
        </script>
		<?php

    }

	/**
	 * Render country flag or default image.
	 *
	 * @param $country
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
    private function render_country_flag( $country ) {

	    /** Prepare flag name from country name. */
	    $flag_name = strtolower( $country );
	    $flag_name = str_replace( ' ', '-', $flag_name );
	    $flag_name = str_replace( '*', '', $flag_name );
	    $flag_name = str_replace( ',', '', $flag_name );
	    $flag_name = str_replace( '(', '', $flag_name );
	    $flag_name = str_replace( ')', '', $flag_name );
	    $flag_name = str_replace( '\'', '', $flag_name );
	    $flag_name = str_replace( '.', '', $flag_name );
	    $flag_name .= '.svg';

	    $src = Coronar::$url . 'images/flags/' . $flag_name;
	    if ( ! file_exists( Coronar::$path . 'images/flags/' . $flag_name ) ) {
		    $src = Coronar::$url . 'images/flags/_default.svg';
        }

	    ?><img src="<?php esc_attr_e( $src ); ?>" alt="<?php esc_attr_e( $country ); ?>" /><?php

    }

	/**
	 * Enqueue styles and scripts only if shortcode used on this page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 **/
	private function enqueue() {

		/** Enqueue styles only if shortcode used on this page. */
        wp_enqueue_style( 'dataTables' );

		if ( 'on' === Settings::get_instance()->options['responsive_table'] ) {

			wp_enqueue_style( 'dataTables-rowReorder' );
			wp_enqueue_style( 'dataTables-responsive' );

        }


		wp_enqueue_style( 'mdp-coronar' );

		/** Enqueue JavaScript only if shortcode used on this page. */
		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'dataTables' );

		if ( 'on' === Settings::get_instance()->options['responsive_table'] ) {

			wp_enqueue_script( 'dataTables-rowReorder' );
			wp_enqueue_script( 'dataTables-responsive' );

        }

		wp_enqueue_script( 'mdp-coronar' );

	}

	/**
	 * Return unique id for current shortcode.
	 *
	 * @param array $atts - Shortcodes attributes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 **/
	private function get_shortcode_id( $atts = [] ) {

		/** $call_count will be initialized on the first time call. */
		static $call_count = 0;

		/** call_count will be incremented each time the method gets called. */
		$call_count ++;

		return 'mdp-coronar-' . md5( json_encode( $atts ) ) . '-' . $call_count;

	}

	/**
	 * Main Shortcodes Instance.
	 *
	 * Insures that only one instance of Shortcodes exists in memory at any one time.
	 *
	 * @static
	 * @return Shortcodes
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Shortcodes ) ) {
			self::$instance = new Shortcodes;
		}

		return self::$instance;
	}

} // End Class Shortcodes.
