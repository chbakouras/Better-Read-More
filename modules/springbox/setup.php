<?php

if ( ! class_exists( 'BRM_Springbox_Setup' ) ) {

	class BRM_Springbox_Setup {

		private 
			$hook;

		function __construct() {
			global $brm_setup_action;

			//Important, this must be manually set in each module
			$this->hook = 'better-read-more';

			if ( isset( $brm_setup_action ) ) {
				
				switch ( $brm_setup_action ) {

					case 'activate':
						$this->execute_activate();
						break;
					case 'upgrade':
						$this->execute_upgrade();
						break;
					case 'deactivate':
						$this->execute_deactivate();
						break;
					case 'uninstall':
						$this->execute_uninstall();
						break;

				}

			} else {
				wp_die( 'error' );
			}

		}

		/**
		 * Execute module activation
		 * 
		 * @return void
		 */
		function execute_activate() {
			
		}

		/**
		 * Execute module deactivation
		 * 
		 * @return void
		 */
		function execute_deactivate() {
			
		}

		/**
		 * Execute module uninstall
		 * 
		 * @return void
		 */
		function execute_uninstall() {
			
			$this->execute_deactivate();

		}

		/**
		 * Execute module upgrade
		 * 
		 * @return void
		 */
		function execute_upgrade() {
			
		}

	}

}

new BRM_Springbox_Setup();