<?php
	require_once(dirname(__FILE__) . '/functions.php');

	$movie = getMovieData($_REQUEST['id']);
	
	if (empty($movie['poster']) || $movie['poster'] == 'N/A') {
		$movie['poster'] = 'http://t0.gstatic.com/images?q=tbn:ANd9GcQalw3XeNDg49Z24Sy-KO5pLtfCYDnU87_kKkwnDiKWv8S2zz9IryY_SEJk';
	}

	$ch = curl_init($movie['poster']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	$response = curl_exec($ch);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $header_size);
	$body = substr($response, $header_size);

	foreach (explode("\n", $header) as $header) {
		header($header);
	}

	echo $body;
?>