#!/usr/bin/env php
<?php
	require_once(dirname(__FILE__) . '/../functions.php');

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

					foreach (glob($movie->dir . '/*.nfo') as $nfo) {
						echo "\t\t", 'Found nfo: ', $nfo, "\n";
						$nfo = file_get_contents($nfo);
						if (preg_match("#(?:http://www.imdb.com/title/|<id>)(tt[0-9]+)(?:/|</id>)#", $nfo, $m)) {
							echo "\t\t\t", 'Found IMDB ID: ', $m[1], "\n";
							$movie->setData(array('imdbid' => $m[1]));
							break;
						}
					}

					if (empty($movie->imdbid) && preg_match('/^(.*) \(([0-9]+)\)$/', $moviedir, $m)) {
						echo "\t\t", 'No useful nfo, guessing from title', "\n";
						list($result, $res) = $omdb->findByNameAndYear($m[1], $m[2]);

						if ($result) {
							echo "\t\t\t", 'Found IMDB ID: ', $res['imdbID'], "\n";
							$movie->setData(array('imdbid' => $res['imdbID']));
						}
					}
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
