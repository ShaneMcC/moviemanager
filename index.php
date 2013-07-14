<?php
	require_once(dirname(__FILE__) . '/functions.php');
	include(dirname(__FILE__) . '/inc/header.php');

	$movies = getMovies();
	$searchGenres = isset($_REQUEST['genre']) ? explode(',', strtolower($_REQUEST['genre'])) : array();
?>

<?php if (!empty($searchGenres)) { ?>
<a class="btn btn-primary pull-right" href="?">Clear Search</a><br><br>
<?php } ?>
<table id="movieslist"  class="table table-striped table-bordered table-condensed">
	<thead>
		<tr class="header">
			<th class="poster">&nbsp;</th>
			<th class="title" colspan=2>Title</th>
			<th class="links">Links</th>
		<tr>
	</thead>

	<tbody>
	<?php foreach ($movies as $movie) {
		$omdb = unserialize($movie['omdb']);

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
			<td class="poster" rowspan=3>
			<ul class="thumbnails"><li><a href="#" class="thumbnail"><?php
				echo '<img src="poster/', $movie['id'], '" alt="Poster" class="movieposter">';
			?></a></li></ul>
			</td>
			<td class="title" colspan=2><?php
				echo '<a href="movie/', $movie['id'], '">';
				if (!empty($movie['name'])) {
					echo $movie['name'];
				} else {
					echo $movie['dirname'];
					echo ' <span class="label label-important">Unknown</span>';
				}
				echo '</a>';
			?></td>
			<td class="links" rowspan=3><?php
				if (!empty($movie['imdbid']) && $movie['imdbid'] != 'N/A') {
					echo '<a href="http://www.imdb.com/title/', $movie['imdbid'], '/"><span class="label label-success">IMDB</span></a>';
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
	<?php } ?>
	</tbody>

</table>

<?php
	include(dirname(__FILE__) . '/inc/footer.php');
?>
