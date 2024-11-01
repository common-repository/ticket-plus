<?php

/**
 * @package TicketPlus
 */

namespace Tidplus\api\callbacks;

use \Tidplus\base\BaseController;

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class AdminCallbacks extends BaseController
{
	/*
         * 
         * @return
         * 	The calendar page when admin click on main menu
         * 
         * 
         * 
         * @used:   PRO
         *          development
         * 
         * 
         */
	public function calendar() {
		return require_once( "$this->plugin_path/templates/page-appointment.php" );
	}

	
        /*
         * 
         * @return
         * 	Orders section when admin click on 'orders' subpage
         * 
         * 
         * 
         * @used:   PRO
         *          development
         *          free
         * 
         * 
         */
	public function orders() {
		return require_once( "$this->plugin_path/templates/backend/orders/page-orders.php" );
	}

	
        /*
         * 
         * @return
         * 	ticket as displayed in fronted
         *      
         * 
         * 
         * 
         * @used:   development 
         * 
         *          shortcode - free/PRO
         *          
         *          
         * 
         * 
         */
	public function frontend() {
		return require_once( "$this->plugin_path/templates/frontend/page-frontend.php" );
	}

	
        /*
         * 
         * @return
         * 	the tickets list when admin press the 'tickets' submenu page
         * 
         * 
         * 
         * @used:   PRO
         *          development
         *          free
         * 
         * 
         */
	public function ticket() {
		return require_once( "$this->plugin_path/templates/backend/tickets/page-ticket.php" );
	}

	
        /*
         * 
         * @return
         * 	Plugin settings when admin press 'settings' submenu page 
         * 
         * 
         * 
         * @used:   PRO
         *          development
         * free
         * 
         * 
         */
	public function settings() {
		return require_once( "$this->plugin_path/templates/backend/settings/page-settings.php" );
	}
}