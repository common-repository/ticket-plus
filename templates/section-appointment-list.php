<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<?php
    use \Tidplus\api\DataApi;
    use \Tidplus\base\AjaxPosts;

    $base = new \Tidplus\base\BaseController();

    if ( AjaxPosts::$param1 != '' ) {
        $date = strtotime(AjaxPosts::$param1);
        $date = date( get_option( 'date_format' ), $date );
    }
    else
	    $date = date( get_option( 'date_format' ) );

    $appointments = DataApi::get_appointments( $date );
    $number_of_appointments = count( $appointments );
?>
    <div class="row">
        <div class="col">
            <h6 class="appointment-list-num"><b>Events List (<?php echo $number_of_appointments;?>)</b></h6>
            <h6 class="mt-4 text-muted appointment-date"><b><i class="fa fa-calendar"></i> <?php echo $date;?></b></h6>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <div class="shadow-box">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>Options</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ( $number_of_appointments > 0 ) :
                                $count = 1;
                            foreach ( $appointments as $row ) :
                                    ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td class="patient-name">
                                    <?php
                                    $tickets = DataApi::get_ticket_info_by_id( $row->ticket_id );
                                    foreach ($tickets as $ticket) {
                                            echo $ticket->name;
                                    }
                                    ?>
                                    <p class="text-muted"></p>
                                </td>
                                
                                <?php /*
                                
                                <td>
                                    <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" value="<?php echo date( get_option( 'date_format' ), $row->appointment_timestamp );?>"
                                                       id="<?php echo $row->appointment_id;?>"
                                                       name="is_visited"
                                                    <?php echo $row->is_visited == 1 ? 'checked' : ''; ?>>
                                            Visited
                                    </label>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-info btn-sm profile"
                                            onclick="present_modal_page( 'modal-patient-profile', 'Patient Profile', <?php echo $row->patient_id; ?> )">
                                        <i class="fa fa-user"></i> Profile
                                    </button>
                                    <?php
                                        $prescription_id = DataApi::get_prescription_of_appointment( $row->appointment_id );
                                        if ( $prescription_id == '' ) {
                                    ?>
                                    <button type="button" class="btn btn-outline-primary btn-sm create"
                                        onclick="present_modal_page( 'modal-prescription-add', 'Create New Prescription', '<?php echo $row->appointment_id; ?>' )">
                                        <i class="fa fa-wpforms"></i> Create Prescription
                                    </button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-outline-primary btn-sm print"
                                                onclick="present_modal_page( 'modal-prescription-view', 'View / Print Prescription', '<?php echo $prescription_id;?>' )">
                                            <i class="fa fa-print"></i> View / Print Prescription
                                        </button>
                                    <?php } ?>
                                </td>    */ ?>
                            </tr>
                            <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php if ( $number_of_appointments == 0 ) : ?>
    <div class="row">
        <div class="col">
            <div class="alert alert-info">
                There is nothing on <b><?php echo $date; ?></b>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>

    jQuery(document).ready(function () {

		jQuery('input[type=checkbox][name=is_visited]').click(function (event) {
		   event.preventDefault();
		   confirm_action( 'confirm-action', 'Are you sure to change the appointment status ?', 'change_appointment_status', this.id,
			'section-appointment-list', 'appointment-list', 'Appointment status was changed successfully', this.value);
		});
		jQuery('input[name=is_visited]:checked').parent().parent().parent().addClass('visited-row');

    });

</script>
