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

use Merkulove\Coronar;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement plugin settings.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Settings {

	/**
	 * Coronar Plugin settings.
	 *
	 * @var array()
	 * @since 1.0.0
	 **/
	public $options = [];

	/**
	 * The one true Settings.
	 *
	 * @var Settings
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Settings instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Get plugin settings. */
		$this->get_options();

	}

	/**
	 * Render Tabs Headers.
	 *
	 * @param string $current - Selected tab key.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_tabs( $current = 'general' ) {

		/** Tabs array. */
		$tabs = [];
		$tabs['general'] = [
			'icon' => 'tune',
			'name' => esc_html__( 'General', 'coronar' )
		];

		$tabs['style'] = [
			'icon' => 'brush',
			'name' => esc_html__( 'Style', 'coronar' )
		];

		$tabs['map'] = [
			'icon' => 'map',
			'name' => esc_html__( 'Map', 'coronar' )
		];

        $tabs['assignments'] = [
            'icon' => 'flag',
            'name' => esc_html__( 'Assignments', 'coronar' )
        ];

		$tabs['css'] = [
			'icon' => 'code',
			'name' => esc_html__( 'Custom CSS', 'coronar' )
		];

		/** Activation tab enable only if plugin have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( (int)$plugin_id > 0 ) {
			$tabs['activation'] = [
				'icon' => 'vpn_key',
				'name' => esc_html__( 'Activation', 'coronar' )
			];
		}

		$tabs['status'] = [
			'icon' => 'info',
			'name' => esc_html__( 'Status', 'coronar' )
		];

		$tabs['uninstall'] = [
			'icon' => 'delete_sweep',
			'name' => esc_html__( 'Uninstall', 'coronar' )
		];

		/** Render Tabs. */
		?>
        <aside class="mdc-drawer">
            <div class="mdc-drawer__content">
                <nav class="mdc-list">

                    <div class="mdc-drawer__header mdc-plugin-fixed">
                        <!--suppress HtmlUnknownAnchorTarget -->
                        <a class="mdc-list-item mdp-plugin-title" href="#wpwrap">
                            <i class="mdc-list-item__graphic" aria-hidden="true">
                                <img src="<?php echo esc_attr( Coronar::$url . 'images/logo-color.svg' ); ?>" alt="<?php echo esc_html__( 'Coronar', 'coronar' ) ?>">
                            </i>
                            <span class="mdc-list-item__text">
                                <?php echo esc_html__( 'Coronar', 'coronar' ) ?>
                                <sup><?php echo esc_html__( 'ver.', 'coronar' ) . esc_html( Coronar::$version ); ?></sup>
                            </span>
                        </a>
                        <button type="submit" name="submit" id="submit"
                                class="mdc-button mdc-button--dense mdc-button--raised">
                            <span class="mdc-button__label"><?php echo esc_html__( 'Save changes', 'coronar' ) ?></span>
                        </button>
                    </div>

                    <hr class="mdc-plugin-menu">
                    <hr class="mdc-list-divider">
                    <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Plugin settings', 'coronar' ) ?></h6>

					<?php

					// Plugin settings tabs
					foreach ( $tabs as $tab => $value ) {
						$class = ( $tab == $current ) ? ' mdc-list-item--activated' : '';
						echo "<a class='mdc-list-item " . $class . "' href='?post_type=coronar_record&page=mdp_coronar_settings&tab=" . $tab . "'><i class='material-icons mdc-list-item__graphic' aria-hidden='true'>" . $value['icon'] . "</i><span class='mdc-list-item__text'>" . $value['name'] . "</span></a>";
					}

					/** Helpful links. */
					$this->support_link();

					/** Activation Status. */
					PluginActivation::get_instance()->display_status();

					?>
                </nav>
            </div>
        </aside>
		<?php
	}

	/**
	 * Displays useful links for an activated and non-activated plugin.
	 *
	 * @since 1.0.0
     *
     * @return void
	 **/
	public function support_link() { ?>

        <hr class="mdc-list-divider">
        <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Helpful links', 'coronar' ) ?></h6>

        <a class="mdc-list-item" href="https://docs.merkulov.design/tag/coronar/" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true"><?php echo esc_html__( 'collections_bookmark' ) ?></i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'Documentation', 'coronar' ) ?></span>
        </a>

		<?php if ( PluginActivation::get_instance()->is_activated() ) : /** Activated. */ ?>
            <a class="mdc-list-item" href="https://1.envato.market/coronarsupport2" target="_blank">
                <i class="material-icons mdc-list-item__graphic" aria-hidden="true">mail</i>
                <span class="mdc-list-item__text"><?php echo esc_html__( 'Get help', 'coronar' ) ?></span>
            </a>
            <a class="mdc-list-item" href="https://1.envato.market/cc-downloads" target="_blank">
                <i class="material-icons mdc-list-item__graphic" aria-hidden="true">thumb_up</i>
                <span class="mdc-list-item__text"><?php echo esc_html__( 'Rate this plugin', 'coronar' ) ?></span>
            </a>
		<?php endif; ?>

        <a class="mdc-list-item" href="https://1.envato.market/cc-merkulove" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true"><?php echo esc_html__( 'store' ) ?></i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'More plugins', 'coronar' ) ?></span>
        </a>
		<?php

	}

	/**
	 * Add plugin settings page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_settings_page() {

		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );

	}

	/**
	 * Create General Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
    public function tab_general() {

	    /** General Tab. */
	    $group_name = 'CoronarOptionsGroup';
	    $section_id = 'mdp_coronar_settings_page_general_section';
	    $option_name = 'mdp_coronar_settings';

	    /** Create settings section. */
	    register_setting( $group_name, $option_name );
	    add_settings_section( $section_id, '', null, $group_name );

	    /** Render Settings fields. */
        add_settings_field( 'cache_time', esc_html__( 'Cache Lifetime:', 'coronar' ),                       ['\Merkulove\Coronar\SettingsFields', 'cache_time' ], $group_name, $section_id );
	    add_settings_field( 'show_search', esc_html__( 'Show Search:', 'coronar' ),                         ['\Merkulove\Coronar\SettingsFields', 'show_search' ], $group_name, $section_id );
	    add_settings_field( 'responsive_table', esc_html__( 'Responsive Tables:', 'coronar' ),              ['\Merkulove\Coronar\SettingsFields', 'responsive_table' ], $group_name, $section_id );
	    add_settings_field( 'use_live', esc_html__( 'Live Data:', 'coronar' ),                              ['\Merkulove\Coronar\SettingsFields', 'use_live' ], $group_name, $section_id );
        add_settings_field( 'label_flag', esc_html__( 'Label for Flag:', 'coronar' ),                       ['\Merkulove\Coronar\SettingsFields', 'label_flag' ], $group_name, $section_id );
	    add_settings_field( 'label_country', esc_html__( 'Label for Country:', 'coronar' ),                 ['\Merkulove\Coronar\SettingsFields', 'label_country' ], $group_name, $section_id );
	    add_settings_field( 'label_province', esc_html__( 'Label for Province:', 'coronar' ),               ['\Merkulove\Coronar\SettingsFields', 'label_province' ], $group_name, $section_id );
	    add_settings_field( 'label_confirmed_total', esc_html__( 'Label for Total Confirmed:', 'coronar' ), ['\Merkulove\Coronar\SettingsFields', 'label_confirmed_total' ], $group_name, $section_id );
	    add_settings_field( 'label_confirmed_new', esc_html__( 'Label for New Confirmed:', 'coronar' ),     ['\Merkulove\Coronar\SettingsFields', 'label_confirmed_new' ], $group_name, $section_id );
	    add_settings_field( 'label_deaths_total', esc_html__( 'Label for Total Deaths:', 'coronar' ),       ['\Merkulove\Coronar\SettingsFields', 'label_deaths_total' ], $group_name, $section_id );
	    add_settings_field( 'label_deaths_new', esc_html__( 'Label for New Deaths:', 'coronar' ),           ['\Merkulove\Coronar\SettingsFields', 'label_deaths_new' ], $group_name, $section_id );
	    add_settings_field( 'label_recovered_total', esc_html__( 'Label for Total Recovered:', 'coronar' ), ['\Merkulove\Coronar\SettingsFields', 'label_recovered_total' ], $group_name, $section_id );
	    add_settings_field( 'label_recovered_new', esc_html__( 'Label for New Recovered:', 'coronar' ),     ['\Merkulove\Coronar\SettingsFields', 'label_recovered_new' ], $group_name, $section_id );
	    add_settings_field( 'label_chart_confirmed', esc_html__( 'Label for Chart Confirmed:', 'coronar' ), ['\Merkulove\Coronar\SettingsFields', 'label_chart_confirmed' ], $group_name, $section_id );
	    add_settings_field( 'label_chart_deaths', esc_html__( 'Label for Chart Deaths:', 'coronar' ),       ['\Merkulove\Coronar\SettingsFields', 'label_chart_deaths' ], $group_name, $section_id );
	    add_settings_field( 'label_chart_recovered', esc_html__( 'Label for Chart Recovered:', 'coronar' ), ['\Merkulove\Coronar\SettingsFields', 'label_chart_recovered' ], $group_name, $section_id );

    }

	/**
	 * Create Style Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function tab_style() {

		/** General Tab. */
		$group_name = 'CoronarStyleOptionsGroup';
		$section_id = 'mdp_coronar_style_settings_page_general_section';
		$option_name = 'mdp_coronar_style_settings';

		/** Create settings section. */
		register_setting( $group_name, $option_name );
		add_settings_section( $section_id, '', null, $group_name );

		/** Render Settings fields. */
		add_settings_field( 'confirmed_color', esc_html__( 'Confirmed Color:', 'coronar' ), ['\Merkulove\Coronar\SettingsFields', 'confirmed_color' ], $group_name, $section_id );
		add_settings_field( 'deaths_color', esc_html__( 'Deaths Color:', 'coronar' ),       ['\Merkulove\Coronar\SettingsFields', 'deaths_color' ], $group_name, $section_id );
		add_settings_field( 'recovered_color', esc_html__( 'Recovered Color:', 'coronar' ), ['\Merkulove\Coronar\SettingsFields', 'recovered_color' ], $group_name, $section_id );
		add_settings_field( 'bg_color', esc_html__( 'Background Color:', 'coronar' ),       ['\Merkulove\Coronar\SettingsFields', 'bg_color' ], $group_name, $section_id );
		add_settings_field( 'shadow', esc_html__( 'Shadow:', 'coronar' ),                   ['\Merkulove\Coronar\SettingsFields', 'shadow' ], $group_name, $section_id );
		add_settings_field( 'margin', esc_html__( 'Margin:', 'coronar' ),                   ['\Merkulove\Coronar\SettingsFields', 'margin' ], $group_name, $section_id );
		add_settings_field( 'padding', esc_html__( 'Padding:', 'coronar' ),                 ['\Merkulove\Coronar\SettingsFields', 'padding' ], $group_name, $section_id );
		add_settings_field( 'border_radius', esc_html__( 'Border Radius:', 'coronar' ),     ['\Merkulove\Coronar\SettingsFields', 'border_radius' ], $group_name, $section_id );
		add_settings_field( 'accent_color', esc_html__( 'Text Color:', 'coronar' ),         ['\Merkulove\Coronar\SettingsFields', 'accent_color' ], $group_name, $section_id );
		add_settings_field( 'font_size', esc_html__( 'Font Size:', 'coronar' ),             ['\Merkulove\Coronar\SettingsFields', 'font_size' ], $group_name, $section_id );
		add_settings_field( 'flag_size', esc_html__( 'Flag Size:', 'coronar' ),             ['\Merkulove\Coronar\SettingsFields', 'flag_size' ], $group_name, $section_id );

	}

	/**
	 * Create Map Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function tab_map() {

		/** General Tab. */
		$group_name = 'CoronarMapOptionsGroup';
		$section_id = 'mdp_coronar_map_settings_page_general_section';
		$option_name = 'mdp_coronar_map_settings';

		/** Create settings section. */
		register_setting( $group_name, $option_name );
		add_settings_section( $section_id, '', null, $group_name );

		/** Render Settings fields. */
		add_settings_field( 'api_key', esc_html__( 'API Key:', 'coronar' ),         ['\Merkulove\Coronar\SettingsFields', 'api_key' ], $group_name, $section_id );
		add_settings_field( 'land_color', esc_html__( 'Land Color:', 'coronar' ),   ['\Merkulove\Coronar\SettingsFields', 'land_color' ], $group_name, $section_id );
		add_settings_field( 'water_color', esc_html__( 'Water Color:', 'coronar' ), ['\Merkulove\Coronar\SettingsFields', 'water_color' ], $group_name, $section_id );

	}

	/**
	 * Create Custom CSS Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function tab_custom_css() {

		/** Custom CSS. */
		$group_name = 'CoronarCSSOptionsGroup';
		$section_id = 'mdp_coronar_settings_page_css_section';

		/** Create settings section. */
		register_setting( $group_name, 'mdp_coronar_css_settings' );
		add_settings_section( $section_id, '', null, $group_name );

    }

	/**
	 * Generate Settings Page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function settings_init() {

		/** General Tab. */
	    $this->tab_general();

		/** Style Tab. */
		$this->tab_style();

		/** Map Tab. */
		$this->tab_map();

		/** Create Assignments Tab. */
		AssignmentsTab::get_instance()->add_settings();

		/** Create Custom CSS Tab. */
		$this->tab_custom_css();

		/** Activation Tab. */
		PluginActivation::get_instance()->add_settings();

		/** Create Status Tab. */
		StatusTab::get_instance()->add_settings();

		/** Create Uninstall Tab. */
		UninstallTab::get_instance()->add_settings();

	}

	/**
	 * Add admin menu for plugin settings.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_admin_menu() {

		add_menu_page(
			esc_html__( 'Coronar Settings', 'coronar' ),
			esc_html__( 'Coronar', 'coronar' ),
			'manage_options',
			'mdp_coronar_settings',
			[ $this, 'options_page' ],
			'data:image/svg+xml;base64,' . base64_encode( file_get_contents( Coronar::$path . 'images/logo-menu.svg' ) ),
			'58.9448'// Always change digits after "." for different plugins.
		);

	}

	/**
	 * Plugin Settings Page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function options_page() {

		/** User rights check. */
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		} ?>
        <!--suppress HtmlUnknownTarget -->
        <form action='options.php' method='post'>
            <div class="wrap">

				<?php
				$tab = 'general';
				if ( isset ( $_GET['tab'] ) ) { $tab = $_GET['tab']; }

				/** Render "Coronar settings saved!" message. */
				SettingsFields::get_instance()->render_nags();

				/** Render Tabs Headers. */
				?><section class="mdp-aside"><?php $this->render_tabs( $tab ); ?></section><?php

				/** Render Tabs Body. */
				?><section class="mdp-tab-content mdp-tab-<?php echo esc_attr( $tab ) ?>"><?php

					/** General Tab. */
					if ( 'general' === $tab ) {
						echo '<h3>' . esc_html__( 'Coronar Settings', 'coronar' ) . '</h3>';
						settings_fields( 'CoronarOptionsGroup' );
						do_settings_sections( 'CoronarOptionsGroup' );

                    /** Style Tab. */
					} elseif ( 'style' === $tab ) {
                        echo '<h3>' . esc_html__( 'Style Settings', 'coronar' ) . '</h3>';
                        settings_fields( 'CoronarStyleOptionsGroup' );
                        do_settings_sections( 'CoronarStyleOptionsGroup' );

                    /** Map Tab. */
					} elseif ( 'map' === $tab ) {
						echo '<h3>' . esc_html__( 'Map Settings', 'coronar' ) . '</h3>';
						settings_fields( 'CoronarMapOptionsGroup' );
						do_settings_sections( 'CoronarMapOptionsGroup' );

                    /** Assignments Tab. */
					} elseif ( 'assignments' === $tab ) {
						echo '<h3>' . esc_html__( 'Assignments Settings', 'coronar' ) . '</h3>';
						settings_fields( 'CoronarAssignmentsOptionsGroup' );
						do_settings_sections( 'CoronarAssignmentsOptionsGroup' );
						AssignmentsTab::get_instance()->render_assignments();

                    /** Custom CSS Tab. */
					} elseif ( 'css' === $tab ) {
						echo '<h3>' . esc_html__( 'Custom CSS', 'coronar' ) . '</h3>';
						settings_fields( 'CoronarCSSOptionsGroup' );
						do_settings_sections( 'CoronarCSSOptionsGroup' );
						SettingsFields::get_instance()->custom_css();

                    /** Activation Tab. */
					} elseif ( 'activation' === $tab ) {
						settings_fields( 'CoronarActivationOptionsGroup' );
						do_settings_sections( 'CoronarActivationOptionsGroup' );
						PluginActivation::get_instance()->render_pid();

                    /** Status tab. */
					} elseif ( 'status' === $tab ) {
						echo '<h3>' . esc_html__( 'System Requirements', 'coronar' ) . '</h3>';
						StatusTab::get_instance()->render_form();

					} /** Uninstall Tab. */
                    elseif ( 'uninstall' === $tab ) {
						echo '<h3>' . esc_html__( 'Uninstall Settings', 'coronar' ) . '</h3>';
						UninstallTab::get_instance()->render_form();
					}

					?>
                </section>
            </div>
        </form>

		<?php
	}

	/**
	 * Get plugin settings with default values.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 **/
	public function get_options() {

		/** Default values. */
		$defaults = [

			# General Tab
			'cache_time'            => '60',
			'label_flag'            => 'Flag',
			'label_country'         => 'Country',
			'label_province'        => 'Province',
			'label_confirmed_new'   => 'New Confirmed',
			'label_confirmed_total' => 'Total Confirmed',
			'label_deaths_new'      => 'New Deaths',
			'label_deaths_total'    => 'Total Deaths',
			'label_recovered_new'   => 'New Recovered',
			'label_recovered_total' => 'Total Recovered',
			'label_chart_confirmed' => 'Chart Confirmed',
			'label_chart_deaths'    => 'Chart Deaths',
			'label_chart_recovered' => 'Chart Recovered',
			'show_search'           => 'on',
			'responsive_table'      => 'on',
			'use_live'              => 'off',

            # Style Tab
			'accent_color'          => '#0274e6',
			'bg_color'              => '#ffffff',
			'confirmed_color'       => '#ffa000',
            'deaths_color'          => '#ff3d00',
            'recovered_color'       => '#2e7d32',
            'shadow'                => 'on',
            'margin'                => '20',
			'padding'               => '20',
			'border_radius'         => '15',
			'font_size'             => 'medium',
            'flag_size'             => '20',

			# Map Tab
            'api_key'               => '',
            'land_color'            => '#ffffff',
			'water_color'            => '#ffffff',

			# Custom CSS Tab
            'custom_css'       => '',

        ];

		/** General Tab Options. */
		$options = get_option( 'mdp_coronar_settings' );
		$results = wp_parse_args( $options, $defaults );

		/** Style tab Options. */
		$style_settings = get_option( 'mdp_coronar_style_settings' );
		$results = wp_parse_args( $style_settings, $results );

		/** Map tab Options. */
		$map_settings = get_option( 'mdp_coronar_map_settings' );
		$results = wp_parse_args( $map_settings, $results );

		/** Custom CSS tab Options. */
		$css_settings = get_option( 'mdp_coronar_css_settings' );
		$results = wp_parse_args( $css_settings, $results );

		$this->options = $results;

	}

	/**
	 * Main Settings Instance.
	 *
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
	 * @return Settings
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Settings ) ) {
			self::$instance = new Settings;
		}

		return self::$instance;
	}

} // End Class Settings.
