<?php

if ( ! class_exists( 'Default_Module') ) {

	class Default_Module {

		private static $instance = null;

		private 
			$core;

		private function __construct( $core ) {

			$this->core = $core;

			add_action( $this->core->plugin->globals['plugin_hook'] . '_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) );
			add_filter( $this->core->plugin->globals['plugin_hook'] . '_add_admin_sub_pages', array( $this, 'add_sub_page' ) );
			add_action( 'admin_init', array( $this, 'define_settings' ) );

		}

		function add_sub_page( $available_pages ) {

			$available_pages[] = add_submenu_page(
				$this->core->plugin->globals['plugin_hook'],
				__( 'Default Module', '[insert text domain string]' ),
				__( 'Default', '[insert text domain string]' ),
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
				'default_module_intro', 
				__( 'Default Module Intro', '[insert text domain string]' ),
				array( $this, 'metabox_normal_intro' ),
				'springbox_page_toplevel_page_springbox_wordpress_plugin_framework-default',
				'normal',
				'core'
			);

			//add metaboxes
			add_meta_box( 
				'default_module_settings', 
				__( 'Default Module Settings', '[insert text domain string]' ),
				array( $this, 'metabox_advanced_settings' ),
				'springbox_page_toplevel_page_springbox_wordpress_plugin_framework-default',
				'advanced',
				'core'
			);

		}

		function define_settings() {

			add_settings_section(  
				'general_settings_section',
				__( 'Default Module Options', '[insert text domain string]' ),
				array( $this, 'sandbox_general_options_callback' ),
				'springbox_page_toplevel_page_springbox_wordpress_plugin_framework-default'
			);

			add_settings_field(   
				'show_header', 
				__( 'Header', '[insert text domain string]' ),
				array( $this, 'sandbox_toggle_header_callback' ),
				'springbox_page_toplevel_page_springbox_wordpress_plugin_framework-default',
				'general_settings_section',
				array(
					__( 'Activate this setting to display the header.', '[insert text domain string]' ),
				)
			);

			add_settings_field(   
				'show_footer', 
				__( 'Footer', '[insert text domain string]' ),
				array( $this, 'sandbox_toggle_footer_callback' ),
				'springbox_page_toplevel_page_springbox_wordpress_plugin_framework-default',
				'general_settings_section',
				array(
					__( 'Activate this setting to display the footer.', '[insert text domain string]' ),
				)
			);

			register_setting(  
				'springbox_page_toplevel_page_springbox_wordpress_plugin_framework-default',
				'show_header'
			);  

			register_setting(  
				'springbox_page_toplevel_page_springbox_wordpress_plugin_framework-default',
				'show_footer'
			);  


		}

		function sandbox_general_options_callback() {
			echo '<p>Select which areas of content you wish to display.</p>';
		}

		function sandbox_toggle_header_callback( $args ) {

			$html = '<input type="checkbox" id="show_header" name="show_header" value="1" ' . checked( 1, get_option( 'show_header' ), false ) . '/>';   
			$html .= '<label for="show_header"> '  . $args[0] . '</label>';   

			echo $html;

		}

		function sandbox_toggle_footer_callback( $args ) {

			$html = '<input type="checkbox" id="show_footer" name="show_footer" value="1" ' . checked( 1, get_option( 'show_footer' ), false ) . '/>';   
			$html .= '<label for="show_footer"> '  . $args[0] . '</label>';   

			echo $html;

		}

		/**
		 * Build and echo the content sidebar metabox
		 * 
		 * @return void
		 */
		public function metabox_normal_intro() {

			$content = '<p>This is a default module you can use as a base to figure out what you want to do elsewhere</p>';

			echo $content;

		}

		public function metabox_advanced_settings() {

			echo '<form name="' . get_current_screen()->id . '" method="post" action="options.php">';

			$this->core->do_settings_sections( 'springbox_page_toplevel_page_springbox_wordpress_plugin_framework-default', false );

			echo '<p>' . PHP_EOL;
			settings_fields( 'springbox_page_toplevel_page_springbox_wordpress_plugin_framework-default' );
			echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save Changes', '[insert text domain string]' ) . '" />' . PHP_EOL;

			echo '</p>' . PHP_EOL;

			echo '</form>';

		}

		/**
		 * Start the Springbox module
		 * 
		 * @param  SB_Core    $core     Instance of core plugin class
		 * @return Springbox 			The instance of the Springbox class
		 */
		public static function start( $core ) {

			if ( ! isset( self::$instance ) || self::$instance === null ) {
				self::$instance = new self( $core );
			}

			return self::$instance;

		}

	}

}