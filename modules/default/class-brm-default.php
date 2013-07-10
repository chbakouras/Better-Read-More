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
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );

			$current_theme = wp_get_theme();		

			if ( in_array( md5( $current_theme['Name'] ), $this->settings['themes'] ) ) {

				add_action( 'wp_enqueue_scripts', array( $this, add_scripts ) ); //Add front-end CSS and Javascript
				add_filter( 'the_content', array( $this, read_more ) ); //Filter the more tag

			}

		}

		/**
		 * Adds frontend CSS and JavaScript
		 *
		 * @return  void
		 */
		public function add_scripts() {

			if ( is_singular() ) {

				wp_register_script(
					'brm', 
					$this->core->plugin->globals['plugin_url'] . "/modules/default/js/brm.js",
					array( 'jquery' )
				);

				wp_enqueue_script( 'brm' );

				if ( isset( $this->settings['use_css'] ) && $this->settings['use_css'] == 1 ) {

					$css = isset( $this->settings['custom_css'] ) ? esc_textarea( $this->settings['custom_css'] ) : '';
					wp_add_inline_style( 'brm_styles', $css );

				} else {

					wp_register_style( 'brm_styles', $this->core->plugin->globals['plugin_url'] . 'modules/default/css/brm.css' );
					wp_enqueue_style( 'brm_styles' );

				}

			}

		}

		/**
		 * Add Away mode Javascript
		 * 
		 * @return void
		 */
		public function admin_script() {

			if ( strpos( get_current_screen()->id,'settings_page_better-read-more' ) !== false ) {
				
				wp_enqueue_script( 'brm_admin', $this->core->plugin->globals['plugin_url'] . 'modules/default/js/brm-admin.js', 'jquery', $this->core->plugin->globals['plugin_build'] );
			}

		}

		/**
		 * Filters more tag to allow for expanded content
		 * 
		 * @param  string $content The content
		 * @return string          The content
		 */
		public function read_more( $content ) {

			global $post;

			if ( is_singular() ) {
				
				if ( strpos( $content, '<!--more-->' ) ) {

					$content_parts = explode( '<!--more-->', $content );

				} else {

					$content_parts = explode( '<span id="more-' . $post->ID . '"></span>', $content );

				}		

				$html = $content_parts[0];

				$more_text = ( isset( $this->settings['more_text'] ) ? sanitize_text_field( $this->settings['more_text'] ) : '(more)' );

				$html .='</p><div class="brm">' . $content_parts[1] . '</div><a href="#" class="more-link">' . $more_text . '</a>';

			} else {

				$html = $content;

			}

			return $html;

		}

		/**
		 * Sets up menu item for Better Read More
		 * 
		 * @param array $available_pages array of BWPS settings pages
		 */
		public function add_sub_page( $available_pages ) {

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
		public function add_admin_meta_boxes( $available_pages ) {

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
		public function initialize_admin() {

			//add primary settings section
			add_settings_section(  
				'brm_settings_1',
				__( 'Configure Better Read More', 'better-read-more' ),
				array( $this, 'brm_general_options_callback' ),
				'settings_page_better-read-more'
			);

			//add custom css settings section
			add_settings_section(  
				'brm_settings_2',
				__( 'Configure Better Read More', 'better-read-more' ),
				array( $this, 'brm_general_options_callback' ),
				'settings_page_better-read-more'
			);

			//add themes field
			add_settings_field(   
				'brm[themes]', 
				__( 'Themes', 'better-read-more' ),
				array( $this, 'brm_select_theme_callback' ),
				'settings_page_better-read-more',
				'brm_settings_1'
			);

			//add more field
			add_settings_field(   
				'brm[more_text]', 
				__( 'More Text', 'better-read-more' ),
				array( $this, 'brm_more_text_callback' ),
				'settings_page_better-read-more',
				'brm_settings_1'
			);

			//add use custom css field
			add_settings_field(   
				'brm[use_css]', 
				__( 'Use CSS', 'better-read-more' ),
				array( $this, 'brm_use_css_callback' ),
				'settings_page_better-read-more',
				'brm_settings_1'
			);

			//add custom css entry field
			add_settings_field(   
				'brm[custom_css]', 
				__( 'Custom CSS', 'better-read-more' ),
				array( $this, 'brm_custom_css_callback' ),
				'settings_page_better-read-more',
				'brm_settings_2'
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
		public function brm_general_settings_callback() {}

		/**
		 * echos theme Field
		 * 
		 * @param  array $args field arguements
		 * @return void
		 */
		public function brm_select_theme_callback( $args ) {

			$available_themes = wp_get_themes();
			$selected_themes = $this->settings['themes'];

			$html = '<select id="brm_themes" name="brm[themes][]" multiple="multiple">';

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
		 * echos more text Field
		 * 
		 * @param  array $args field arguements
		 * @return void
		 */
		public function brm_more_text_callback( $args ) {

			$text = ( isset( $this->settings['more_text'] )? $this->settings['more_text'] : '(more)' );
			
			$html = '<input type="text" name="brm[more_text] id="brm_more_text value="' . $text . '" /><br />';

			$html .= sprintf( '<em>%s</em>', __( 'This is the text that will display for the more link.', 'better-read-more' ) );

			echo $html;

		}

		/**
		 * echos default css Field
		 * 
		 * @param  array $args field arguements
		 * @return void
		 */
		public function brm_use_css_callback( $args ) {

			$html = '<input type="checkbox" id="brm_use_css" name="brm[use_css]" value="1" ' . checked( 1, $this->settings['use_css'], false ) . '/><br />';  
			$html .= sprintf( '<em>%s</em>', __( 'Check this box to enter custom CSS to style the more button.', 'better-read-more' ) );

			echo $html;

		}

		/**
		 * echos custom css Field
		 * 
		 * @param  array $args field arguements
		 * @return void
		 */
		public function brm_custom_css_callback( $args ) {

			//if we have save css, use it
			if ( isset( $this->settings['custom_css'] ) ) {

				$css = esc_textarea( $this->settings['custom_css'] );

			} else { //load the default css from the plugin file

				$url = wp_nonce_url( 'options.php?page=brm', 'better-read-more' );

				if ( false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $form_fields ) ) ) {
					return true; // stop the normal page form from displaying
				}

				if ( ! WP_Filesystem( $creds ) ) {

	    			// our credentials were no good, ask the user for them again
	    			request_filesystem_credentials( $url, $method, true, false, $form_fields );
	    			return true;

				}

				global $wp_filesystem;

				if ( $wp_filesystem->exists( $this->core->plugin->globals['plugin_dir'] . 'modules/default/css/brm.css' ) ) { //check for existence

					$css = $wp_filesystem->get_contents( $this->core->plugin->globals['plugin_dir'] . 'modules/default/css/brm.css' );

				}

			}

			$html = '<textarea name="brm[custom_css]" id="brm_custom_css" style="width: 100%;" rows="10">' . $css . '</textarea><br />';
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

			printf( '<form name="%s" method="post" action="options.php">', get_current_screen()->id );

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

			if ( isset( $input['more_text'] ) ) {
				$output['more_text'] = sanitize_text_field( $input['more_text'] );
			}

			$output['use_css'] = ( isset( $input['use_css'] ) && $input['use_css'] == 1 ) ? 1 : 0;

			$output['custom_css'] = isset( $input['custom_css' ] ) ? esc_textarea( $input['custom_css' ] ) : '';

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