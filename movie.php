<?php
	require_once(dirname(__FILE__) . '/functions.php');

	$movie = Movie::getFromID($_REQUEST['id']);

	if ($movie === false) {
		include(dirname(__FILE__) . '/inc/header.php');
		echo 'No such Movie ID found.';
		include(dirname(__FILE__) . '/inc/footer.php');
		die();
	}

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
			<th class="title" colspan=3>
			<?php
				if (!empty($movie->name)) {
					echo $movie->name;
				} else {
					echo $movie->dirname;
					echo ' <span class="label label-important">Unknown</span>';
				}

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

				<div class="pull-right movieicons">
					<i id="staricon" class="<?=$staricon?>" data-toggle="tooltip" title="<?=$starcaption?>"></i>
					<i id="watchicon" class="<?=$watchedicon?>" data-toggle="tooltip" title="<?=$watchedcaption?>"></i>
				</div>

				<script>
					function toggleWatched() {
						$.get('<?=BASEDIR?>setwatched/<?=$movie->id?>', '', function(data) {
							if (data) {
								if (data == 'true') {
									updateIcon($('#watchicon'), 'icon-eye-open', 'Watched')
								} else {
									updateIcon($('#watchicon'), 'icon-film', 'Not watched')
								}
							}
						});
					}

					function toggleStarred() {
						$.get('<?=BASEDIR?>setstarred/<?=$movie->id?>', '', function(data) {
							if (data) {
								if (data == 'true') {
									updateIcon($('#staricon'), 'icon-star', 'Starred')
								} else {
									updateIcon($('#staricon'), 'icon-star-empty', 'Not starred')
								}
							}
						});
					}

					function updateIcon(elem, icon, tooltip) {
						elem.removeClass();
						elem.addClass(icon);
						elem.attr('title', tooltip);
						elem.tooltip('destroy');
						elem.tooltip();
						if (elem.is(":hover")) {
							elem.tooltip('show');
						}
					}

					$(document).ready(function(){
						$('[data-toggle="tooltip"]').tooltip();
						$('#watchicon').click(function() { toggleWatched(); });
						$('#staricon').click(function() { toggleStarred(); });
					});
				</script>

			</th>
		</tr>
		<tr class="movie">
			<td class="fullposter" rowspan=<?=$rowspan?>>
			<ul class="thumbnails"><li><a href="#" class="thumbnail"><?php
				echo '<img src="', BASEDIR, '/poster/', $movie->id, '" alt="Poster" class="movieposter">';
			?></a></li></ul>
			</td>
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
