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
	<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- <link href="./bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet"> -->

	<link href="./inc/style.css" rel="stylesheet">

	<script src="./bootstrap/js/jquery.js"></script>
	<script src="./bootstrap/js/bootstrap.min.js"></script>


	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	</head>
	<body>			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container">
						<a class="brand" href="#">Movie Manager</a>
						<div class="nav">
							<ul class="nav">
								<li  class="active" ><a href="./">Home</a></li>
							</ul>
						</div>
						<div class="nav pull-right">
							<ul class="nav">
								<li ><a href="./admin.php">Admin</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<script type="text/javascript">
				$('.dropdown-toggle').dropdown();
			</script>	<div class="container">


