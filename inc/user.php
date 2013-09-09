<?php

	class User {
		private $id;
		private $username;

		private function __construct($userRow) {
			$this->id = $userRow['id'];
			$this->username = $userRow['name'];
		}

		public static function getNullUser() {
			return new User(array('id' => '-1', 'name' => 'Null'));
		}

		public static function getUserByID($id) {
			$db = getDB();

			$statement = $db->prepare('SELECT * from users AS u WHERE u.id = :id');
			$statement->execute(array(':id' => $id));
			$data = $statement->fetch(PDO::FETCH_ASSOC);

			return ($data === false) ? FALSE : new User($data);
		}

		public static function getUserByName($name, $autoCreate = false) {
			$db = getDB();

			$statement = $db->prepare('SELECT * from users AS u WHERE u.name = :name');
			$statement->execute(array(':name' => $name));
			$data = $statement->fetch(PDO::FETCH_ASSOC);

			if ($data === false && $autoCreate) {
				return self::createUser($name);
			}

			return ($data === false) ? FALSE : new User($data);
		}

		public static function createUser($name) {
			$db = getDB();

			$statement = $db->prepare('INSERT INTO users (name) VALUES (:name)');
			$statement->execute(array(':name' => $name));

			return self::getUserByName($name, false);
		}

		public function getUsername() {
			return $this->username;
		}

		public function getUserID() {
			return $this->id;
		}

		public function getStarred() {
			$db = getDB();
			$statement = $db->prepare('SELECT * from userstars AS us WHERE us.userid = :userid');
			$statement->execute(array(':userid' => $this->getUserID()));
			$movies = $statement->fetchAll(PDO::FETCH_ASSOC);

			$result = array();
			foreach ($movies as $m) {
				$movie = Movie::getFromID($m);
				if ($movie !== false) { $result[] = $movie; }
			}
			return $result;
		}

		public function getWatched() {
			$db = getDB();
			$statement = $db->prepare('SELECT * from userwatched AS uw WHERE uw.userid = :userid');
			$statement->execute(array(':userid' => $this->getUserID()));
			$movies = $statement->fetchAll(PDO::FETCH_ASSOC);

			$result = array();
			foreach ($movies as $m) {
				$movie = Movie::getFromID($m);
				if ($movie !== false) { $result[] = $movie; }
			}
			return $result;
		}

		public function hasStarred($id) {
			$db = getDB();
			$statement = $db->prepare('SELECT * from userstars AS us WHERE us.userid = :userid AND us.movieid = :movieid');
			$statement->execute(array(':userid' => $this->getUserID(), ':movieid' => $id));
			$data = $statement->fetch(PDO::FETCH_ASSOC);

			return ($data !== false);
		}

		public function hasWatched($id) {
			$db = getDB();
			$statement = $db->prepare('SELECT * from userwatched AS uw WHERE uw.userid = :userid AND uw.movieid = :movieid');
			$statement->execute(array(':userid' => $this->getUserID(), ':movieid' => $id));
			$data = $statement->fetch(PDO::FETCH_ASSOC);

			return ($data !== false);
		}

		public function setStarred($id, $value) {
			$db = getDB();
			if ($value) {
				$statement = $db->prepare('INSERT INTO userstars (userid, movieid) VALUES (:userid, :movieid)');
			} else {
				$statement = $db->prepare('DELETE FROM userstars WHERE userid = :userid AND movieid = :movieid');
			}
			$statement->execute(array(':userid' => $this->getUserID(), ':movieid' => $id));

			return $value;
		}

		public function setWatched($id, $value) {
			$db = getDB();
			if ($value) {
				$statement = $db->prepare('INSERT INTO userwatched (userid, movieid) VALUES (:userid, :movieid)');
			} else {
				$statement = $db->prepare('DELETE FROM userwatched WHERE userid = :userid AND movieid = :movieid');
			}
			$statement->execute(array(':userid' => $this->getUserID(), ':movieid' => $id));

			return $value;
		}
	}

?>
