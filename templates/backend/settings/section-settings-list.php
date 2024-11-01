<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );
use \Tidplus\api\DataApi;
$customer_notification = \Tidplus\api\DataApi::get_settings( 'customer_notification' );
$admin_notification = \Tidplus\api\DataApi::get_settings( 'admin_notification' );
?>
<form method="post" class="settings-edit-form"
      action="<?php echo admin_url();?>admin-post.php">

	<input type="hidden" name="action" value="tidplus">
	<input type="hidden" name="task" value="edit_settings">
    <input type="hidden" name="edit_settings_nonce" value="<?php echo wp_create_nonce( 'edit_settings_nonce' );?>">

    <br>
    
    <?php  
        /**
         * Notifications settings
         * 
         *  - Enable customer notifications
         * 
         *  - Enable administrator notifications
         * 
         *  - set administrator email
         * 
         *  - set administrator email name
         * 
         *  - set administrator phone number
         * 
         */
    ?>
    
    <h5><i class="fa fa-comments"></i> Notifications</h5>
    
    <div class="form-group">
        <label><b>Enable customer notification</b></label>
        <label class="switch">
            <input id="customer_notification" name="customer_notification" type="checkbox" <?php echo $customer_notification && $customer_notification == 'on' ? 'checked' : ''; ?> />
            <span class="slider"></span>
        </label>
    </div>
    
    <div class="form-group">
        <label><b>Enable admin notification</b></label>
        <label class="switch">
            <input id="admin_notification" name="admin_notification" type="checkbox" <?php echo $admin_notification && $admin_notification == 'on' ? 'checked' : ''; ?> />
            <span class="slider"></span>
        </label>
    </div>
    
    <div class="form-group">
        <label><b>Email</b></label>
        <input type="email" name="default_email" class="form-control" id="default_email"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'default_email' ); ?>">
    </div>
    
    <div class="form-group">
        <label><b>Email name</b></label>
        <input type="text" name="email_name" class="form-control" id="default_email"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'email_name' ); ?>">
    </div>

    <div class="form-group">
        <label><b>Phone</b></label>
        <input type="text" name="phone_number" class="form-control" id="phone_number"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'phone_number' ); ?>">
    </div>

	<div class="form-group" style="display: none;">
		<label><b>Default Ticket</b></label>
		<select name="default_ticket_id" class="form-control select2" id="default_ticket_id"
		        style="width: 100%;">
			<?php
			$settings_ticket = \Tidplus\api\DataApi::get_settings( 'default_ticket_id' );
			$tickets = \Tidplus\api\DataApi::get_tickets();
			foreach ( $tickets as $ticket ) :
				if ( $ticket->status == 0 )
					continue;
				?>
				<option value="<?php echo $ticket->ticket_id;?>"
					<?php if ( $settings_ticket == $ticket->ticket_id ) echo 'selected'; ?>>
					<?php echo $ticket->name;?>
				</option>
			<?php endforeach;?>
		</select>
	</div>    
    <br>
     <?php  
        /**
         * Tickets general settings
         * 
         *  - Currency
         * 
         *  - Organizer name
         * 
         *  - Terms & Conditions
         * 
         */
    ?>
    <h5><i class="fa fa-cogs"></i> Tickets general settings</h5>
    
	<div class="form-group">
		<label><b>Default Currency</b></label>
		<select name="default_currency" class="form-control select2" id="default_currency"
		        style="width: 100%;">
			<?php
			$settings_currency = \Tidplus\api\DataApi::get_settings( 'default_currency' );
			$countries = \Tidplus\api\DataApi::get_countries();
			foreach ( $countries as $country ) :
                if ( $country->currency_symbol == '' )
                    continue;
				?>
				<option value="<?php echo $country->ID;?>"
					<?php if ( $settings_currency == $country->ID ) echo 'selected'; ?>>
					<?php echo $country->name . ' - ' . $country->currency_name . ' - ' . $country->currency_symbol;?>
				</option>
			<?php endforeach;?>
		</select>
	</div>
        
    <div class="form-group">
        <label><b>Organiser</b></label>
        <input type="text" name="organiser_name" class="form-control" id="organiser_name"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'organiser_name' ); ?>">
    </div>
    
    <div class="form-group">
        <label>
            <b>Enable Terms & Conditions</b>
        </label>

        <label class="switch">
            <?php  
                $terms_conditions = \Tidplus\api\DataApi::get_settings( 'terms_conditions' );
            ?>
            <input id="terms_conditions" name="terms_conditions" type="checkbox" <?php echo $terms_conditions && $terms_conditions == 'on' ? 'checked' : ''; ?> />
            <span class="slider"></span>
        </label>
    </div>
    
    <div class="form-group">
        <label>
            <b>Manual approve</b>
        </label>

        <label class="switch">
            <?php  
                $manual_approve = \Tidplus\api\DataApi::get_settings( 'manual_approve' );
            ?>
            <input id="manual_approve" name="manual_approve" type="checkbox" <?php echo $manual_approve && $manual_approve == 'on' ? 'checked' : ''; ?> />
            <span class="slider"></span>
        </label>
    </div>

    <div class="form-group">
        <label for="terms_conditions_link"><b>Terms & Conditions link</b></label>
        <input type="text" name="terms_conditions_link" class="form-control" id="terms_conditions_link"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'terms_conditions_link' ); ?>" placeholder="Enter the Terms & Conditions URL">
    </div>
    <br>
    <?php  
        /**
         * PayPal settings
         * 
         *  - enable PayPal payment gateway - Live Mode
         * 
         *      - PayPal email address
         * 
         *      - Live client ID
         * 
         *      - Live secret ID
         * 
         *  - enable PayPal Sandbox
         * 
         *      - Test client ID
         * 
         *      - Test secret ID
         * 
         */
    ?>
    <h5><i class="fa fa-paypal"></i> PayPal Standard Settings</h5>

    <div class="form-group">
        <label>
            <b>Enable Payapl Gateway</b>
        </label>

        <label class="switch">
            <?php  
                $paypal_enable = \Tidplus\api\DataApi::get_settings( 'paypal_enable' );
            ?>
            <input id="paypal_enable" name="paypal_enable" type="checkbox" <?php echo $paypal_enable && $paypal_enable == 'on' ? 'checked' : ''; ?> />
            <span class="slider"></span>
        </label>
    </div>

    <div class="form-group">
        <label for="paypal_email"><b>PayPal Email</b></label>
        <input type="text" name="paypal_email" class="form-control" id="paypal_email"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'paypal_email' ); ?>" placeholder="Enter your PayPal account's email">
    </div>

    <div class="form-group">
        <label for="paypal_live_client_id"><b>Live Client ID</b></label>
        <input type="text" name="paypal_live_client_id" class="form-control" id="paypal_live_client_id"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'paypal_live_client_id' ); ?>" placeholder="Your PayPal live client ID.">
    </div>

    <div class="form-group">
        <label for="paypal_live_client_secret"><b>Live Client Secret</b></label>
        <input type="text" name="paypal_live_client_secret" class="form-control" id="paypal_live_client_secret"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'paypal_live_client_secret' ); ?>" placeholder="Your PayPal live client secret.">
    </div>

    <div class="form-group">
        <label>
            <b>Enable test mode</b>
        </label>

        <label class="switch">
            <?php  
                $paypal_sandbox_mode = \Tidplus\api\DataApi::get_settings( 'paypal_sandbox_mode' );
            ?>
            <input id="paypal_sandbox_mode" name="paypal_sandbox_mode" type="checkbox" <?php echo $paypal_sandbox_mode && $paypal_sandbox_mode == 'on' ? 'checked' : ''; ?> />
            <span class="slider"></span>
        </label>
    </div>

    <div class="form-group">
        <label for="paypal_test_client_id"><b>Test Client ID</b></label>
        <input type="text" name="paypal_test_client_id" class="form-control" id="paypal_test_client_id"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'paypal_test_client_id' ); ?>" placeholder="Your PayPal test client ID.">
    </div>

    <div class="form-group">
        <label for="paypal_test_client_secret"><b>Test Client Secret</b></label>
        <input type="text" name="paypal_test_client_secret" class="form-control" id="paypal_test_client_secret"
               value="<?php echo \Tidplus\api\DataApi::get_settings( 'paypal_test_client_secret' ); ?>" placeholder="Your PayPal test client secret.">
    </div>

    <br>
    
    <?php  
        /**
         * PayPal settings
         * 
         *  - enable PayPal payment gateway - Live Mode
         * 
         *      - PayPal email address
         * 
         *      - Live client ID
         * 
         *      - Live secret ID
         * 
         *  - enable PayPal Sandbox
         * 
         *      - Test client ID
         * 
         *      - Test secret ID
         * 
         */
    ?>
    <h5><i class="fa fa-paint-brush"></i> Ticket Style Settings</h5>
    <div class="form-group">
        <label>
            <b>Show ticket info</b>
        </label>

        <label class="switch">
            <?php  
                $show_ticket_info = \Tidplus\api\DataApi::get_settings( 'show_ticket_info' );
            ?>
            <input id="show_ticket_info" name="show_ticket_info" type="checkbox" <?php echo $show_ticket_info && $show_ticket_info == 'on' ? 'checked' : ''; ?> />
            <span class="slider"></span>
        </label>
    </div>
    <br>
	<div class="form-group">
		<button type="submit" class="btn btn-success">
			<i class="fa fa-save"></i> &nbsp; Update Settings
		</button>
            <div id="preloader" class="preloader">
			<?php $base = new \Tidplus\base\BaseController();?>
            <img src="<?php echo $base->plugin_url;?>assets/images/preloader.gif">
            </div>
	</div>    

</form>

<script>

    var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";

    jQuery(document).ready(function () {
        // Initialize select2
        jQuery('.select2').select2({
            width: 'resolve'
        });
		// Binding the form with Jquery
        var options = {
            beforeSubmit        :   validate,
            success             :   showResponse,
            resetForm           :   true
        };
        jQuery('.settings-edit-form').submit(function() {
            jQuery(this).ajaxSubmit(options);
            return false;
        });

        /**
         * Author: Nam Loc Vo
         * Email : namloc254@gmai.com
         * Description : Disable/Enable paypal sandbox fields when changed sandbox mode checkbox.
         */
        jQuery('#paypal_sandbox_mode').on('change', function(e){
            e.preventDefault();
            if( jQuery(this).is(':checked') ) {
                // Checked
                jQuery('input[name="paypal_test_client_id"], input[name="paypal_test_client_secret"]').removeAttr('disabled');
            } else {
                // Unchecked
                jQuery('input[name="paypal_test_client_id"], input[name="paypal_test_client_secret"]').attr('disabled', 'disabled');
            }
            return false;
        }).trigger('change');

    });

    function validate() {
        var ticket_id = jQuery('#default_ticket_id').val();
        var currency = jQuery('#default_currency').val();
        var email = jQuery('#default_email').val();
        var name = jQuery('#organiser_name').val();
        if (ticket_id == '' || currency == '') {
            notify( 'Please provide all the information required', 'warning' );
            return false;
        }
        return true;
    }

    function showResponse() {

        jQuery('#ajax-modal-page').modal('hide');

        make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-settings-list', 'settings-list' );

        notify( 'Settings was updated successfully ', 'success' );

    }
    
    
    $body = jQuery("body");

jQuery(document).on({
    ajaxStart: function() { $body.addClass("loading");    },
     ajaxStop: function() { $body.removeClass("loading"); }    
});

</script>