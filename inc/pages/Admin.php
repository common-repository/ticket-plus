<?php

/**
 * @package TicketPlus
 * 
 * page: inc/pages/Admin.php
 * 
 * 
 */

namespace Tidplus\pages;

use \Tidplus\base\BaseController;
use \Tidplus\api\SettingsApi;
use \Tidplus\api\callbacks\AdminCallbacks;

/*
 * 
 * Stop the execution of the script if the file is called directly
 * 
 */

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class Admin extends BaseController
{
	public $settings;
	public $callbacks;
	public $pages = array();
	public $sub_pages = array();


	// Method that sets the main page of the plugin
	public function set_pages() {
            $icon = "$this->plugin_url/assets/images/ticket-plus.png";
		$this->pages = array(
			array(
				'page_title' => 'Tickets',
				'menu_title' => 'Ticket Plus',
				'capability' => 'manage_options',
				'menu_slug' => 'tidplus-manage-appointments',
				'callback' => array( $this->callbacks, 'ticket' ),
				'icon_url' => $icon,
				'position' => 100
			)
		);
	}

	// Method that sets information of all the sub menus present in the plugin 
	public function set_sub_pages() {
		$this->sub_pages = array(
/*			array(
				'parent_slug' => 'tidplus-manage-appointments',
				'page_title' => 'Prescription',
				'menu_title' => 'Prescription',
				'capability' => 'manage_options',
				'menu_slug' => 'tidplus-manage-prescriptions',
				'callback' => array( $this->callbacks, 'prescription' )
			),
                        array(
				'parent_slug' => 'tidplus-manage-appointments',
				'page_title' => 'Tickets',
				'menu_title' => 'Tickets',
				'capability' => 'manage_options',
				'menu_slug' => 'tidplus-manage-tickets',
				'callback' => array( $this->callbacks, 'ticket' )
			), */
			array(
				'parent_slug' => 'tidplus-manage-appointments',
				'page_title' => 'Orders',
				'menu_title' => 'Orders',
				'capability' => 'manage_options',
				'menu_slug' => 'tidplus-manage-orders',
				'callback' => array( $this->callbacks, 'orders' )
			),
/*			array(
				'parent_slug' => 'tidplus-manage-appointments',
				'page_title' => 'Frontend',
				'menu_title' => 'Frontend',
				'capability' => 'manage_options',
				'menu_slug' => 'tidplus-manage-frontend',
				'callback' => array( $this->callbacks, 'frontend' )
			), */ 
			array(
				'parent_slug' => 'tidplus-manage-appointments',
				'page_title' => 'Settings',
				'menu_title' => 'Settings',
				'capability' => 'manage_options',
				'menu_slug' => 'tidplus-settings',
				'callback' => array( $this->callbacks, 'settings' )
			)
		);
	}

	/*
         * 
         *  Method for adding the pages into this plugin
         * 
         *  
         */
	public function register() {
		$this->settings = new SettingsApi();
		$this->callbacks = new AdminCallbacks();
		$this->set_pages();
		$this->set_sub_pages();
		$this->settings->add_pages( $this->pages )->with_sub_page( 'Tickets' )->add_sub_pages( $this->sub_pages )->register();
	}
}