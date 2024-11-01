<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<?php
    use \Tidplus\base\AjaxPosts;
    use \Tidplus\api\DataApi;

    $results = DataApi::get_ticket_info_by_id( AjaxPosts::$param1 );
    foreach ( $results as $row ):
?>
<div class="row mt-4">
    <div class="col">
        <form method="post" class="ticket-edit-details-form" action="<?php echo admin_url();?>admin-post.php">

            <input type="hidden" name="action" value="tidplus">
            <input type="hidden" name="task" value="edit_ticket_details">
            <input type="hidden" name="ticket_id" value="<?php echo $row->ticket_id; ?>">
            <input type="hidden" name="edit_ticket_details_nonce" value="<?php echo wp_create_nonce( 'edit_ticket_details_nonce' ); ?>">
            <div class="form-group">
                <label><b>Info</b></label>
                <input type="text" name="details" rows="2" class="form-control" id="details" value="<?php echo $row->details; ?>">
            </div>
            <div class="form-group">
                <label><b>Note (will be red highlighted)</b></label>
                <input type="text" name="extra-details" rows="2" class="form-control" id="extra-details" value="<?php echo $row->extra_details; ?>">
            </div> 
<div style="display: none"> 
            <label><b>Comments</b></label>
                <div id="comments_entry">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-5">
                                <input type="text" name="comments_name[]" class="form-control" placeholder="-">
                            </div>
                            <div class="col-6">
                                <input type="text" name="comments_note[]" class="form-control" placeholder="-">
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-outline-danger delete-btn btn-sm"
                                        onclick="delete_parent_element(this, 'comments')">
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="append_holder_for_comments_entries"></div>
                <div class="form-group">
                    <button type="button" class="btn btn-info btn-add-more"
                            onclick="append_blank_entry('comments')">
                        <i class="fa fa-plus"></i> &nbsp; Add more comments
                    </button>
                </div>   
                
           <label><b>Extra</b></label>
                <div id="extra_entry">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-5">
                                <input type="text" name="extra_name[]" class="form-control" placeholder="-">
                            </div>
                            <div class="col-6">
                                <input type="text" name="extra_note[]" class="form-control" placeholder="-">
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-outline-danger delete-btn btn-sm"
                                        onclick="delete_parent_element(this, 'extra')">
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="append_holder_for_extra_entries"></div>
                <div class="form-group">
                    <button type="button" class="btn btn-info btn-add-more"
                            onclick="append_blank_entry('extra')">
                        <i class="fa fa-plus"></i> &nbsp; Add More Tests
                    </button>
                </div>
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
    
     var blank_comments_entry = '';
    var blank_extra_entry = '';
    var number_of_comments = 1;
    var number_of_extra = 1;

    var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
    
    jQuery(document).ready(function() {
          jQuery('.datepicker').datepicker({
            onSelect: function () {
              
            }
        });
        

        // Initialize select2
        jQuery('.select2').select2({
            width: 'resolve'
        });
        
        blank_comments_entry = jQuery('#comments_entry').html();
        blank_extra_entry = jQuery('#extra_entry').html();

        var options = {
            beforeSubmit        :   validate,
            success             :   showResponse,
            resetForm           :   true
        };
        jQuery('.ticket-edit-details-form').submit(function() {
            jQuery(this).ajaxSubmit(options);
            return false;
        });
    });
    
    function append_blank_entry(selector) {
        if (selector == 'comments') {
            number_of_comments = number_of_comments + 1;
            jQuery('#append_holder_for_comments_entries').append(blank_comments_entry);
        } else {
            number_of_extra = number_of_extra + 1;
            jQuery('#append_holder_for_extra_entries').append(blank_extra_entry);
        }
    }

    function delete_parent_element(n, selector) {
        if (selector == 'comments') {
            if (number_of_comments > 1) {
                n.parentNode.parentNode.parentNode.parentNode.removeChild(n.parentNode.parentNode.parentNode);
            }
            if (number_of_comments != 1) {
                number_of_comments = number_of_comments - 1;
            }
        } else {
            if (number_of_extra > 1) {
                n.parentNode.parentNode.parentNode.parentNode.removeChild(n.parentNode.parentNode.parentNode);
            }
            if (number_of_extra != 1) {
                number_of_extra = number_of_extra - 1;
            }
        }
    }
    

    function validate() {

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

        notify( 'Ticket details was updated successfully', 'success' );

    }
    
        
    $body = jQuery("body");

jQuery(document).on({
    ajaxStart: function() { $body.addClass("loading");    },
     ajaxStop: function() { $body.removeClass("loading"); }    
});
    
</script>