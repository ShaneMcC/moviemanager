<?php
	require_once(dirname(__FILE__) . '/functions.php');

	$url = $_REQUEST['url'];
	$id = $_REQUEST['id'];

	if (!isset($config['plex']['servers']) || empty($config['plex']['servers'])) { die(); }
	if (empty($url)) { die(); }
	if (empty($id) && $id !== '0') { die(); }

	$ch = curl_init('http://'.$config['plex']['servers'][$id].'/'.$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
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
