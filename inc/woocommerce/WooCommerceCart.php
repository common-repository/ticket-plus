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
use \Tidplus\base\Posts;
/*
 * 
 * Stop the execution of the script if the file is called directly
 * 
 */

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

    class WooCommerceCart {
    
        function __construct(){
                /*
                 * Set reservation info in cart.
                 */
                add_filter('woocommerce_get_item_data', array(&$this, 'set'), 10, 2);
                
                add_action ('woocommerce_add_cart_item_data', array(&$this, 'add'));
                
                /*
                 * Delete reservations when cart item is deleted. We are not going to use it yet because of the "Undo" feature.
                 */
                // add_action('woocommerce_cart_updated', array(&$this, 'delete'));
                
                /*
                 * Delete cart item reservation. Must be initialized before "update" function.
                 */
                add_action('woocommerce_check_cart_items', array(&$this, 'deleteItem'));
                
                /*
                 * Validate cart items.
                 */
                add_action('woocommerce_check_cart_items', array(&$this, 'validate'));
                
                /*
                 * Update prices of the cart items that have the booking system. Must be initialized after "deleteItem" function.
                 */
                add_action('woocommerce_before_calculate_totals', array(&$this, 'update'));
                
                /*
                 * Remove quantity input from cart.
                 */
                add_filter('woocommerce_is_sold_individually', array(&$this, 'removeQuantity'), 10, 2);

        }

            /*
             * Add reservation to cart.
             * 
             * @post calendar_id (integer): calendar id
             * @post language (string): selected language
             * @post currency (string): selected currency sign
             * @post currency_code (string): selected currency code
             * @post cart_data (array): list of reservations
             * @post product_id (integer): product ID
             * 
             * @return reservation status
             */
            function add(){
		global $TID;
                global $wpdb;
                global $woocommerce;
                global $TIDPLUS;
                global $TIDPLUSWooCommerce;
                
                $ticket_id = esc_attr('ticket_id');
                $currency = esc_attr('currency');
                $currency_code = esc_attr('currency_code');
                $cart_data = esc_attr('cart_data');
                $product_id = esc_attr('product_id');
                
                
                /*
                 * Verify if product already exists and attach the reservation to the cart key.
                 */
                $cart = $woocommerce->cart->get_cart();
                
                foreach ($cart as $cart_item_key => $cart_item){
                    if ($cart_item['product_id'] == $product_id){
                        $reservation_data['cart_item_key'] = $cart_item_key;
                        $reservation_data['token'] = $cart_item['tidplus_token'];
                        
                        if (!$this->validateOverlap($ticket_id, $product_id, $reservation_data['cart_item_key'], $reservation_data['token'], $reservation)){
                            echo 'overlap'.' <a href="'.wc_get_cart_url().'">'.'view cart'.'</a>';
                            die();
                        }
                        else{
                            $wpdb->insert('wp_tidplus_woocommerce', $reservation_data);
                            echo 'success'.' <a href="'.wc_get_cart_url().'">'.'view cart'.'</a>';
                            die();
                        }
                    }
                }
		
                
                /*
                 * Reservation data.
                 */
                $reservation_data = array('cart_item_key' => '',
                                          'token' => '',
                                          'product_id' => $product_id,
                                          'ticket_id' => $ticket_id,
                                          'currency' => $currency,
                                          'currency_code' => $currency_code,
                                          'data' => json_encode($cart_data[0]));
                
                /*
                 * Verify if product already exists and attach the reservation to the cart key.
                 */
                $cart = $woocommerce->cart->get_cart();
                
                foreach ($cart as $cart_item_key => $cart_item){
                    if ($cart_item['product_id'] == $product_id){
                        $reservation_data['cart_item_key'] = $cart_item_key;
                        $reservation_data['token'] = $cart_item['tidplus_token'];
                        
                            $wpdb->insert('wp_tidplus_woocommerce', $reservation_data);
                            die();
                        
                    }
                }
                
                /*
                 * If the product does not exist add it and attach the reservation to the new cart key.
                 */    
                $token = $this->generateRandomString(10);
                $cart_item_key = $woocommerce->cart->add_to_cart($product_id, 
                                                                 1, 
                                                                 0,
                                                                 array(),
                                                                 array('tidplus_token' => $token));
                $woocommerce->cart->maybe_set_cart_cookies();
                $reservation_data['cart_item_key'] = $cart_item_key;
                $reservation_data['token'] = $token;

                $wpdb->insert('wp_tidplus_woocommerce', $reservation_data);
		
		wp_cache_flush();
		
                echo 'success;;;;;'.'SUCCESS'.' <a href="'.wc_get_cart_url().'">'.'View cart'.'</a>';
                
                die();
            }
            
            /*
             * Set reservation info to cart.
             * 
             * @param other_data (array): the array to which the info will be added
             * @param car_item (array): cart data
             * 
             * @return info array
             */
            function set($other_data, 
                         $cart_item){
                global $wpdb;
                global $post;
                global $TIDPLUS;
                global $TIDPLUSWooCommerce;
                
                $product_id = $cart_item['product_id'];
                
                /*
                 * Skip products without reservations.
                 */
                if (!isset($cart_item['tidplus_token'])){
                    return $other_data;
                }
                
                $reservations_data = $wpdb->get_results($wpdb->prepare('SELECT * FROM wp_tidplus_woocommerce WHERE token="%s" AND product_id=%d ORDER BY id',
                                                                       $cart_item['tidplus_token'], $product_id));
                
                foreach ($reservations_data as $reservation_data){
                    $reservation = json_decode($reservation_data->data);
                    $reservation->currency = $reservation_data->currency;

                    $settings_calendar = $DOPBSP->classes->backend_settings->values($reservation_data->calendar_id,  
                                                                                    'calendar');


                    /*
                     * Display reservation data.
                     */
                    $other_data[] =  array('name' => $DOPBSP->text('RESERVATIONS_RESERVATION_ID').' #'.$reservation_data->id,
                                           'value' => count($reservations_data) > 1 ? '<a href="'.add_query_arg(array('dopbsp_remove_item_id' => $reservation_data->id), get_permalink($post->ID), 'woocommerce-cart').'">Remove</a>':'');
                
                    /*
                     * Display details data.
                     */
                    $other_data[] =  array('name' => $DOPBSP->text('RESERVATIONS_RESERVATION_DETAILS_TITLE'),
                                           'value' => $DOPBSPWooCommerce->classes->order->getDetails($reservation,
                                                                                                     $settings_calendar));

                }
                
                return $other_data;
            }
            
            /*
             * Update prices of the cart items that have the booking system.
             * 
             * @param $cart_object (object): WooCommerce cart object
             */
            function update($cart_object){
                global $wpdb;
                global $DOPBSPWooCommerce;
                global $woocommerce;
                
                foreach ($cart_object->cart_contents as $cart_item_key => $cart_item){
                    if (isset($cart_item['dopbsp_token'])){
                        $reservations_data = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$DOPBSPWooCommerce->tables->woocommerce.' WHERE cart_item_key="%s" AND token="%s" AND product_id=%d',
                                                                               $cart_item_key, $cart_item['dopbsp_token'], $cart_item['product_id']));
                        $cart_item['data']->price = 0;
                        
                        foreach ($reservations_data as $reservation_data){
                            $reservation = json_decode($reservation_data->data);
                            
                            if($reservation->deposit_price != 0) {
                                $reservation->price_total = $reservation->deposit_price;
                            }
                            $cart_item['data']->price += $reservation->price_total;
                            
                            if($woocommerce->version >= 3) {
                                $product = $cart_item['data'];
                                $product->set_price($cart_item['data']->price);
                            }
                        }
                    }
                } 
            }
            
            /*
             * Delete reservations when cart item is deleted.
             * 
             * @get remove_item (string): deleted item cart key
             */
            function delete(){
		global $DOT;
                global $wpdb;
                global $DOPBSP;
                global $DOPBSPWooCommerce;
                
                if ($DOT->get('remove_item')){
                    if ($wpdb->delete($DOPBSPWooCommerce->tables->woocommerce, array('cart_item_key' => $DOT->get('remove_item')))){
                
                        /*
                         * Set language
                         */
                        $DOPBSP->classes->translation->set(DOPBSP_CONFIG_TRANSLATION_DEFAULT_LANGUAGE,
                                                           false,
                                                           array('frontend',
                                                                 'woocommerce_frontend'));
                        
                        wc_add_notice($DOPBSP->text('WOOCOMMERCE_DELETED'), 'success');
                    }
                }
            }
            
            /*
             * Delete cart item reservation.
             * 
             * @param $cart_object (object): WooCommerce cart object
             */
            function deleteItem($cart_object){
		global $DOT;
                global $wpdb;
                global $DOPBSP;
                global $DOPBSPWooCommerce;
                
                if ($DOT->get('dopbsp_remove_item_id', 'int')){
                    if ($wpdb->delete($DOPBSPWooCommerce->tables->woocommerce, array('id' => $DOT->get('dopbsp_remove_item_id', 'int')))){
                
                        /*
                         * Set language
                         */
                        $DOPBSP->classes->translation->set(DOPBSP_CONFIG_TRANSLATION_DEFAULT_LANGUAGE,
                                                           false,
                                                   array('frontend',
                                                         'woocommerce_frontend'));
                        wc_add_notice($DOPBSP->text('WOOCOMMERCE_DELETED'), 'success');
                    }
                }
            }
            
            /*
             * Remove quantity input from cart.
             * 
             * @param $return (boolean): initial value
             * @param $product (object): product data
             * 
             * @return true/false
             */
            function removeQuantity($return, 
                                    $product){
                $dopbsp_woocommerce_calendar_id = get_post_meta($product->get_id(), 
                                                                'dopbsp_woocommerce_calendar', 
                                                                true);
                
                return $dopbsp_woocommerce_calendar_id != '' && $dopbsp_woocommerce_calendar_id != '0' ? true:$return;
            }
            
// Validations            
            
            /*
             * Verify all cart items that contain a reservation request.
             * 
             * @return error messages
             */
            function validate(){
                global $wpdb;
                global $woocommerce;
                global $DOPBSP;
                global $DOPBSPWooCommerce;
                
                $errors = array();
                
                /*
                 * Get reservations that are in this cart and from same calendar.
                 */
                $cart = $woocommerce->cart->get_cart();
		
                foreach ($cart as $cart_item_key => $cart_item){
                    if (isset($cart_item['dopbsp_token'])){
                        $reservations = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$DOPBSPWooCommerce->tables->woocommerce.' WHERE cart_item_key="%s" AND token="%s" ORDER BY date_created', 
                                                                          $cart_item_key, $cart_item['dopbsp_token']));

                        foreach ($reservations as $reservation){
                            if (!$this->validateAvailability($reservation->calendar_id, json_decode($reservation->data))){
                                array_push($errors, '<a href="'.get_permalink($reservation->product_id).'">'.get_the_title($reservation->product_id).'</a> - Reservation #'.$reservation->id.': '.$DOPBSP->text('WOOCOMMERCE_UNAVAILABLE'));
                            }
                        }

                        if (count($errors) > 0){
                            wc_add_notice(implode('<br /><br />', $errors), 'error');
                        }
                    }
                }
            }
            
            /*
             * Verify if cart item reservation is available.
             * 
             * @param calendar_id (integer): calendar ID
             * @param reservation (object): reservation data
             * 
             * @return true/false
             */
            function validateAvailability($calendar_id,
                                          $reservation){
                global $DOPBSP;
                
                $reservation = (object)$reservation;
                
                if ($reservation->start_hour == ''){
                    if (!$DOPBSP->classes->backend_calendar_schedule->validateDays($calendar_id, $reservation->check_in, $reservation->check_out, $reservation->no_items)){
                        return false;
                    }
                }
                else{
                    if (!$DOPBSP->classes->backend_calendar_schedule->validateHours($calendar_id, $reservation->check_in, $reservation->start_hour, $reservation->end_hour, $reservation->no_items)){
                        return false;
                    }
                }
                
                return true;
            }
            
            /*
             * Validate new reservation before adding it to cart, to not overlap with the ones already existing in cart.
             * 
             * @param calendar_id (integer): calendar ID
             * @param product_id (integer): product ID 
             * @param cart_item_key (string): cart item key
             * @param token (string): cart item token
             * @param reservation (object): reservation data
             * 
             * @return true/false
             */
            function validateOverlap($ticket_id,
                                     $product_id,
                                     $cart_item_key,
                                     $token,
                                     $reservation = ''){
                global $wpdb;
                global $TIDPLUS;
                global $TIDPLUSWooCommerce;
		
                $reservation = $reservation == '' ? $reservation:(object)$reservation;
                
                /*
                 * Get reservations that are in this cart and from same calendar.
                 */
                $reservations = array();
                
                $reservations_data = $wpdb->get_results($wpdb->prepare('SELECT * FROM wp_tidplus_woocommerce WHERE cart_item_key="%s" AND token="%s" AND product_id=%d ORDER BY date_created',
                                                                       $cart_item_key, $token, $product_id));
		
                /*
                 * Verify reservations.
                 */
                foreach ($reservations_data as $reservation_data){
                    array_push($reservations, json_decode($reservation_data->data));
                }
                $reservation != '' ? array_push($reservations, $reservation):'';
                
                
                
                return true;
            }
            
            
            function generateRandomString($length = 10) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                return $randomString;
            }
    }