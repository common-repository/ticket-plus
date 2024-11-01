<?php

/**
 * @package TicketPlus
 */

namespace Tidplus\api;

use \Tidplus\base\BaseController;

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class DataApi extends BaseController
{
	// Method for getting an object of all the tickets
	public static function get_tickets() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_ticket';
		$result = $wpdb->get_results( "SELECT * FROM $table ORDER BY `ticket_id` ASC" );
		return $result;
	}

	// Method for getting an object of a specific ticket (EXPECTS AN ARGUMENT 'TICKET_ID')
	public static function get_ticket_info_by_id( $ticket_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_ticket';
		$result = $wpdb->get_results( "SELECT * FROM $table WHERE ticket_id = '$ticket_id'" );
		return $result;
	}
        
        // Method for getting an object of all the orders
	public static function get_orders() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_orders';
		$result = $wpdb->get_results( "SELECT * FROM $table ORDER BY `order_id` ASC" );
		return $result;
	}
        
        // Method for getting an object of all the orders
	public static function get_latest_order_id() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_orders';
		$result = $wpdb->get_results( "SELECT MAX(order_id) as MaxOrderId FROM $table" );
		return $result;
	}
        
        // Method for getting an object of a specific ticket (EXPECTS AN ARGUMENT 'TICKET_ID')
	public static function get_order_info_by_id( $order_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_orders';
		$result = $wpdb->get_results( "SELECT * FROM $table WHERE order_id = '$order_id'" );
		return $result;
	}

	// Method for getting an object of all the appointments (ESPECTS AN ARGUMENT 'TIMESTAMP' WHICH IS THE DAY FOR WHICH YOU WISH TO GET THE APPOINTMENT OBJECT)
	public static function get_appointments( $timestamp ) {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_ticket';
		$app_date = strtotime( $timestamp );
		$default_ticket_id = self::get_settings( 'default_ticket_id' );
		$result = $wpdb->get_results( "SELECT * FROM $table WHERE ticket_timestamp = $app_date" );
		return $result;
	}

	// Method for getting an object of appointment counts on each day
	public static function get_days_appointment_counts() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_appointment';
		$default_ticket_id = self::get_settings( 'default_ticket_id' );
		$result = $wpdb->get_results( "SELECT `appointment_timestamp`, COUNT(*) AS total FROM $table WHERE ticket_id = $default_ticket_id GROUP BY `appointment_timestamp`" );
		return $result;
	}
        
        	// Method for getting an object of appointment counts on each day
	public static function get_days_event_counts() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_ticket';
		$default_ticket_id = self::get_settings( 'default_ticket_id' );
		$result = $wpdb->get_results( "SELECT `ticket_timestamp`, COUNT(*) AS total FROM $table GROUP BY `ticket_timestamp`" );
		return $result;
	}

	// Method for getting an object of all the appointment of a single patient (EXPECTS AN ARGUMENT 'PATIENT_ID')
	public static function get_appointments_of_patient( $patient_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_appointment';
		$result = $wpdb->get_results( "SELECT * FROM $table WHERE patient_id = $patient_id ORDER BY appointment_timestamp DESC" );
		return $result;
	}

	// Method for getting an object of all the information of a single appointment (EXPECTS AN ARGUMENT 'APPOINTMENT_ID')
	public static function get_appointment_info_by_id( $appointment_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_appointment';
		$result = $wpdb->get_results( "SELECT * FROM $table WHERE appointment_id = $appointment_id" );
		return $result;
	}


	// Method for getting an object of all the countries
	public static function get_countries() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_country';
		$result = $wpdb->get_results( "SELECT * FROM $table" );
		return $result;
	}
        
        public static function get_countries_currency($type) {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_country';
		$result = $wpdb->get_results( "SELECT * FROM $table WHERE ID = '$type'" );
		foreach ( $result as $row ) {
			return $row->currency_symbol;
		}
	}

	// Method for getting an info from country table (EXPECTS TWO ARGUMENTS 'COUNTRY_ID' and 'INFO')
	public static function get_country_info_by_id( $country_id, $info ) {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_country';
		$result = $wpdb->get_results( "SELECT * FROM $table WHERE ID = $country_id" );
		foreach ( $result as $value ) {
			return $value->$info;
		}
	}

	public static function get_currency_code_by_id( $country_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_country';
		$result = $wpdb->get_results( "SELECT * FROM $table WHERE ID = $country_id" );
		foreach ( $result as $value ) {
			return $value->currency_code;
		}
	}

	// Method for getting an specific value from settings table (EXPECTS AN ARGUMENT 'TYPE')
	public static function get_settings( $type ) {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_settings';
		$result = $wpdb->get_results( "SELECT * FROM $table WHERE type = '$type'" );
		foreach ( $result as $row ) {
			return $row->description;
		}
	}
}