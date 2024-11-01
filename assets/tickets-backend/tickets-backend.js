/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */




jQuery('[tooltip="tooltip"]').tooltip({
     
    disabled: true,
    close: function( event, ui ) { jQuery(this).tooltip('die');}
});

