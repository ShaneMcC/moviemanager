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


/*
$(function() {
ZeroClipboard.config( { swfPath: "https://cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.2.0/ZeroClipboard.swf" } );
// var clip = new ZeroClipboard.Client($('[data-clipboard-text]'));
// var clip = new ZeroClipboard.Client('#cliptest');
// clip.on( 'complete', function(client, args) {alert("Copied text to clipboard: " + args.text );});
// new ZeroClipboard($("#cliptest"));
alert($("#cliptest"));
}); */


$(function() {
	ZeroClipboard.config({swfPath: "//cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.2.0/ZeroClipboard.swf"});
	var client = new ZeroClipboard($('[data-clipboard-text]'));

	client.on("ready", function(readyEvent) {
  		client.on("aftercopy", function(event) {
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
  		});
	});
});
