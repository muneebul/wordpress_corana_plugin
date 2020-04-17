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
 * SINGLETON: Class used to render plugin settings fields.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class SettingsFields {

	/**
	 * The one true SettingsFields.
	 *
	 * @var SettingsFields
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Render Font size field.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 **/
	public static function font_size() {

		/** Prepare font size list. */
		$options = [
			'xx-small' => esc_html__( 'XX-small', 'coronar' ),
			'x-small' => esc_html__( 'S-small', 'coronar' ),
			'small' => esc_html__( 'Small', 'coronar' ),
			'medium' => esc_html__( 'Medium', 'coronar' ),
			'large' => esc_html__( 'Large', 'coronar' ),
			'x-large' => esc_html__( 'X-large', 'coronar' ),
			'xx-large' => esc_html__( 'XX-large', 'coronar' ),
			'xxx-large' => esc_html__( 'XXX-large', 'coronar' ),
		];

		/** Render font size dropdown. */
		UI::get_instance()->render_select(
			$options,
			Settings::get_instance()->options['font_size'], // Selected option.
			esc_html__('Font Size', 'coronar' ),
			esc_html__( 'Absolute font size, based on default font size (which is medium).', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[font_size]',
				'id' => 'mdp_coronar_style_settings_font_size'
			]
		);

	}

	/**
	 * Render Shadow field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function shadow() {

		/** Render Shadow switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['shadow'],
			esc_html__( 'Row/Card Shadow', 'coronar' ),
			esc_html__( 'Outer shadow for one country in the row or card', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[shadow]',
				'id' => 'mdp_coronar_style_settings_shadow'
			]
		);

	}

	/**
	 * Render Use Live field.
	 *
	 * @since 1.0.7
	 * @access public
	 **/
	public static function use_live() {

		/** Render Responsive table switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['use_live'],
			esc_html__( 'Live Data', 'coronar' ),
			esc_html__( 'Use Live Data Source. Updated Every 10 minutes.', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[use_live]',
				'id' => 'mdp_coronar_settings_use_live'
			]
		);

	}

	/**
	 * Render Responsive table field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function responsive_table() {

		/** Render Responsive table switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['responsive_table'],
			esc_html__( 'Responsive Table', 'coronar' ),
			esc_html__( 'Enable responsive behavior in table views', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[responsive_table]',
				'id' => 'mdp_coronar_settings_responsive_table'
			]
		);

	}

	/**
	 * Render Show Search field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function show_search() {

		/** Render Show Search switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['show_search'],
			esc_html__( 'Table Search', 'coronar' ),
			esc_html__( 'Show Search input in table views', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[show_search]',
				'id' => 'mdp_coronar_settings_show_search'
			]
		);

	}

	/**
	 * Render Flag size field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function flag_size() {

		/** Flag size slider. */
		UI::get_instance()->render_slider(
			Settings::get_instance()->options['flag_size'],
			0,
			256,
			1,
			esc_html__( 'Flag Size', 'coronar' ),
			esc_html__( 'Flag size: ', 'coronar' ) .
			'<strong>' . Settings::get_instance()->options['flag_size'] . '</strong>' .
			esc_html__( ' px.', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[flag_size]',
				'id' => 'mdp_coronar_style_settings_flag_size',
				'class' => 'mdc-slider-width'
			]
		);

	}

	/**
	 * Render Land Color field.
	 *
	 * @since 1.0.5
	 * @access public
	 **/
	public static function land_color() {

		/** Render Land Color colorpicker. */
		UI::get_instance()->render_colorpicker(
			Settings::get_instance()->options['land_color'],
			esc_html__( 'Land Color', 'coronar' ),
			esc_html__( 'Select color for countries without data.', 'coronar' ),
			[
				'name' => 'mdp_coronar_map_settings[land_color]',
				'id' => 'mdp_coronar_map_settings_land_color',
				'readonly' => 'readonly'
			]
		);

	}

	/**
	 * Render Water Color field.
	 *
	 * @since 1.0.5
	 * @access public
	 **/
	public static function water_color() {

		/** Render Water Color colorpicker. */
		UI::get_instance()->render_colorpicker(
			Settings::get_instance()->options['water_color'],
			esc_html__( 'Water Color', 'coronar' ),
			esc_html__( 'Select color for water.', 'coronar' ),
			[
				'name' => 'mdp_coronar_map_settings[water_color]',
				'id' => 'mdp_coronar_map_settings_water_color',
				'readonly' => 'readonly'
			]
		);

	}

	/**
	 * Render Margin field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function margin() {

		/** Margin slider. */
		UI::get_instance()->render_slider(
			Settings::get_instance()->options['margin'],
			0,
			100,
			1,
			esc_html__( 'Margin', 'coronar' ),
			esc_html__( 'Margin: ', 'coronar' ) .
			'<strong>' . Settings::get_instance()->options['margin'] . '</strong>' .
			esc_html__( ' px.', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[margin]',
				'id' => 'mdp_coronar_style_settings_margin',
				'class' => 'mdc-slider-width'
			]
		);

	}

	/**
	 * Render Padding field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function padding() {

		/** Padding slider. */
		UI::get_instance()->render_slider(
			Settings::get_instance()->options['padding'],
			0,
			100,
			1,
			esc_html__( 'Padding', 'coronar' ),
			esc_html__( 'Padding: ', 'coronar' ) .
			'<strong>' . Settings::get_instance()->options['padding'] . '</strong>' .
			esc_html__( ' px.', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[padding]',
				'id' => 'mdp_coronar_style_settings_padding',
				'class' => 'mdc-slider-width'
			]
		);

	}

	/**
	 * Render Border Radius field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function border_radius() {

		/** Padding slider. */
		UI::get_instance()->render_slider(
			Settings::get_instance()->options['border_radius'],
			0,
			100,
			1,
			esc_html__( 'Border radius', 'coronar' ),
			esc_html__( 'Border radius: ', 'coronar' ) .
			'<strong>' . Settings::get_instance()->options['border_radius'] . '</strong>' .
			esc_html__( ' px.', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[border_radius]',
				'id' => 'mdp_coronar_style_settings_border_radius',
				'class' => 'mdc-slider-width'
			]
		);

	}

	/**
	 * Render Accent Color field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function accent_color() {

		/** Render Accent Color colorpicker. */
		UI::get_instance()->render_colorpicker(
			Settings::get_instance()->options['accent_color'],
			esc_html__( 'Accent Color', 'coronar' ),
			esc_html__( 'Select the text color', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[accent_color]',
				'id' => 'mdp_coronar_style_settings_accent_color',
				'readonly' => 'readonly'
			]
		);

	}

	/**
	 * Render Background color field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function bg_color() {

		/** Render Background color colorpicker. */
		UI::get_instance()->render_colorpicker(
			Settings::get_instance()->options['bg_color'],
			esc_html__( 'Background color', 'coronar' ),
			esc_html__( 'Select Background Color ', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[bg_color]',
				'id' => 'mdp_coronar_style_settings_bg_color',
				'readonly' => 'readonly'
			]
		);

	}

	/**
	 * Render Confirmed color field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function confirmed_color() {

		/** Render Confirmed color colorpicker. */
		UI::get_instance()->render_colorpicker(
			Settings::get_instance()->options['confirmed_color'],
			esc_html__( 'Confirmed color', 'coronar' ),
			esc_html__( 'Select Confirmed Color ', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[confirmed_color]',
				'id' => 'mdp_coronar_style_settings_confirmed_color',
				'readonly' => 'readonly'
			]
		);

	}

	/**
	 * Render Deaths color field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function deaths_color() {

		/** Render Deaths color colorpicker. */
		UI::get_instance()->render_colorpicker(
			Settings::get_instance()->options['deaths_color'],
			esc_html__( 'Deaths color', 'coronar' ),
			esc_html__( 'Select Deaths Color ', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[deaths_color]',
				'id' => 'mdp_coronar_style_settings_deaths_color',
				'readonly' => 'readonly'
			]
		);

	}

	/**
	 * Render Recovered color field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function recovered_color() {

		/** Render Recovered color colorpicker. */
		UI::get_instance()->render_colorpicker(
			Settings::get_instance()->options['recovered_color'],
			esc_html__( 'Recovered color', 'coronar' ),
			esc_html__( 'Select Recovered Color ', 'coronar' ),
			[
				'name' => 'mdp_coronar_style_settings[recovered_color]',
				'id' => 'mdp_coronar_style_settings_recovered_color',
				'readonly' => 'readonly'
			]
		);

	}

	/**
	 * Render label Flag field
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_flag() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_flag'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_flag]',
				'id' => 'mdp_coronar_settings_label_flag',
			]
		);

    }

	/**
	 * Render API Key field
	 *
	 * @since 1.0.4
	 * @access public
	 **/
	public static function api_key() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['api_key'],
			esc_html__( 'API Key', 'coronar' ),
			esc_html__( 'Enter Google Map API Key. ', 'coronar' ) .
            '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">' .
			    esc_html__( 'Get an API Key.', 'coronar' ) .
            '</a>',
			[
				'name' => 'mdp_coronar_map_settings[api_key]',
				'id' => 'mdp_coronar_map_settings_api_key',
			]
		);

	}

	/**
	 * Render label Country field
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_country() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_country'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_country]',
				'id' => 'mdp_coronar_settings_label_country',
			]
		);

	}

	/**
	 * Render label Province field
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_province() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_province'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_province]',
				'id' => 'mdp_coronar_settings_label_province',
			]
		);

	}

	/**
	 * Render label New Confirmed field
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_confirmed_new() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_confirmed_new'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_confirmed_new]',
				'id' => 'mdp_coronar_settings_label_confirmed_new',
			]
		);

	}

	/**
	 * Render label Total Confirmed field
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_confirmed_total() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_confirmed_total'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_confirmed_total]',
				'id' => 'mdp_coronar_settings_label_confirmed_total',
			]
		);

	}

	/**
	 * Render label New Deaths field
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_deaths_new() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_deaths_new'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_deaths_new]',
				'id' => 'mdp_coronar_settings_label_deaths_new',
			]
		);

	}

	/**
	 * Render label Total Deaths field
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_deaths_total() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_deaths_total'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_deaths_total]',
				'id' => 'mdp_coronar_settings_label_deaths_total',
			]
		);

	}

	/**
	 * Render label New Recovered field
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_recovered_new() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_recovered_new'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_recovered_new]',
				'id' => 'mdp_coronar_settings_label_recovered_new',
			]
		);

	}

	/**
	 * Render label Total Recovered field
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_recovered_total() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_recovered_total'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_recovered_total]',
				'id' => 'mdp_coronar_settings_label_recovered_total',
			]
		);

	}

	/**
	 * Render label Chart Confirmed
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_chart_confirmed() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_chart_confirmed'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_chart_confirmed]',
				'id' => 'mdp_coronar_settings_label_chart_confirmed',
			]
		);

	}

	/**
	 * Render label Chart Deaths
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_chart_deaths() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_chart_deaths'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_chart_deaths]',
				'id' => 'mdp_coronar_settings_label_chart_deaths',
			]
		);

	}

	/**
	 * Render label Chart Recovered
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function label_chart_recovered() {

		UI::get_instance()->render_input(
			Settings::get_instance()->options['label_chart_recovered'],
			esc_html__( 'Label', 'coronar' ),
			esc_html__( 'Enter the text to replace the default label', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[label_chart_recovered]',
				'id' => 'mdp_coronar_settings_label_chart_recovered',
			]
		);

	}

	/**
	 * Render Clear Cache button.
	 *
	 * @since 1.0.3
	 * @access public
	 **/
	public static function clear_cache() {

	    ?><br><?php
		UI::get_instance()->render_button(
			esc_html__( 'Clear Cache', 'coronar' ),
			esc_html__( 'Press to reset cache and download fresh data.', 'coronar' ),
			[
				"name" => "mdp-coronar-clear-cache",
				"id" => "mdp-coronar-clear-cache",
				"class" => "mdc-button--outlined mdp-reset",
			],
			'close'
		);

    }

	/**
	 * Render Cache Lifetime field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function cache_time() {

		/** Max duration slider. */
		UI::get_instance()->render_slider(
			Settings::get_instance()->options['cache_time'],
			30,
			300,
			15,
			esc_html__( 'Cache Lifetime', 'coronar' ),
			esc_html__( 'Refresh data every: ', 'coronar' ) .
			'<strong>' . Settings::get_instance()->options['cache_time'] . '</strong>' .
			esc_html__( ' minutes. The higher the value, the less the load on the hosting and the lower the relevance of the data.', 'coronar' ),
			[
				'name' => 'mdp_coronar_settings[cache_time]',
				'id' => 'mdp_coronar_settings_cache_time',
				'class' => 'mdc-slider-width'
			]
		);

		self::clear_cache();

	}

	/**
	 * Render CSS field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function custom_css() {
		?>
		<div>
            <label>
                <textarea
                    id="mdp_custom_css_fld"
                    name="mdp_coronar_css_settings[custom_css]"
                    class="mdp_custom_css_fld"><?php echo esc_textarea( Settings::get_instance()->options['custom_css'] ); ?></textarea>
            </label>
			<p class="description"><?php esc_html_e( 'Add custom CSS here.', 'coronar' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render "SettingsFields Saved" nags.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 **/
	public static function render_nags() {

		/** Did we try to save settings? */
		if ( ! isset( $_GET['settings-updated'] ) ) { return; }

		/** Are the settings saved successfully? */
		if ( $_GET['settings-updated'] === 'true' ) {

			/** Render "SettingsFields Saved" message. */
			UI::get_instance()->render_snackbar( esc_html__( 'Settings saved!', 'coronar' ) );
		}

		if ( ! isset( $_GET['tab'] ) ) { return; }

		if ( strcmp( $_GET['tab'], "activation" ) == 0 ) {

			if ( PluginActivation::get_instance()->is_activated() ) {

				/** Render "Activation success" message. */
				UI::get_instance()->render_snackbar( esc_html__( 'Plugin activated successfully.', 'coronar' ), 'success', 5500 );

			} else {

				/** Render "Activation failed" message. */
				UI::get_instance()->render_snackbar( esc_html__( 'Invalid purchase code.', 'coronar' ), 'error', 5500 );

			}

		}

	}

	/**
	 * Main SettingsFields Instance.
	 *
	 * Insures that only one instance of SettingsFields exists in memory at any one time.
	 *
	 * @static
	 * @return SettingsFields
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SettingsFields ) ) {
			self::$instance = new SettingsFields;
		}

		return self::$instance;
	}

} // End Class SettingsFields.
