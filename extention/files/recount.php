<?php

include "php 4-2+.php";

class module extends fpboard { 

function execute () { 
	if (isset($forum)) {
		$this->recountforum();
	} else {
		$this->recountforums();
	}
} 

function recountforums () { 
	$categories = $this->selectall("forums/categories.cgi"); 
	foreach ($categories as $category) { 
		$forums = explode(",", $category[2]); 
		foreach ($forums as $forumid) { 
			$forumtopics = 0; $forumposts = 0; 
			$forumlist = $this->selectsort("forums/".$forumid."/list.cgi", 6); 
			$forumtopics = count($forumlist); 
			foreach ($forumlist as $topic) { 
				$forumposts = $forumposts + $topic[2]; 
			} 
			$forumposts = $forumposts + $forumtopics; 
			$forum = $this->select("forums/forums.cgi", $forumid); 
			$forum[2] = $forumtopics; 
			$forum[3] = $forumposts; 
			$forum[4] = $forumlist[count($forumlist)-1][0]; 
			$this->update("forums/forums .cgi", $forumid, $forum); 
		} 
	} 
} 

function recountforum () { 
	global $forum;
	$forumlist = $this->selectsort("forums/".$forum."/list.cgi", 6); 
	$forumtopics = count($forumlist); 
	foreach ($forumlist as $topic) { 
		$forumposts = $forumposts + $topic[2]; 
	} 
	$forumposts = $forumposts + $forumtopics; 
	$dbforum = $this->select("forums/forums.cgi", $forumid); 
	$dbforum[2] = $forumtopics; 
	$dbforum[3] = $forumposts; 
	$dbforum[4] = $forumlist[count($forumlist)-1][0]; 
	$this->update("forums/forums .cgi", $forum, $dbforum);  
 
} 

} 
?>