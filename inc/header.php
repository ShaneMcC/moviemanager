<!DOCTYPE html>
<html lang="en">
	<head>
	<title>Movie Manager<?=(isset($titleExtra) ? $titleExtra : '')?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Bootstrap -  http://twitter.github.com/bootstrap/index.html -->
	<!-- Using Icons from GlyphIcons - http://glyphicons.com/ -->
	<link href="<?=BASEDIR?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- <link href="./bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet"> -->

	<link href="<?=BASEDIR?>/inc/style.css" rel="stylesheet">

	<script src="<?=BASEDIR?>/bootstrap/js/jquery.js"></script>
	<script src="<?=BASEDIR?>/bootstrap/js/bootstrap.min.js"></script>

	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.2.0/ZeroClipboard.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">


	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	</head>
	<body>			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container">
						<a class="brand" href="<?=BASEDIR?>">Movie Manager</a>
						<!-- <div class="nav">
							<ul class="nav">
								<li  class="active" ><a href="<?=BASEDIR?>">Home</a></li>
							</ul>
						</div> -->
						<div class="nav pull-right">
							<ul class="nav">
								<!-- <li ><a href="<?=BASEDIR?>./admin.php">Admin</a></li> -->
								<li ><a href="#"><?=getUser()->getUsername()?></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<script type="text/javascript">
				function toggleWatched() {
					target = $(event.target);
					$.get('<?=BASEDIR?>setwatched/' + target.data('movieid'), '', function(data) {
						if (data) {
							if (data == 'true') {
								updateIcon(target, 'icon-eye-open', 'Watched')
							} else {
								updateIcon(target, 'icon-film', 'Not watched')
							}
						}
					});
				}

				function toggleStarred() {
					target = $(event.target);
					$.get('<?=BASEDIR?>setstarred/' + target.data('movieid'), '', function(data) {
						if (data) {
							if (data == 'true') {
								updateIcon(target, 'icon-star', 'Starred')
							} else {
								updateIcon(target, 'icon-star-empty', 'Not starred')
							}
						}
					});
				}
			</script>
			<script src="<?=BASEDIR?>/inc/script.js" type="text/javascript"></script>
			<div class="container">


