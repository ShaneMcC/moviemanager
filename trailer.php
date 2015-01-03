<?php
	require_once(dirname(__FILE__) . '/functions.php');

	$type = isset($_REQUEST['youtube']) ? 'youtube' : '';

	$trailers = Movie::getFromID($_REQUEST['id'])->getTrailers($type);
	if (count($trailers) == 0) { die(); }

	$hasYoutube = false;
	foreach ($trailers as $t) { if ($t['type'] == 'youtube') { $hasYoutube = true; break; } }
?>

<?php if (!$hasYoutube || $type == 'youtube') { ?>
	<div id="youtubebutton">
		<?php if (!$hasYoutube) { $searchType = 'youtube'; ?>
			<button class="btn btn-mini" id="searchYoutube" type="button">Search Youtube</button>
		<?php } else if ($type == 'youtube') { $searchType = 'trailer'; ?>
			<button class="btn btn-mini" id="searchYoutube" type="button">Search Trailers</button>
		<?php } ?>
		<script>
			$('#searchYoutube').click(function() {
				$('#youtubebutton').html('<img src="<?=BASEDIR?>inc/ajax-loader.gif" alt="..." />');

				$.get('<?=BASEDIR?><?=$searchType?>/<?=$_REQUEST['id']?>', '', function(data) {
					if (data) {
						$('#youtubebutton').parent().html(data);
					} else {
						$('#youtubebutton').html('<em>No youtube videos found.</em>');
					}
				});
			});
		</script>
	</div>
	<br>
<?php } ?>

<div id="trailers" class="accordion">
	<?php $i = 0; foreach ($trailers as $t) { ?>
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#trailers" href="#trailer<?=$i?>">
					<?=htmlspecialchars($t['title']);?>
				</a>
			</div>
			<div class="accordion-body collapse <?=($i == 0 ? '' : 'in')?>" id="trailer<?=$i++?>">
				<div class="accordion-inner">
					<?=$t['embed']?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
<script type="text/javascript">
	$(".collapse").collapse()
</script>
