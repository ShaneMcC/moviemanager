<?php
	require_once(dirname(__FILE__) . '/functions.php');
	$imdbid = preg_replace('/^tt/', '', $_REQUEST['imdbID']);
	$imdbid = preg_replace('/[^0-9]/', '', $imdbid);
	echo getTrailerByIMDB($imdbid);
?>