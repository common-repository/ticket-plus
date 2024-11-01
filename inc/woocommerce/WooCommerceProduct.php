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
/*
 * 
 * Stop the execution of the script if the file is called directly
 * 
 */

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

    class WooCommerceProduct {
    
        function __construct(){
       /*
        * Add ticket in product summary.
        */
       add_filter('woocommerce_single_product_summary', array(&$this, 'summary'), 25);
       
       /*
        * Add ticket in product tab.
        */
       add_filter('woocommerce_product_tabs', array(&$this, 'tab'));
        }

        function summary(){
            global $post;

            $tidplus_woocommerce_options = array('ticket' => get_post_meta($post->ID, 'tidplus_woocommerce_ticket', true),
                                                 'position' => get_post_meta($post->ID, 'tidplus_woocommerce_position', true),
                                                 'add_to_cart' => get_post_meta($post->ID, 'tidplus_woocommerce_add_to_cart', true) == '' ? 'false':get_post_meta($post->ID, 'tidplus_woocommerce_add_to_cart', true));


            if ($tidplus_woocommerce_options['ticket'] != '' 
                    && $tidplus_woocommerce_options['ticket'] != '0'){
                /*
                 * Add all tickets.
                 */
                if ($tidplus_woocommerce_options['position'] == 'summary'){
                    echo do_shortcode('[tidplus id='.$tidplus_woocommerce_options['ticket'].']');
                }

                /*
                 * Add only sidebar.
                 */    
                if ($tidplus_woocommerce_options['position'] == 'summary-tabs'){
                    echo '';
                }
            }
        }
        
        
        
        /*
             * Add ticket in product tab section.
             * 
             * @return tab object
             */
            function tab(){
		global $post;
                
		$tab = array();
	
                $tidplus_woocommerce_options = array('ticket' => get_post_meta($post->ID, 'tidplus_woocommerce_ticket', true),
                                                 'position' => get_post_meta($post->ID, 'tidplus_woocommerce_position', true),
                                                 'add_to_cart' => get_post_meta($post->ID, 'tidplus_woocommerce_add_to_cart', true) == '' ? 'false':get_post_meta($post->ID, 'tidplus_woocommerce_add_to_cart', true));
                
                if ($tidplus_woocommerce_options['ticket'] != '' 
                        && $tidplus_woocommerce_options['ticket'] != '0' 
                        && ($tidplus_woocommerce_options['position'] == 'tabs' 
                                || $tidplus_woocommerce_options['position'] == 'summary-tabs')){
                    
                    
                    $tab['ticket-plus'] = array('title' => 'Ticket',
                                                'priority' => 1,
                                                'callback' => array($this, 'tabContent'));
                    return $tab;
                }
            }
            
            /*
             * Add ticket in product tab section.
             * 
             * @return ticket shortcode
             */
            function tabContent(){
                global $post;
	
                $tidplus_woocommerce_options = array('ticket' => get_post_meta($post->ID, 'tidplus_woocommerce_ticket', true),
                                                     'position' => get_post_meta($post->ID, 'tidplus_woocommerce_position', true),
                                                     'add_to_cart' => get_post_meta($post->ID, 'tidplus_woocommerce_add_to_cart', true) == '' ? 'false':get_post_meta($post->ID, 'tidplus_woocommerce_add_to_cart', true));
                    
                echo do_shortcode('[tidplus id='.$tidplus_woocommerce_options['ticket'].']');
            }
            
    }