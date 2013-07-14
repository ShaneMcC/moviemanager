<?php
	require_once(dirname(__FILE__) . '/functions.php');
	$imdbid = preg_replace('/^tt/', '', $_REQUEST['imdbID']);
	$imdbid = preg_replace('/[^0-9]/', '', $imdbid);

	$trailers = getTrailerByIMDB($imdbid);
?>
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