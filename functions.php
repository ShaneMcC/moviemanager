<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once(dirname(__FILE__) . '/api/OMDB.php');

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

	function getTrailerByID($id, $type = null) {
		$movie = getMovieData($id);
		$o = unserialize($movie['omdb']);
		$imdbid = preg_replace('/^tt/', '', $o['imdbID']);
		$imdbid = preg_replace('/[^0-9]/', '', $imdbid);

		$trailerlist = array();

		if ($type == null || $type == 'traileraddict' || $type == 'traileraddict_id') {
			$trailers = simplexml_load_file('http://api.traileraddict.com/?count=10&width=900&imdb='.$imdbid); 
			foreach($trailers->trailer as $trailer) {
				$trailerlist[] = array('title' => (string)$trailer->title, 'embed' => (string)$trailer->embed, 'type' > 'traileraddict_id');
			}
		}

		if (count($trailerlist) == 0) {
			// Failed by imdbid, try by name instead.
			$omdb = new OMDB();
			list($result, $data) = $omdb->findByIMDB('tt'.$imdbid);
			$name = str_replace(' ', '-', strtolower($data['Title']));
			if ($type == null || $type == 'traileraddict' || $type == 'traileraddict_name') {
				$trailers = simplexml_load_file('http://api.traileraddict.com/?count=10&width=900&film='.$name);
				foreach($trailers->trailer as $trailer) {
					$trailerlist[] = array('title' => (string)$trailer->title, 'embed' => (string)$trailer->embed, 'type' > 'traileraddict_name');
				}
			}

			if (count($trailerlist) == 0) {
				// How annoying, we still found no trailers from traileraddict :(
				// Fallback to youtube...
				if ($type == null || $type == 'youtube' ) {
					$url = 'https://gdata.youtube.com/feeds/api/videos?orderby=relevance&format=5&max-results=10&v=2&alt=json&q=' . urlencode($data['Title'] . ' trailer');
					$items = @json_decode(@file_get_contents($url), true);

					foreach ($items['feed']['entry'] as $entry) {
						$title = $entry['title']['$t'];
						$content = $entry['content']['src'];
						$id = $entry['id']['$t'];
						$embed = '';
						$embed .= '<object type="application/x-shockwave-flash" style="width:900px;height:506px;">';
						$embed .= '<param name="movie" value="' . $content. '&amp;rel=0&amp;hd=1&amp;showsearch=0" />';
						$embed .= '<param name="allowFullScreen" value="true" />';
						$embed .= '<param name="allowscriptaccess" value="always" />';
						$embed .= '</object>';

						$trailerlist[] = array('title' => $title, 'embed' => $embed, 'type' > 'youtube');
					}
				}
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