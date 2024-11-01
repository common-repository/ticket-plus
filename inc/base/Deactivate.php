<?php

/**
 * @package TicketPlus
 */

namespace Tidplus\base;

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class Deactivate
{
	// Method called while the plugin deactivates
	public static function deactivate() {
		flush_rewrite_rules();
	}
}