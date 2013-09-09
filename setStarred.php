<?php
	require_once(dirname(__FILE__) . '/functions.php');

	$movie = Movie::getFromID($_REQUEST['id']);
	if ($movie === false) { return 'no'; }

	// Toggle value and return new value.
	getUser()->setStarred($movie->id, !getUser()->hasStarred($movie->id));
	echo getUser()->hasStarred($movie->id)? 'true' : 'false';
?>
