<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<?php
    use \Tidplus\api\DataApi;
    $tickets_count = DataApi::get_days_event_counts();
?>

<style>
    .fc-event {
        background-color: #0074aa;
        border-radius: 0px;
        border: 2px solid #0074aa;
        color: #ffffff !important;
        cursor: pointer;
    }
    .fc-day {
        cursor: pointer;
    }
    .fc-left h2 {
        font-weight: 700;
        font-size: 1.1em;
    }
</style>

<div class="row">
    <div class="col">
        <h6 class="text-muted"><i class="fa fa-info-circle"></i> &nbsp; Click on a date or event to have an overview</h6>
    </div>
</div>

<div class="row mt-4">
    <div class="col">
        <div class="shadow-box">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<script>
    var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";

    jQuery(document).ready(function() {

        jQuery('#calendar').fullCalendar({
            dayClick: function ( date ) {
                var selected_date = date.format();
                // jQuery('#appointment-list').block( { message: null, overlayCSS: { backgroundColor: '#fff' } } );
                make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-appointment-list', 'appointment-list', selected_date );
            },
            eventClick: function (calEvent) {
                var selected_date = calEvent.start.format();
                // jQuery('#appointment-list').block( { message: null, overlayCSS: { backgroundColor: '#fff' } } );
                make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-appointment-list', 'appointment-list', selected_date );
            },
			eventRender: function(event, element, view) {                   
				var ntoday = new Date().getTime();
				var eventEnd = moment( event.end ).valueOf();
				var eventStart = moment( event.start ).valueOf();
				if (!event.end){
					if (eventStart < ntoday){
						element.addClass("fc-past-event");
						element.children().addClass("fc-past-event");
					}
				} else {
					if (eventEnd < ntoday){
						element.addClass("fc-past-event");
						element.children().addClass("fc-past-event");
					}
				}
			},
            events: [
		        <?php foreach ( $tickets_count as $row ): ?>
                {
                    title: '<?php echo $row->total; if($row->total>1): echo ' Events'; else: echo' Event'; endif;?>',
                    start: new Date(<?php echo date('Y', $row->ticket_timestamp); ?>,
				        <?php echo date('m', $row->ticket_timestamp) - 1; ?>,
				        <?php echo date('d', $row->ticket_timestamp); ?>),
                    allDay: true
                },
		        <?php endforeach;?>
            ]
        });

        jQuery('.fc-today-button').click(function () {
            make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-appointment-list', 'appointment-list', '<?php echo date('Y-m-d');?>' );
        });

    });
    
</script>