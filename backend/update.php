#!/usr/bin/env php
<?php
	require_once(dirname(__FILE__) . '/../functions.php');

	$movies = Movie::getMovies();

	foreach ($movies as $movie) {
		echo 'Updating movie dir: ', $movie->dirname, "\n";

		// Check to see if it still exists...
		if (file_exists($movie->dir)) {
			$movie->setData(array('deleted' => 'false'));
		} else {
			echo "\t", 'Movie has been deleted..', "\n";
			removedMovie($movie);
			$movie->setData(array('deleted' => 'true'));
			continue;
		}

		// Check to see if we still agree with the IMDB id...

		echo "\t", 'Updating IMDB ID..', "\n";
		$imdbID = getIMDBIDFromDir($movie, true);
		if (!empty($imdbID) && $imdbID != $movie->imdbid) {
			echo "\t\t\t\t", 'New IMDB ID does not match old ID (',$imdbID,' != ',$movie->imdbid,'), fixing...', "\n";

			$newData = getOMDBDataForMovie($imdbID);
			if ($newData !== false) {
				$newData['imdbid'] = $imdbID;
				removedMovie($movie);
				$oldName = $movie->name;
				$movie->setData($newData);
				foundNewMovie($movie);
				echo "\t", 'Movie renamed from ', $oldName, ' to: ', $newData['name'], "\n";
			}
		}

		echo "\n";
	}



	die();
	$omdb = new OMDB();

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
					list($result, $data) = $omdb->findByIMDB($movie->imdbid);
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
