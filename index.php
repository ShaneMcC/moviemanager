<?php
	require_once(dirname(__FILE__) . '/functions.php');
	include(dirname(__FILE__) . '/inc/header.php');

	$movies = Movie::getMovies();
	$searchGenres = isset($_REQUEST['genre']) ? explode(',', strtolower($_REQUEST['genre'])) : array();
?>

<a class="btn btn-success pull-right" href="?random=10<?php if (!empty($searchGenres)) { echo '&genre=' . implode(',', $searchGenres); } ?>">Random 10</a>
<a class="btn btn-success pull-right" href="?random=5<?php if (!empty($searchGenres)) { echo '&genre=' . implode(',', $searchGenres); } ?>">Random 5</a>
<a class="btn btn-success pull-right" href="?random=1<?php if (!empty($searchGenres)) { echo '&genre=' . implode(',', $searchGenres); } ?>">Random 1</a>
<?php if (!empty($searchGenres) || isset($_REQUEST['random'])) { ?>
<a class="btn btn-primary pull-right" href="?">Clear Modifiers</a>
<?php } ?>
<br>
<br>
<table id="movieslist"  class="table table-striped table-bordered table-condensed">
	<thead>
		<tr class="header">
			<th class="poster">&nbsp;</th>
			<th class="title" colspan=2>Title</th>
			<th class="links">Links</th>
		<tr>
	</thead>

	<tbody>
	<?php
	$showMovies = array();
	foreach ($movies as $movie) {
		$omdb = unserialize($movie->omdb);

		$genres = explode(',', preg_replace('/\s/', '', strtolower($omdb['Genre'])));

		// Check if this film is in the genres we care about,
		$ignore = false;
		if (!empty($searchGenres)) {
			foreach ($searchGenres as $g) {
				if (!in_array($g, $genres)) {
					$ignore = true;
				}
			}
		}
		if ($ignore) { continue; }
		$showMovies[] = $movie;
	}

	if (isset($_REQUEST['random']) && is_numeric($_REQUEST['random']) && $_REQUEST['random'] > 0) {
		$keys = array_rand($showMovies, min((int)$_REQUEST['random'], count($showMovies)));
		if (!is_array($keys)) { $keys = array($keys); }
		$randMovies = array();
		foreach ($keys as $key) { $randMovies[] = $showMovies[$key]; }
		$showMovies = $randMovies;
	}

	foreach ($showMovies as $movie) {
		$omdb = unserialize($movie->omdb);

		$genres = explode(',', preg_replace('/\s/', '', strtolower($omdb['Genre'])));

		foreach ($genres as &$g) {
			$sg = $searchGenres;
			$sg[] = strtolower($g);
			$sg = array_unique($sg);
			$sg = implode(',', $sg);
			$g = '<a href="?genre=' . urlencode($sg) . '"><span class="badge badge-info">' . ucfirst($g) . '</span></a>';
		}
		$genres = implode(' ', $genres);

		$rating = isset($omdb['imdbRating']) ? $omdb['imdbRating'] : 'Unknown';
	?>
		<tr class="movie">
			<td class="poster" rowspan=4>
			<ul class="thumbnails"><li><a href="#" class="thumbnail"><?php
				echo '<img src="poster/', $movie->id, '" alt="Poster" class="movieposter">';
			?></a></li></ul>
			</td>
			<td class="title" colspan=2><?php
				echo '<a href="movie/', $movie->id, '">';
				if (!empty($movie->name)) {
					echo $movie->name;
				} else {
					echo $movie->dirname;
					echo ' <span class="label label-important">Unknown</span>';
				}
				echo '</a>';
				if (isset($omdb['Released'])) {
					echo '<div class="pull-right">', $omdb['Released'], '</div>';
				}
			?></td>
			<td class="links" rowspan=4><?php
				if (!empty($movie->imdbid) && $movie->imdbid != 'N/A') {
					echo '<a href="http://www.imdb.com/title/', $movie->imdbid, '/"><span class="label label-success">IMDB</span></a>';
				} else {
					echo '<span class="label label-important">IMDB</span>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th class="genre">Genres</td>
			<td class="genre"><?=$genres?></td>
		</tr>
		<tr>
			<th class="rating">Rating</td>
			<td class="rating"><?=$rating?></td>
		</tr>
		<tr>
			<th class="plot">Plot</td>
			<td class="plot"><?=$omdb['Plot']?></td>
		</tr>
	<?php } ?>
	</tbody>

</table>

<?php
	include(dirname(__FILE__) . '/inc/footer.php');
?>
