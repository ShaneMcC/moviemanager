#!/usr/bin/env php
<?php
	require_once(dirname(__FILE__) . '/../functions.php');

	$dirs = getDirectories();

	foreach ($dirs as $dir) {
		$path = $dir['path'] . '/';
		$pathid = $dir['id'];

		foreach (scandir($path) as $moviedir) {
			if ($moviedir == '.' || $moviedir == '..') { continue; }
			if (!is_dir($path . $moviedir)) { continue; }

			$movie = Movie::getFromDir($pathid, $moviedir);

			if (empty($movie->name)) {
				echo 'Found new movie: ', $moviedir, "\n";

				if (empty($movie->imdbid)) {
					echo "\t", 'No IMDB ID Known.', "\n";

					$imdbID = getIMDBIDFromDir($movie, true);
					if ($imdbID !== false) { $movie->setData(array('imdbid' => $imdbID)); }
				}

				if (!empty($movie->imdbid)) {
					$newData = getOMDBDataForMovie($movie->imdbid);
					if ($newData !== false) {
						$movie->setData($newData);
						echo "\t", 'Detected movie as: ', $newData['name'], "\n";
						foundNewMovie($movie);
					}
				} else {
					echo "\t", 'Unable to find movie data.', "\n";
				}
			}
		}
	}
?>
