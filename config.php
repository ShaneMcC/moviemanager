<?php
	$config['db']['type'] = 'mysql';
	$config['db']['database'] = 'moviemanager';
	$config['db']['host'] = 'localhost';
	$config['db']['user'] = 'moviemanager';
	$config['db']['pass'] = 'moviemanager';

	$config['plex']['servers'] = array('127.0.0.1:32400');

	$config['omdb']['apikey'] = '';
	$config['tmdb']['apikey'] = '';
	$config['traileraddict']['apikey'] = '';
	$config['youtube']['apikey'] = '';

	if (file_exists(dirname(__FILE__) . '/config.local.php')) {
		require_once(dirname(__FILE__) . '/config.local.php');
	}
