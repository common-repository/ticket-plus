<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

        use \Tidplus\api\DataApi;
        use \Tidplus\base\AjaxPosts;
        use \Tidplus\base\BaseController;

        if (!function_exists('tidplus_ticket_shortcode')){
        function tidplus_ticket_shortcode($atts){
            
            $a = shortcode_atts( array('id' => 1), $atts );
            $id = $a['id'];

            
        $base = new \Tidplus\base\BaseController();
        $count = 1;
        $ticket = \Tidplus\api\DataApi::get_ticket_info_by_id($id);        
        $settings_currency = \Tidplus\api\DataApi::get_settings( 'default_currency' );
        $countries =  \Tidplus\api\DataApi::get_countries_currency($settings_currency);

        ob_start(); ?>
<head>


</head>
<div id="page-ticket">
<body>
    <form class="ticket-frontend-user-form-<?php echo $ticket[0]->ticket_id ?>" method="POST" autocomplete="off" action="<?php echo admin_url();?>admin-post.php"> 
<table id="ticket-frontend-table" class="ticket-frontend-table">
  <tr id="ticket-frontend-row1" class="ticket-frontend-row1">
    <th id="ticket-frontend-row1-header" class="ticket-frontend-row1-header" rowspan="4">
        <h4 class="ticket-frontend-row1-h4"><i class="fa fa-calendar-o"></i></h4>    
        <h1 class="ticket-frontend-row1-h1"><?php echo date( 'd' , $ticket[0]->ticket_timestamp );?></h1>
        <h4 class="ticket-frontend-row1-h4"><?php echo date( 'F' , $ticket[0]->ticket_timestamp );?></h4>
        <h4 class="ticket-frontend-row1-h4"><?php echo date( 'Y' , $ticket[0]->ticket_timestamp );?></h4>
    </th>
    <th id="ticket-frontend-row1-header" class="ticket-frontend-row1-header" rowspan="2" ><i class="fa fa-clock-o"></i>
        <?php  $time = json_decode($ticket[0] -> ticket_time);
                        foreach ($time as $value) {
                                $hour = $value->time_hour;
                                $minute = $value->time_minute;
                                print_r($hour.':'.$minute);}?>
    </th>
    <th colspan="4" class="ticket-frontend-row1-name">
        <h4 class="ticket-frontend-row1-h4-name"><?php print_r ($ticket[0]->name) ?></h4>
    </th>
    <th class="ticket-frontend-row1-header"><?php echo '' ?></th>  
  </tr>
  <tr id="ticket-frontend-row2" class="ticket-frontend-row2">
    <td id="ticket-frontend-row2-address" class="ticket-frontend-row2-address" colspan="3"><?php print_r ($ticket[0]->address) ?> <i class="fa fa-map-marker"></i></td>
    <td id="ticket-frontend-row2" class="ticket-frontend-row2" colspan="2" rowspan="3"><?php echo '' ?></td>
  </tr>
  <tr id="ticket-frontend-row3" class="ticket-frontend-row3">
    <td id="ticket-frontend-row2" class="ticket-frontend-row2" rowspan="2"><?php echo '' ?></td>
    <td  id="ticket-frontend-row2" class="ticket-frontend-row2" colspan="3"><?php echo '' ?></td>
  </tr>
  <tr id="ticket-frontend-row4" class="ticket-frontend-row4">
    <td id="ticket-frontend-row4" class="ticket-frontend-row4"><?php echo '' ?></td>
    <td id="ticket-frontend-row4" class="ticket-frontend-row4"><?php echo '' ?></td>
    <td id="ticket-frontend-row4" class="ticket-frontend-row4"><?php echo '' ?></td>
  </tr>
  <tr id="ticket-frontend-row5" class="ticket-frontend-row5">
    <td id="ticket-frontend-row5-price" class="ticket-frontend-row5-price" rowspan="2" colspan="2" value="">
        <h2 id="ticket-frontend-row5-price-badge" class="ticket-frontend-row5-price-badge">
            <span class="ticket-frontend-row5-price-badge-span-<?php $ticket[0]->price == true ? 'success' : 'danger' ;?>">
                <?php echo $ticket[0]->price == true ? $ticket[0]->price . ' ' . $countries : 'No price'; ?>
            </span>
        </h2>
    </td>
    <td id="ticket-frontend-row5-available" class="ticket-frontend-row5-available"><?php $ticket[0]->units>1 ? print_r ('Available:'.' '.$ticket[0]->units) : print_r ('SOLD OUT') ?></td>
    <td id="ticket-frontend-row5" class="ticket-frontend-row5"><?php echo '' ?></td>
    <td id="ticket-frontend-row5" class="ticket-frontend-row5" colspan="3"><?php echo '' ?>
        <?php  
            $show_ticket_info = \Tidplus\api\DataApi::get_settings( 'show_ticket_info' );
            if( $show_ticket_info && $show_ticket_info == 'on' ) :
        ?>
        <div id="ticket-frontend-row5-info-button">
            <a href="javascript:void(0)"class="ticket-frontend-row5-info-button-flip" id="ticket-frontend-row5-info-button-flip-<?php echo $ticket[0]->ticket_id ?>"><i class="fa fa-info-circle"></i></a>
        </div>
        <?php endif; ?>
    </td>
  </tr>
  <tr id="ticket-frontend-row6" class="ticket-frontend-row6">
      
        <td class="ticket-frontend-row6-qty">
            <select name="quantity" class="ticket-frontend-row6-qty-select" id="ticket-frontend-row6-qty-select-<?php echo $ticket[0]->ticket_id ?>" onchange="return calculatePrice(<?php echo $ticket[0]->ticket_id ?>, <?php echo $ticket[0]->price ?> )">
                <ul>
              <?php
                  $units = $ticket[0]->units;
                  $qty = array();
                  for($i=1; $i<=$units; $i++){
                      array_push($qty, $i);
                  }
                  foreach ($qty as $value) {
                      echo '<option value='.$value.'>'.$value.'</option>';
                  }
                  $json = json_encode($qty);
              ?>
                 </ul>
          </select> 
        </td>
      <td>
      <div id="ticket-frontend-row6-total-<?php echo $ticket[0]->ticket_id ?>" class="ticket-frontend-row6-total"><?php echo 'Total: ' .$ticket[0]->price . ' ' . $countries ?></div>
      <td>
      <td id="ticket-frontend-row6-more" class="ticket-frontend-row6-more" colspan="3">
          <div id="ticket-frontend-row6-more-button">
          <a href="javascript:void(0)"class="ticket-frontend-row6-more-button-flip" id="ticket-frontend-row6-more-button-flip-<?php echo $ticket[0]->ticket_id ?>">BUY</a>
          </div>
      </td>    
  </tr>
</table>
    <div id="preloader" class="preloader">
			<?php $base = new \Tidplus\base\BaseController();?>
            <img src="<?php echo $base->plugin_url;?>assets/images/preloader.gif">
    </div>    
    <div id="ticket-frontend-user-form" class="ticket-frontend-user-form-info-toggle-<?php echo $ticket[0]->ticket_id ?>">
        <table id="ticket-frontend-user-form-table">
                <tr>
                    <td class="ticket-frontend-user">
                        <div class="ticket-frontend-user-form">  
                            <div id="details"><?php echo $ticket[0]->details ?></div>
                            <div id="note"><?php echo $ticket[0]->extra_details ?></div>
                        </div>
                    </td>
                </tr>
        </table>
    </div>
    <div id="ticket-frontend-user-form" class="ticket-frontend-user-form-toggle-<?php echo $ticket[0]->ticket_id ?>">
        
            
            <input type="hidden" name="action" value="tidplus">
            <input type="hidden" name="task" value="new_order">
            <input type="hidden" name="new_order_nonce" value="<?php echo wp_create_nonce( 'new_order_nonce' ); ?>">
            
            <table id="ticket-frontend-user-form-table">
                <tr>
                    <td class="ticket-frontend-user">
                        <div class="ticket-frontend-user-form">    
                            <input type="text" name="name" value="" placeholder="First name" id="user_name" ><br>
                             <input type="text" name="surname" value=""  placeholder="Last name" id="user_surname" ><br>
                             <input type="text" name="email_address" value="" placeholder="Email address" id="user_email" ><br>
                            <input type="text" name="phone" value=""  placeholder="Phone" id="user_phone"><br>
                            
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket[0]->ticket_id ?>">
                            <input type="hidden" name="ticket_name" value="<?php echo $ticket[0]->name ?>">
                            <input type="hidden" name="price" value="<?php echo $ticket[0]->price ?>">
                            <input type="hidden" name="currency" value="<?php echo $countries ?>">
                        </div>
                    </td>
                    <td id="ticket-frontend-user" class="ticket-frontend-user">
                        <div class="ticket-frontend-user-form"> 
                            <label class="ticket-frontend-user-form-payment-method"><i class="fa fa-money"></i> Payment on arrival
                                <input type="radio" name="payment_method" value="payment_on_arrival" checked />
                                <span class="checkmark"></span>
                            </label>

                            <?php  
                                global $wp;
                                $paypal_enable = \Tidplus\api\DataApi::get_settings( 'paypal_enable' );
                                if( $paypal_enable && $paypal_enable == 'on' ) :
                            ?>
                                <label class="ticket-frontend-user-form-payment-method"><i class="fa fa-paypal"></i>  Paypal
                                    <input type="radio" name="payment_method" value="paypal" />
                                    <span class="checkmark"></span>
                                </label>
                                <input type="hidden" name="return_url" value="<?php echo home_url( $wp->request ); ?>" />
                            <?php endif; ?>
                        </div>
                    </td>
                    <td id="ticket-frontend-user" class="ticket-frontend-user">
                        <input type="submit" value="Confirm" name="Submit" class="button-submit" />
                    </td>
                </tr>
                
                        <?php  
                                $terms_conditions = \Tidplus\api\DataApi::get_settings( 'terms_conditions' );
                                $terms_conditions_link = \Tidplus\api\DataApi::get_settings( 'terms_conditions_link' );
                                if( $terms_conditions && $terms_conditions == 'on' ) :
                            ?>
                <tr>
                   <td id="ticket-frontend-user" class="ticket-frontend-user">
                       <label class="ticket-frontend-user-form-terms-conditions"><a href="<?php echo $terms_conditions_link ?>">I agree with Terms and Conditions.</a> 
                           <input type="checkbox" name="terms_conditions" value="" id="terms_conditions"/>
                            <span class="checkmark"></span>
                        </label>
                    </td> 
                </tr>
                       <?php endif; ?>

            </table>
        
    </div>
        </form>
</body>    
</div>
        
<script type="text/javascript">
    
    var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";


       

    var select_value = document.getElementById("ticket-frontend-row6-qty-select-<?php echo $ticket[0]->ticket_id ?>").value;
    var total_div = document.getElementById("ticket-frontend-row6-total-<?php echo $ticket[0]->ticket_id ?>");
    
    function calculatePrice(ticketId, ticketPrice){
        console.log(ticketId);
        var total_div = document.getElementById("ticket-frontend-row6-total-"+ticketId);
        console.log("#ticket-frontend-row6-total-"+ticketId);
        select_value = document.getElementById("ticket-frontend-row6-qty-select-"+ticketId).value;
        var total = select_value * ticketPrice;
        if (select_value == 1){
            total_div.innerHTML = 'Total: '+ ticketPrice + ' ' + "<?=$countries?>";
        }
        else{
            total_div.innerHTML ='Total: ' + total + ' ' + "<?=$countries?>";
        }
   }


    function calculatePriceOnLoad(){
        var total_div = document.getElementById("ticket-frontend-row6-total-<?php echo $ticket[0]->ticket_id ?>");
        var select_value = document.getElementById("ticket-frontend-row6-qty-select-<?php echo $ticket[0]->ticket_id ?>").value;
        
        select_value = document.getElementById("ticket-frontend-row6-qty-select-<?php echo $ticket[0]->ticket_id ?>").value;
        var total = select_value * <?php echo $ticket[0]->price ?>;
        if (select_value == 1){
            total_div.innerHTML = 'Total: '+ <?php echo $ticket[0]->price ?> + ' ' + "<?=$countries?>";
        }
        else{
            total_div.innerHTML ='Total: ' + total + ' ' + "<?=$countries?>";
        }
   }

   window.onload = calculatePriceOnLoad ;
   
   jQuery(document).ready(function(){
  jQuery("#ticket-frontend-row6-more-button-flip-<?php echo $ticket[0]->ticket_id ?>").click(function(){
    jQuery(".ticket-frontend-user-form-toggle-<?php echo $ticket[0]->ticket_id ?>").slideToggle("slow");
  });
});

jQuery(document).ready(function(){
  jQuery("#ticket-frontend-row5-info-button-flip-<?php echo $ticket[0]->ticket_id ?>").click(function(){
    jQuery(".ticket-frontend-user-form-info-toggle-<?php echo $ticket[0]->ticket_id ?>").slideToggle("slow");
  });
});
 

        jQuery(document).ready(function() {
          

        var options = {
            beforeSubmit        :   validate,
            success             :   showResponse,
            resetForm           :   true,
        };
        jQuery('.ticket-frontend-user-form-<?php echo $ticket[0]->ticket_id ?>').submit(function() {
            jQuery(this).ajaxSubmit(options);
            return false;
        });
    });


    function validate() {
        var user_name = jQuery('#user_name').val();
        var user_surname = jQuery('#user_surname').val();
        var user_email = jQuery('#user_email').val();
        var terms_conditions = jQuery('#terms_conditions').prop("checked");
    
        if (user_name == '' || user_surname == '' || user_email == '') {
            notify('You must enter name, surname and a valid email address', 'warning');
            return false;
        } 
        
        if (terms_conditions == false) {
            notify('You must agree Terms & Conditions', 'warning');
            return false;
        }
        return true; 
    }


    function showResponse( response ) {
        // console.log(response);
        if( typeof response.success != 'undefined' ) {
            if( response.success ) {
                window.location.href = response.return_url;
            } else {
                notify( response.msg, 'error' );
            }
            return false;
        }
        var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";

        make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-frontend', 'page-ticket' );

        notify( '   SUCCESS!    the page will reload soon...', 'success' );
        
        setTimeout(function(){
            location.reload();
        }, 4000);        
    }
    
     $body = jQuery("body");

jQuery(document).on({
    ajaxStart: function() { $body.addClass("loading");    },
     ajaxStop: function() { $body.removeClass("loading"); }    
});   
 
</script>
<?php 
 return ob_get_clean();

 
 
                }
        }
if(!function_exists('tidplus_ticket_register_shortcode')){
function tidplus_ticket_register_shortcode() {

    add_shortcode( 'tidplus', 'tidplus_ticket_shortcode' );
} 
}

add_action( 'init', 'tidplus_ticket_register_shortcode' );

?>