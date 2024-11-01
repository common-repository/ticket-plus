<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<div class="row mt-4">
    <div class="col">
        <form method="post" class="appointment-add-form" action="<?php echo admin_url();?>admin-post.php">
            <input type="hidden" name="action" value="tidplus">
            <input type="hidden" name="task" value="new_appointment">
            <input type="hidden" name="new_appointment_nonce" value="<?php echo wp_create_nonce( 'new_appointment_nonce' ); ?>">

            <div class="form-group radio-group">
                <label>
                    <input class="form-check-input" type="radio" name="patient_type" value="old" checked="checked">
                    Old Patient
                </label>
                <label>
                    <input class="form-check-input" type="radio" name="patient_type" value="new">
                    New Patient
                </label>
            </div>
            <div id="new_patient">
                <div class="form-group">
                    <label><b>Name</b></label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Patient's Name">
                </div>
                <div class="form-group">
                    <label><b>Email</b></label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Patient's email address">
                </div>
                <div class="form-group">
                    <label><b>Phone</b></label>
                    <input type="text" name="phone" class="form-control" id="phone" placeholder="Patient's phone number">
                </div>
                <div class="form-group">
                    <label><b>Age</b></label>
                    <input type="text" name="age" class="form-control" placeholder="Patient's age">
                </div>
                <div class="form-group">
                    <label><b>Gender</b></label>
                    <select name="gender" class="form-control select2" style="width: 100%">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
            </div>
            <div id="old_patient">
                <div class="form-group">
                    <label><b>Patient</b></label>
                    <select name="patient_id" class="form-control select2" id="patient_id" style="width: 100%">
                        <option value="">Select a patient</option>
                        <?php
                            $patients = \Tidplus\api\DataApi::get_patients();
                            foreach ( $patients as $row ):
                        ?>
                        <option value="<?php echo $row->patient_id; ?>">
                            <?php echo $row->name; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label><b>Ticket</b></label>
                <select name="ticket_id" class="form-control select2" id="ticket_id" style="width: 100%;" onchange="load_schedules()">
                    <?php
                    $tickets = \Tidplus\api\DataApi::get_tickets();
                    $default_ticket = \Tidplus\api\DataApi::get_settings( 'default_ticket_id' );
                    foreach ( $tickets as $row ):
                    if ( $row->status == 0 )
                        continue;
                                            ?>
                    <option value="<?php echo $row->ticket_id; ?>"
                        <?php if ( $default_ticket == $row->ticket_id ) echo 'selected'; ?>>
                        S<?php echo $row->name; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label><b>Date</b></label>
                <input type="text" class="form-control datepicker" name="appointment_timestamp" id="timestamp" value="<?php echo date( get_option( 'date_format' ) );?>">
            </div>
            <div id="schedule-holder">

            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> &nbsp; Create Appointment
                </button>
                <a href="#" class="btn btn-info btn-cancel" data-dismiss="modal">
                    <i class="fa fa-close"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>

    var patient_type = 'old';
    var date, day;
    var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";

    jQuery(document).ready(function() {

        jQuery('.datepicker').datepicker({
            onSelect: function () {
                load_schedules();
            }
        });

        jQuery('.select2').select2({
            width: 'resolve'
        });

        jQuery('#new_patient').hide();

        jQuery('input[type=radio][name=patient_type]').change(function () {
            patient_type = this.value;
            if (patient_type == 'new') {
                jQuery('#old_patient').hide();
                jQuery('#new_patient').fadeIn();
            } else if (patient_type == 'old') {
                jQuery('#new_patient').hide();
                jQuery('#old_patient').fadeIn();
            }
        });

        var options = {
            beforeSubmit        :   validate,
            success             :   showResponse,
            resetForm           :   true
        };
        jQuery('.appointment-add-form').submit(function() {
            jQuery(this).ajaxSubmit(options);
            return false;
        });

        load_schedules();

    });

    function validate() {
        var ticket_id = jQuery('#ticket_id').val();
        date = jQuery('#timestamp').val();
        var schedule = jQuery('#schedule').val();
        if (ticket_id == '' || date == '' || schedule == '') {
            notify( 'You must select a ticket, date and schedule', 'warning' );
            return false;
        }
        if ( patient_type === 'old' ) {
            var patient_id = jQuery('#patient_id').val();
            if ( patient_id == '' ) {
                notify( 'You must select a patient', 'warning' );
                return false;
            }
        } else {
            var name = jQuery('#name').val();
            var email = jQuery('#email').val();
            var phone = jQuery('#phone').val();
            if (name == '' || email == '' || phone == '') {
                notify( 'You must enter name, email and phone number for a new patient', 'warning' );
                return false;
            }
        }
        return true;
    }

    function showResponse() {

        jQuery('#ajax-modal-page').modal('hide');

        make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-appointment-list', 'appointment-list', date );

        make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-appointment-calendar', 'appointment-calendar' );

        notify( 'Appointment was created on ' + date, 'success' );

    }

    function get_day() {
        date = jQuery('.datepicker').datepicker('getDate');
        day = date.getDay();
    }

    function load_schedules() {
        var ticket_id = jQuery('#ticket_id').val();

        get_day();

        make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-appointment-schedule-selector', 'schedule-holder', day, ticket_id, false );
    }

</script>
