<?php
/**
 * @package TicketPlus
 */

/**
 * Plugin Name: Ticket Plus
 * Plugin URI: 
 * Description: Ticket Plus is a ticket management system which allows you to sell tickets directly in your WordPress website.
 * Version: 1.0.0
 * Author: ID-PLUS
 * Author URI: 
 * License: GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

/*
 * 
 * Stop the execution of the script if the file is called directly
 * 
 */
defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

/*
 * 
 * 
 * Require once the composer autoload
 * 
 */ 
define( 'TICKET_PLUS_ROOT_PATH', plugin_dir_path( __FILE__ ) );

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/*
 * 
 * Functions called during the plugin activation
 * 
 */
function activate_tidplus() {
     global $pagenow;
     if ( is_admin() ) {
         Tidplus\base\Activate::activate();}
}
register_activation_hook( __FILE__, 'activate_tidplus' );

/*
 * 
 * Functions called during the plugin deactivation
 * 
 */
function deactivate_tidplus() {
    Tidplus\base\Deactivate::deactivate();
    
}
register_deactivation_hook( __FILE__, 'deactivate_tidplus' );

/*
 * 
 * Functions called during the plugin uninstall 
 * 
 * to be developed
 * 
 */






/*
 * 
 * Initialize all the core classes of the plugin with all the necessary hooks that are needed to be registered
 *  
 * Enque CSS and JS
 * 
 */
if ( class_exists( 'Tidplus\\Init' ) ) {
	Tidplus\Init::register_services();
}


/*
 * 
 * Include the shortcode.
 * 
 * 
 */

include_once 'templates/frontend/section-frontend.php' ;
