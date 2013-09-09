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
?>
