<?php
	require_once(dirname(__FILE__) . '/functions.php');

	$movie = Movie::getFromID($_REQUEST['id']);
	if ($movie === false) { return 'false'; }

	// Toggle value and return new value.
	getUser()->setWatched($movie->id, !getUser()->hasWatched($movie->id));
	echo getUser()->hasWatched($movie->id)? 'true' : 'false';
?>
