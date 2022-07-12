<?php

include "php 4-2+.php";

class module extends fpboard {

var $forum, $topic;

function execute () {
	global $forum, $topic, $type, $message, $subject;
	$this->forum = $forum;
	$this->topic = $topic;
	// check to make sure user is not posting to fast.
	if (($subject != "") || ($message != "")) {
		$this->checkposttime();
	}
	$this->header();
	if ($type == "newpoll") {
		$this->checkforumpermission(6, true, $this->forum);
    		//$this->newpoll(); 
           	} elseif ($type == "newpoll2") {
		$this->checkforumpermission(6, true, $this->forum);
		//$this->newpoll2();
	}  else {
		if ($this->topic == "") {
			$this->checkforumpermission(6, true, $this->forum);
			if ($subject == "") {
				$this->newtopic();
			} else {
				$this->newtopic2();
			}
		} else {
			$this->checkforumpermission(7, true, $this->forum);
			if ($message == "") {
				$this->reply();
			} else {
				$this->reply2();
			}
		}
	}
	$this->footer();
}

function checkposttime () {
	global $lastposttime, $HTTP_COOKIE_VARS;
	if ($lastposttime > time()-$this->posttime) {
		$this->header();
		$this->displaymessage("Error", "You have already posted in the last ".$this->posttime." seconds. Please wait and try again.");
		$this->footer();
		die();
	} else {
		setcookie("lastposttime", time(), time()+9999999999);
	}
}

function printguestwarning() {
    $reglink = "Please <a href=\"index.php?a=member&type=register\">register</a>";
    if($this->allowguestposting)
      echo(" (".$reglink." to fully enjoy the board!)");
    else
      echo($reglink." before posting!");
}

function displaysmilies () {
	// go ssf!
	?>
	<table border='0' cellpadding='2' cellspacing='0' align=center>
	 <tr>
	 <td class='ctable2' colspan='3'><u>Clickable Smilies</u></td>
	 </tr>
	 <tr>
	 <td align="center"><a href="javascript:emoticon(':)')"><img src="images/emoticons/smile.gif" border='0'></a></td>
	 <td align="center"><a href="javascript:emoticon(':(')"><img src="images/emoticons/sad.gif" border='0'></a></td>
	 <td align="center"><a href="javascript:emoticon(';)')"><img src="images/emoticons/wink.gif" border='0'></a></td>
	 </tr>
	 <tr>
	 <td align="center"><a href="javascript:emoticon(':D')"><img src="images/emoticons/biggrin.gif" border='0'></a></td>
	 <td align="center"><a href="javascript:emoticon(':lol:')"><img src="images/emoticons/chuckle.gif" border='0'></a></td>
	 <td align="center"><a href="javascript:emoticon(':rolleyes:')"><img src="images/emoticons/rolleyes.gif" border='0'></a></td>
	 </tr>
	 <tr>
	 <td align="center"><a href="javascript:emoticon(':p')"><img src="images/emoticons/tongue.gif" border='0'></a></td>
	 <td align="center"><a href="javascript:emoticon(':unsure:')"><img
		 src="images/emoticons/unsure.gif" border='0'></a></td>
	 <td></td>
	 </tr>
	 <tr>
	 <td align="center" colspan="3">
	<a href="javascript:emoticon('[img]image address goes here[/img]')"><font size="2">Add An Image</font></A>
	 </td>
	 </tr>
	 <tr>
	 <td align="center" colspan="3">
	 <a href="javascript:emoticon('[url]link address goes here[/url]')"><font size="2">Add A Link</font></A>
	 </td>
	 </tr>
	</table>
	  <?
}


function newpoll() {
	global $message, $thesession, $HTTP_COOKIE_VARS;
	if (empty($this->polllength)) { $this->polllength = 70; }
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post" name="Post">
        <input type="hidden" name="a" value="post">
        <input type="hidden" name="type" value="newpoll2">
        <input type="hidden" name="forum" value="<?=$this->forum?>">
        <input type="hidden" name="thesession" value="<?=$HTTP_COOKIE_VARS['sessionid']?>">
     <tr>
          <td colspan=2 class="category" align="center">Posting New Poll</td>
         </tr>
          <td class="ctable2" width=100>Logged in as:</td>
          <td class="ctable2"><?=$this->user[1]?><?
          if ($this->user[1]=="Guest")
          	$this->printguestwarning();
          ?>
          </td>
         </tr>
          <td class="ctable1" width=150>Poll Question</td>
          <td class="ctable1"><input size=30 maxlength="<?=$this->polllength?>" name="question"></td>
         </tr>
	 <tr>
	  <td class="ctable2" valign="top" width=150>Poll Icons</td>
	  <td class="ctable2">
	   <table width="400" border="0" cellpadding="0" cellspacing="0" align="left">
	    <tr>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi1"> <img src="images/picons/pi1.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi2"> <img src="images/picons/pi2.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi3"> <img src="images/picons/pi3.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi4"> <img src="images/picons/pi4.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi5"> <img src="images/picons/pi5.gif"></td>
		</tr>
	    <tr>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi6"> <img src="images/picons/pi6.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi7"> <img src="images/picons/pi7.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi8"> <img src="images/picons/pi8.gif"></td>
		</tr>
	   </table></td>
	 </tr>
         <tr>
          <td class="ctable1" valign="top" width=150>Poll Choices</td>
          <td class="ctable2">
           <table width="400" border="0" cellpadding="0" cellspacing="0" align="left">
            <tr>
                 <td class="ctable1">Choice 1: <input size=30 maxlength="<?=$this->polllength?>" name="choice1"></td>
            </tr>
            <tr>
                 <td class="ctable1">Choice 2: <input size=30 maxlength="<?=$this->polllength?>" name="choice2"></td>
            </tr>
            <tr>
                 <td class="ctable1">Choice 3: <input size=30 maxlength="<?=$this->polllength?>" name="choice3"></td>
            </tr>
            <tr>
                 <td class="ctable1">Choice 4: <input size=30 maxlength="<?=$this->polllength?>" name="choice4"></td>
            </tr>
            <tr>
                 <td class="ctable1">Choice 5: <input size=30 maxlength="<?=$this->polllength?>" name="choice5"></td>
            </tr>
            <tr>
                 <td class="ctable1">Choice 6: <input size=30 maxlength="<?=$this->polllength?>" name="choice6"></td>
            </tr>
            <tr>
                 <td class="ctable1">Choice 7: <input size=30 maxlength="<?=$this->polllength?>" name="choice7"></td>
            </tr>
            <tr>
                 <td class="ctable1">Choice 8: <input size=30 maxlength="<?=$this->polllength?>" name="choice8"></td>
            </tr>
            <tr>
                 <td class="ctable1">Choice 9: <input size=30 maxlength="<?=$this->polllength?>" name="choice9"></td>
            </tr>
            <tr>
                 <td class="ctable1">Choice 10: <input size=30 maxlength="<?=$this->polllength?>" name="choice10"></td>
            </tr>
           </table></td>
         </tr>
         <tr>
          <td colspan=2 align=center class="ctable2"><?$this->button("Post")?></td>
         </tr>
        </form>
        </table>
        <?
}

function newpoll2() {
        global $question, $choice1, $choice2, $choice3, $choice4, $choice5, $choice6, $choice7, $choice8;
	global $HTTP_COOKIE_VARS, $choice9, $choice10, $gtime, $thesession;
        if ($this->validate(2) == 0) {
                $message = "Could not validate user. Either you are not logged in, or you do not have permissions to post.";
        } elseif ($this->validate(2) == 2) {
                $message = "You are not logged in. Guest posting is not allowed.";
        } elseif (question == "") {
                $message = "You did not type in a poll question.";
        } elseif (choice1 == "") {
                $message = "You did not type in a minimum of 2 poll choices.";
        } elseif (choice2 == "") {
                $message = "You did not type in a minimum of 2 poll choices.";
        } else {
		$type= "poll";
		if (empty($this->polllength)) { $this->polllength = 70; }
                $question = $this->inputize($question, $this->polllength);
                $choice1  = $this->inputize($choice1, $this->maxpostsize, $this->postwrap);
                $choice2  = $this->inputize($choice2, $this->maxpostsize, $this->postwrap);
                $choice3  = $this->inputize($choice3, $this->maxpostsize, $this->postwrap);
                $choice4  = $this->inputize($choice4, $this->maxpostsize, $this->postwrap);
                $choice5  = $this->inputize($choice5, $this->maxpostsize, $this->postwrap);
                $choice6  = $this->inputize($choice6, $this->maxpostsize, $this->postwrap);
                $choice7  = $this->inputize($choice7, $this->maxpostsize, $this->postwrap);
                $choice8  = $this->inputize($choice8, $this->maxpostsize, $this->postwrap);
                $choice9  = $this->inputize($choice9, $this->maxpostsize, $this->postwrap);
                $choice10 = $this->inputize($choice10, $this->maxpostsize, $this->postwrap);

                // add to list.cgi
                $line = array($question, 0, $this->user[1], time(), $this->user[1], time(), 0, $posticon, 0, 0, $thesession, $type);
                $lineid = $this->insert("forums/".$this->forum."/list.cgi", $line);

                // make the poll file for this post
		$line = array(time(), $this->user[0], $HTTP_COOKIE_VARS['sessionid'], $type);
                $this->insert("forums/".$this->forum."/".$lineid.".cgi", $line); //line 1
                $line = array($question);
                $this->insert("forums/".$this->forum."/".$lineid.".cgi", $line); //line 2
		$line = array("", "", " ", " ", " ", " ", " ", " ", " ", " ");
                $this->insert("forums/".$this->forum."/".$lineid.".cgi", $line); //line 3
		$voters = array("");
                $this->insert("forums/".$this->forum."/".$lineid.".cgi", $voters); //line 4
		$line = array($choice1, $choice2, $choice3, $choice4, $choice5, $choice6, $choice7, $choice8, $choice9, $choice10);
                $this->insert("forums/".$this->forum."/".$lineid.".cgi", $line); //line 5
                // get info for this forum and update it.
                $lines = $this->select("forums/forums.cgi", $this->forum);
                $lines[2] = $lines[2] + 1;
                $lines[3] = $lines[3] + 1;
                $lines[4] = $lineid;
                $this->update("forums/forums.cgi", $this->forum, $lines);
                // increase user's post counter
                $this->addtopostcounter();
                $message = "New Poll Posted. Go to <a href=\"index.php?a=poll&forum=".$this->forum."&poll=".$lineid."\">Poll</a> | <a href=\"index.php?a=forum&forum=".$this->forum."\">Forum</a>.";
        }
        $this->displaymessage("New Poll", $message);
}

function reply2() {
	global $subject, $message, $gtime, $thesession, $HTTP_COOKIE_VARS, $inca, $attachmenturl;
	$topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
	if ($this->validate(3) == 0) {
		$message = "Could not validate user. Either you are not logged in, or you do not have permissions to reply.";
	} elseif ($this->validate(3) == 2) {
		$message = "You are not logged in. Guest posting is not allowed.";
	} elseif ($message == "") {
		$message = "You did not type in a message.";
	} elseif ($topic[7] == 1) {
		$message = "You may not reply to closed topics.";
	} else {
		$badwords = $this->options1[9];
		$subject = $this->inputize($subject, $this->topiclength);
		$message = $this->inputize($message, $this->maxpostsize, $this->postwrap);
		if ($badwords != "") {
			$words = explode(",", $badwords);
			foreach($words as $word) {
				$subject = eregi_replace("$word","******",$subject);
				$message = eregi_replace("$word","******",$message);
			}
		}
		if ($inca == 1) {
			$att = $attachmenturl;
			$temptype = substr($attachmenturl, (strlen($attachmenturl)-5));
			if (strstr($temptype, ".")) {
				$temptype = explode(".", $temptype);
				if (strstr($temptype[1],"htm")) {
					$desc = "HTML file";
				} elseif (strstr($temptype[1],"php")) {
					$desc = " file";
				} elseif (strstr($temptype[1],"jpg") || strstr($temptype[1],"gif") || strstr($temptype[1],"jpeg")
				|| strstr($temptype[1],"bmp")) {
					$desc = "Image file";
				} elseif (strstr($temptype[1],"gz") || strstr($temptype[1],"tar") || strstr($temptype[1],"zip")) {
					$desc = "Archive file (zip)";
				} elseif (strstr($temptype[1],"exe")) {
					$desc = "Application file";
				} elseif (strstr($temptype[1],"txt")) {
					$desc = "Text file";
				} elseif (strstr($temptype[1],"com") || strstr($temptype[1],"net") || strstr($temptype[1],"org") || $temptype[1] == "co") {
					$desc = "Web site";
				} else {
					$desc = "Unknown file type";
				}
			} else {
				$desc = "Web directory";
			}
		} else {
			$att = null;
			$desc = null;
		}
		$closed = $topic[6];
		$posticon = $topic[7];
		$views = $topic[8];
		$pinned = $topic[9];
		$line = array($subject, $message, time(), $this->user[0], $thesession, $closed, $posticon, $views, $pinned, $att, $desc);
		$this->insert("forums/".$this->forum."/".$this->topic.".cgi", $line);
		// update this topic's listing in list.cgi
		$line = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
		$line[2] = $line[2] + 1;
		$line[5] = $this->user[1];
		$line[6] = time();
		// update this topic's forum.
		$this->update("forums/".$this->forum."/list.cgi", $this->topic, $line);
		$lines = $this->select("forums/forums.cgi", $this->forum);
		$lines[3] = $lines[3] + 1;
		$lines[4] = $this->topic;
		$this->update("forums/forums.cgi", $this->forum, $lines);
		// increase user's post counter
		$this->addtopostcounter();
		$message = "Reply added. Click <a href=\"index.php?a=topic&forum=".$this->forum."&topic=".$this->topic."&page=last\">here</a> to return to the topic.";
		$redirect = "index.php?a=topic&forum=".$this->forum."&topic=".$this->topic."&page=last";
	}
	if ($this->writeerror == 1) {
		$this->displaymessage("Error", "The board was unable to post your reply.  Please go back and try one more time.");
	} else {
		$this->displaymessage("Reply", $message, $redirect);
	}
}

function reply() {
	global $quote;
	if (isset($quote)) {
		$post = $this->select("forums/".$this->forum."/".$this->topic.".cgi", $quote);
		$user = $this->select($this->file['users'], $post[4]);
		$quote = "[quote][i]Originally Posted By: [/i][b]".$user[1]."[/b]\n".$this->deinputize($post[2])."[/quote]\n";
	}
	?>
	<table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
	<form action="index.php" method="post" name="Post">
	<input type="hidden" name="a" value="post">
	<input type="hidden" name="forum" value="<?=$this->forum?>">
	<input type="hidden" name="topic" value="<?=$this->topic?>">
     <tr>
	  <td colspan=2 class="category" align="center">Posting Reply</td>
	 </tr>
	 <tr>
	  <td class="ctable2" width=100>Logged in as:</td>
	  <td class="ctable2"><?=$this->user[1]?><?php
	    if ($this->user[1]=="Guest")
	      $this->printguestwarning();
	  ?>
	  </td>
	 </tr>
	 <tr>
	  <td class="ctable1" width=150>Subject</td>
	  <td class="ctable1"><input size=30 maxlength="<?=$this->topiclength?>" name="subject" value="Re: <?=$this->listtopic[1]?>"></td>
	 </tr>
	 <tr>
	  <td class="ctable2" valign="top" width=150>
	  Message
		<?php $this->displaysmilies();?>
	  </td>
	  <td class="ctable2"><textarea name="message" cols=80 rows=8 onSelect="storeCaret(this);"
									 onClick="storeCaret(this);"
									 onKeyUp="storeCaret(this);"><?=$quote?></textarea></td>
	 </tr>
	 <tr>
	  <td class="ctable1"><input type="checkbox" name="inca" value="1">Include An Attachment:</td>
	  <td class="ctable1">Attachment URL:<br><input type="text" title="The attachment to this post has to be a file on the internet." name="attachmenturl" value="http://"></td>
	 </tr>
	 <tr>
	  <td colspan=2 align=center class="ctable1"><?$this->button("Post")?></td>
	 </tr>
	</form>
	</table>
	<br><br>
	<table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
	 <tr>
	  <td class="category" align="center" colspan="2">Posts, Reverse Order</td>
	 </tr>
	<?
	$posts = $this->selectall("forums/".$this->forum."/".$this->topic.".cgi");
	$posts = array_reverse($posts);
	foreach ($posts as $post) {
		$user = $this->select($this->file['users'], $post[4]);
		$post[2] = $this->tags($post[2]);
		?>
		<tr>
		 <td class="ctable1" width="100" valign="top"><?=$user[1]?></td>
		 <td class="ctable2" width="85%"><span class="small">Subject: <?=$post[1]?></span><hr size="1"><?=$post[2]?></td>
		</tr>
		<?
	}
	?></table><?
}

function newtopic() {
global $message, $thesession, $HTTP_COOKIE_VARS;
	?>
	<table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
	<form action="index.php" method="post" name="Post">
	<input type="hidden" name="a" value="post">
	<input type="hidden" name="forum" value="<?=$this->forum?>">
	<input type="hidden" name="thesession" value="<?=$HTTP_COOKIE_VARS['sessionid']?>">
     <tr>
	  <td colspan=2 class="category" align="center">Posting New Topic</td>
	 </tr>
	  <td class="ctable2" width=100>Logged in as:</td>
	  <td class="ctable2"><?=$this->user[1]?><?php
	    if ($this->user[1]=="Guest")
	      $this->printguestwarning();
	  ?>
	  </td>
	 </tr>
	  <td class="ctable1" width=150>Subject</td>
	  <td class="ctable1"><input size=30 maxlength="<?=$this->topiclength?>" name="subject"></td>
	 </tr>
	 <tr>
	  <td class="ctable2" valign="top" width=150>Post Icons</td>
	  <td class="ctable2">
	   <table width="400" border="0" cellpadding="0" cellspacing="0" align="left">
	    <tr>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi1"> <img src="images/picons/pi1.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi2"> <img src="images/picons/pi2.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi3"> <img src="images/picons/pi3.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi4"> <img src="images/picons/pi4.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi5"> <img src="images/picons/pi5.gif"></td>
		</tr>
	    <tr>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi6"> <img src="images/picons/pi6.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi7"> <img src="images/picons/pi7.gif"></td>
		 <td width="20%" align="center"><input type="radio" name="posticon" value="pi8"> <img src="images/picons/pi8.gif"></td>
		</tr>
	   </table></td>
	 </tr>
	 <tr>
	  <td class="ctable1" valign="top" width=150>Message
				<?php $this->displaysmilies();?>
	  </td>
	  <td class="ctable2"><textarea name="message" cols=80 rows=8 onSelect="storeCaret(this);"
									 onClick="storeCaret(this);"
									 onKeyUp="storeCaret(this);"></textarea></td>
	 </tr>
	  <td class="ctable1"><input type="checkbox" name="inca" value="1">Include An Attachment:</td>
	  <td class="ctable1">Attachment URL:<br><input type="text" title="The attachment to this post has to be a file on the internet." name="attachmenturl" value="http://"></td>
	 <tr>
	  <td colspan=2 align=center class="ctable2"><?$this->button("Post")?></td>
	 </tr>
	</form>
	</table>
	<?
}

function newtopic2() {
	global $subject, $message, $posticon, $gtime, $thesession, $inca, $attachmenturl;
	if ($this->validate(2) == 0) {
		$message = "Could not validate user. Either you are not logged in, or you do not have permissions to post.";
	} elseif ($this->validate(2) == 2) {
		$message = "You are not logged in. Guest posting is not allowed.";
	} elseif ($message == "") {
		$message = "You did not type in a message.";
	} else {
		$badwords = $this->options1[9];
		$subject = $this->inputize($subject, $this->topiclength);
		$message = $this->inputize($message, $this->maxpostsize, $this->postwrap);
		if ($badwords != "") {
			$words = explode(",", $badwords);
			foreach($words as $word) {
				$subject = eregi_replace("$word","******",$subject);
				$message = eregi_replace("$word","******",$message);
			}
		}
		// add to list.cgi
		$line = array($subject, 0, $this->user[1], time(), $this->user[1], time(), 0, $posticon, 0, 0, $thesession);
		$lineid = $this->insert("forums/".$this->forum."/list.cgi", $line);
		// make the topic file for this post
		if ($inca == 1) {
			$att = $this->inputize($attachmenturl);
			$temptype = substr($attachmenturl, (strlen($attachmenturl)-5));
			if (strstr($temptype, ".")) {
				$temptype = explode(".", $temptype);
				if (strstr($temptype[1],"htm")) {
					$desc = "HTML file";
				} elseif (strstr($temptype[1],"php")) {
					$desc = "PHP file";
				} elseif (strstr($temptype[1],"jpg") || strstr($temptype[1],"gif") || strstr($temptype[1],"jpeg")
				|| strstr($temptype[1],"bmp")) {
					$desc = "Image file";
				} elseif (strstr($temptype[1],"gz") || strstr($temptype[1],"tar") || strstr($temptype[1],"zip")) {
					$desc = "Archive file (zip)";
				} elseif (strstr($temptype[1],"exe")) {
					$desc = "Application file";
				} elseif (strstr($temptype[1],"txt")) {
					$desc = "Text file";
				} elseif (strstr($temptype[1],"com") || strstr($temptype[1],"net") || strstr($temptype[1],"org") || $temptype[1] == "co") {
					$desc = "Web site";
				} else {
					$desc = "Unknown file type";
				}
			} else {
				$desc = "Web directory";
			}
		} else {
			$att = null;
			$desc = null;
		}
		$line = array($subject, $message, time(), $this->user[0], $thesession, 0, $posticon, 0, $att, $desc);
		$this->insert("forums/".$this->forum."/".$lineid.".cgi", $line);
		// get info for this forum and update it.
		$lines = $this->select("forums/forums.cgi", $this->forum);
		$lines[2] = $lines[2] + 1;
		$lines[3] = $lines[3] + 1;
		$lines[4] = $lineid;
		$this->update("forums/forums.cgi", $this->forum, $lines);
		// increase user's post counter
		$this->addtopostcounter();
		$message = "New Topic Posted. Go to <a href=\"index.php?a=topic&forum=".$this->forum."&topic=".$lineid."\">Topic</a> | <a href=\"index.php?a=forum&forum=".$this->forum."\">Forum</a>.";
	}
	if ($this->writeerror == 1) {
		$this->displaymessage("Error", "The board was unable to post your topic.  Please go back and try one more time.");
	} else {
		$this->displaymessage("New Topic", $message);
	}
}

}

?>