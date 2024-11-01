<?php

/**
 * @package TicketPlus
 */

namespace Tidplus\base;

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class AjaxPosts extends BaseController
{
	protected $page_to_load;

	// Arbitrary parameters that might be sent during any ajax call
	public static $param1;
	public static $param2;
	public static $param3;
	public static $param4;
	public static $param5;
	public static $param6;
	public static $param7;
	public static $param8;
	public static $param9;

	// Method for registering ajax submit hook to the plugin
	public function register() {

                add_action( 'wp_ajax_nopriv_tidplus' , array( $this, 'post' ) );
                add_action( 'wp_ajax_tidplus' , array( $this, 'post' ) );
	}

	// Method for sanitizing all the received parameters and assign it to the public variables declared in this class
	public function post() {
		$task = sanitize_text_field( $_POST['task'] );

		if ( isset ( $_POST['page'] ) )
			$this->page_to_load = sanitize_text_field( $_POST['page'] );

		if ( isset ( $_POST['param1'] ) )
			self::$param1 = sanitize_text_field( $_POST['param1'] );

		if ( isset ( $_POST['param2'] ) )
			self::$param2 = sanitize_text_field( $_POST['param2'] );

		if ( isset ( $_POST['param3'] ) )
			self::$param3 = sanitize_text_field( $_POST['param3'] );

		if ( isset ( $_POST['param4'] ) )
			self::$param4 = sanitize_text_field( $_POST['param4'] );

		if ( isset ( $_POST['param5'] ) )
			self::$param5 = sanitize_text_field( $_POST['param5'] );

		if ( isset ( $_POST['param6'] ) )
			self::$param6 = sanitize_text_field( $_POST['param6'] );

		if ( isset ( $_POST['param7'] ) )
			self::$param7 = sanitize_text_field( $_POST['param7'] );

		if ( isset ( $_POST['param8'] ) )
			self::$param8 = sanitize_text_field( $_POST['param8'] );

		if ( isset ( $_POST['param9'] ) )
			self::$param9 = sanitize_text_field( $_POST['param9'] );

		$this->handle_ajax_posts( $task );
	}

	private function verify_ajax_nonce() {
		if ( isset( $_POST['nonce'] ) ) {
			if ( wp_verify_nonce( $_POST['nonce'], 'tidplus-ajax-nonce' ) ) {
				return true;
			}
			return false;
		}
		return false;
	}

	// Method for determining the ajax request type and send feedback accordingly
	private function handle_ajax_posts( $task ) {
		if ( $task == 'load_modal_page' )
			$this->load_modal_page();

		if ($task == 'load_response') {
			if ( $this->verify_ajax_nonce() == true ) {
				$this->load_response();
			}
		}

	}

	// Method for presenting modal with contents sent from ajax post request
	private function load_modal_page() {
            switch ($this->page_to_load) {
                case 'confirm-action':
                    require ( "$this->plugin_path/templates/ajax-modals/$this->page_to_load.php" );
                    break;
                
                case 'confirm-action-orders':
                    require ( "$this->plugin_path/templates/ajax-modals/$this->page_to_load.php" );
                    break;
                
                case 'modal-ticket-add':
                    require ( "$this->plugin_path/templates/backend/tickets/$this->page_to_load.php" );
                    break;
                
                case 'modal-ticket-edit':
                    require ( "$this->plugin_path/templates/backend/tickets/$this->page_to_load.php" );
                    break;
                
                case 'section-ticket-add-details':
                    require ( "$this->plugin_path/templates/backend/tickets/$this->page_to_load.php" );
                    break;
                
                case 'section-settings-list':
                    require ( "$this->plugin_path/templates/backend/settings/$this->page_to_load.php" );
                    break;
                
                case 'section-appointment-list':
                    require ( "$this->plugin_path/templates/$this->page_to_load.php" );
                    break;
                
                case 'section-frontend':
                    require ( "$this->plugin_path/templates/frontend/$this->page_to_load.php" );
                    break;
                
                default:
                    break;
            }
		//require ( "$this->plugin_path/templates/backend/tickets/$this->page_to_load.php" );
                //require ( "$this->plugin_path/templates/$this->page_to_load.php" );
		die();
	}

	// Method for loading any response to a page after ajax post request
	private function load_response() {
		switch ($this->page_to_load) {
                case 'confirm-action':
                    require ( "$this->plugin_path/templates/ajax-modals/$this->page_to_load.php" );
                    break;
                
                case 'modal-ticket-add':
                    require ( "$this->plugin_path/templates/backend/tickets/$this->page_to_load.php" );
                    break;
                
                case 'modal-ticket-edit':
                    require ( "$this->plugin_path/templates/backend/tickets/$this->page_to_load.php" );
                    break;
                
                case 'section-ticket-add-details':
                    require ( "$this->plugin_path/templates/backend/tickets/$this->page_to_load.php" );
                    break;
                
                case 'section-settings-list':
                    require ( "$this->plugin_path/templates/backend/settings/$this->page_to_load.php" );
                    break;
                
                case 'section-appointment-list':
                    require ( "$this->plugin_path/templates/$this->page_to_load.php" );
                    break;
                
                case 'section-orders-list':
                    require ( "$this->plugin_path/templates/backend/orders/$this->page_to_load.php" );
                    break;
                
                case 'section-frontend':
                    require ( "$this->plugin_path/templates/frontend/$this->page_to_load.php" );
                    break;
                
                default:
                    
                    require ( "$this->plugin_path/templates/backend/tickets/$this->page_to_load.php") ;
                    break;
            }
		//require ( "$this->plugin_path/templates/backend/tickets/$this->page_to_load.php" );
                //require ( "$this->plugin_path/templates/$this->page_to_load.php" );
		die();
	}
}