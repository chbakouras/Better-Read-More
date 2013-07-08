<?php

if ( ! class_exists( 'BRM_Default') ) {

	class BRM_Default {

		private static $instance = null;

		private 
			$core,
			$settings;

		private function __construct( $core ) {

			$this->core = $core;
			$this->settings = get_site_option( 'brm' );

			add_action( $this->core->plugin->globals['plugin_hook'] . '_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) );
			add_filter( $this->core->plugin->globals['plugin_hook'] . '_add_admin_sub_pages', array( $this, 'add_sub_page' ) );
			add_action( 'admin_init', array( $this, 'initialize_admin' ) );

		}

		/**
		 * Sets up menu item for Better Read More
		 * 
		 * @param array $available_pages array of BWPS settings pages
		 */
		function add_sub_page( $available_pages ) {

			$available_pages[] = add_submenu_page(
				$this->core->plugin->globals['plugin_hook'],
				__( 'Default Module', 'better-read-more' ),
				__( 'Default', 'better-read-more' ),
				$this->core->plugin->globals['plugin_access_lvl'],
				$available_pages[0] . '-default',
				array( $this->core, 'render_page' )
			);

			return $available_pages;

		}

		/**
		 * Add meta boxes to primary options pages
		 * 
		 * @param array $available_pages array of available page_hooks
		 */
		function add_admin_meta_boxes( $available_pages ) {

			//add metaboxes
			add_meta_box( 
				'default_module_settings', 
				__( 'Default Module Settings', 'better-read-more' ),
				array( $this, 'metabox_advanced_settings' ),
				'settings_page_better-read-more"',
				'normal',
				'core'
			);

		}

		/**
		 * Execute admin initializations
		 * 
		 * @return void
		 */
		function initialize_admin() {

			add_settings_section(  
				'brm_settings',
				__( 'Configure Better Read More', 'better-read-more' ),
				array( $this, 'brm_general_options_callback' ),
				'settings_page_better-read-more'
			);

			add_settings_field(   
				'brm[themes]', 
				__( 'Themes', 'better-read-more' ),
				array( $this, 'brm_select_theme_callback' ),
				'settings_page_better-read-more',
				'brm_settings'
			);

			register_setting(  
				'settings_page_better-read-more',
				'brm',
				array( $this, 'sanitize_brm_options' )
			);


		}

		/**
		 * Settings section callback
		 *
		 * Can be used for an introductory setction or other output. Currently is used by both settings sections.
		 * 
		 * @return void
		 */
		function brm_general_settings_callback() {}

		/**
		 * echos theme Field
		 * 
		 * @param  array $args field arguements
		 * @return void
		 */
		function brm_select_theme_callback( $args ) {

			$available_themes = wp_get_themes();
			$selected_themes = $this->settings['themes'];

			$html = '<select id="brm[themes]" name="brm[themes][]" multiple="multiple">';

			foreach ( $available_themes as $theme ) {
				
				$theme_hash = md5( $theme['Name'] );

				if ( in_array( $theme_hash, $selected_themes ) ) {
					$selected = true;
				} else {
					$selected = false;
				}

				$html .= '<option value="' . $theme_hash . '" ' . selected( true, $selected, false ) . '/>' . $theme['Name'] . '</option>';

			}

			$html .= '</select>';
			$html .= sprintf( '<em>%s</em>', __( 'Hold down the "ctrl" key on Windows or the "command" key on Mac to select multiple themes.', 'better-read-more' ) );

			echo $html;

		}

		/**
		 * Render the settings metabox
		 * 
		 * @return void
		 */
		public function metabox_advanced_settings() {

			_e( 'Select which themes that you would like to use Better Read More on.', 'better-read-more' );

			echo '<form name="' . get_current_screen()->id . '" method="post" action="options.php">';

			$this->core->do_settings_sections( 'settings_page_better-read-more', false );

			echo '<p>' . PHP_EOL;
			settings_fields( 'settings_page_better-read-more' );
			echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save Changes', 'better-read-more' ) . '" />' . PHP_EOL;

			echo '</p>' . PHP_EOL;

			echo '</form>';

		}

		/**
		 * Sanitize and validate input
		 * 
		 * @param  Array $input  array of input fields
		 * @return Array         Sanitized array
		 */
		public function sanitize_brm_options( $input ) {

			foreach ( $input['themes'] as $theme ) {

				if ( preg_match( '/^[a-f0-9]{32}$/', $theme ) !== 0 && preg_match( '/^[a-f0-9]{32}$/', $theme ) !== false ) {
					$output['themes'][] = $theme;
				}

			}

			return $output;

		}

		/**
		 * Start the Springbox module
		 * 
		 * @param  BRM_Core    $core     Instance of core plugin class
		 * @return BRM_Default 			The instance of the BRM_Default class
		 */
		public static function start( $core ) {

			if ( ! isset( self::$instance ) || self::$instance === null ) {
				self::$instance = new self( $core );
			}

			return self::$instance;

		}

	}

}