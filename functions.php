<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once(dirname(__FILE__) . '/api/OMDB.php');
	require_once(dirname(__FILE__) . '/inc/movie.php');
	require_once(dirname(__FILE__) . '/inc/user.php');

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

	function getUser() {
		global $__currentUser;

		if (!isset($__currentUser)) {
			if (isset($_SERVER['REMOTE_ADDR'])) {
				$__currentUser = User::getUserByName($_SERVER['REMOTE_ADDR'], true);
			} else {
				$__currentUser = User::getNullUser();
			}
		}

		return $__currentUser;
	}

	function showMovieIcons($movie) {
		if ($movie->starred) {
			$staricon = 'icon-star';
			$starcaption = 'Starred';
		} else {
			$staricon = 'icon-star-empty';
			$starcaption = 'Not starred';
		}

		if ($movie->watched) {
			$watchedicon = 'icon-eye-open';
			$watchedcaption = 'Watched';
		} else {
			$watchedicon = 'icon-film';
			$watchedcaption = 'Not watched';
		}
		?>
		<i class="staricon <?=$staricon?>" data-movieid="<?=$movie->id?>" data-toggle="tooltip" title="<?=$starcaption?>" onclick="toggleStarred()"></i>
		<i class="watchicon <?=$watchedicon?>" data-movieid="<?=$movie->id?>" data-toggle="tooltip" title="<?=$watchedcaption?>" onclick="toggleWatched()"></i>
		<?php
	}

	function getIMDBIDFromDir($movie, $debug = false) {
		$nfos = array();
		// Prioritise original NFO first...
		$nfos = array_merge($nfos, glob($movie->dir . '/*.orig.nfo'));
		$nfos = array_merge($nfos, glob($movie->dir . '/*.nfo'));

		foreach ($nfos as $nfo) {
			if ($debug) { echo "\t\t", 'Found nfo: ', $nfo, "\n"; }
			$nfo = file_get_contents($nfo);
			if (preg_match("#(?:http://www.imdb.com/title/|<id>)(tt[0-9]+)(?:/|</id>)#", $nfo, $m)) {
				if ($debug) { echo "\t\t\t", 'Found IMDB ID: ', $m[1], "\n"; }
				return $m[1];
				break;
			}
		}

		if (preg_match('/^(.*) \(([0-9]+)\)$/', $movie->dirname, $m)) {
			$omdb = new OMDB();
			if ($debug) { echo "\t\t", 'No useful nfo, guessing from title', "\n"; }
			list($result, $res) = $omdb->findByNameAndYear($m[1], $m[2]);

			if ($result) {
				if ($debug) { echo "\t\t\t", 'Found IMDB ID: ', $res['imdbID'], "\n"; }
				return $res['imdbID'];
			}
		}

		return FALSE;
	}

	function getOMDBDataForMovie($imdbid) {
		$omdb = new OMDB();
		list($result, $data) = $omdb->findByIMDB($imdbid);
		if ($result) {
			$newData = array();

			$newData['name'] = $data['Title'];
			if ($data['Poster'] != 'N/A') {
				$newData['poster'] = $data['Poster'];
			}

			// Categories
			// Actors
			// Directors

			// TODO: Be less shit.
			$newData['omdb'] = serialize($data);

			return $newData;
		}

		return FALSE;
	}

	require_once(dirname(__FILE__) . '/functions.local.php');

	if (!function_exists('foundNewMovie')) {
		function foundNewMovie($movie) {
			/* Do Nothing */
		}

		function removedMovie($movie) {
			/* Do Nothing */
		}
	}
?>
