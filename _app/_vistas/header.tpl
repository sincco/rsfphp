<!doctype html>
<html lang="es">
	<head>
		<meta charset="utf-8" />
  		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?= APP_NAME ?></title>
		<link href="http://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="html/css/personalizados.css" />
		<link rel="stylesheet" href="html/css/foundation.css" />
		<script src="html/js/vendor/modernizr.js"></script>
		<script src="html/js/vendor/jquery.js"></script>
		<script src="html/js/foundation.min.js"></script>
		<script type="text/javascript" src="html/fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="html/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		<script>
			$(document).foundation();
		</script>
	</head>
	<body>
		<div style="display:none;">
		<!-- Fancybox para carga de datos directos (paso de html) -->
			<div id="html_div_pop" style=""></div>
			<a id="html_pop" href="#html_div_pop"></a>
		<!-- Fancybox para carga de un frame directos (paso de html) -->
			<a id="frame_pop" data-fancybox-type="iframe" href="/demo/iframe.html">Iframe</a>
		</div>