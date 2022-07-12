<?php

include "php 4-2+.php";

class module extends fpboard {

var $forum, $topic, $post;

function execute () {
        global $type, $subject, $forum, $topic, $post;
        $this->forum = $forum;
        $this->topic = $topic;
        $this->post = $post;
        $this->header();
        $this->guestblock();
        if (($type == "edit") && ($subject == "")) {
                $this->edit1();
        } elseif (($type == "edit") && ($subject != "")) {
                $this->edit2();
        }
        if ($type == "postdelete") {
                $this->postdelete1();
        } elseif ($type == "postdelete2") {
                $this->postdelete2();
        }
        if ($type == "topicdelete") {
                $this->topicdelete1();
        } elseif ($type == "topicdelete2") {
                $this->topicdelete2();
        }
        if ($type == "ban") {
                $this->ban();
        } elseif ($type == "ban2") {
                $this->ban2();
        }
        if ($type == "close") {
                $this->close();
        } elseif ($type == "close2") {
                $this->close2();
        }
        if ($type == "move") {
                $this->move();
        } elseif ($type == "move2") {
                $this->move2();
        }
        if ($type == "pin") {
                $this->pin();
        } elseif ($type == "pin2") {
                $this->pin2();
        }
        if ($type == "rename") {
                $this->rename();
        } elseif ($type == "rename2") {
                $this->rename2();
        }
        $this->footer();
}

// come on, moderation validation, you just gotta like that name :)
// uservalidation = the line to see if this is the user who made this post
// allvalidation = if this user can edit all posts
// topicpost = topic if we're looking in list.cgi, post if we're looking in topicid.cgi
function moderationvalidation ($uservalidation, $allvalidation, $topicpost) {
        if ($topicpost == "topic") {
                $lookingin = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
                $user = $this->select($this->file['users'], $lookingin[3]);
        } else {
                $lookingin = $this->select("forums/".$this->forum."/".$this->topic.".cgi", $this->post);
                $user = $this->select($this->file['users'], $lookingin[4]);
        }
        // is the user deleting this post the same user who made this topic?
        if ($this->user[1] == $user[1]) {
                $permission = $this->validate($uservalidation);
        } else {
                $permission = $this->validate($allvalidation);
        }
        if ($permission == 0) {
                $this->displaymessage("Error", "You do not have the correct permissions to perform this action.");
                $this->footer();
                die();
        } elseif ($permission == 2) {
                $this->displaymessage("Error", "You are not logged in and may not perform this action.");
                $this->footer();
                die();
        }
}

function ban () {
	global $bansessionid, $id;
	$this->doomsday(14);
	$title = "Confirm Banishemnt";	
	$url = "index.php?a=moderate&type=ban2&bansessionid=".$bansessionid."&id=".$id."";
	$message = "Are you sure you want to ban this user?  This operation is irreversable until a later version of edgeboard is released.";
	$message = $message."<br>( <a href='$url'>Yes</a> | <a href='index.php'>No</a> )";
	$this->displaymessage($title, $message);
}

function find ($file, $partid, $part) {
	$openfile = $this->selectall($file);
	foreach ($openfile as $array) {
		if ($array[$partid] == $part) {
			$return = $array;
		}
	}
	if (isset($return)) {
		return $return;
	} else {
		return 0;
	}
}

function ban2 () {
	global $bansessionid, $id;
	$this->doomsday(14);
	if ($id == "guest") {
		$where = $this->find($this->file['ip'], 3, $bansessionid);
		if ($where != 0) {
			$newban = array($where[1], $where[2]);
			$this->insert($this->file['banned'], $newban);
			$this->displaymessage("Success", "Banishment successful.  The user is now banned from the board.");
		} else {
			$this->displaymessage("Error", "Banishment unsuccessful.  The board was not able to log the user the last time they were online.");
		}
	} else {
		$where = $this->find($this->file['ip'], 1, $id);
		if ($where != 0) {
			$this->disableuser2($id);
			$newban = array($where[1], $where[2]);
			$this->insert($this->file['banned'], $newban);
			$this->displaymessage("Success", "Banishment successful.  The user is now banned from the board.");
		} else {
			$this->displaymessage("Error", "Banishment unsuccessful.  The board was not able to log the user the last time they were online.");
		}
	}
}

//block guests from moderating posts/topics
function guestblock () {
        global $user;
        if ($this->user[0] == "0") {
                $this->displaymessage("Error", "You are not logged in and may not perform this action.<br>You also do not have the correct permissions to perform this action.");
                $this->footer();
                die();
        } elseif ($this->user[1] == "Guest") {
                $this->displaymessage("Error", "You are not logged in and may not perform this action.<br>You also do not have the correct permissions to perform this action.");
                $this->footer();
                die();
        }
}

function rename2 () {
        global $subject;
        if ($this->validate(14) == 1) {
        $topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        $topic[1] = $subject;
        $this->update("forums/".$this->forum."/list.cgi", $this->topic, $topic);
        $this->displaymessage("Rename Topic", "Topic renamed.");
        }
}

function rename () {
        $this->moderationvalidation(10, 11, "topic");
        $topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post">
        <input type="hidden" name="a" value="moderate">
        <input type="hidden" name="type" value="rename2">
        <input type="hidden" name="forum" value="<?=$this->forum?>">
        <input type="hidden" name="topic" value="<?=$this->topic?>">
         <tr>
          <td colspan="2" class="category" align="center">Rename Topic</td>
         </tr>
         <tr>
          <td class="ctable1" width=100>Subject:</td>
          <td class="ctable1"><?=$topic[1]?></td>
         </tr>
         <tr>
          <td class="ctable1" width=100>Rename:</td>
          <td class="ctable1"><input size=30 name="subject"></td>
         <tr>
          <td colspan=2 align=center class="ctable1"><?
          echo $this->button("Rename Topic");
          ?></td>
         </tr>
        </form>
        </table>
        <?
}

function close2 () {
        $this->moderationvalidation(10, 11, "topic");
        $dbtopic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        if ($dbtopic[7] == 1) {
                $dbtopic[7] = 0;
		$res = 0;
        } else {
                $dbtopic[7] = 1;
		$res = 1;
        }
        $this->update("forums/".$this->forum."/list.cgi", $this->topic, $dbtopic);
	$topics = $this->selectall("forums/".$this->forum."/".$this->topic.".cgi");
	$count = count($topics);// - 1;
	$topic = $this->select("forums/".$this->forum."/".$this->topic.".cgi", $count);
	$topic[6] = $res;
	$this->update("forums/".$this->forum."/".$this->topic.".cgi", $count, $topic);
        $this->displaymessage("Close/Open Topic", "Topic closed/opened.");
}

function close () {
        $this->moderationvalidation(10, 11, "topic");
        $topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post">
        <input type="hidden" name="a" value="moderate">
        <input type="hidden" name="type" value="close2">
        <input type="hidden" name="forum" value="<?=$this->forum?>">
        <input type="hidden" name="topic" value="<?=$this->topic?>">
         <tr>
          <td colspan="2" class="category" align="center">Close/Open Topic</td>
         </tr>
         <tr>
          <td class="ctable1" width=100>Subject:</td>
          <td class="ctable1"><?=$topic[1]?></td>
         </tr>
         <tr>
          <td colspan=2 align=center class="ctable1"><?
          if ($topic[7] == 1) {
                    echo $this->button("Open Topic");
          } else {
                    echo $this->button("Close Topic");
          } ?></td>
         </tr>
        </form>
        </table>
        <?
}

function pin2 () {
        $this->moderationvalidation(14, 15, "topic");
        $dbtopic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        if ($dbtopic[10] != 1) {
                $dbtopic[10] = 1;
		$res = 1;
        } else {
                $dbtopic[10] = 0;
		$res = 0;
        }
        $this->update("forums/".$this->forum."/list.cgi", $this->topic, $dbtopic);
	$topics = $this->selectall("forums/".$this->forum."/".$this->topic.".cgi");
	$count = count($topics);// - 1;
	$topic = $this->select("forums/".$this->forum."/".$this->topic.".cgi", $count);
	$topic[9] = $res;
	$this->update("forums/".$this->forum."/".$this->topic.".cgi", $count, $topic);
        $this->displaymessage("Pin/Unpin Topic", "Topic successfully pinned/unpinned.");
}

function pin () {
        $this->doomsday(13);
        $topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post">
        <input type="hidden" name="a" value="moderate">
        <input type="hidden" name="type" value="pin2">
        <input type="hidden" name="forum" value="<?=$this->forum?>">
        <input type="hidden" name="topic" value="<?=$this->topic?>">
         <tr>
          <td colspan="2" class="category" align="center">Pin/Unpin Topic</td>
         </tr>
         <tr>
          <td class="ctable1" width=100>Subject:</td>
          <td class="ctable1"><?=$topic[1]?></td>
         </tr>
         <tr>
          <td colspan=2 align=center class="ctable1"><?
          if ($topic[10] == 1) {
                    echo $this->button("Unpin Topic");
          } else {
                    echo $this->button("Pin Topic");
          } ?></td>
         </tr>
        </form>
        </table>
        <?
}

function move2 () {
        $this->moderationvalidation(12, 13, "topic");
        global $newforum;
        // get the topic info from list.cgi, and then take it out.
        $topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        $newtopic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        $topic = $this->delete("forums/".$this->forum."/list.cgi", $this->topic);
        // update the lastpost info for the old forum
        $forum = $this->select("forums/forums.cgi", $this->forum);
        $forum[2] = $forum[2] - 1;
        $forum[3] = $forum[3] - 1 - $topic[2];
        // the next two lines of code were taken directly from forum.php
        $topics = $this->selectsort("forums/".$this->forum."/list.cgi", 6);
        $topics = array_reverse($topics);
        $forum[4] = $topics[0][0];
        // now update the forum.
        $this->update("forums/forums.cgi", $this->forum, $forum);
        // we need to get rid of the old topic id on $newtopic before inserting it
        // into the new forum.
        array_shift($newtopic);
        // add the newtopic into the new forum's list.cgi
        $lineid = $this->insert("forums/".$newforum."/list.cgi", $newtopic);
        // now add this topic into the new forum in forums.cgi
        $forum = $this->select("forums/forums.cgi", $newforum);
        $forum[2]++;
        $forum[3] = $forum[3] + 1 + $newtopic[1];
        $forum[4] = $lineid;
        $this->update("forums/forums.cgi", $newforum, $forum);
        // now copy the old topic file to the new forum
        copy("forums/".$this->forum."/".$this->topic.".cgi", "forums/".$newforum."/".$lineid.".cgi");
        //and last, delete the old topic file.
        unlink("forums/".$this->forum."/".$this->topic.".cgi");
        $this->displaymessage("Move Topic", "Topic moved. Click <a href=\"index.php?a=topic&forum=".$newforum."&topic=".$lineid."\">here</a> to visit the new topic.");
}

function move () {
        $this->moderationvalidation(12, 13, "topic");
        $topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        $forums = $this->selectall("forums/forums.cgi");
        $forumlist = "";
        foreach ($forums as $forum) {
                $forumlist = $forumlist . "<option value=\"".$forum[0]."\">".$forum[1]."</option>";
        }
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post">
        <input type="hidden" name="a" value="moderate">
        <input type="hidden" name="type" value="move2">
        <input type="hidden" name="forum" value="<?=$this->forum?>">
        <input type="hidden" name="topic" value="<?=$this->topic?>">
         <tr>
          <td colspan="2" class="category" align="center">Move Topic</td>
         </tr>
         <tr>
          <td class="ctable1" width=100>Subject:</td>
          <td class="ctable1"><?=$topic[1]?></td>
         </tr>
         <tr>
          <td class="ctable1" width=100>Destination Forum:</td>
          <td class="ctable1"><select name="newforum"><?=$forumlist?></select></td>
         </tr>
         <tr>
          <td colspan=2 align=center class="ctable1"><?$this->button("Move Topic")?></td>
         </tr>
        </form>
        </table>
        <?
}

function topicdelete2 () {
        $this->moderationvalidation(8, 9, "topic");
        // get topic info from list.cgi. we need the replies to know how many
        // posts to subtract from the forum.
        $topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        // now we can take this topic out of list.cgi
        $this->delete("forums/".$this->forum."/list.cgi", $this->topic);
        // get forum info. subtract 1 from topics & 1 + replies from posts.
        // then find the last topic in list.cgi and make it last post in forums.cgi
        $forum = $this->select("forums/forums.cgi", $this->forum);
        $forum[2] = $forum[2] - 1;
        $forum[3] = $forum[3] - 1 - $topic[2];
        // the next two lines of code were taken directly from forum.php
        $topics = $this->selectsort("forums/".$this->forum."/list.cgi", 6);
        $topics = array_reverse($topics);
        $forum[4] = $topics[0][0];
        // now update the forum.
        $this->update("forums/forums.cgi", $this->forum, $forum);
        // let's not forget to actually get rid of the topic file.
        // looks like this function isn't in our flatfile class, and why should it
        // be, since it's only one line! that's 3 lines less than the amount of lines
        // is used to comment for it :).
        unlink("forums/".$this->forum."/".$this->topic.".cgi");
        $this->displaymessage("Delete Topic", "Topic deleted.");
}

function topicdelete1 () {
        $this->moderationvalidation(8, 9, "topic");
        $topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post">
        <input type="hidden" name="a" value="moderate">
        <input type="hidden" name="type" value="topicdelete2">
        <input type="hidden" name="forum" value="<?=$this->forum?>">
        <input type="hidden" name="topic" value="<?=$this->topic?>">
         <tr>
          <td colspan="2" class="category" align="center">Delete Topic</td>
         </tr>
         <tr>
          <td class="ctable1" width=100>Subject:</td>
          <td class="ctable1"><?=$topic[1]?></td>
         </tr>
         <tr>
          <td colspan=2 align=center class="ctable1"><?$this->button("Delete Topic")?></td>
         </tr>
        </form>
        </table>
        <?
}

function postdelete2 () {
        $this->moderationvalidation(6, 7, "post");
        if ($this->post == 1) {
                displaymessage("Error", "You may not delete the first post of a topic.");
        } else {
                // get user and decrease post count by 1
                $post = $this->select("forums/".$this->forum."/".$this->topic.".cgi", $this->post);
                $user = $this->select($this->file['users'], $post[4]);
                $user[11] = $user[11] - 1;
                $this->update($this->file['users'], $user[0], $user);
                // delete this post
                $this->delete("forums/".$this->forum."/".$this->topic.".cgi", $this->post);
                // subtract one from replies in list.cgi
                $topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
                $topic[2] = $topic[2] - 1;
                $topic = $this->update("forums/".$this->forum."/list.cgi", $this->topic, $topic);
                // subtract one from posts in forums.cgi
                $line = $this->select("forums/forums.cgi", $this->forum);
                $line[3] = $line[3] - 1;
                $this->update("forums/forums.cgi", $this->forum, $line);
                // give them their "delete successful" message.
                $this->displaymessage("Post Deleted.", "Click <A href=\"index.php?a=topic&forum=".$this->forum."&topic=".$this->topic."\">here</a> to return to the topic.", $message);
        }
}

function postdelete1 () {
        $this->moderationvalidation(6, 7, "post");
        if ($this->post == 1) {
                $this->displaymessage("Error", "You may not delete the first post of a topic.");
        } else {
        $post = $this->select("forums/".$this->forum."/".$this->topic.".cgi", $this->post);
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post">
        <input type="hidden" name="a" value="moderate">
        <input type="hidden" name="type" value="postdelete2">
        <input type="hidden" name="forum" value="<?=$this->forum?>">
        <input type="hidden" name="topic" value="<?=$this->topic?>">
        <input type="hidden" name="post" value="<?=$this->post?>">
         <tr>
          <td colspan="2" class="category" align="center">Delete Post</td>
         </tr>
         <tr>
          <td class="ctable1" width=100>Subject:</td>
          <td class="ctable1"><?=$post[1]?></td>
         </tr>
         <tr>
          <td colspan=2 align=center class="ctable1"><?$this->button("Delete Post")?></td>
         </tr>
        </form>
        </table>
        <?
        }
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
         </table>
          <?
}

function edit1 () {
        $this->moderationvalidation(4, 5, "post");
        $post = $this->select("forums/".$this->forum."/".$this->topic.".cgi", $this->post);
        ?>
        <script language='javascript'>
         function emoticon(smilie)
         {
        document.Post.message.value = document.Post.message.value + smilie;
        document.Post.message.focus();
                                }
                                </script>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post" name="Post">
        <input type="hidden" name="a" value="moderate">
        <input type="hidden" name="type" value="edit">
        <input type="hidden" name="forum" value="<?=$this->forum?>">
        <input type="hidden" name="topic" value="<?=$this->topic?>">
        <input type="hidden" name="post" value="<?=$this->post?>">
         <tr>
          <td colspan="2" class="category" align="center">Edit Post</td>
         </tr>
         <tr>
          <td class="ctable1" width=150>Topic Info:</td>
          <td class="ctable1">Posted by: <?=$user[1]?> on <?=date($this->timeformat, $post[3])?></td>
         </tr>
         <tr>
          <td class="ctable2" width=150>Logged in as:</td>
          <td class="ctable2"><?=$this->user[1]?></td>
         </tr>
         <tr>
          <td class="ctable1" width=150>Subject</td>
          <td class="ctable1"><input size=30 name="subject" value="<?=$post[1]?>"></td>
         </tr>
         <tr>
          <td class="ctable2" width=150>
                       Message
                       <?php $this->displaysmilies();?>
          </td>
          <td class="ctable2"><textarea name="message" cols=80 rows=8><?=$this->deinputize($post[2])?></textarea></td>
         </tr>
         <tr>
          <td colspan=2 align=center class="ctable1"><?$this->button("Edit")?></td>
         </tr>
        </form>
        </table>
        <?
}

function edit2 () {
        global $subject, $message, $thetime;
        $thetime = date($this->timeformat, time());
        $thetime = str_replace("\n", "", $thetime);
        $thetime = $this->inputize($thetime);
        $this->moderationvalidation(4, 5, "post");
        $post = $this->select("forums/".$this->forum."/".$this->topic.".cgi", $this->post);
        $message = $message . "\n\nEdited by " . $this->user[1] . " on " . $thetime . ".";
        $subject = $this->inputize($subject);
        $message = $this->inputize($message);
        $post[1] = $subject;
        $post[2] = $message;
        $this->update("forums/".$this->forum."/".$this->topic.".cgi", $this->post, $post);
        $this->displaymessage("Edit Post", "Post Edited. Click <a href=\"index.php?a=topic&forum=".$this->forum."&topic=".$this->topic."\">here</a> to return to the topic.");
}

}

?>