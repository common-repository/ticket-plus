<?php

/**
 * @package TicketPlus
 */

namespace Tidplus\base;

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class Enqueue extends BaseController
{
	// Method for registering admin script enqueue hook to this plugin
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );
	}

	// Method to enqueue css and js specifically for this plugin so that they does not conflict with other plugins
	function enqueue( $hook ) {
		if (    //$hook != 'toplevel_page_tidplus-manage-appointments' ||
                        $hook == 'toplevel_page_tidplus-manage-appointments' ||
			$hook == 'ticket-plus_page_tidplus-manage-prescriptions' ||
			$hook == 'ticket-plus_page_tidplus-manage-orders' ||
			$hook == 'ticket-plus_page_tidplus-manage-frontend' ||
			$hook == 'ticket-plus_page_tidplus-manage-tickets' ||
			$hook == 'ticket-plus_page_tidplus-settings') {
				$this->enqueue_styles();
				$this->enqueue_scripts();
		}
	}
        
        function enqueue_frontend() {
		
                $this->enqueue_styles_frontend();
                $this->enqueue_scripts_frontend();
        }
	

	// Method for enqueueing stylesheets
	private function enqueue_styles() {
		wp_enqueue_style( 'bootstrap-css', $this->plugin_url . 'assets/bootstrap.css' );
		wp_enqueue_style( 'font-awesome', $this->plugin_url . 'assets/font-awesome/css/font-awesome.css' );
		wp_enqueue_style( 'jquery-ui', $this->plugin_url . 'assets/jquery-ui/jquery-ui.css' );
		wp_enqueue_style( 'select2-css', $this->plugin_url . 'assets/select2/css/select2.css' );
		wp_enqueue_style( 'toastr-css', $this->plugin_url . 'assets/toastr/toastr.css' );
		wp_enqueue_style( 'daterangepicker-css', $this->plugin_url . 'assets/daterangepicker/daterangepicker.css' );
		wp_enqueue_style( 'customstyle', $this->plugin_url . 'assets/custom.css' );
	}
        
        // Method for enqueueing stylesheets in frontend
	private function enqueue_styles_frontend() {
		wp_enqueue_style( 'font-awesome', $this->plugin_url . 'assets/font-awesome/css/font-awesome.css' );
		wp_enqueue_style( 'toastr-css', $this->plugin_url . 'assets/toastr/toastr.css' );
		wp_enqueue_style( 'customstyle', $this->plugin_url . 'assets/custom.css' );
	}

	// Method for enqueueing javascripts
	private function enqueue_scripts() {
		wp_enqueue_script( 'tether-js', $this->plugin_url . 'assets/tether.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'bootstrap-js', $this->plugin_url . 'assets/bootstrap.js', array( 'jquery' ) );
		wp_enqueue_script( 'select2-js', $this->plugin_url . 'assets/select2/js/select2.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-form-js', $this->plugin_url . 'assets/jquery.form.js', array( 'jquery' ) );
		wp_enqueue_script( 'toastr-js', $this->plugin_url . 'assets/toastr/toastr.js', array( 'jquery' ) );
		wp_enqueue_script( 'daterangepicker-js', $this->plugin_url . 'assets/daterangepicker/daterangepicker.js', array( 'jquery' ) );
		wp_enqueue_script( 'printThis-js', $this->plugin_url . 'assets/printThis.js', array( 'jquery' ) );
		wp_enqueue_script( 'blockui-js', $this->plugin_url . 'assets/blockui.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'customscript', $this->plugin_url . 'assets/custom.js', array( 'jquery' ) );
                wp_enqueue_script( 'tickets-backend', $this->plugin_url . 'assets/tickets-backend/tickets-backend.js', array( 'jquery' ) );
	}
        
        // Method for enqueueing javascripts
	private function enqueue_scripts_frontend() {
		wp_enqueue_script( 'tether-js', $this->plugin_url . 'assets/tether.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'toastr-js', $this->plugin_url . 'assets/toastr/toastr.js', array( 'jquery' ) );
		wp_enqueue_script( 'blockui-js', $this->plugin_url . 'assets/blockui.js', array( 'jquery' ) );
		wp_enqueue_script( 'customscript', $this->plugin_url . 'assets/custom.js', array( 'jquery' ) );
                wp_enqueue_script( 'jquery-form-js', $this->plugin_url . 'assets/jquery.form.js', array( 'jquery' ) );
	}
}