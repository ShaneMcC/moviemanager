<?php

	class Movie {
		private function __construct($movieRow) {
			foreach ($movieRow as $k => $v) {
				$this->$k = $v;
			}
		}

		function getTrailers($wantedType = null) {
			global $config;

			$type = $wantedType;
			if ($type == null) {
				if (!empty($config['traileraddict']['apikey'])) {
					$type = 'traileraddict';
				} else if (!empty($config['tmdb']['apikey'])) {
					$type = 'tmdb';
				} else if (!empty($config['youtube']['apikey'])) {
					$type = 'youtube';
				} else {
					$type = '';
				}
			}

			$o = unserialize($this->omdb);
			$imdbid = preg_replace('/^tt/', '', $o['imdbID']);
			$imdbid = preg_replace('/[^0-9]/', '', $imdbid);

			$title = $o['Title'];

			$trailerlist = array();

			// TODO: This needs an API Key now as well.
			if ($type == 'traileraddict' || $type == 'traileraddict_id') {
				$trailers = simplexml_load_file('http://api.traileraddict.com/?api_key=' . urlencode($config['traileraddict']['apikey']) .'count=10&width=900&imdb='.$imdbid);
				foreach ($trailers->trailer as $trailer) {
					$trailerlist[] = array('title' => (string)$trailer->title, 'embed' => (string)$trailer->embed, 'type' => 'traileraddict_id');
				}
			}

			if (($type == 'traileraddict' && count($trailerlist) == 0) || $type == 'traileraddict_name') {
				$name = str_replace(' ', '-', strtolower($o['Title']));
				$trailers = simplexml_load_file('http://api.traileraddict.com/?api_key=' . urlencode($config['traileraddict']['apikey']) .'count=10&width=900&film='.$name);
				foreach ($trailers->trailer as $trailer) {
					$trailerlist[] = array('title' => (string)$trailer->title, 'embed' => (string)$trailer->embed, 'type' => 'traileraddict_name');
				}
			}

			if ($type == 'tmdb') {
				$url = 'https://api.themoviedb.org/3/find/tt' . $imdbid . '?api_key=' . urlencode($config['tmdb']['apikey']) . '&external_source=imdb_id';
				$data = @json_decode(@file_get_contents($url), true);

				$tmdbID = isset($data['movie_results'][0]['id']) ? $data['movie_results'][0]['id'] : '';
				if (!empty($tmdbID)) {
					$url = 'https://api.themoviedb.org/3/movie/' . $tmdbID . '/videos?api_key=' . urlencode($config['tmdb']['apikey']);
					$data = @json_decode(@file_get_contents($url), true);

					foreach ($data['results'] as $video) {
						if (strtolower($video['site']) == 'youtube') {
							$embed = '<iframe width="900" height="506" src="https://www.youtube.com/embed/' . $video['key'] . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
						} else {
							$embed = '<pre>' . htmlspecialchars(json_encode($video)) . '</pre>';
						}

						$trailerlist[] = array('title' => $video['type'] . ': ' . $video['name'], 'embed' => $embed, 'type' => 'tmdb');
					}
				}
			}

			if ($type == 'youtube') {
				$url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&key=' . urlencode($config['youtube']['apikey']) . '&q=' . urlencode($title . ' trailer');
				$items = @json_decode(@file_get_contents($url), true);

				foreach ($items['items'] as $entry) {

					$title = $entry['snippet']['title'];
					$id = $entry['id']['videoId'];

					$embed = '<iframe width="900" height="506" src="https://www.youtube.com/embed/' . $id . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

					$trailerlist[] = array('title' => $title, 'embed' => $embed, 'type' => 'youtube');
				}
			}

			// If we got no results and this was a non-default search then
			// try falling back to the default
			if (count($trailerlist) == 0 && $wantedType != null) {
				$trailerlist = getTrailers(null);
			}

			return $trailerlist;
		}

		function setData($data) {
			if (count($data) == 0) { return; }
			$db = getDB();

			$params = array(':id' => $this->id);
			$sql = array();

			foreach (array_keys($data) as $col) {
				$sql[] = $col . ' = :' . $col;
				$params[':' . $col] = $data[$col];
			}

			$statement = $db->prepare('UPDATE movies SET ' . implode(', ', $sql) . ' WHERE id = :id');
			$statement->execute($params);

			foreach ($data as $k => $v) { $this->$k = $v; }
		}

		public static function getMovies($deleted = false) {
			$db = getDB();

			$statement = $db->prepare('SELECT m.*, CONCAT(d.path, "/", m.dirname) AS dir, not ISNULL(us.userid) AS starred, not ISNULL(uw.userid) AS watched FROM movies AS m JOIN directories AS d ON d.id = m.pathid LEFT JOIN userstars AS us ON us.movieid = m.id AND us.userid = :userid LEFT JOIN userwatched AS uw ON uw.movieid = m.id AND uw.userid = :userid WHERE deleted = :deleted ORDER BY name');
			$statement->execute(array(':userid' => getUser()->getUserID(), ':deleted' => $deleted ? 'true' : 'false'));
			$movies = $statement->fetchAll(PDO::FETCH_ASSOC);

			$result = array();
			foreach ($movies as $m) {
				$result[] = new Movie($m);
			}
			return $result;
		}

		public static function getFromID($id) {
			$db = getDB();

			$statement = $db->prepare('SELECT m.*, CONCAT(d.path, "/", m.dirname) AS dir, not ISNULL(us.userid) AS starred, not ISNULL(uw.userid) AS watched FROM movies AS m JOIN directories AS d ON d.id = m.pathid LEFT JOIN userstars AS us ON us.movieid = m.id AND us.userid = :userid LEFT JOIN userwatched AS uw ON uw.movieid = m.id AND uw.userid = :userid WHERE m.id = :id');
			$statement->execute(array(':id' => $id, ':userid' => getUser()->getUserID()));
			$data = $statement->fetch(PDO::FETCH_ASSOC);

			return ($data === false) ? FALSE : new Movie($data);
		}

		public static function getFromDir($pathid, $dirname) {
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

			return Movie::getFromID($data['id']);
		}
	}

