<?
if ($this->validate(14) == 1) {
	$bd = "backup/";
	if (!file_exists($bd))			{ 	mkdir($bd, 0777); 		}
	if (!file_exists($bd."forums/")) 	{ 	mkdir($bd."forums/", 0777); 	}
	if (!file_exists($bd."data/"))		{ 	mkdir($bd."data/", 0777); 	}
	if (!file_exists($bd."data/logs/"))	{ 	mkdir($bd."data/logs/", 0777); 	}
	if (!file_exists($bd."html/")) 		{ 	mkdir($bd."html/", 0777); 	}
	$x = "<br>";
	if (($selection == 1) || ($selection == 2)) {
		clearstatcache();
		if ($selection == 1) {
			if (file_exists("forums/categories.cgi")) {
				$failed = "Failed to backup the file, \"";
				$failed2 = "\"!<br>";
				$categories = $this->selectall("forums/categories.cgi");
				foreach ($categories as $category) {
					$forums = explode(",", $category[2]);
					foreach ($forums as $forum) {
						if (!file_exists($bd."forums/".$forum)) { mkdir($bd."forums/".$forum, 0777); }
						$forumlist = "forums/".$forum."/list.cgi";
						if (file_exists($forumlist)) {
							if (file_exists($bd.$forumlist)) { unlink($bd.$forumlist); }
							if (!copy($forumlist, $bd.$forumlist)) {
									$list = $list.$failed.$forumlist.$failed2;
							}
							$lines = $this->selectall($forumlist);
							foreach ($lines as $line) {
								$current = "forums/".$forum."/".$line[0].".cgi";
								if (file_exists($bd.$current)) { unlink($bd.$current); }
								if (!copy($current, $bd.$current)) {
									$list = $list.$failed.$current.$failed2;
								}
							}
						}
					}
				}
				$files = array("forums/categories.cgi","forums/forums.cgi");
				foreach ($files as $file) {
					if (!copy($file, $bd.$file)) {
						$list = $list.$failed.$file.$failed2;
					}
				}
				$result = 1;
				$settng = 2;
			} else {
				$result = 0;
				$problm = 1;
			}
		} elseif ($selection == 2) {
			$failed = "Failed to backup the file, \"";
			$failed2 = "\"!<br>";
			foreach ($this->file as $file) {
				if (file_exists($bd.$file)) { unlink($bd.$file); }
				if (!copy($file, $bd.$file)) {
					$list = $list.$failed.$file.$failed2;
				} else {
					chmod($bd.$file, 0777);
				}
			}
			$result = 1;
			$settng = 2;
		}
	} elseif (($selection == 3) || ($selection == 4)) {
		clearstatcache();
		if ($selection == 3) {
			if (file_exists($bd."forums/categories.cgi")) {
				$failed = "Failed to restore the file, \"";
				$failed2 = "\"!<br>";
				$categories = $this->selectall($bd."forums/categories.cgi");
				foreach ($categories as $category) {
					$forums = explode(",", $category[2]);
					foreach ($forums as $forum) {
						$forumlist = $bd."forums/".$forum."/list.cgi";
						if (file_exists($forumlist)) {
							$lines = $this->selectall($forumlist);
							foreach ($lines as $line) {
								$current = "forums/".$forum."/".$line[0].".cgi";
								if (!copy($bd.$current, $current)) {
									$list = $list.$x.$failed.$current.$failed2;
								}
							}
						}
					}
				}
				$files = array("forums/categories.cgi","forums/forums.cgi");
				foreach ($files as $file) {
					if (!copy($bd.$file, $file)) {
						$list = $list.$x.$failed.$file.$failed2;
					}
				}
				$result = 1;
				$settng = 1;
			} else {
				$result = 0;
				$problm = 1;
			}
		} elseif ($selection == 4) {
			$failed = "Failed to restore the file, \"";
			$failed2 = "\"!<br>";
			foreach ($this->file as $file) {
				if (!copy($bd.$file, $file)) {
					$list = $list.$x.$failed.$file.$failed2;
				} else {
					chmod($bd.$file, 0777);
					chmod($file, 0777);
				}
			}
			$result = 1;
			$settng = 1;
		}
	}
	if ($result != 1) {
		if ($problm == 1) {
			$this->displaymessage("Error", "The files that you have selected to restore have yet to be backed up.");
		}
	} elseif ($result == 1) {
		if ($settng == 1) {
			$this->displaymessage("Success", "The files that you have selected to restore have been restored.".$x.$list);
		} else {
			$this->displaymessage("Success", "The files that you have selected to back up have been backed up.".$x.$list);
		}
	}
} else {
	$this->displaymessage("Error", "You may not access the Administrator's Control Panel");
	$this->footer();
	die();
}
?>