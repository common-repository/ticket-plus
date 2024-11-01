<?php

/**
 * @package TicketPlus
 * 
 * page: inc/woocommerce/woocommerce-product.php
 * 
 * 
 */

namespace Tidplus\woocommerce;

use \Tidplus\base\BaseController;
use \Tidplus\api\SettingsApi;
use \Tidplus\api\callbacks\AdminCallbacks;
use \Tidplus\api\DataApi;
use \Tidplus\woocommerce\WooCommerceTab;
use \Tidplus\woocommerce\WooCommerceProduct;
use \Tidplus\woocommerce\WooCommerceCart;

/*
 * 
 * Stop the execution of the script if the file is called directly
 * 
 */

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

    class WooCommerce {
        
        function __construct() {
            $this->initFrontEndAJAX();
        }
    
        /**
	 * Store all the classes inside an array
	 * @return array full list of classes
	 */
	public static function get_services() {
		return array (
                    woocommerce\WooCommerceTab::class,
                    woocommerce\WooCommerceProduct::class,
                    woocommerce\WooCommerceCart::class
		);
	}

	/**
	 * Loop through the classes, initialize them and call the register() method if it exists
	 *
	 */
	public static function register_services() {
		foreach ( self::get_services() as $class ) {
			$service = self::instantiate( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	private static function instantiate( $class ) {
		$service = new $class();
		return $service;
	}
        
        function initFrontEndAJAX(){
                /*
                 * WooCommerce front end AJAX requests.
                 */
                add_action('wp_ajax_tidplus_woocommerce_add_to_cart', array(&$this, 'add'));
                add_action('wp_ajax_nopriv_tidplus_woocommerce_add_to_cart', array(&$this, 'add'));
            }

    }