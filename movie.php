<?php
	require_once(dirname(__FILE__) . '/functions.php');

	$movie = getMovieData($_REQUEST['id']);
	$omdb = unserialize($movie['omdb']);
	unset($omdb['Poster']);
	unset($omdb['Title']);
	unset($omdb['Imdbid']);
	$rowspan = count($omdb) + 3;

	$titleExtra = ' :: ' . $movie['name'];

	include(dirname(__FILE__) . '/inc/header.php');
?>

<table id="moviedata"  class="table table-striped table-bordered table-condensed">
	<tbody>
		<tr class="movie">
			<td class="fullposter" rowspan=<?=$rowspan?>><?php
				if (empty($movie['poster']) || $movie['poster'] == 'N/A') {
					$movie['poster'] = 'http://t0.gstatic.com/images?q=tbn:ANd9GcQalw3XeNDg49Z24Sy-KO5pLtfCYDnU87_kKkwnDiKWv8S2zz9IryY_SEJk';
				}

				echo '<img src="', $movie['poster'], '" alt="Poster" class="movieposter">';
			?></td>
			<th class="title">Title</th>
			<td class="title"><?php
				if (!empty($movie['name'])) {
					echo $movie['name'];
				} else {
					echo $movie['dirname'];
					echo ' <span class="label label-important">Unknown</span>';
				}
			?></td>
		</tr>
		<tr>
			<th class="links">Links</th>
			<td class="links"><?php
				if (!empty($movie['imdbid']) && $movie['imdbid'] != 'N/A') {
					echo '<a href="http://www.imdb.com/title/', $movie['imdbid'], '/"><span class="label label-success">IMDB</span></a>';
				} else {
					echo '<span class="label label-important">IMDB</span>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th class="directory">Local Directory</th>
			<td class="directory"><?=$movie['dir']?></td>
		</tr>
		<?php
			foreach ($omdb as $key => $value) {
				$key = htmlspecialchars($key);
				echo '<tr>';
				echo '<th class="', strtolower($key), '">', $key, '</th>';
				echo '<td class="', strtolower($key), '">', htmlspecialchars($value), '</td>';
				echo '</tr>';
			}
		?>
		<?php if (isset($omdb['imdbID'])) { ?>
		<tr>
			<th colspan=3>Trailer</th>
		</tr>
		<tr>
			<td colspan=3 id="trailercontainer">
				<img src="inc/ajax-loader.gif" alt="..." />
				<br>
				<em><small>Loading trailer...</small></em>
				<script>
					// Get Trailer.
					$.get('trailer.php?imdbID=<?=$omdb['imdbID']?>', '', function(data) {
						$('#trailercontainer').html(data);
					});
				</script>
			</td>
		</tr>
		<? } ?>
	</tbody>
</table>

<?php /* var_dump($movie); */ ?>

<?php
	include(dirname(__FILE__) . '/inc/footer.php');
?>