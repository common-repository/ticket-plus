<?php

/**
 * @package TicketPlus
 * 
 * page: inc/woocommerce/WooCommerceTab.php
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

    class WooCommerceTab {
    
        function __construct(){           
       /*
        * Add tab.
        */
       add_action('woocommerce_product_write_panel_tabs', array(&$this, 'add'));

       /*
        * Add content to tab.
        */
       add_action('woocommerce_product_data_panels', array(&$this, 'display'));

       /*
        * Save tab data.
        */
       add_action('woocommerce_process_product_meta', array(&$this, 'set'));

        }



       /*
        * Add booking system in product tabs list.
        * 
        * @return HTML tab button
        */
       function add(){
           global $TIDPLUS;

           echo '<li class="tidplus_tab"><a href="#tidplus_tab_data"><span>'.'Ticket Plus'.'</span></a></li>';
       }


       /*
        * Display tab content.
        * 
        * @return HTML form
        */
       function display(){
           global $post;
           global $TIDPLUS;

           $tidplus_woocommerce_options = array('ticket' => get_post_meta($post->ID, 'tidplus_woocommerce_ticket', true),
                                                'position' => get_post_meta($post->ID, 'tidplus_woocommerce_position', true) == '' ? 'summary':get_post_meta($post->ID, 'tidplus_woocommerce_position', true),
                                                'add_to_cart' => get_post_meta($post->ID, 'tidplus_woocommerce_add_to_cart', true) == '' ? 'false':get_post_meta($post->ID, 'tidplus_woocommerce_add_to_cart', true));

    ?>
                <div id="tidplus_tab_data" class="panel woocommerce_options_panel">
                <div class="options_group">
                <p class="form-field">
    <?php 
                woocommerce_wp_select(array('id' => 'tidplus_woocommerce_ticket',
                                            'options' => $this->getTickets(),
                                            'label' => 'Ticket',
                                            'description' => 'Attach a ticket to this product'));

                woocommerce_wp_select(array('id' => 'tidplus_woocommerce_position',
                                            'options' => array('summary' => 'Summary',
                                                               'tabs' => 'Tabs',
                                                               'summary-tabs' => 'Summary & Tabs'),
                                            'label' => 'Position',
                                            'description' => 'Choose where the ticket will be displayed inside your product',
                                            'value' => $tidplus_woocommerce_options['position']));
                
                woocommerce_wp_select(array('id' => 'tidplus_woocommerce_add_to_cart',
                                            'options' => array('false' => 'WooCommerce',
                                                               'true' => 'Ticket Plus'),
                                            'label' => 'Buy button',
                                            'description' => 'Choose what "Buy button" you will use',
                                            'value' => $tidplus_woocommerce_options['add_to_cart']));

    ?>
                </p>
                </div>	
                </div>
    <?php
       }


        /*
         * Get tickets list.
         * 
         * @return tickets list
         */
        function getTickets(){
            global $DOPBSP;

            $tickets_list = array();
             $tickets = \Tidplus\api\DataApi::get_tickets();     

            if (count($tickets) > 0){
                $tickets_list[0] = 'Select ticket';

                foreach ($tickets as $ticket){
                    $tickets_list[$ticket->ticket_id] = 'ID '.$ticket->ticket_id.': '.$ticket->name;
                }
            }
            else{
                $tickets_list[0] = 'No tickets';
            }

            return $tickets_list;
        }

        /*
         * Save attached ticket.
         * 
         * @return true/false
         */
        function set($post_id){

        $tidplus_woocommerce_ticket = sanitize_text_field($_POST['tidplus_woocommerce_ticket']);
        $tidplus_woocommerce_position = sanitize_text_field($_POST['tidplus_woocommerce_position']);
        $tidplus_woocommerce_add_to_cart = sanitize_text_field($_POST['tidplus_woocommerce_add_to_cart']);

                update_post_meta($post_id, 'tidplus_woocommerce_ticket', esc_attr($tidplus_woocommerce_ticket));
                
        if (!empty($tidplus_woocommerce_position))
                update_post_meta($post_id, 'tidplus_woocommerce_position', esc_attr($tidplus_woocommerce_position));       
        
        if (!empty($tidplus_woocommerce_add_to_cart))
                update_post_meta($post_id, 'tidplus_woocommerce_add_to_cart', esc_attr($tidplus_woocommerce_add_to_cart));       
        }
    }