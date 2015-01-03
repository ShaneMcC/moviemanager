

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
