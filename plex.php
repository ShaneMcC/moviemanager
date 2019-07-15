<?php
	require_once(dirname(__FILE__) . '/functions.php');
	$movie = Movie::getFromID($_REQUEST['id']);

	if ($movie == null) { die(); }
	if (!isset($config['plex']['servers']) || empty($config['plex']['servers'])) { die(); }
	if (empty($movie->name)) { die(); }

	ob_start();
	echo '<ul class="thumbnails">';

	$found = false;
	foreach ($config['plex']['servers'] as $id => $data) {
		if (!isset($data['token']) || !isset($data['url'])) { continue; }

		$secure = (isset($data['secure']) && $data['secure']);

		$token = $data['token'];
		$serverurl = ($secure ? 'https://' : 'http://') . $data['url'];

		$serverinfo = simplexml_load_string(@file_get_contents($serverurl . '/?X-Plex-Token=' . urlencode($token)));
		if ($serverinfo === false) { continue; }
		$servername = $serverinfo->attributes()->friendlyName;
		$serverid = $serverinfo->attributes()->machineIdentifier;

		$searchurl = $serverurl.'/search?local=1&query=' . urlencode($movie->name) . '&X-Plex-Token=' . urlencode($token);
		$xml = simplexml_load_string(file_get_contents($searchurl));

		foreach ($xml->Video as $video) {
			$found = true;
			$plexURL = 'https://plex.tv/web/app#!/server/'.$serverid.'/details?key='.urlencode($video->attributes()->key);

			echo '<li><a href="', $plexURL, '" class="thumbnail">';
			echo '<img src="', BASEDIR, '/plexproxy/', $id, '/', $video->attributes()->thumb, '" alt="Poster" class="moviethumb">';
			echo '<br>', $video->attributes()->title;
			echo '<br> on ', $servername;
			echo '</a></li>';
		}
	}

	echo '</ul>';

	if ($found) { ob_end_flush(); } else { ob_end_clean(); }
