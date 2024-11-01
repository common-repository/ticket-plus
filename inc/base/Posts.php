<?php

/**
 * @package TicketPlus
 */


namespace Tidplus\base;
use \Tidplus\api\DataApi;

/**
 * Paypal SDK
 */
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use PayPal\Api\PayerInfo;

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class Posts extends BaseController
{
	// Method for registering form submission hook to this plugin
	public function register() {
            add_action( 'admin_post_tidplus', array( $this, 'post' ) );
            add_action( 'admin_post_nopriv_tidplus', array( $this, 'post' ) );

            add_action( 'wp_ajax_paypal_callback', array( $this, 'paypal_callback' ) );
            add_action( 'wp_ajax_nopriv_paypal_callback', array( $this, 'paypal_callback' ) );
            }

	// Main method for handling all the post data submitted during a form submission
	public function post() {
		$task = sanitize_text_field( $_POST['task'] );
		$this->handle_form_submission( $task );
	}

	// Method for handling form submission according to task of the form
	public function handle_form_submission( $task ) {

		if ( $task == 'new_ticket' ){
			$this->add_ticket();
                }
                
                if ( $task == 'new_order' ){
			$this->process_order();
                }
                
		if ( $task == 'edit_ticket' ){
			$this->edit_ticket();
                }
                
                if ( $task == 'edit_ticket_details' ){
			$this->edit_ticket_details();
                }
                
                if ( $task == 'delete_ticket' ){
			$this->delete_ticket();
                }
                
                if ( $task == 'delete_order' ){
			$this->delete_order();
                }
                
                if ( $task == 'edit_order_status_approved' ){
			$this->edit_order_status_ajax('approved');
                        $customer_notification = \Tidplus\api\DataApi::get_settings( 'customer_notification' );
                        if($customer_notification == true){ 
                            $this->send_customer_email_on_approval();
                        }
                        
                }
                
                if ( $task == 'edit_order_status_canceled' ){
			$this->edit_order_status_ajax('canceled');
                }

                if ( $task == 'edit_settings' ){
			$this->edit_settings();
                }
	}


	// Method for adding a new ticket
	private function add_ticket() {
		if ( $this->verify_nonce( 'new_ticket_nonce' ) == true ) {
			$data['name']     =   sanitize_text_field( $_POST['name'] );
			$data['address']  =   sanitize_text_field( $_POST['address'] );
			$data['status']   =   sanitize_text_field( $_POST['status'] );
                        $data['price']   =   sanitize_text_field( $_POST['price'] );
                        $data['units']   =   sanitize_text_field( $_POST['units'] );
                        $data['ticket_timestamp']      =   strtotime( sanitize_text_field( $_POST['ticket_timestamp'] ) );
                        $time_hour         =   $this->sanitized_array( $_POST['time_hour'] );
			$time_minute         =   $this->sanitized_array( $_POST['time_minute'] );
			$data['ticket_time']       =   $this->encode_time( $time_hour, $time_minute );
			global $wpdb;
			$table = $this->get_table_name( 'ticket' );
			$wpdb->insert( $table, $data );
		}
	}
        
    /*
     * Description : Processing order before create order
     */
    private function process_order() {
    	if ( $this->verify_nonce( 'new_order_nonce' ) == true ) {
    		$payment_method = sanitize_text_field( $_POST['payment_method'] );
                    switch ($payment_method) {
				case 'paypal':
					$this->process_paypal_purchase();
					break;
				
				case 'payment_on_arrival':
				default:
					$this->add_order();
					break;
			}
    	}
    }

    /*
     * 
     * Description : Connect paypal api
     */
    public static function paypal_api() {
    	$result = [
    		'success' => false,
    		'msg' => '',
    	];

    	if( ! file_exists(TICKET_PLUS_ROOT_PATH . 'logs') )
    		mkdir(TICKET_PLUS_ROOT_PATH . 'logs');

    	$paypal_enable = \Tidplus\api\DataApi::get_settings( 'paypal_enable' );
        if( $paypal_enable && $paypal_enable == 'on' ) {
        	// Live settings
        	$paypal_live_client_id = \Tidplus\api\DataApi::get_settings( 'paypal_live_client_id' );
        	$paypal_live_client_secret = \Tidplus\api\DataApi::get_settings( 'paypal_live_client_secret' );

        	// Sandbox settings
        	$paypal_sandbox_mode = \Tidplus\api\DataApi::get_settings( 'paypal_sandbox_mode' );
        	$paypal_test_client_id = \Tidplus\api\DataApi::get_settings( 'paypal_test_client_id' );
        	$paypal_test_client_secret = \Tidplus\api\DataApi::get_settings( 'paypal_test_client_secret' );

        	$live_mode = true;
        	$clientId = $paypal_live_client_id;
                $clientSecret = $paypal_live_client_secret;
			if( $paypal_sandbox_mode && $paypal_sandbox_mode == 'on' ) {
				$live_mode = false;
				$clientId = $paypal_test_client_id;
				$clientSecret = $paypal_test_client_secret;
			}

			try {
				$paypal_api = new ApiContext(
			        new OAuthTokenCredential(
			            $clientId,
			            $clientSecret
			        )
			    );

			    $paypal_api->setConfig(
			        [
			            'mode' => $live_mode ? 'live' : 'sandbox',
			            'log.LogEnabled' => true,
			            'log.FileName' => TICKET_PLUS_ROOT_PATH . 'logs/paypal_'. date('Y-m-d') .'.log',
			            'log.LogLevel' => $live_mode ? 'INFO' : 'DEBUG',
			            'cache.enabled' => true,
			        ]
			    );
			    
			    $result = [
		    		'success' => true,
		    		'msg' => '',
		    		'api' => $paypal_api,
		    	];
	    	} catch (\PayPal\Exception\PayPalConnectionException $ex) {
				$ex_data = json_decode($ex->getData());
				$msg = '';
				if( isset($ex_data->error_description) ) { 
					$msg = $ex_data->error_description;
				} else if ( isset($ex_data->message) ) {
					$msg = $ex_data->message;
				} else {
					$msg = $ex->getMessage();
				}
				$result = [
		    		'success' => false,
		    		'msg' => $msg,
		    	];
			}

        } else {
        	$result = [
	    		'success' => false,
	    		'msg' => 'Paypal gateway was not enabled!',
	    	];
        }

        return $result;
    }
    
    /*
     * 
     * Description : Process Paypal Purchase
     */
    private function process_paypal_purchase() {
    	$result = [
    		'success' => false,
    		'msg' => '',
    		'return_url' => '',
    	];
    	
    	$paypal_api = self::paypal_api();
    	if( ! $paypal_api['success'] )
    		wp_send_json( $paypal_api );

    	$paypal_api = $paypal_api['api'];
    	try {
    		$settings_currency = \Tidplus\api\DataApi::get_settings( 'default_currency' );
    		$currency_code = \Tidplus\api\DataApi::get_currency_code_by_id($settings_currency);

    		$payerInfo = new PayerInfo();
    		if( isset( $_POST['email_address'] ) )
				$payerInfo->setEmail(sanitize_email($_POST['email_address']));

			if( isset( $_POST['name'] ) )
				$payerInfo->setFirstName(sanitize_text_field($_POST['name']));

			if( isset( $_POST['surname'] ) )
				$payerInfo->setLastName(sanitize_text_field($_POST['surname']));

			// if( isset( $_POST['phone'] ) )
			// 	$payerInfo->setPhone($_POST['phone']);

		    $payer = new Payer();
			$payer
				->setPaymentMethod('paypal')
				->setPayerInfo($payerInfo);

                        $item = new Item();
			$item
			    ->setName(sanitize_text_field($_POST['ticket_name']))
			    ->setCurrency($currency_code)
			    ->setQuantity( (int)$_POST['quantity'] )
			    ->setPrice(floatval($_POST['price']));
			$itemList = new ItemList();
			$itemList->setItems([$item]);

			$total = floatval( $_POST['price'] * $_POST['quantity'] );

			$details = new Details();
			$details
				->setShipping(0)
			    ->setTax(0)
			    ->setSubtotal($total);

		    $amount = new Amount();
			$amount
				->setCurrency($currency_code)
			    ->setTotal($total)
			    ->setDetails($details);

			$transaction = new Transaction();
			$transaction
				->setAmount($amount)
			    ->setItemList($itemList)
			    ->setDescription('')
			    ->setInvoiceNumber(uniqid());

			$baseUrl = admin_url( 'admin-ajax.php?action=paypal_callback' );
			$redirectUrls = new RedirectUrls();
			$redirectUrls
				->setReturnUrl("$baseUrl&paypal_status=true&return_url=" . $_POST['return_url'])
			    ->setCancelUrl("$baseUrl&paypal_status=false&return_url=" . $_POST['return_url']);

			$payment = new Payment();
			$payment
				->setIntent('sale')
			    ->setPayer($payer)
			    ->setRedirectUrls($redirectUrls)
			    ->setTransactions(array($transaction));
		    $payment->create($paypal_api);

		    // Create order - Status : PENDING
		    $this->add_order2();
		   
		    $result = [
		    	'success' => true,
		    	'msg' => '',
		    	'return_url' => $payment->getApprovalLink()
		    ];
		} catch (\PayPal\Exception\PayPalConnectionException $ex) {
			$ex_data = json_decode($ex->getData());
			$msg = '';
			if( isset($ex_data->error_description) ) { 
				$msg = $ex_data->error_description;
			} else if ( isset($ex_data->message) ) {
				$msg = $ex_data->message;
			} else {
				$msg = $ex->getMessage();
			}
			$result = [
	    		'success' => false,
	    		'msg' => $msg,
	    	];
		}

    	wp_send_json( $result );
    }

    /*
     *
     * Description : Process Paypal Callback
     */
    public function paypal_callback() {
    	$return_url = isset( $_GET['return_url'] ) ? $_GET['return_url'] : home_url();
    	$paypal_api = self::paypal_api();

    	if( ! $paypal_api['success'] )
    		exit( wp_redirect($return_url) );
    	
    	$paypal_api = $paypal_api['api'];
    	if( isset( $_GET['paypal_status'] ) ) {
    		try {
                $paymentId = $_GET['paymentId'];
                $payment = Payment::get($paymentId, $paypal_api);

                $execution = new PaymentExecution();
                $execution->setPayerId($_GET['PayerID']);
                
                // Execute the payment
                $result = $payment->execute($execution, $paypal_api);
                $paypal_return_msg = 'Executed Payment';

                $payment = Payment::get($paymentId, $paypal_api);
                
                $this->edit_order_status('completed');
                $this->edit_ticket_units();
                $customer_notification = \Tidplus\api\DataApi::get_settings( 'customer_notification' );
                $admin_notification = \Tidplus\api\DataApi::get_settings( 'admin_notification' );
                if($customer_notification == true){ 
                    $this->send_customer_email();
                }
                if($admin_notification == true){ 
                    $this->send_admin_email();
                }
                // Purchase success - Order status : Complete
                
            } catch (\PayPal\Exception\PayPalConnectionException $ex) {
                $ex_data = json_decode($ex->getData());
                $this->edit_order_status('failed');
                // Purchase fail - Order status : Fail
            }
    	} else {
                $this->edit_order_status('canceled');
    		// Cancel order - Order status : Cancel
    	}
    	exit( wp_redirect($return_url) );
    }
        
    // Method for adding a new order, updating the ticket with new info and send the email.
	private function add_order() {
		if ( $this->verify_nonce( 'new_order_nonce' ) == true ) {

            $manual_approve = \Tidplus\api\DataApi::get_settings( 'manual_approve' );        
                    
            $this->add_order2();
            if ($manual_approve == false && $manual_approve == ''){
                $this->edit_ticket_units();
            }
            
            $customer_notification = \Tidplus\api\DataApi::get_settings( 'customer_notification' );
            $admin_notification = \Tidplus\api\DataApi::get_settings( 'admin_notification' );
            
            if($customer_notification == true){ 
                $this->send_customer_email();
            }
            
            if($admin_notification == true){ 
                $this->send_admin_email();
            }
        }
    }
           

    // Method for adding a new ticket
	private function add_order2() {
		global $wpdb;
		if ( $this->verify_nonce( 'new_order_nonce' ) == true ) {
                        
                        $manual_approve = \Tidplus\api\DataApi::get_settings( 'manual_approve' );
                        $quantity   =   sanitize_text_field( $_POST['quantity'] );
                        $price   =   sanitize_text_field( $_POST['price'] );
                        $total_price = $quantity * $price;

                        $timestamp = date('Y-m-d H:i:s');
                        $data['ticket_id']     =   sanitize_text_field( $_POST['ticket_id'] );
                        $data['ticket_name']  =   sanitize_text_field( $_POST['ticket_name'] );
                        $data['units']   =   sanitize_text_field( $_POST['quantity'] );
                        $data['price']   =   $total_price;
                        $data['currency']   =   sanitize_text_field( $_POST['currency'] );
                        $data['user_name']     =   sanitize_text_field( $_POST['name'] );
                        $data['user_surname']  =   sanitize_text_field( $_POST['surname'] );
                        $data['user_email']   =   sanitize_text_field( $_POST['email_address'] );
                        $data['user_phone']   =   sanitize_text_field( $_POST['phone'] );
                        $data['payment_method']   =   sanitize_text_field( $_POST['payment_method'] );
                        if($_POST['payment_method'] == 'paypal'){
                            $data['status']   =   'pending';
                        }
                        else{
                            if($manual_approve == true && $manual_approve == 'on'){
                                $data['status']   =   'pending';
                            }
                            else {
                                $data['status']   =   'approved';
                            }
                        }
                        $data['date_added']      =   $timestamp;

                        $table = $this->get_table_name( 'orders' );
                        $wpdb->insert( $table, $data );
                }
	}
        
        
        private function edit_order_status($status) {
                $data['status']   =   $status;
                $order_id_max = \Tidplus\api\DataApi::get_latest_order_id();
                $order_id = $order_id_max[0]->MaxOrderId;
                global $wpdb;
                $table = $this->get_table_name( 'orders' );
                $wpdb->update( $table, $data, array( 'order_id' => $order_id ) );
        }      
        
        
         private function edit_order_status_ajax($status) {
                $data['status']   =   $status;
                $order_id    =   sanitize_text_field( $_POST['id'] );
                global $wpdb;
                $table = $this->get_table_name( 'orders' );
                $wpdb->update( $table, $data, array('order_id' => $order_id  ) );
        }
        
	/* Method for editing an existing ticket
	private function edit_ticket_units() {
			$ticket_id       =   sanitize_text_field( $_POST['ticket_id'] );
                        $units_fe   =    $_POST['quantity'];                                  
                        $ticket = \Tidplus\api\DataApi::get_ticket_info_by_id($ticket_id);
                        $units_db = $ticket[0]->units;                                          
                        $data['units']   =   ($units_fe - $units_db) * (-1);                   
			global $wpdb;
			$table = $this->get_table_name( 'ticket' );
			$wpdb->update( $table, $data, array( 'ticket_id' => $ticket_id ) );
		
	}   */
        
        
        private function edit_ticket_units() {
                $order_id_max = \Tidplus\api\DataApi::get_latest_order_id();
                $order_id = $order_id_max[0]->MaxOrderId;
                $order_info = \Tidplus\api\DataApi::get_order_info_by_id($order_id);
                $ticket_id = $order_info[0]-> ticket_id;
                $quantity = $order_info[0]-> units;
                $ticket_info = \Tidplus\api\DataApi::get_ticket_info_by_id($ticket_id);
                $units_db = $ticket_info[0]->units; 
                $data['units']   =   ($units_db - $quantity);   
               
                global $wpdb;
                $table = $this->get_table_name( 'ticket' );
                $wpdb->update( $table, $data, array( 'ticket_id' => $ticket_id ) );
        }
        
        
        private function send_customer_email() {

                        $order_id_max = \Tidplus\api\DataApi::get_latest_order_id();
                        $order_id = $order_id_max[0]->MaxOrderId;
                        $order_info = \Tidplus\api\DataApi::get_order_info_by_id($order_id);
                        $ticket_id = $order_info[0]-> ticket_id;
                        $ticket_info = \Tidplus\api\DataApi::get_ticket_info_by_id($ticket_id);
                        
                        $time = json_decode($ticket_info[0] -> ticket_time);
                        foreach ($time as $value) {
                                $hour = $value->time_hour;
                                $minute = $value->time_minute;                            
                        }
                        
                        $message = array();  
			$email_to     =   $order_info[0]->user_email;
                        $subject      =   "You bought new tickets! - ORDER NO. ".$order_id_max[0]->MaxOrderId;  
                        $from = \Tidplus\api\DataApi::get_settings( 'default_email' );
                        $from_name = \Tidplus\api\DataApi::get_settings( 'email_name' );
                        
                        array_push($message, 'TICKET DETAILS');
                        array_push($message, $order_info[0]-> ticket_name);
                        array_push($message, 'Location : '.$ticket_info[0]-> address);
                        array_push($message, 'Time : '.$hour.':'.$minute);
                        array_push($message, 'Number of items : '.$order_info[0]-> units);
                        array_push($message, 'Total price : '.$order_info[0]-> price.' '.$order_info[0]-> currency);
                        array_push($message, 'Refference No : '.$order_id);
                        array_push($message, 'Status : '.$order_info[0]-> status);
                        array_push($message, " ");
                        array_push($message, 'CUSTOMER DETAILS');
			array_push($message, $order_info[0]-> user_name);
                        array_push($message, $order_info[0]-> user_surname);
                        array_push($message, $order_info[0]-> user_email);
                        array_push($message, $order_info[0]-> user_phone);

                        $headers  = 'From: '.$from_name.'<'.$from.'>'."\r\n";
                        $headers .= 'Reply-To: '.$from_name.'<'.$from.'>';
                   
                        wp_mail($email_to, $subject, implode(" \n", $message), $headers);
                        
			return true;
	}
        
        
        private function send_customer_email_on_approval() {

                        $order_id    =   sanitize_text_field( $_POST['id'] );
                        $order_info = \Tidplus\api\DataApi::get_order_info_by_id($order_id);
                        $ticket_id = $order_info[0]-> ticket_id;
                        $ticket_info = \Tidplus\api\DataApi::get_ticket_info_by_id($ticket_id);
                        
                        $time = json_decode($ticket_info[0] -> ticket_time);
                        foreach ($time as $value) {
                                $hour = $value->time_hour;
                                $minute = $value->time_minute;                            
                        }
                        
                        $message = array();  
			$email_to     =   $order_info[0]->user_email;
                        $subject      =   "You bought new tickets! - ORDER NO. ".$order_id;  
                        $from = \Tidplus\api\DataApi::get_settings( 'default_email' );
                        $from_name = \Tidplus\api\DataApi::get_settings( 'email_name' );
                        
                        array_push($message, 'TICKET DETAILS');
                        array_push($message, $order_info[0]-> ticket_name);
                        array_push($message, 'Location : '.$ticket_info[0]-> address);
                        array_push($message, 'Time : '.$hour.':'.$minute);
                        array_push($message, 'Number of items : '.$order_info[0]-> units);
                        array_push($message, 'Total price : '.$order_info[0]-> price.' '.$order_info[0]-> currency);
                        array_push($message, 'Refference No : '.$order_id);
                        array_push($message, 'Status : '.$order_info[0]-> status);
                        array_push($message, " ");
                        array_push($message, 'CUSTOMER DETAILS');
			array_push($message, $order_info[0]-> user_name);
                        array_push($message, $order_info[0]-> user_surname);
                        array_push($message, $order_info[0]-> user_email);
                        array_push($message, $order_info[0]-> user_phone);

                        $headers  = 'From: '.$from_name.'<'.$from.'>'."\r\n";
                        $headers .= 'Reply-To: '.$from_name.'<'.$from.'>';
                   
                        wp_mail($email_to, $subject, implode(" \n", $message), $headers);
                        
			return true;
	}
        
        
        private function send_admin_email() {
		              
                    
                        $order_id_max = \Tidplus\api\DataApi::get_latest_order_id();
                        $order_id = $order_id_max[0]->MaxOrderId;
                        $order_info = \Tidplus\api\DataApi::get_order_info_by_id($order_id);
                        $ticket_id = $order_info[0]-> ticket_id;
                        $ticket_info = \Tidplus\api\DataApi::get_ticket_info_by_id($ticket_id);
                        
                        $time = json_decode($ticket_info[0] -> ticket_time);
                        foreach ($time as $value) {
                                $hour = $value->time_hour;
                                $minute = $value->time_minute;                            
                        }
                        
                        $message = array();  
			$email_to     =   \Tidplus\api\DataApi::get_settings( 'default_email' );
                        $subject      =   "New Order! - ORDER NO. ".$order_id_max[0]->MaxOrderId;  
                        $from = $order_info[0]-> user_email;
                        $from_name = $order_info[0]-> user_name;
                        
                        array_push($message, 'TICKET DETAILS');
                        array_push($message, $order_info[0]-> ticket_name);
                        array_push($message, 'Location : '.$ticket_info[0]-> address);
                        array_push($message, 'Time : '.$hour.':'.$minute);
                        array_push($message, 'Number of items : '.$order_info[0]-> units);
                        array_push($message, 'Total price : '.$order_info[0]-> price.' '.$order_info[0]-> currency);
                        array_push($message, 'Refference No : '.$order_id);
                        array_push($message, 'Status : '.$order_info[0]-> status);
                        array_push($message, " ");
                        array_push($message, 'CUSTOMER DETAILS');
			array_push($message, $order_info[0]-> user_name);
                        array_push($message, $order_info[0]-> user_surname);
                        array_push($message, $order_info[0]-> user_email);
                        array_push($message, $order_info[0]-> user_phone);

                        $headers  = 'From: '.$from_name.'<'.$from.'>'."\r\n";
                        $headers .= 'Reply-To: '.$from_name.'<'.$from.'>';
                   
                        wp_mail($email_to, $subject, implode(" \n", $message), $headers);
                        
			return true;
		
	}
 
        
        // Method for editing an existing ticket
	private function edit_ticket() {
		if ( $this->verify_nonce( 'edit_ticket_nonce' ) == true ) {
			$ticket_id       =   sanitize_text_field( $_POST['ticket_id'] );
			$data['name']     =   sanitize_text_field( $_POST['name'] );
			$data['address']  =   sanitize_text_field( $_POST['address'] );
			$data['status']   =   sanitize_text_field( $_POST['status'] );
                        $data['ticket_timestamp']      =   strtotime( sanitize_text_field( $_POST['ticket_timestamp'] ) );
                        $time_hour         =   $this->sanitized_array( $_POST['time_hour'] );
			$time_minute         =   $this->sanitized_array( $_POST['time_minute'] );
			$data['ticket_time']       =   $this->encode_time( $time_hour, $time_minute );
                        $data['price']   =   sanitize_text_field( $_POST['price'] );
                        $data['units']   =   sanitize_text_field( $_POST['units'] );
			global $wpdb;
			$table = $this->get_table_name( 'ticket' );
			$wpdb->update( $table, $data, array( 'ticket_id' => $ticket_id ) );
		}
	}
        
        
        	// Method for editing an existing ticket
	private function edit_ticket_details() {
		if ( $this->verify_nonce( 'edit_ticket_details_nonce' ) == true ) {
			$ticket_id       =   sanitize_text_field( $_POST['ticket_id'] );
			$data['details']     =   sanitize_text_field( $_POST['details'] );
                        $data['extra_details']     =   sanitize_text_field( $_POST['extra-details'] );
                        $comments_names         =   $this->sanitized_array( $_POST['comments_name'] );
			$comments_notes         =   $this->sanitized_array( $_POST['comments_note'] );
			$data['comments']       =   $this->encode_comments( $comments_names, $comments_notes );
			$extra_names             =   $this->sanitized_array( $_POST['extra_name'] );
			$extra_notes             =   $this->sanitized_array( $_POST['extra_note'] );
			$data['extra']           =   $this->encode_extra( $extra_names, $extra_notes );
			global $wpdb;
			$table = $this->get_table_name( 'ticket' );
			$wpdb->update( $table, $data, array( 'ticket_id' => $ticket_id ) );
		}
	}
        
        // Method for encoding time when creating ticket
        private function encode_time( $time_hour, $time_minute ) {
            $number_of_comments_entries = sizeof( $time_hour );
            $comments_entries           = array();
            for ( $i = 0; $i < $number_of_comments_entries; $i++ ) {
                    $new_comments_entry = array(
                            'time_hour' => $time_hour[$i],
                            'time_minute' => $time_minute[$i]
                    );
                    array_push( $comments_entries, $new_comments_entry );
            }
            return json_encode( $comments_entries );
	}
        
    
        // Method for deleting an existing prescription
	private function delete_ticket() {
			if ( $this->verify_nonce( 'delete_ticket_nonce' ) == true ) {
			$ticket_id       =   sanitize_text_field( $_POST['id'] );
			global $wpdb;
			$table = $this->get_table_name( 'ticket' );
			$wpdb->delete( $table, array( 'ticket_id' => $ticket_id ) );
		}
	}
            
        // Method for deleting an existing order
	private function delete_order() {
			if ( $this->verify_nonce( 'delete_order_nonce' ) == true ) {
			$order_id       =   sanitize_text_field( $_POST['id'] );
			global $wpdb;
			$table = $this->get_table_name( 'orders' );
			$wpdb->delete( $table, array( 'order_id' => $order_id ) );
		}
	}

	// Method for encoding multiple medicine entries into a json object
	private function encode_comments( $comments_names, $comments_notes ) {
		$number_of_comments_entries = sizeof( $comments_names );
		$comments_entries           = array();
		for ( $i = 0; $i < $number_of_comments_entries; $i++ ) {
			$new_comments_entry = array(
				'comments_name' => $comments_names[$i],
				'comments_note' => $comments_notes[$i]
			);
			array_push( $comments_entries, $new_comments_entry );
		}
		return json_encode( $comments_entries );
	}
        


	// Method for encoding multiple extra entries into a json object
	private function encode_extra( $extra_names, $extra_notes ) {
		$number_of_extra_entries = sizeof( $extra_names );
		$extra_entries           = array();
		for ( $i = 0; $i < $number_of_extra_entries; $i++ ) {
			$new_extra_entry = array(
				'extra_name' => $extra_names[$i],
				'extra_note' => $extra_notes[$i]
			);
			array_push( $extra_entries, $new_extra_entry );
		}
		return json_encode( $extra_entries );
	}

	// Method for editing plugin settings
	private function edit_settings() {
		if ( $this->verify_nonce( 'edit_settings_nonce' ) == true ) {
			$table = $this->get_table_name( 'settings' );
			global $wpdb;
			// Update organiser name
			$data['description']    =   sanitize_text_field( $_POST['organiser_name'] );
			$wpdb->update( $table, $data, array( 'type' => 'organiser_name' ) );
			// Update phone number
			$data['description']    =   sanitize_text_field( $_POST['phone_number'] );
			$wpdb->update( $table, $data, array( 'type' => 'phone_number' ) );
                        
                        // Update customer notification
			$data['description']    =   sanitize_text_field( $_POST['customer_notification'] );
			$wpdb->update( $table, $data, array( 'type' => 'customer_notification' ) );
                        
                        // Update admin notification
			$data['description']    =   sanitize_text_field( $_POST['admin_notification'] );
			$wpdb->update( $table, $data, array( 'type' => 'admin_notification' ) );
                        
			// Update "from" email address
			$data['description']    =   sanitize_email( $_POST['default_email'] );
			$wpdb->update( $table, $data, array( 'type' => 'default_email' ) );
            // Update email address name
			$data['description']    =   sanitize_text_field( $_POST['email_name'] );
			$wpdb->update( $table, $data, array( 'type' => 'email_name' ) );
			// Update default ticket
			$data['description']    =   sanitize_text_field( $_POST['default_ticket_id'] );
			$wpdb->update( $table, $data, array( 'type' => 'default_ticket_id' ) );
			// Update default currency
			$data['description']    =   sanitize_text_field( $_POST['default_currency'] );
			$wpdb->update( $table, $data, array( 'type' => 'default_currency' ) );
                        
                        // Update terms & conditions status
			$data['description']    =   sanitize_text_field( $_POST['terms_conditions'] );
			$wpdb->update( $table, $data, array( 'type' => 'terms_conditions' ) );
                        
                        // Update terms & conditions link
			$data['description']    =   sanitize_text_field( $_POST['terms_conditions_link'] );
			$wpdb->update( $table, $data, array( 'type' => 'terms_conditions_link' ) );
                        
                        // Update ticket info status (show/hide)
			$data['description']    =   sanitize_text_field( $_POST['show_ticket_info'] );
			$wpdb->update( $table, $data, array( 'type' => 'show_ticket_info' ) );

			/**
			 * Author : Nam Loc Vo
			 * email : namloc254@gmail.com
			 * Description : Save paypal settings
			 */
			// Update paypal enable
			$data['description']    =   sanitize_text_field( $_POST['paypal_enable'] );
			$wpdb->update( $table, $data, array( 'type' => 'paypal_enable' ) );

			// Update paypal email
			$data['description']    =   sanitize_text_field( $_POST['paypal_email'] );
			$wpdb->update( $table, $data, array( 'type' => 'paypal_email' ) );

			// Update paypal live client ID
			$data['description']    =   sanitize_text_field( $_POST['paypal_live_client_id'] );
			$wpdb->update( $table, $data, array( 'type' => 'paypal_live_client_id' ) );

			// Update paypal live client secret
			$data['description']    =   sanitize_text_field( $_POST['paypal_live_client_secret'] );
			$wpdb->update( $table, $data, array( 'type' => 'paypal_live_client_secret' ) );

			// Update paypal sandbox mode
			$data['description']    =   sanitize_text_field( $_POST['paypal_sandbox_mode'] );
			$wpdb->update( $table, $data, array( 'type' => 'paypal_sandbox_mode' ) );

			// Update paypal test client ID
			$data['description']    =   sanitize_text_field( $_POST['paypal_test_client_id'] );
			$wpdb->update( $table, $data, array( 'type' => 'paypal_test_client_id' ) );

			// Update paypal test client secret
			$data['description']    =   sanitize_text_field( $_POST['paypal_test_client_secret'] );
			$wpdb->update( $table, $data, array( 'type' => 'paypal_test_client_secret' ) );
                        
                        // Update approving method
			$data['description']    =   sanitize_text_field( $_POST['manual_approve'] );
			$wpdb->update( $table, $data, array( 'type' => 'manual_approve' ) );

		}
	}
       
        

	// Convenient method for getting a table name of this plugin
	private function get_table_name( $table ) {
		global $wpdb;
		return $wpdb->prefix . 'tidplus_' . $table;
	}

	// Convenient method for sanitizing an array and return a sanitized array
	private function sanitized_array( $array ) {
		$sanitized_array = array();
		$i = 0;
		foreach ( $array as $value ) {
			$sanitized_array[ $i ] = ( isset( $value ) ) ? sanitize_text_field( $value ) : '';
			$i++;
		}
		return $sanitized_array;
	}

	// Convenient method for verifying wp nonce (provided that nonce field name and action is same)
	private function verify_nonce( $nonce_name ) {
		if ( $_POST[$nonce_name] ) {
			if ( wp_verify_nonce( $_POST[$nonce_name], $nonce_name ) ) {
				return true;
			}
			return false;
		}
		return false;
	}
           
}