<?php
/*
	Plugin Name: Better Read More
	Plugin URI: http://springbox.com
	Description: Use WordPress built-in <!--more--> tag to shorten text on pages.
	Version: 1.3
	Text Domain: better-read-more
	Domain Path: /languages
	Author: Springbox
	Author URI: http://springbox.com
	License: GPLv2
	Copyright 2013  Springbox  (email : opensource@springbox.com)
*/

if ( ! class_exists( 'SB_Better_Read_More' ) ) {


	/**
	 * Plugin class used to create plugin object and load both core and needed modules
	 */
	final class SB_Better_Read_More {

		private static $instance = null; //instantiated instance of this plugin

		public //see documentation upon instantiation 
			$core,
			$dashboard_menu_title,
			$dashboard_page_name,
			$globals,
			$menu_icon,
			$menu_name,
			$settings_menu_title,
			$settings_page,
			$settings_page_name,
			$top_level_menu;

		/**
		 * Default plugin execution used for settings defaults and loading components
		 * 
		 * @return void
		 */
		private function __construct() {

			//Set plugin defaults
			$this->globals = array(
				'plugin_build'			=> 1, //plugin build number - used to trigger updates
				'plugin_file'			=> __FILE__, //the main plugin file
				'plugin_access_lvl' 	=> 'manage_options', //Access level required to access plugin options
				'plugin_dir' 			=> plugin_dir_path( __FILE__ ), //the path of the plugin directory
				'plugin_homepage' 		=> 'http://wordpress.org/plugins/better-read-more/', //The plugins homepage on WordPress.org
				'plugin_hook'			=> 'better-read-more', //the hook for text calls and other areas
				'plugin_name' 			=> __( 'Better Read More', 'better-read-more' ), //the name of the plugin
				'plugin_url' 			=> plugin_dir_url( __FILE__ ), //the URL of the plugin directory
				'support_page' 			=> 'http://wordpress.org/support/plugin/better-read-more', //address of the WordPress support forums for the plugin
				'wordpress_page'		=> 'http://wordpress.org/plugins/better-read-more/', //plugin's page in the WordPress.org Repos
			);

			$this->top_level_menu = false; //true if top level menu, else false
			$this->menu_name = __( 'Better Read More', 'better-read-more' ); //main menu item name

			//load core functionality for admin use
			require_once( $this->globals['plugin_dir'] . 'inc/class-brm-core.php' );
			$this->core = BRM_Core::start( $this );

			//load modules
			$this->load_modules();

			//builds admin menus after modules are loaded
			if ( is_admin() ) {
				$this->core->build_admin(); 
			}
			
		}

		/**
		 * Loads required plugin modules
		 *
		 * Note: Do not modify this area other than to specify modules to load. 
		 * Build all functionality into the appropriate module.
		 * 
		 * @return void
		 */
		public function load_modules() {

			//load Default module
			require_once( $this->globals['plugin_dir'] . 'modules/default/class-brm-default.php' );
			BRM_Default::start( $this->core );

			//load Springbox module
			require_once( $this->globals['plugin_dir'] . 'modules/springbox/class-brm-springbox.php' );
			BRM_Springbox::start( $this->core );
			
		}

		/**
		 * Start the plugin
		 * 
		 * @return SB_Better_Read_More     The instance of the plugin
		 */
		public static function start() {

			if ( ! isset( self::$instance ) || self::$instance === null ) {
				self::$instance = new self;
			}

			return self::$instance;

		}

	}

}

SB_Better_Read_More::start();
