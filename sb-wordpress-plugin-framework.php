<?php
/*
	Plugin Name: Springbox WordPress Plugin Framework
	Plugin URI: http://springbox.com
	Description: Plugin framework for Springbox WordPress Projects
	Version: 0.0.1
	Text Domain: springbox_wordpress_plugin_framework
	Domain Path: /languages
	Author: Springbox
	Author URI: http://springbox.com
	License: GPLv2
	Copyright 2013  Springbox  (email : opensource@springbox.com)
*/

if ( ! class_exists( 'SB_WordPress_Plugin_Framework' ) ) {


	/**
	 * Plugin class used to create plugin object and load both core and needed modules
	 */
	final class SB_WordPress_Plugin_Framework {

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
				'plugin_homepage' 		=> 'http://www.wordpress.org', //The plugins homepage on WordPress.org
				'plugin_hook'			=> 'springbox_wordpress_plugin_framework', //the hook for text calls and other areas
				'plugin_name' 			=> __( 'Springbox WordPress Plugin Framework', '[insert text domain string]' ), //the name of the plugin
				'plugin_url' 			=> plugin_dir_url( __FILE__ ), //the URL of the plugin directory
				'support_page' 			=> 'http://wordpress.org/', //address of the WordPress support forums for the plugin
				'wordpress_page'		=> 'http://wordpress.org/', //plugin's page in the WordPress.org Repos
			);

			$this->top_level_menu = true; //true if top level menu, else false
			$this->menu_name = __( 'Springbox', $this->globals['plugin_hook'] ); //main menu item name

			//the following options must only be set if it's a top-level section
			$this->settings_page = true; //when using top_level menus this will always create a "Dashboard" page. Should it create a settings page as well?
			$this->menu_icon = $this->globals['plugin_url'] . 'img/sb-small.png'; //image icon 
			$this->dashboard_menu_title = __( 'Dashboard', '[insert text domain string]' ); //the name of the dashboard menu item (if different "Dashboard")
			$this->settings_menu_title = __( 'Settings', '[insert text domain string]' ); //the name of the settings menu item (if different from "Settings")
			$this->dashboard_page_name = __( 'Dashboard', '[insert text domain string]' ); //page name - appears after plugin name on the dashboard page
			$this->settings_page_name = __( 'Options', '[insert text domain string]' ); //page name - appears after plugin name on the dashboard page
			

			//load core functionality for admin use
			require_once( $this->globals['plugin_dir'] . 'inc/class-sb-core.php' );
			$this->core = SB_Core::start( $this );

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
			require_once( $this->globals['plugin_dir'] . 'modules/default-module/class-default-module.php' );
			Default_Module::start( $this->core );

			//load Springbox module
			require_once( $this->globals['plugin_dir'] . 'modules/springbox/class-springbox.php' );
			Springbox::start( $this->core );
			
		}

		/**
		 * Start the plugin
		 * 
		 * @return SB_WordPress_Plugin_Framework     The instance of the plugin
		 */
		public static function start() {

			if ( ! isset( self::$instance ) || self::$instance === null ) {
				self::$instance = new self;
			}

			return self::$instance;

		}

	}

}

SB_WordPress_Plugin_Framework::start();
