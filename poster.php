<?php
	require_once(dirname(__FILE__) . '/functions.php');
	
	$movie = getMovieData($_REQUEST['id']);
	$remotePoster = false;
	$poster = $movie['poster'];

	if (isset($_REQUEST['fanart'])) {
		if (file_exists($movie['dir'] . '/fanart.jpg')) {	
			$poster = $movie['dir'] . '/fanart.jpg';
		} else {
			die();
		}
	} else if (file_exists($movie['dir'] . '/movie.tbn')) {
		$poster = $movie['dir'] . '/movie.tbn';
	} else {
		$remotePoster = true;
		if (empty($movie['poster']) || $movie['poster'] == 'N/A') {
			$poster = 'http://t0.gstatic.com/images?q=tbn:ANd9GcQalw3XeNDg49Z24Sy-KO5pLtfCYDnU87_kKkwnDiKWv8S2zz9IryY_SEJk';
		}
	}

	if ($remotePoster) {
		$ch = curl_init($poster);
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
	} else {
		header('Content-type: image/jpeg');
		echo file_get_contents($poster);
	}
?>