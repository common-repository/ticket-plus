<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );
         use \Tidplus\api\DataApi;
         use \Tidplus\base\AjaxPosts;
         use \Tidplus\base\BaseController;

        $base = new \Tidplus\base\BaseController();
        $count = 1;
        $tickets = \Tidplus\api\DataApi::get_tickets();        
        
        if(count($tickets)<1){
            ?>
<div class="row mt-4">
    <div class="col">
        <form method="post" class="ticket-add-form"
              action="<?php echo admin_url();?>admin-post.php">

            <input type="hidden" name="action" value="tidplus">
            <input type="hidden" name="task" value="new_ticket">
            <input type="hidden" name="new_ticket_nonce" value="<?php echo wp_create_nonce( 'new_ticket_nonce' ); ?>">

            <div class="form-group">
                    <label><b>Name</b></label>
                    <input type="text" name="name" class="form-control" placeholder="Ticket name" id="ticket-name" value="">
            </div>
            <div class="form-group">
                    <label><b>Address</b></label>
                    <input type="text" name="address" class="form-control" placeholder="Ticket address" id="ticket-address" value="">
            </div>
            <div class="form-group">
                <label><b>Date</b></label>
                <input type="text" class="form-control datepicker" name="ticket_timestamp" id="timestamp" value="<?php echo date( get_option( 'date_format' ) );?>">
            </div>

            <div class="form-group">
                <label><b>Time</b></label>
                <select name="time_hour[]" class="form-control select2" id="default_time"
		        style="width: 20%;">
                    <?php 
                    $hours = array();
                    for ($i=0;$i<25;$i++){
                        array_push($hours, $i);
                    }
                    foreach ($hours as $value) {
                        if($value<10){
                            echo '<option value='."0".$value.'>'."0".$value.'</option>';
                        }
                        else {
                            echo '<option value='.$value.'>'.$value.'</option>';                        
                        }
                    }
                    ?>
		</select>
                <select name="time_minute[]" class="form-control select2" id="default_time"
		        style="width: 20%;">
                    <?php 
                    $hours = array();
                    for ($i=0;$i<61;$i++){
                        array_push($hours, $i);
                    }
                    foreach ($hours as $value) {
                        if($value<10){
                            echo '<option value='."0".$value.'>'."0".$value.'</option>';
                        }
                        else {
                            echo '<option value='.$value.'>'.$value.'</option>';                        
                        }
                    }
                    ?>
		</select>
            </div>
            <div class="form-group">
                    <label><b>Price</b></label>
                    <input type="text" name="price" class="form-control" placeholder="Ticket price" id="ticket-price" value="">
            </div>
            <div class="form-group">
                    <label><b>Units</b></label>
                    <input type="text" name="units" class="form-control" id="units" value="">
            </div>
            <div class="form-group">
                <label><b>Status</b></label>
                <select name="status" class="form-control select2"
                    style="width: 100%;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="form-group">
                    <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> &nbsp; Save
                    </button>
                    <a href="#" class="btn btn-info btn-cancel" data-dismiss="modal">
                            <i class="fa fa-close"></i> Cancel
                    </a>
            </div>
        </form>
    </div>
</div>
        <?php }
        else{
     echo 'You cannot create more tickets on this free version.';
        }?>

<script>
    jQuery(document).ready(function() {
        
        jQuery('.datepicker').datepicker({
            onSelect: function () {
              
            }
        });

        // Initialize select2
        jQuery('.select2').select2({
           width: 'resolve'
        });

        var options = {
            beforeSubmit        :   validate,
            success             :   showResponse,
            resetForm           :   true
        };
        jQuery('.ticket-add-form').submit(function() {
            jQuery(this).ajaxSubmit(options);
            return false;
        });
    });

    function validate() {
         date = jQuery('#timestamp').val();
        var ticket_name = jQuery('#ticket-name').val();
        var ticket_address = jQuery('#ticket-address').val();
        var ticket_price = jQuery('#ticket-price').val();

        if (ticket_name == '' || ticket_address == '' || ticket_price == '') {
            notify('You must enter ticket name, address and price', 'warning');
            return false;
        }
        return true;
    }
    
    function get_day() {
        date = jQuery('.datepicker').datepicker('getDate');
        day = date.getDay();
    }

    function showResponse() {
        var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";

        jQuery('#ajax-modal-page').modal('hide');

        make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-ticket-list', 'ticket-list' );

        notify( 'Ticket was added successfully', 'success' );

    }
    
        
    $body = jQuery("body");

    
</script>
