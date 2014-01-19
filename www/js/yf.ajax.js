/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
(function($) {

	$.nette = {
		success: function(payload)
		{
			// redirect
			if (payload.redirect) {
				window.location.href = payload.redirect;
				return;
			}

			// state
			if (payload.state) {
				$.nette.state = payload.state;
			}

			// snippets
			if (payload.snippets) {
				for (var i in payload.snippets) {
					$.nette.updateSnippet(i, payload.snippets[i]);
				}
			}

			// change URL (requires HTML5)
			if (window.history && history.pushState && $.nette.href) {
				history.pushState({href: $.nette.href}, '', $.nette.href);
			}
		},

		updateSnippet: function(id, html)
		{
			$('#' + id).html(html);
		},

		// create animated spinner
		createSpinner: function(id)
		{
			return this.spinner = $('<div></div>').attr('id', id ? id : 'ajax-spinner').ajaxStart(function() {
				$(this).show();

			}).ajaxStop(function() {
				$(this).hide().css({
					/*position: 'fixed',
					left: '50%',
					top: '50%'*/
				/*});

			//}).appendTo('body').hide();
	/*	    }).prependTo('#header .inner-wrapper #notifications').hide();
		},

		// current page state
		state: null,
		href: null,

		// spinner element
		spinner: null
	};


})(jQuery);


jQuery.nette.updateSnippet = function (id, html) {
    $("#" + id).fadeTo("fast", 0.3, function () {
        $(this).html(html).fadeTo("fast", 1);
    });
};

jQuery(function($) {
	// HTML 5 popstate event
	$(window).bind('popstate', function(event) {
		$.nette.href = null;
		$.post(event.originalEvent.state.href, $.nette.success);
	});

	$.ajaxSetup({
		success: $.nette.success,
		dataType: 'json'
	});

	$.nette.createSpinner('ajax-spinner');

	// apply AJAX unobtrusive way
	$('a.ajax').live('click', function(event) {
		event.preventDefault();
		if ($.active) return;

		$.post($.nette.href = this.href, $.nette.success);

		/*$.nette.spinner.css({
		    position: 'absolute'
		    height: $(this).innerHeight(),
		    width: $(this).innerWidth(),
		    top: $(this).offset().top,
		    left: $(this).offset().left
		});*/
		
		/*$.nette.spinner.css({
			position: 'absolute',
			left: event.pageX,
			top: event.pageY - 6
		});*/
           

	/*});

});*/