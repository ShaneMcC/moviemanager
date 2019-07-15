function updateIcon(elem, icon, tooltip) {
	elem.removeClass();
	elem.addClass(icon);
	elem.attr('title', tooltip);
	elem.tooltip('destroy');
	elem.tooltip();
	if (elem.is(":hover")) {
		elem.tooltip('show');
	}
}

$('.dropdown-toggle').dropdown();


$(function() {
	var clipboard = new ClipboardJS('[data-clipboard-text]');

	clipboard.on('success', function(e) {
		toastr.options = {
			"closeButton": true,
			"progressBar": false,
			"positionClass": "toast-top-right",
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "2500",
			"extendedTimeOut": "1000",
		}
		toastr.success('Path copied to clipboard!');

	    e.clearSelection();
	});

	clipboard.on('error', function(e) {
		toastr.options = {
			"closeButton": true,
			"progressBar": false,
			"positionClass": "toast-top-right",
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "2500",
			"extendedTimeOut": "1000",
		}
		toastr.error('Path failed to copy to clipboard!');
	});
});
