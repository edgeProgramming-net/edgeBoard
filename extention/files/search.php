<?php

class module extends fpboard {

function execute () {
	global $search;
	$this->header();
	if (isset($search)) {
		//  showHeader("edgeBoard Forums Search Results");
		$this->config();
		$this->displayform();
?>
	<table width="100%" cellpadding=2 cellspacing=1 class="bordertable">
	 <tr>
	  <td class="category" width=20></td>
	  <td class="category" width=20></td>
	  <td class="category">Name</td>
	  <td class="category" align="center" width="120">Posted By</td>
	  <td class="category" width="50" align="center">Replies</td>
	  <td class="category" width="50" align="center">Views</td>
	  <td class="category" align="center" width="140">Last Post</td>
	 </tr>
<?
		$this->displaySERP($search);
		$this->displayform();
		//  showFooter();
	} else {
		$this->displayform();
	}
	$this->footer();
}

function config() {
	global $DOCUMENT_ROOT, $forum;
	$absurl = ""; # absolute URL to the edgeBoard directory
}

function getForumNumbers() {
	$forums = "";
	$cat = file("forums/categories.cgi", r);
	foreach ($cat as $acat) {
		list ($cat_id, $cat_name, $forum_id) = explode ("|", $acat);
		$forums .= trim($forum_id).","; # collect all the forum numbers
	}
	$forums = substr($forums, 0, strlen($forums)-1); # trim out the last comma
	return explode(",",$forums); # convert the string into array
}

function getTopicsInfo($forums) {
	global $absdir;
	$alltopics = array("");
	$i = 0;
	foreach ($forums as $aforum) {
		$myfile = "forums/$aforum/list.cgi";
		if (file_exists($myfile)) {
			$topics = file($myfile, r);
 			foreach ($topics as $atopic) {
				list ($topic, $title, $replies, $poster, $p_date, $lastposter, $r_date, $img, $picon, $hitss, $pinned) = explode ("|", $atopic);
				$alltopics[$i] = "$r_date|$aforum|$topic|$title|$user|$replies|$hitss|$picon|$img"; # reformat info
				$i++;
			}
		}
	}
  	rsort($alltopics);
  	return $alltopics;
}


function displaySERP($search) {
	global $absurl, $timeformat;
	$search = strtolower($search);
	$s_term = explode(" ",$search);
	$forums = $this->getForumNumbers();
	$this->forum = $forums;
	$i = 0;
	$alltopics = array("");
	foreach ($forums as $aforum) {
		$this->forum = $aforum;
		if ($this->checkforumpermission(5, false, $this->forum)=="1") {
			$myfile = "forums/$aforum/list.cgi";
			if (file_exists($myfile)) {
				$topics = file($myfile, r);
				foreach ($topics as $atopic) {
					list ($topic, $title, $replies, $poster, $p_date, $lastposter, $r_date, $img, $picon, $hitss, $pinned) = explode("|", $atopic);
					$myfile = "forums/$aforum/$topic.cgi";
					$fp   = @fopen($myfile, r);
					$text = @fread($fp, filesize($myfile));
					@fclose ($fp);
					$text = strtolower($text);
					$phrase_hits = substr_count($text,$search);
					$hits = 0;
					foreach ($s_term as $term) { $hits = $hits + substr_count($text,$term); }
					$hits = $phrase_hits*10 + $hits;
					$hits = str_pad($hits, 4,"0", STR_PAD_LEFT);
					if ($hits > 0) {
						$alltopics[$i] = "$hitss|$r_date|$aforum|$topic|$title|$poster|$lastposter|$replies|$picon|$img|$pinned";
						$i++;
					}
      				}
    			}
  		}
	}
	rsort ($alltopics);
	if ($alltopics[0][0] == "") {
		echo "	  <tr><td class=\"ctable1\" colspan=\"100%\" align=\"center\">No results found</td></tr>";
		echo "</table><br>";
	} else {
		echo "";
		foreach ($alltopics as $atopic) {
			list ($hits, $date, $forum, $topic, $title, $poster, $lastposter, $replies, $picon, $img, $pinned) = explode ("|", $atopic);
			if ($this->user[10] < $r_date) {
			if ($img != 1) {
				$folderimg = "foldernew.gif";
			}	else {
				$folderimg = "foldernewclosed.gif";
			}
		} else {
			if ($img != 1) {
				$folderimg = "folderold.gif";
			} else {
				$folderimg = "folderoldclosed.gif";
			}
		}
		if ($picon == "") {
			$posticon = "";
		} else {
			$posticon = "<img src=\"images/picons/".$picon.".gif\">";
		}
		$topicinfo = "";
		if ($img == "1") {$topicinfo = "Closed: ";}
//		if ($pinned == "1") {$topicinfo = $topicinfo . "Pinned: ";}
?>
			<tr>
		<td class="ctable2" align=center width=20><img src="images/<?=$folderimg?>"></td>
		<td class="ctable2" align=center width=20><?=$posticon?></td>
		<td class="ctable1" onMouseOver="tableOver(this);" onClick="document.location.href='index.php?a=topic&forum=<?=$forum?>&topic=<?=$topic?>'" onMouseOut="tableOut(this);" style="cursor: hand;">
		<?=$topicinfo?>  <a href="index.php?a=topic&forum=<?=$forum?>&topic=<?=$topic?>"><?=$title?></a></td>
		<td class="ctable1" align=center width="120"><a href="index.php?a=member&type=viewprofile&user=<?=$poster?>"><?=$poster?></a></td>
		<td class="ctable2" align=center width="50"><?=$replies?></td>
		<td class="ctable2" align=center width="50"><?=$hits?></td>
		<td class="ctable2" align=center width="140"><a href="index.php?a=member&type=viewprofile&user=<?=$lastposter?>"><?=$lastposter?></a><br>
		<span class="small"><?=date($this->timeformat, $date)?></span></td>
		</tr>
<?
    	}
  	echo "</table><br>";
  	}
}

function displayform() { 
	global $search;
	if (!isset($search)) { $search = "..."; }
?>
		<table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
<form method="post" action="index.php">
  <input type="hidden" name="a" value="search">
	 <tr>
	  <td class="ctable1" width=150>Search Forums</td>
	  <td class="ctable1"><input type="text" name="search" value="<?php echo $search ?>"> <?=$this->button("Search")?>
	</form>
	</table><br>
<?
}

}
?>