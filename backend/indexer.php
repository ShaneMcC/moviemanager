#!/usr/bin/env php
<?php
	require_once(dirname(__FILE__) . '/../functions.php');

	$omdb = new OMDB();

	$dirs = getDirectories();

	foreach ($dirs as $dir) {
		$path = $dir['path'] . '/';
		$pathid = $dir['id'];

		foreach (scandir($path) as $movie) {
			if ($movie == '.' || $movie == '..') { continue; }
			if (!is_dir($path . $movie)) { continue; }

			$id = getMovieIDFromDir($pathid, $movie);
			$data = getMovieData($id);

			if (empty($data['name'])) {
				echo 'Found new movie: ', $movie, "\n";

				if (empty($data['imdbid'])) {
					echo "\t", 'No IMDB ID Known.', "\n";
					
					foreach (glob($data['dir'] . '/*.nfo') as $nfo) {
						echo "\t\t", 'Found nfo: ', $nfo, "\n";
						$nfo = file_get_contents($nfo);
						if (preg_match("#(?:http://www.imdb.com/title/|<id>)(tt[0-9]+)(?:/|</id>)#", $nfo, $m)) {
							echo "\t\t\t", 'Found IMDB ID: ', $m[1], "\n";
							setMovieData($id, array('imdbid' => $m[1]));
							$data['imdbid'] = $m[1];
							break;
						}
					}

					if (empty($data['imdbid']) && preg_match('/^(.*) \(([0-9]+)\)$/', $movie, $m)) {
						echo "\t\t", 'No useful nfo, guessing from title', "\n";
						list($result, $res) = $omdb->findByNameAndYear($m[1], $m[2]);
						
						if ($result) {
							echo "\t\t\t", 'Found IMDB ID: ', $res['imdbID'], "\n";
							setMovieData($id, array('imdbid' => $res['imdbID']));
							$data['imdbid'] = $res['imdbID'];
						}
					}
				}

				if (!empty($data['imdbid'])) {
					list($result, $data) = $omdb->findByIMDB($data['imdbid']);
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

						setMovieData($id, $newData);
						echo "\t", 'Detected movie as: ', $newData['name'], "\n";
					}
				} else {
					echo "\t", 'Unable to find movie data.', "\n";
				}
			}
		}
	}
?>