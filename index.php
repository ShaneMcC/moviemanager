<?php
	require_once(dirname(__FILE__) . '/functions.php');
	include(dirname(__FILE__) . '/inc/header.php');

	$movies = Movie::getMovies();

	/** TODO: This bit all sucks, but it's a quick hack that so it'll do for now. */
	$searchGenres = isset($_REQUEST['genre']) ? explode(',', strtolower($_REQUEST['genre'])) : array();
	$linkGenres = empty($searchGenres) ? '' : '&genre=' . implode(',', $searchGenres);
	$linkRandom = isset($_REQUEST['random']) ? '&random=' . $_REQUEST['random'] : '';
	$linkStarred = isset($_REQUEST['starred']) ? '&starred=' . $_REQUEST['starred'] : '';
	$linkWatched = isset($_REQUEST['watched']) ? '&watched=' . $_REQUEST['watched'] : '';

	$checkWatched = isset($_REQUEST['watched']) && $_REQUEST['watched'] == '1';
	$checkUnwatched = isset($_REQUEST['watched']) && $_REQUEST['watched'] == '0';
	$checkStarred = isset($_REQUEST['starred']) && $_REQUEST['starred'] == '1';
	$checkUnstarred = isset($_REQUEST['starred']) && $_REQUEST['starred'] == '0';

	$linkSearch = isset($_REQUEST['search']) && !empty($_REQUEST['search']) ? '&search=' . urlencode($_REQUEST['search']) : '';

	$hasModifiers = !empty($linkGenres) || !empty($linkRandom) || !empty($linkStarred) || !empty($linkWatched) || !empty($linkSearch) || isset($_REQUEST['showAll']);
?>

<?php /* TODO: The code for these buttons sucks... This is really fucking **fugly** code.*/ ?>

<div class="pull-left">
	<form class="form-search" method="post" action="?<?=$linkRandom?><?=$linkGenres?><?=$linkWatched?><?=$linkStarred?>">
		<input type="text" name="search" class="input-medium search-query" value="<?=htmlspecialchars(isset($_REQUEST['search']) ? $_REQUEST['search'] : '')?>">
		<button type="submit" class="btn">Search</button>
	</form>
</div>

<a class="btn <?=($checkWatched) ? 'btn-success' : 'btn-info'?> pull-right" href="?watched=1<?=$linkGenres?><?=$linkRandom?><?=$linkStarred?><?=$linkSearch?>" data-toggle="tooltip" title="Show only watched films"><i class="icon-eye-open"></i></a>
<a class="btn <?=($checkUnwatched) ? 'btn-success' : 'btn-info'?> pull-right" href="?watched=0<?=$linkGenres?><?=$linkRandom?><?=$linkStarred?><?=$linkSearch?>" data-toggle="tooltip" title="Show only unwatched films"><i class="icon-film"></i></a>
<a class="btn <?=($checkStarred) ? 'btn-success' : 'btn-info'?> pull-right" href="?starred=1<?=$linkGenres?><?=$linkRandom?><?=$linkWatched?><?=$linkSearch?>" data-toggle="tooltip" title="Show only starred films"><i class="icon-star"></i></a>
<a class="btn <?=($checkUnstarred) ? 'btn-success' : 'btn-info'?> pull-right" href="?starred=0<?=$linkGenres?><?=$linkRandom?><?=$linkWatched?><?=$linkSearch?>" data-toggle="tooltip" title="Show only unstarred films"><i class="icon-star-empty"></i></a>

<a class="btn <?=(isset($_REQUEST['random']) && $_REQUEST['random'] == 10) ? 'btn-success' : 'btn-info'?> pull-right" href="?random=10<?=$linkGenres?><?=$linkWatched?><?=$linkStarred?><?=$linkSearch?>" data-toggle="tooltip" title="Pick 10 random films"><i class="icon-random"></i> 10</a>
<a class="btn <?=(isset($_REQUEST['random']) && $_REQUEST['random'] == 5) ? 'btn-success' : 'btn-info'?> pull-right" href="?random=5<?=$linkGenres?><?=$linkWatched?><?=$linkStarred?><?=$linkSearch?>" data-toggle="tooltip" title="Pick 5 random films"><i class="icon-random"></i> 5</a>
<a class="btn <?=(isset($_REQUEST['random']) && $_REQUEST['random'] == 1) ? 'btn-success' : 'btn-info'?> pull-right" href="?random=1<?=$linkGenres?><?=$linkWatched?><?=$linkStarred?><?=$linkSearch?>" data-toggle="tooltip" title="Pick 1 random film"><i class="icon-random"></i> 1</a>

<?php if (!empty($searchGenres) || isset($_REQUEST['random']) || isset($_REQUEST['starred']) || isset($_REQUEST['watched'])) { ?>
	<a class="btn btn-danger pull-right" href="?" data-toggle="tooltip" title="Remove all list modifiers"><i class="icon-remove"></i> Clear Modifiers</a>
<?php } ?>
<br>
<br>
<script type="text/javascript">
	$('[data-toggle="tooltip"]').tooltip();
</script>
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
		// Get the list of valid movies.
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

			// Check if we care about starred/non-starred
			if (isset($_REQUEST['starred']) && $movie->starred != $_REQUEST['starred']) { $ignore = true; }

			// Same for watched/unwatched.
			if (isset($_REQUEST['watched']) && $movie->watched != $_REQUEST['watched']) { $ignore = true; }

			// Holy crap, we also search!?
			if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
				$searchFor = strtolower($_REQUEST['search']);
				$title = strtolower($movie->name);
				if (preg_match('#^s?/(.*)/$#', $searchFor, $matches)) {
					// Regex match if user starts with s/ or / and ends with /
					if (!preg_match('/' . $matches[1] . '/', $title)) { $ignore = true; }
				} else if (strpos($searchFor, '*') !== FALSE || strpos($searchFor, '?') !== FALSE) {
					// wildcard match if user has * or ? anywhere in the search string
					// http://www.php.net/manual/en/function.fnmatch.php#71725
					if (!preg_match("#".strtr(preg_quote($searchFor, '#'), array('\*' => '.*', '\?' => '.'))."#", $title)) { $ignore = true; }
				} else {
					// otherwise, just search where the given string is anywhere in the title.
					if (strpos($title, $searchFor) === false) { $ignore = true; }
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

		$hadMovies = $showMovies;
		if (count($movies) > 200 && !$hasModifiers) { $showMovies = array(); }

		foreach ($showMovies as $movie) {
			$omdb = unserialize($movie->omdb);

			$genres = explode(',', preg_replace('/\s/', '', strtolower($omdb['Genre'])));

			foreach ($genres as &$g) {
				$sg = $searchGenres;
				$sg[] = strtolower($g);
				$sg = array_unique($sg);
				$sg = implode(',', $sg);
				$g = '<a href="?genre=' . urlencode($sg) . $linkRandom . $linkStarred . $linkWatched . '"><span class="badge badge-info">' . ucfirst($g) . '</span></a>';
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
				if (isset($omdb['Year'])) {
					echo ' (', $omdb['Year'], ')';
				}
				echo '</a>';
			?>
				<div class="pull-right movieicons">
					<?=showMovieIcons($movie);?>
				</div>
			</td>
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

	<?php if (count($showMovies) == 0) { ?>
		<tr>
			<td colspan=4>
				<?php if (count($hadMovies) > 0) { ?>
					<em>There are too many movies (<?=count($hadMovies)?>) to show, either narrow your search, or <a href="?showAll<?=$linkRandom?><?=$linkGenres?><?=$linkWatched?><?=$linkStarred?><?=$linkSearch?>">show movies anyway.</a></em>
				<?php } else { ?>
				<em>There are no movies to show.</em>
				<?php } ?>
			</td>
		</tr>
	<?php } ?>

	</tbody>

</table>

<?php
	include(dirname(__FILE__) . '/inc/footer.php');
?>
