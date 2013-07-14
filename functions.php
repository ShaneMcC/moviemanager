<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once(dirname(__FILE__) . '/api/OMDB.php');

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

	function getMovies() {
		$db = getDB();

		$statement = $db->prepare('SELECT * FROM movies');
		$statement->execute();
		$movies = $statement->fetchAll(PDO::FETCH_ASSOC);

		return $movies;
	}

	function getMovieIDFromDir($pathid, $dirname) {
		$db = getDB();

		$statement = $db->prepare('SELECT id FROM movies WHERE pathid = :pathid AND dirname = :dirname');
		$result = $statement->execute(array(':pathid' => $pathid, ':dirname' => $dirname));
		$data = $statement->fetch(PDO::FETCH_ASSOC);

		if ($data == false) {
			$statement2 = $db->prepare('INSERT INTO movies (pathid, dirname) VALUES (:pathid, :dirname)');
			$statement2->execute(array(':pathid' => $pathid, ':dirname' => $dirname));

			$result = $statement->execute(array(':pathid' => $pathid, ':dirname' => $dirname));
			$data = $statement->fetch(PDO::FETCH_ASSOC);
		}

		return $data['id'];
	}

	function getMovieData($id) {
		$db = getDB();

		$statement = $db->prepare('SELECT m.*, CONCAT(d.path, "/", m.dirname) AS dir FROM movies AS m JOIN directories AS d ON d.id = m.pathid WHERE m.id = :id');
		$statement->execute(array(':id' => $id));
		$data = $statement->fetch(PDO::FETCH_ASSOC);

		return $data;
	}

	function getTrailerByIMDB($id) {
		$trailerlist = array();

		$trailers = simplexml_load_file('http://api.traileraddict.com/?count=10&width=900&imdb='.$id); 
		foreach($trailers->trailer as $trailer) {
			$trailerlist[] = array('title' => (string)$trailer->title, 'embed' => (string)$trailer->embed);
		}

		if (count($trailerlist) == 0) {
			// Failed by imdbid, try by name instead.
			$omdb = new OMDB();
			list($result, $data) = $omdb->findByIMDB('tt'.$id);
			$name = str_replace(' ', '-', strtolower($data['Title']));
			$trailers = simplexml_load_file('http://api.traileraddict.com/?count=10&width=900&film='.$name); 
			foreach($trailers->trailer as $trailer) {
				$trailerlist[] = array('title' => (string)$trailer->title, 'embed' => (string)$trailer->embed);
			}
		}

		return $trailerlist;
	}

	function setMovieData($id, $data) {
		if (count($data) == 0) { continue; }
		$db = getDB();

		$params = array(':id' => $id);
		$sql = array();

		foreach (array_keys($data) as $col) {
			$sql[] = $col . ' = :' . $col;
			$params[':' . $col] = $data[$col];
		}

		$statement = $db->prepare('UPDATE movies SET ' . implode(', ', $sql) . ' WHERE id = :id');
		$statement->execute($params);
	}
?>