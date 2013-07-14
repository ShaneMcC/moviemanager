<?php
	require_once(dirname(__FILE__) . '/functions.php');
	$imdbid = preg_replace('/^tt/', '', $_REQUEST['imdbID']);
	$imdbid = preg_replace('/[^0-9]/', '', $imdbid);
	
	$trailers = getTrailerByIMDB($imdbid);
?>
<div id="trailers" class="tabbable">
	<ul class="nav nav-tabs">
		<?php $i = 0; foreach ($trailers as $t) { ?>
			<li class="<?=($i == 0 ? 'active' : '')?>"><a href="#trailer-<?=$i++?>" data-toggle="tab"><?=htmlspecialchars($t['title']);?></a></li>
		<?php } ?>
	</ul>
	<div class="tab-content">
		<?php $i = 0; foreach ($trailers as $t) { ?>
		<div class="tab-pane <?=($i == 0 ? 'active' : '')?>" id="trailer-<?=$i++?>">
			<?=$t['embed']?>
		</div>
		<?php } ?>
	</div>
</div>