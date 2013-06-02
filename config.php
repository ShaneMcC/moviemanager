<?php
	$config['db']['type'] = 'mysql';
	$config['db']['database'] = 'moviemanager';
	$config['db']['host'] = 'localhost';
	$config['db']['user'] = 'moviemanager';
	$config['db']['pass'] = 'moviemanager';

	if (file_exists(dirname(__FILE__) . '/config.local.php')) {
		require_once(dirname(__FILE__) . '/config.local.php');
	}
?>