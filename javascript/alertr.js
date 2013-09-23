(function($) {
	$.fn.alertr = function(alertDuration) {
		var element = this;
		if ($(element).length) {
			$(element).fadeIn('slow');
			if (alertDuration) {
				var alerttimer = window.setTimeout(function() {
					$(element).trigger('click');
				}, alertDuration);
			}
			$(element).click(function() {
				window.clearTimeout(alerttimer);
				$(element).fadeOut('slow');
			});
		}
	};
})(jQuery);

// Center Function
jQuery.fn.center = function(parent) {
    if (parent) {
        parent = this.parent();
    } else {
        parent = window;
    }
    this.css({
        "position": "absolute",
        "top": ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
        "left": ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
	return this;
};

// New Center Function
jQuery.fn.centernew = function() {
    this.css({
        "position": "absolute",
        "left": (((1000 - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
	return this;
}
