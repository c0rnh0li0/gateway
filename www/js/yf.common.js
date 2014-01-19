$(document).ready(function() {
   
    $.datepicker.setDefaults( $.datepicker.regional[ "cs" ] );

    // odeslání na formulářích
    $("form").submit(function () {
	    $(this).ajaxSubmit();
	    return false;
    });
  
});

