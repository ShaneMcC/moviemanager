<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once(dirname(__FILE__) . '/api/OMDB.php');
	require_once(dirname(__FILE__) . '/inc/movie.php');

	if (isset($_SERVER['SCRIPT_NAME'])) {
		define('BASEDIR', dirname($_SERVER['SCRIPT_NAME']) . '/');
	} else {
		define('BASEDIR', dirname(__FILE__) . '/');
	}

	function getDB() {
		global $__db, $config;

		if (!isset($__db)) {
			$__db = new PDO(sprintf('%s:host=%s;dbname=%s', $config['db']['type'], $config['db']['host'], $config['db']['database']), $config['db']['user'], $config['db']['pass']);
		}

		return $__db;
	}

	function getDirectories() {
		$db = getDB();

		$statement = $db->prepare('SELECT * FROM directories');
		$statement->execute();
		$dirs = $statement->fetchAll(PDO::FETCH_ASSOC);

		return $dirs;
	}
?>