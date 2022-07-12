<?php

include "php 4-2+.php";

class module extends fpboard {

var $forum, $poll, $replylink, $posts, $page, $pagelinks, $listtopic;

function execute () {
/*
	global $forum, $poll, $page, $type;
	$this->forum = $forum;
	$this->poll = $poll;
	$this->posts = $this->selectall("forums/".$this->forum."/".$this->poll.".cgi");
	$this->setlistpoll();
	if ($page == "") {
		$this->page = 1;
	} elseif ($page == "last") {
		$this->setlastpage();
	} else {
		$this->page = $page;
	}
	$this->header();
	$this->checkforumpermission(5, true, $this->forum);
	$this->setreplylink();
	$voted = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 4);
	$show = false;
	//foreach ($voted as $voter) {
		//if ($voter == $this->user[0]) {
			$this->displaypollinfotop();
			$this->showpoll();
			$this->displaypollinfobottom();
			$show = true;
		//}
	//}
	if ($show == false) {
		if ($type == "vote2") {
			$this->vote2();
		} else {
			$this->vote();
		}
	}
	$this->footer();
*/
}

function setlastpage () {
	$this->page = ceil(count($this->posts)/$this->postsperpage);
}

function setlistpoll () {
	$this->listpoll = $this->select("forums/".$this->forum."/list.cgi", $this->poll);
	$this->listpoll[9]++;
	$this->update("forums/".$this->forum."/list.cgi", $this->poll, $this->listpoll);
}

function setreplylink () {
	if ($this->listpoll[7] == 1) {
		$this->replylink = "<img src=\"images/topic_closed.gif\" border=\"0\">";
	} else {
		$this->replylink = "<a href=\"index.php?a=post&forum=".$this->forum."&poll=".$this->poll."\"><img src=\"images/topic_reply.gif\" border=\"0\"></a>";
	}
}

function displaypollinfotop () {
if ($this->listtopic[7] == "1") {$topicname = "Closed: " . $topicname;}
?>
<table width=100% cellpadding=2 cellspacing=1 class="bordertable">
 <tr>
  <td class="category" align="center" colspan="2">Poll</td>
 </tr>
 <tr>
  <td class="ctable1" width="100%"><span class="regular">Viewing Poll: <?=$this->listpoll[1]?></span></td>
  <td class="ctable2" nowrap rowspan="2"><span class="regular"><a href="index.php?a=post&forum=<?=$this->forum?>"><img src="images/topic_new.gif" border="0"></a> <a href="index.php?a=post&forum=<?=$this->forum?>&type=newpoll"><img src="images/poll_new.gif" border="0"></a></span></td>
 </tr>
 <tr>
  <td class="ctable1">&nbsp;</td>
 </tr>
</table>
<br>
<?
}

function vote () {
	$post = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 5);
	$poll = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 1);
	$qpost = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 2);
?>
<table width="100%" cellpadding=5 cellspacing=1 class="bordertable">
<table width="100%" cellpadding=5 cellspacing=1 class="ctable1">
	<form action="index.php" method="post" name="Post">
	<input type="hidden" name="a" value="poll">
	<input type="hidden" name="type" value="vote2">
	<input type="hidden" name="poll" value="<?=$this->poll?>">
	<input type="hidden" name="forum" value="<?=$this->forum?>">
	<input type="hidden" name="thesession" value="<?=$HTTP_COOKIE_VARS['sessionid']?>">
<tr>
<TD valign="middle" align="left" width="60%"><div align="left"> 
<span class="small">Poll Question: <?=$qpost[1]?></span></div></td> 
</tr> 
<TR><TD colspan="2" height="1" class="bordertable"></td></tr> 
<TR><TD colspan="2"><span class="ctable1">
<?
if ($post[1] != "") {
	echo "<input type=\"radio\" name=\"choice\" value=\"1\">".$post[1]."<br>";
}
if ($post[2] != "") {
	echo "<input type=\"radio\" name=\"choice\" value=\"2\">".$post[2]."<br>";
}
if ($post[3] != "") {
	echo "<input type=\"radio\" name=\"choice\" value=\"3\">".$post[3]."<br>";
}
if ($post[4] != "") {
	echo "<input type=\"radio\" name=\"choice\" value=\"4\">".$post[4]."<br>";
}
if ($post[5] != "") {
	echo "<input type=\"radio\" name=\"choice5\" value=\"5\">".$post[5]."<br>";
}
if ($post[6] != "") {
	echo "<input type=\"radio\" name=\"choice6\" value=\"6\">".$post[6]."<br>";
}
if ($post[7] != "") {
	echo "<input type=\"radio\" name=\"choice7\" value=\"7\">".$post[7]."<br>";
}
if ($post[8] != "") {
	echo "<input type=\"radio\" name=\"choice8\" value=\"8\">".$post[8]."<br>";
}
if ($post[9] != "") {
	echo "<input type=\"radio\" name=\"choice9\" value=\"9\">".$post[9]."<br>";
}
if ($post[10] != "") {
	echo "<input type=\"radio\" name=\"choice10\" value=\"10\">".$post[10]."<br>";
}
?>
<?$this->button("Vote")?>
</span></td></tr></table>
</form></td>
</tr>
<td class="ctable2" align="center"><?=date($this->timeformat, $post[22])?></td>
</tr>
<tr>
<td class="darkspacer" height="4" colspan="3"></td>
</tr>
</table>
</table>
<?
}

function vote2() {
	global $question, $choice1, $choice2, $choice3, $choice4, $choice5, $choice6, $choice7;
	global $choice8, $choice9, $choice10, $gtime, $thesession;
	$voted = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 3);
	foreach ($voted as $voter) {
		if ($voter == $this->user[0]) {
			$this->displaymessage("Error", "You have already voted.");
			$this->footer();
			die();
		}
	}
	if ($this->validate(2) == 0) {
		$message = "Could not validate user. Either you are not logged in, or you do not have permissions to vote.";
	} elseif ($this->validate(2) == 2) {
		$message = "You are not logged in. Guest voting is not allowed.";
	} elseif (question == "") {
		$message = "You did not type in a poll question.";
	} else {
		
		$type= "poll";
	 	$dbpost = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 2);
		if ($choice1 != "") {
			$dbpost[1] = $dbpost[1] + 1;
		}
		if ($choice2 != "") {
			$dbpost[2] = $dbpost[2] + 1;
				}
		if ($choice3 != "") {
			$dbpost[3] = $dbpost[3] + 1;
		}
		if ($choice4 != "") {
			$dbpost[4] = $dbpost[4] + 1;
		}
		if ($choice5 != "") {
			$dbpost[5] = $dbpost[5] + 1;
		}
		if ($choice6 != "") {
			$dbpost[6] = $dbpost[6] + 1;
		}
		if ($choice7 != "") {
			$dbpost[7] = $dbpost[7] + 1;
		}
		if ($choice8 != "") {
			$dbpost[8] = $dbpost[8] + 1;
		}
		if ($choice9 != "") {
			$dbpost[9] = $dbpost[9] + 1;
		}
		if ($choice10 != "") {
			$dbpost[10] = $dbpost[10] + 1;
		}
		$this->update("forums/".$this->forum."/".$this->poll.".cgi", 2, $dbpost);
		$voters = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 3);
		$voters = array_push($voters, $this->user[0]);
		$this->update("forums/".$this->forum."/".$this->poll.".cgi", 3, $voters);
		// increase user's post counter
		$this->addtopostcounter();
		$message = "Your vote has been recorded. Go to <a href=\"index.php?a=poll&forum=".$this->forum."&poll=".$lineid."\">Poll</a> | <a href=\"index.php?a=forum&forum=".$this->forum."\">Forum</a>.";
	}
	$this->displaymessage("Vote", $message);
}

function showpoll () {
	$ipost = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 1);
	$cpost = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 3);
	$post = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 5);
	$user = $this->select($this->file['users'], $ipost[2]);
	$qpost = $this->select("forums/".$this->forum."/".$this->poll.".cgi", 4);
	$postcount = $cpost[1] + $cpost[2] + $cpost[3] + $cpost[4] + $cpost[5] + $cpost[6] + $cpost[7] + $cpost[8] + $cpost[9] + $cpost[10];
	if ($postcount == 0) { 
		$postcount = .01;
	}
?>
<table width="100%" cellpadding=5 cellspacing=1 class="bordertable">
<table width="100%" cellpadding=5 cellspacing=1 class="ctable1">
<tr>
<TD valign="middle" align="left" width="60%"><div align="left"> 
<span class="small">Question: <?=$qpost[1]?></span><br>
<span class="small">Posted By: <a href="index.php?a=member&type=viewprofile&user=<?=$user[1]?>"><?=$user[1]?></a></span></div></td> 
</tr> 
<TR><TD colspan="2" height="1" class="bordertable"></td></tr> 
<TR><TD colspan="2"><span class="regular">

<?
if ($post[1] != "") {
	if ($post[1] != "0") {
		$percent1 = (($cpost[1]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent1."\" height=\"20\"> ".$post[1]." ".$percent1." (".$cpost[1]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[1]." Votes)<br>";
	}
}
if ($post[2] != "") {
	if ($post[2] != "0") {
		$percent2 = (($cpost[2]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent2."\" height=\"20\"> ".$post[2]." ".$percent2." (".$cpost[2]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[2]." Votes)<br>";
	}
}
if ($post[3] != "") {
	if ($post[3] != "0") {
		$percent3 = (($cpost[3]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent3."\" height=\"20\"> ".$post[3]." ".$percent3." (".$cpost[3]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[3]." Votes)<br>";
	}
}
if ($post[4] != "") {
	if ($post[4] != "0") {
		$percent4 = (($cpost[4]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent4."\" height=\"20\"> ".$post[4]." ".$percent4." (".$cpost[4]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[4]." Votes)<br>";
	}
}
if ($post[5] != "") {
	if ($post[5] != "0") {
		$percent5 = (($cpost[5]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent5."\" height=\"20\"> ".$post[5]." ".$percent5." (".$cpost[5]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[5]." Votes)<br>";
	}
}
if ($post[6] != "") {
	if ($post[6] != "0") {
		$percent6 = (($cpost[6]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent6."\" height=\"20\"> ".$post[6]." ".$percent6." (".$cpost[6]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[6]." Votes)<br>";
	}
}
if ($post[7] != "") {
	if ($post[7] != "0") {
		$percent7 = (($cpost[7]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent7."\" height=\"20\"> ".$post[7]." ".$percent7." (".$cpost[7]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[7]." Votes)<br>";
	}
}
if ($post[8] != "") {
	if ($post[8] != "0") {
		$percent8 = (($cpost[8]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent8."\" height=\"20\"> ".$post[8]." ".$percent8." (".$cpost[8]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[8]." Votes)<br>";
	}
}
if ($post[9] != "") {
	if ($post[9] != "0") {
		$percent9 = (($cpost[9]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent9."\" height=\"20\"> ".$post[9]." ".$percent9." (".$cpost[9]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[9]." Votes)<br>";
	}
}
if ($post[10] != "") {
	if ($post[10] != "0") {
		$percent10 = (($cpost[10]/$postcount) * 100)."%";
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"".$percent10."\" height=\"20\"> ".$post[10]." ".$percent10." (".$cpost[10]." Votes)<br>";
	} else {
		echo "<img src=\"images/catbgnitsuj.gif\" width=\"3\" height=\"20\"> ".$post[2]." (".$cpost[10]." Votes)<br>";
	}
}
?>

</span></td></tr></table></td>
</tr>
<tr>
<td class="ctable2" align="center" width="22%"><?=date($this->timeformat, $ipost[1])?></td>
</tr>
<tr>
<td class="darkspacer" height="4" colspan="3"></td>
</tr>
</table>
</table>
<?
}

function displaypollinfobottom () {
	$forum = $this->select("forums/forums.cgi", $this->forum);
	?>
	</table>
	<br>
	<table width=100% cellpadding=2 cellspacing=1 class="bordertable">
	 <tr>
	  <td class="ctable1">&nbsp;</td>
	   <td class="ctable2" nowrap rowspan="2"><a href="index.php?a=post&forum=<?=$this->forum?>"><img src="images/topic_new.gif" border="0"></a> <a href="index.php?a=post&forum=<?=$this->forum?>&type=newpoll"><img src="images/poll_new.gif" border="0"></a></td>
	 </tr>
	 <tr>
	  <td class="ctable1" width="100%">Moderate Poll ( <a href="index.php?a=moderate&type=close&forum=<?=$this->forum?>&poll=<?=$this->poll?>">Close/Open</a> | <a href="index.php?a=moderate&type=polldelete&forum=<?=$this->forum?>&poll=<?=$this->poll?>">Delete</a> | <a href="index.php?a=moderate&type=move&forum=<?=$this->forum?>&poll=<?=$this->poll?>">Move</a> | <a href="index.php?a=moderate&type=pin&forum=<?=$this->forum?>&poll=<?=$this->poll?>">Pin/Unpin</a>)</td>
	 </tr>
	</table>
	<table width=100%>
	 <tr>
	  <td><span class="regular">Back To: <a href="index.php?a=forum&forum=<?=$forum[0]?>"><?=$forum[1]?></a></span></td>
	 </tr>
	</table>
	<?
}

}
