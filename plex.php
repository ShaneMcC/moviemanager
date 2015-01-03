<?php
	require_once(dirname(__FILE__) . '/functions.php');
	$movie = Movie::getFromID($_REQUEST['id']);

	if ($movie == null) { die(); }
	if (!isset($config['plex']['servers']) || empty($config['plex']['servers'])) { die(); }
	if (empty($movie->name)) { die(); }

	echo '<ul class="thumbnails">';

	foreach ($config['plex']['servers'] as $id => $server) {
		$serverinfo = simplexml_load_string(file_get_contents('http://' . $server . '/'));
		$servername = $serverinfo->attributes()->friendlyName;
		$serverid = $serverinfo->attributes()->machineIdentifier;

		$searchurl = 'http://'.$server.'/search?local=1&query=' . urlencode($movie->name);
		$xml = simplexml_load_string(file_get_contents($searchurl));

		foreach ($xml->Video as $video) {
			$plexURL = 'http://plex.tv/web/app#!/server/'.$serverid.'/details/'.urlencode($video->attributes()->key);

			echo '<li><a href="', $plexURL, '" class="thumbnail">';
			echo '<img src="', BASEDIR, '/plexproxy/', $id, '/', $video->attributes()->thumb, '" alt="Poster" class="moviethumb">';
			echo '<br>', $video->attributes()->title;
			echo '<br> on ', $servername;
			echo '</a></li>';
		}
	}

	echo '</ul>';
?>
