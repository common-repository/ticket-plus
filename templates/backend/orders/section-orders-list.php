<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<div class="shadow-box">
    
        
        <?php
         use \Tidplus\api\DataApi;
         use \Tidplus\base\AjaxPosts;
         use \Tidplus\base\BaseController;

        $base = new \Tidplus\base\BaseController();
        $count = 1;
        $orders = \Tidplus\api\DataApi::get_orders();        
        $settings_currency = \Tidplus\api\DataApi::get_settings( 'default_currency' );
	$countries =  \Tidplus\api\DataApi::get_countries_currency($settings_currency);
        
        if($orders == false){
            print_r('There are no orders.');
        }
        
        foreach ($orders as $order): ?>
        <table class="table mt-3">
        <thead>
            <tr>
                <td colspan="2" class="order-status-<?php echo $order->status;?>">
                    <span class="order-status-<?php echo $order->status;?>"><?php echo $order->status;?> </span>    
                </td>
                
               
    <?php                     switch ($order->status) {
                         case 'pending':
?>                       <td colspan="1">                                
                            <button type="button" class="btn btn-outline-success btn-sm"
                            onclick="confirm_action( 'confirm-action-orders',
                                                     'Approve this order?',
                                                     'edit_order_status_approved',
                                                     '<?php echo $order->order_id;?>',
                                                     'section-orders-list',
                                                     'orders-list',
                                                     'The order was approved successfully',
                                                     '<?php echo $order->user_name;?>',
                                                     '<?php echo $order->user_surname;?>',
                                                     '<?php echo $order->user_phone;?>')">
                            <i class="fa fa-check-circle"></i> &nbsp; Approve
                            </button>
                        </td>
                        <td colspan="1">                                
                            <button type="button" class="btn btn-outline-warning btn-sm"
                            onclick="confirm_action( 'confirm-action-orders',
                                                     'Cancel this order?',
                                                     'edit_order_status_canceled',
                                                     '<?php echo $order->order_id;?>',
                                                     'section-orders-list',
                                                     'orders-list',
                                                     'The order was canceled successfully',
                                                     '<?php echo $order->user_name;?>',
                                                     '<?php echo $order->user_surname;?>',
                                                     '<?php echo $order->user_phone;?>')">
                            <i class="fa fa-exclamation-circle"></i> &nbsp; Cancel
                            </button>
                        </td>
  <?php        break;
                         case 'approved':
                             ?>
                        <td colspan="1">                                
                            <button type="button" class="btn btn-outline-warning btn-sm"
                            onclick="confirm_action( 'confirm-action-orders',
                                                     'Cancel this order?',
                                                     'edit_order_status_canceled',
                                                     '<?php echo $order->order_id;?>',
                                                     'section-orders-list',
                                                     'orders-list',
                                                     'The order was canceled successfully',
                                                     '<?php echo $order->user_name;?>',
                                                     '<?php echo $order->user_surname;?>',
                                                     '<?php echo $order->user_phone;?>')">
                            <i class="fa fa-exclamation-circle"></i> &nbsp; Cancel
                            </button>
                        </td> 
                        <?php
                             break;
                         case 'completed':
                             ?>
                        <td colspan="1">                                
                            <button type="button" class="btn btn-outline-warning btn-sm"
                            onclick="confirm_action( 'confirm-action-orders',
                                                     'Cancel this order?',
                                                     'edit_order_status_canceled',
                                                     '<?php echo $order->order_id;?>',
                                                     'section-orders-list',
                                                     'orders-list',
                                                     'The order was canceled successfully',
                                                     '<?php echo $order->user_name;?>',
                                                     '<?php echo $order->user_surname;?>',
                                                     '<?php echo $order->user_phone;?>')">
                            <i class="fa fa-exclamation-circle"></i> &nbsp; Cancel
                            </button>
                        </td> 
                        <?php
                             break;

                         default:
                             break;
                     } ?>            
                
                <td colspan="6">
                
                <button type="button" class="btn btn-outline-danger btn-sm"
                    onclick="confirm_action( 'confirm-action-orders',
                                             'Delete?',
                                             'delete_order',
                                             '<?php echo $order->order_id;?>',
                                             'section-orders-list',
                                             'orders-list',
                                             'The order was deleted successfully',
                                             '<?php echo $order->user_name;?>',
                                             '<?php echo $order->user_surname;?>',
                                             '<?php echo $order->user_phone;?>')">
                    <i class="fa fa-trash"></i> &nbsp; Delete
                </button>
                </td>
            </tr>    
        <tr>
                <th>#</th>
                <th>Name</th>                
                <th>Surname</th>
                <th style="width: 300px">Email</th>
                <th>Phone</th>
                <th>Ticket</th>
                <th>Units</th>
                <th>Price</th>
                <th>Timestamp</th>
        </tr>
        </thead>
        <tbody>
        <tr class="">
            <td class="order-id" style="width: 10px;"><?php echo $order->order_id; ?></td>
            <td class="order-user-name">
                    <?php echo $order->user_name; ?>
            </td>
            
            <td class="order-user-surname">
                    <?php echo $order->user_surname; ?>
            </td>
            <td class="order-user-email">
                    <?php echo $order->user_email; ?>
            </td>
            <td class="order-user-phone">
                    <?php echo $order->user_phone; ?>
            </td>
            <td class="order-ticket">
                    <?php echo $order->ticket_name; ?>
            </td>
            <td class="order-units">
                    <?php echo $order->units; ?>
            </td>
            <td class="order-price">
                    <?php echo $order->price; ?>
            </td>
            <td class="order-timestamp">
                    <?php echo $order->date_added; ?>
            </td>
        </tr>
         </tbody>
    </table>
    <br><br>
    <?php endforeach; ?>
       
    
    <div id="preloader" class="preloader">
			<?php $base = new \Tidplus\base\BaseController();?>
            <img src="<?php echo $base->plugin_url;?>assets/images/preloader.gif">
    </div>
</div>

