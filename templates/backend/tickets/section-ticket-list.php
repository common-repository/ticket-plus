<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<div class="shadow-box">
    
        
        <?php
         use \Tidplus\api\DataApi;
         use \Tidplus\base\AjaxPosts;
         use \Tidplus\base\BaseController;

        $base = new \Tidplus\base\BaseController();
        $count = 1;
        $tickets = \Tidplus\api\DataApi::get_tickets();        
        $settings_currency = \Tidplus\api\DataApi::get_settings( 'default_currency' );
	$countries =  \Tidplus\api\DataApi::get_countries_currency($settings_currency);
        
        if($tickets == false){
            print_r('There are no tickets. Please add a new ticket pressing "Add Ticket".');
        }
        
        
        foreach ($tickets as $ticket): ?>
        <table class="table mt-3">
        <thead>
            <tr>
                <td colspan="7">         
                <button type="button" class="btn btn-outline-info btn-sm"
                    onclick="present_modal_page( 'modal-ticket-edit', 
                                                 'Edit Ticket',
                                                 '<?php echo $ticket->ticket_id; ?>' )">
                    <i class="fa fa-pencil"></i> &nbsp; Main
                </button>
                <button type="button" class="btn btn-outline-info btn-sm"
                    onclick="present_modal_page( 'section-ticket-add-details',
                                                 '<?php echo $ticket->name;?>',
                                                 '<?php echo $ticket->ticket_id;?>' )">
                    <i class="fa fa-newspaper-o"></i> &nbsp; Details
                </button>              
                <button type="button" class="btn btn-outline-danger btn-sm"
                    onclick="confirm_action( 'confirm-action',
                                             'Delete?',
                                             'delete_ticket',
                                             '<?php echo $ticket->ticket_id;?>',
                                             'section-ticket-list',
                                             'ticket-list',
                                             'The ticket was deleted successfully',
                                             '<?php echo $ticket->name;?>',
                                             '<?php echo $ticket->address;?>',
                                             '<?php echo $ticket->status;?>')">
                    <i class="fa fa-trash"></i> &nbsp; Delete
                </button>

            </td>
            </tr>    
        <tr>
                <th>#</th>
                <th>Name</th>                
                <th>Date</th>
                <th>Status</th>
                <th>Price</th>
                <th>Units</th>
                <th>Address</th>
        </tr>
        </thead>
        <tbody>
        <tr class="<?php echo $ticket->status == 1 ? 'active' : 'inactive' ;?>">
            <td class="ticket-id" style="width: 10px;"><?php echo $ticket->ticket_id; ?></td>
            <td>
                <div tooltip="tooltip" title="[tidplus id=<?php echo $ticket->ticket_id; ?>]" data-placement="bottom">
                    <?php echo $ticket->name; ?>
                </div>
            </td>
            
            <td>
                <div class="col">
                <h6 class="mt-1 text-muted appointment-date"><i class="fa fa-calendar"></i> <?php if($ticket->ticket_timestamp){
                                                                                                        echo date( get_option( 'date_format' ), $ticket->ticket_timestamp );
                                                                                                           } 
                                                                                                        else echo '-'; ?></h6>
                    <h6 class="mt-1 text-muted appointment-date"><i class="fa fa-clock-o"></i> <?php if($ticket->ticket_time){
                        $time = json_decode($ticket -> ticket_time);
                        foreach ($time as $value) {
                            $hour = $value->time_hour;
                            $minute = $value->time_minute;
                             print_r($hour.':'.$minute);
                        }
                       
                                                                                                           } 
                                                                                                        else echo '-'; ?></h6>
                </div>
            </td>
            <td>
                <span class="badge badge-<?php echo $ticket->status == 1 ? 'success' : 'danger' ;?>">
                    <?php echo $ticket->status == 1 ? 'Active' : 'Inactive'; ?>
                </span>
            </td>
            <td>
                <span class="badge badge-<?php echo $ticket->price == true ? 'success' : 'danger' ;?>">
                    <?php echo $ticket->price == true ? $ticket->price . ' ' . $countries : 'No price'; ?>

                </span>
            </td>
            <td>
                <span class="badge badge-<?php echo $ticket->units == true ? 'success' : 'danger' ;?>">
                    <?php echo $ticket->units == true ? $ticket->units : 'No units'; ?>

                </span>
            </td>
            <td><?php echo $ticket->address; ?></td>
        </tr>
         </tbody>
    </table>
    <br><br>
    <?php endforeach; ?>
       
</div>
<div id="preloader" class="preloader">
			<?php $base = new \Tidplus\base\BaseController();?>
            <img src="<?php echo $base->plugin_url;?>assets/images/preloader.gif">
</div>

<script>

jQuery('[tooltip="tooltip"]').tooltip({
     
    disabled: true,
    close: function( event, ui ) { jQuery(this).tooltip('die');}
});


</script>