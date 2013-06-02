<?php
	class OMDB {
		private $url = 'http://www.omdbapi.com/';

		private function getData($url) {
			$data = json_decode(file_get_contents($url), true);

			$result = strtolower($data['Response']) == 'true';
			unset($data['Response']);

			return array($result, $data);
		}

		function findByNameAndYear($title, $year) {
			return $this->getData(sprintf('%s?t=%s&y=%d', $this->url, urlencode($title), $year));
		}

		function findByIMDB($id) {
			return $this->getData(sprintf('%s?i=%s', $this->url, urlencode($id)));
		}
	}
?>