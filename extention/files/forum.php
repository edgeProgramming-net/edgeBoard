<?php

include "php 4-2+.php";

class module extends fpboard {
var $forum, $page, $topics, $pagelinks;

function execute () {
        global $forum, $page;
	$this->verifyintegrity($forum);
	$this->forum = $forum;
        $this->topics = $topics = $this->selectsort("forums/".$this->forum."/list.cgi", 6);
        if ($page == "") {
                $this->page = 1;
        } else {
                $this->page = $page;
        }
        $this->header();
        $this->checkforumpermission(5, true, $this->forum);
        $this->setpagelinks();
        $this->displayforuminfo();
        $this->displaytopics();
        $this->displayfolders();
        $this->footer();
}

function setpagelinks () {
        $this->pagelinks = "";
        if (count($this->topics) != 0) {
                for ($i=1;count($this->topics)>=$i*$this->topicsperpage-$this->topicsperpage+1;$i++){
			if ($i == $this->page) {
				$b = " <b>";
				$r = "</b>";
			} else {
				$b = " <a href=\"index.php?a=forum&forum=".$this->forum."&page=".$i."\">";
				$r = "</a>";
			}
                        $this->pagelinks = $this->pagelinks.$b.$i.$r;
                }
        } else {
                $this->pagelinks = "No Topics";
        }
}

function settopiclinks ($topicid) {
        $this->topiclinks['$topicid'] = "";
	$topicfile = count(file("forums/".$this->forum."/".$topicid.".cgi"));
	$pages = ceil($topicfile/$this->postsperpage);
	$i = 1;
        while ($i <= $pages){
		$b = " <a href=\"index.php?a=topic&forum=".$this->forum."&topic=".$topicid."&page=".$i."\">";
		$r = "</a>";
                $this->topiclinks[$topicid] = $this->topiclinks[$topicid].$b.$i.$r;
		$i++;
        }
	if ($i > 2) {
		$this->topiclinks[$topicid] = "[ Pages:".$this->topiclinks[$topicid]." ]";
	} else {
		$this->topiclinks[$topicid] = "";
	}
}

function displayforuminfo () {
	global $HTTP_COOKIE_VARS;
	$forum = $this->select("forums/forums.cgi", $this->forum);
	$forumid = $forum[0];
	$records = $HTTP_COOKIE_VARS['disanre'];
	$newrecord = $forumid.",".time();
	$records = explode(":", $records);
	$i = 0;
	$b = 0;
	foreach ($records as $record) {
		$b++;
		$rec = explode(",", $record);
		if ($rec[0] == $forumid) {
			$all = $all.$forumid.",".time().":";
		} else {
			$i++;
			$all = $all.$rec[0].",".$rec[1].":";
		}
	}
	if ($i == $b) {
		$all = $forumid.",".time().":";
	}
	setcookie("disanre", $all, time()+5184000);
?>
<table width="100%" cellpadding=2 cellspacing=1 border="0" class="bordertable">
 <tr>
  <td width="100%" class="ctable1">Viewing Forum: <?=$forum[1]?></td>
  <td nowrap rowspan="2" class="ctable2"><span class="regular"><a href="index.php?a=post&forum=<?=$this->forum?>"><img src="images/topic_new.gif" border="0"></a></span></td>
 </tr>
 <tr>
  <td width="100%" class="ctable1">[ <?=$this->pagelinks?> ]</td>
 </tr>
</table>
<br>
<?
}

function displaytopics () {
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
        $pintopics = $this->selectwhere("forums/".$this->forum."/list.cgi", 10, 1);
        foreach ($pintopics as $topic) {
                // find out what type of folder to display.
                // display closed: if need be
                if ($this->user[10] < $topic[6]) {
                        if ($topic[7] != 1) {
                                $folderimg = "foldernew.gif";
                        }        else {
                                $folderimg = "foldernewclosed.gif";
                        }
                } else {
                        if ($topic[7] != 1) {
                                $folderimg = "folderold.gif";
                        } else {
                                $folderimg = "folderoldclosed.gif";
                        }
                }
                if ($topic[8] == "" || $topic[8] == "0" || $topic[8] == "1") {
                        $posticon = "";
                } else {
                        $posticon = "<img src=\"images/picons/".$topic[8].".gif\">";
                }
		$topicinfo = "";
			if ($topic[12] == "poll") {
                		if ($topic[7] == "1") { 
					$topicinfo = "Closed Poll: ";
				}
                		if ($topic[10] == "1") {
					$topicinfo = $topicinfo . "Pinned Poll: ";
				}
				$posttype = "poll";
				$replies = "-";
			} else {
                		if ($topic[7] == "1") {
					$topicinfo = "Closed: ";
				}
                		if ($topic[10] == "1") {
					$topicinfo = $topicinfo . "Pinned: ";
				}
				$posttype = "topic";
				$replies = $topic[2];
			}
		$this->settopiclinks($topic[0]);
                ?>
                <tr>
                <td class="ctable2" align=center width=20><img src="images/<?=$folderimg?>"></td>
                <td class="ctable2" align=center width=20><?=$posticon?></td>
                <td class="ctable1" <?$this->frfx($a, ($this->forum), ($topic[0]))?>>
                <?=$topicinfo?> <a href="index.php?a=topic&forum=<?=$this->forum?>&topic=<?=$topic[0]?>"><?=$topic[1]?></a> <?=$this->topiclinks[$topic[0]]?></td>
                <td class="ctable1" align=center width="120"><a href="index.php?a=member&type=viewprofile&user=<?=$topic[3]?>"><?=$topic[3]?></a></td>
                <td class="ctable2" align=center width="50"><?=$topic[2]?></td>
                <td class="ctable2" align=center width="50"><?=$topic[9]?></td>
                <td class="ctable2" align=center width="140"><a href="index.php?a=member&type=viewprofile&user=<?=$topic[5]?>"><?=$topic[5]?></a><br>
                <span class="small"><?=$this->bdate($this->timeformat, $topic[6])?></span></td>
                </tr>
                <?
        }
        ?>
        <?
        $this->topics = array_reverse($this->sort($this->selectwhere("forums/".$this->forum."/list.cgi", 10, 0), 6));
        $topics = $this->limit($this->topics, ($this->page-1)*$this->topicsperpage, $this->topicsperpage-1);
        if (count($topics) == 0) {
                ?>
         <tr>
          <td class="ctable1" colspan=7 align=center>No topics for this forum.</td>
         </tr>
                <?
        }
        foreach ($topics as $topic) {
                // find out what type of folder to display.
                // display closed: if need be
                if ($this->user[10] < $topic[6]) {
                        if ($topic[7] != 1) {
                                $folderimg = "foldernew.gif";
                        }        else {
                                $folderimg = "foldernewclosed.gif";
                        }
                } else {
                        if ($topic[7] != 1) {
                                $folderimg = "folderold.gif";
                        } else {
                                $folderimg = "folderoldclosed.gif";
                        }
                }
                if ($topic[8] == "" || $topic[8] == "0" || $topic[8] == "1") {
                        $posticon = "";
                } else {
                        $posticon = "<img src=\"images/picons/".$topic[8].".gif\">";
                }
                $topicinfo = "";
                if ($topic[12] == "poll") {
			$posttype = "poll";
                	if ($topic[7] == "1") {
				$topicinfo = "Closed Poll: ";
                	} else {
				$topicinfo = "Poll: ";
			}
			$replies = "-";
		} else {
                	if ($topic[7] == "1") {
				$topicinfo = "Closed: ";
			}
			$posttype = "topic";
			$replies = $topic[2];
		}
		$this->settopiclinks($topic[0]);
                ?>
                <tr>
                <td class="ctable2" align=center width=20><img src="images/<?=$folderimg?>"></td>
                <td class="ctable2" align=center width=20><?=$posticon?></td>
                <td class="ctable1" <?$this->frfx($a, ($this->forum), ($topic[0]))?>>
                <?=$topicinfo?> <a href="index.php?a=topic&forum=<?=$this->forum?>&topic=<?=$topic[0]?>"><?=$topic[1]?></a> <?=$this->topiclinks[$topic[0]]?></td>
                <td class="ctable1" align=center width="120"><a href="index.php?a=member&type=viewprofile&user=<?=$topic[3]?>"><?=$topic[3]?></a></td>
                <td class="ctable2" align=center width="50"><?=$topic[2]?></td>
                <td class="ctable2" align=center width="50"><?=$topic[9]?></td>
                <td class="ctable2" align=center width="140"><a href="index.php?a=member&type=viewprofile&user=<?=$topic[5]?>"><?=$topic[5]?></a><br>
                <span class="small"><?=$this->bdate($this->timeformat, $topic[6])?></span></td>
                </tr>
                <?
        }
        ?>
        </table><br>
        <?
}

function displayfolders() {
        ?>
        <table border="0" cellpadding="2" cellspacing="1" align="center" class="bordertable">
         <tr>
          <td align="center" class="ctable1">
           <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                 <td><span class="regular">Recently Active Topic</span></td>
                 <td><img src="images/foldernew.gif"></td>
                </tr>
           </table>
          </td>
          <td align="center" class="ctable1">
           <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                 <td><span class="regular">Recently Inactive Topic</span></td>
                 <td><img src="images/folderold.gif"></td>
                </tr>
           </table>
          </td>
         </tr>
         <tr>
          <td align="center" class="ctable1">
           <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                 <td><span class="regular">Recently Active Topic (Closed)</span></td>
                 <td><img src="images/foldernewclosed.gif"></td>
                </tr>
           </table>
          </td>
          <td align="center" class="ctable1">
           <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                 <td><span class="regular">Recently Inactive Topic (Closed)</span></td>
                 <td><img src="images/folderoldclosed.gif"></td>
                </tr>
           </table>
          </td>
         </tr>
        </table>
        <?

}

}

?>