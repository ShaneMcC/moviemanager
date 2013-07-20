<?php
	require_once(dirname(__FILE__) . '/functions.php');

	$movie = Movie::getFromID($_REQUEST['id']);
	$omdb = unserialize($movie->omdb);
	unset($omdb['Poster']);
	unset($omdb['Title']);
	unset($omdb['Imdbid']);
	$rowspan = count($omdb) + 3;

	$titleExtra = ' :: ' . $movie->name;

	include(dirname(__FILE__) . '/inc/header.php');
	
?>

<script>
$('body').css('background-image', 'url("<?=BASEDIR;?>/fanart/<?=$movie->id;?>")');
$('body').css('background-size', '100%');
$('body').css('background-repeat', 'no-repeat');
$('body').css('background-attachment', 'fixed');
</script>

<table id="moviedata"  class="table table-striped table-bordered table-condensed">
	<tbody>
		<tr class="movie">
			<td class="fullposter" rowspan=<?=$rowspan?>>
			<ul class="thumbnails"><li><a href="#" class="thumbnail"><?php
				echo '<img src="', BASEDIR, '/poster/', $movie->id, '" alt="Poster" class="movieposter">';
			?></a></li></ul>
			</td>
			<th class="title">Title</th>
			<td class="title"><?php
				if (!empty($movie->name)) {
					echo $movie->name;
				} else {
					echo $movie->dirname;
					echo ' <span class="label label-important">Unknown</span>';
				}
			?></td>
		</tr>
		<tr>
			<th class="links">Links</th>
			<td class="links"><?php
				if (!empty($movie->imdbid) && $movie->imdbid != 'N/A') {
					echo '<a href="http://www.imdb.com/title/', $movie->imdbid, '/"><span class="label label-success">IMDB</span></a>';
				} else {
					echo '<span class="label label-important">IMDB</span>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th class="directory">Local Directory</th>
			<td class="directory"><?=$movie->dir?></td>
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
	</tbody>
</table>
<table id="trailerdata"  class="table table-striped table-bordered table-condensed">
	<tbody>
		<tr>
			<th>Trailer</th>
		</tr>
		<tr>
			<td id="trailercontainer">
				<img src="<?=BASEDIR?>inc/ajax-loader.gif" alt="..." />
				<br>
				<em><small>Loading trailer...</small></em>
				<script>
					// Get Trailer.
					$.get('<?=BASEDIR?>trailer/<?=$movie->id?>', '', function(data) {
						if (data) {
							$('#trailercontainer').html(data);
						} else {
							$('#trailerdata').hide();
						}
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