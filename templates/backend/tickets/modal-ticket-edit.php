<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<?php
	use \Tidplus\base\AjaxPosts;
	use \Tidplus\api\DataApi;

	$results = DataApi::get_ticket_info_by_id( AjaxPosts::$param1 );
	foreach ( $results as $row ):
?>
<div class="row mt-4">
    <div class="col">
        <form method="post" class="ticket-edit-form"
              action="<?php echo admin_url();?>admin-post.php">

            <input type="hidden" name="action" value="tidplus">
            <input type="hidden" name="task" value="edit_ticket">
            <input type="hidden" name="ticket_id" value="<?php echo $row->ticket_id; ?>">
            <input type="hidden" name="edit_ticket_nonce" value="<?php echo wp_create_nonce( 'edit_ticket_nonce' ); ?>">

            <div class="form-group">
                    <label><b>Name</b></label>
                    <input type="text" name="name" class="form-control" placeholder="Ticket name" id="ticket-name" value="<?php echo $row->name; ?>">
            </div>
            <div class="form-group">
                    <label><b>Address</b></label>
                    <input type="text" name="address" class="form-control" placeholder="Ticket address" id="ticket-address" value="<?php echo $row->address; ?>">
            </div>
            <div class="form-group">
                <label><b>Date</b></label>
                <input type="text" class="form-control datepicker" name="ticket_timestamp" id="timestamp" value="<?php echo date( get_option( 'date_format' ), $row->ticket_timestamp );?>">
            </div>
            <div class="form-group">
                <label><b>Time</b></label>
                <select name="time_hour[]" class="form-control select2" id="default_time" value="12"
		        style="width: 20%;">
                    <?php
                    $time = json_decode($row->ticket_time);
                    foreach ($time as $values) {
                        $hour = $values->time_hour;
                        $minute = $values->time_minute;
                    }
                    $hours = array();
                    for ($i=0;$i<24;$i++){
                        array_push($hours, $i);
                    }
                    foreach ($hours as $value) {
                        if($value<10){
                            $class = '';
                            if ("0".$value == $hour){$class = 'selected';}
                            echo '<option value='."0".$value.' '.$class.'>'."0".$value.'</option>';
                        }
                        else {
                            $class = '';
                            if ($value == $hour){$class = 'selected';}
                            echo '<option value='.$value.' '.$class.'>'.$value.'</option>';                        
                        }
                    }
                    ?>
		</select>
                <select name="time_minute[]" class="form-control select2" id="default_time"
		        style="width: 20%;">
                    <?php 
                    $minutes = array();
                    for ($i=0;$i<60;$i++){
                        array_push($minutes, $i);
                    }
                    foreach ($minutes as $value) {
                        if($value<10){
                            $class = '';
                            if ("0".$value == $minute){$class = 'selected';}
                            echo '<option value='."0".$value.' '.$class.'>'."0".$value.'</option>';
                        }
                        else {
                            $class = '';
                            if ($value == $minute){$class = 'selected';}
                            echo '<option value='.$value.' '.$class.'>'.$value.'</option>';                        
                        }
                    }
                    ?>
		</select>
                <span class="mt-1 text-muted appointment-date" style="width: 20%;"><i class="fa fa-clock-o"></i> 
                    <?php if($row->ticket_time){
                        $time = json_decode($row -> ticket_time);
                        foreach ($time as $value) {
                            $hour = $value->time_hour;
                            $minute = $value->time_minute;
                             print_r(' '.'currently set at: '.$hour.':'.$minute);
                        }
                       
                        } 
                        else echo '-'; ?></span>
            </div>
            <div class="form-group">
                    <label><b>Price</b></label>
                    <input type="text" name="price" class="form-control" placeholder="Ticket price" id="ticket-price" value="<?php echo $row->price; ?>">
            </div>
            <div class="form-group">
                    <label><b>Units</b></label>
                    <input type="text" name="units" class="form-control" id="units" value="<?php echo $row->units; ?>">
            </div>
            <div class="form-group">
                <label><b>Status</b></label>
                <select name="status" class="form-control select2"
                        style="width: 100%;">
                    <option value="1" <?php if ( $row->status == 1 ) echo 'selected'; ?>>Active</option>
                    <option value="0" <?php if ( $row->status == 0 ) echo 'selected'; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> &nbsp; Update
                </button>
                <a href="#" class="btn btn-info btn-cancel" data-dismiss="modal">
                        <i class="fa fa-close"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

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
        jQuery('.ticket-edit-form').submit(function() {
            jQuery(this).ajaxSubmit(options);
            return false;
        });
    });

    function validate() {
        date = jQuery('#timestamp').val();
        var ticket_name = jQuery('#ticket-name').val();
        var ticket_address = jQuery('#ticket-address').val();

        if (ticket_name == '' || ticket_address == '') {
            notify('You must enter ticket name and address', 'warning');
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

        notify( 'Ticket was updated successfully', 'success' );

    }
        
    $body = jQuery("body");

    
</script>
