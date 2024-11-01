<?php

/**
 * @package TicketPlus
 */

namespace Tidplus\base;

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class BaseController
{
	public $plugin_path;
	public $plugin_url;
	public $plugin;

	// Defines the public variables initiated in this class
	public function __construct() {
		$this->plugin_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) );
		$this->plugin_url  = plugin_dir_url( dirname( dirname( __FILE__ ) ) );
		$this->plugin      = plugin_basename( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/ticket-plus.php';
	}
}