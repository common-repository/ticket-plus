<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<div class="container-fluid">
    <div class="row mr-1 page-title">
        <div class="col-lg-6">
            <h5 class="mt-2">Manage Tickets</h5>
        </div>
        
        <?php
         use \Tidplus\api\DataApi;
         use \Tidplus\base\AjaxPosts;
         use \Tidplus\base\BaseController;

        $base = new \Tidplus\base\BaseController();
        $count = 1;
        $tickets = \Tidplus\api\DataApi::get_tickets();        
        
        if(count($tickets)>=1){
            ?>
                <div class="col-lg-6">
                <a href="#" class="btn btn-success pull-right"
                            onclick="present_modal_page( 'modal-ticket-add', 'Add Ticket')">
                    <i class="fa fa-lock"></i> &nbsp; Add Ticket (only in PRO)
                </a>
                    
        </div>
        <?php     
        }
        
        else{
        ?>
        <div class="col-lg-6">
                <a href="#" class="btn btn-success pull-right"
                            onclick="present_modal_page( 'modal-ticket-add', 'Add Ticket')">
                    <i class="fa fa-plus"></i> &nbsp; Add Ticket
                </a>
        </div>
        <?php
        
        }
        ?>
    </div> 

    <hr>
    <div class="row mr-1">
        <div class="col">
            <div id="ticket-list">
                <?php include ( "$this->plugin_path/templates/backend/tickets/section-ticket-list.php" ); ?>
            </div>
        </div>
    </div>

    <?php include ( "$this->plugin_path/templates/ajax-modals/modal.php" ); ?>
</div>