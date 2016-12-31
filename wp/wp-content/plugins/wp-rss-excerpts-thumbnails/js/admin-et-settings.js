jQuery(document).ready(function($) { 
   $("#excerpt-enable").click(function() {
        $("#excerpt-settings").toggle(this.checked);

    }).triggerHandler('click');
});
