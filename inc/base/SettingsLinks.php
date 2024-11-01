<?php

/**
 * @package TicketPlus
 */

namespace Tidplus\base;

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class SettingsLinks extends BaseController
{
	// Method for registering plugin action link
	public function register() {
		add_filter( 'plugin_action_links_' . $this->plugin, array( $this, 'settings_links' ) );
	}

	// Method for showing settings link on plugin list which redirects directly to this plugin's settings page
	public function settings_links( $links ) {
		$settings_link = '<a href="admin.php?page=tidplus-settings">Settings</a>';
		array_push( $links, $settings_link );
		return $links;
	}
}