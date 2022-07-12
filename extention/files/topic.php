<?php

include "php 4-2+.php";

class module extends fpboard {
var $forum, $topic, $replylink, $posts, $page, $pagelinks, $listtopic;

function execute () {
        global $forum, $topic, $page;
        $this->forum = $forum;
        $this->topic = $topic;
        $this->posts = $this->selectall("forums/".$this->forum."/".$this->topic.".cgi");
        $this->setlisttopic();
        if ($page == "") {
                $this->page = 1;
        } elseif ($page == "last") {
                $this->setlastpage();
        } else {
                $this->page = $page;
        }
        $this->header();
        $this->checkforumpermission(5, true, $this->forum);
	if ($post[12] == "poll") {
		$message = "You are not allowed to view polls with the topic script. Go to <a href=\"index.php?a=poll&forum=".$this->forum."&poll=".$this->topic."\">Poll</a> | <a href=\"index.php?a=forum&forum=".$this->forum."\">Forum</a>.";
		$this->displaymessage("New Poll", $message);
	} else {
        	$this->setpagelinks();
	        $this->setreplylink();
        	$this->displaytopicinfotop();
        	$this->displayposts();
        	$this->displaytopicinfobottom();
        }
        $this->footer();
}

function setlastpage () {
        $this->page = ceil(count($this->posts)/$this->postsperpage);
}

function setlisttopic () {
        $this->listtopic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
        $this->listtopic[9]++;
        $this->update("forums/".$this->forum."/list.cgi", $this->topic, $this->listtopic);
}

function setreplylink () {
        if ($this->listtopic[7] == 1) {
                $this->replylink = "<img src=\"images/topic_closed.gif\" border=\"0\">";
        } else {
                $this->replylink = "<a href=\"index.php?a=post&forum=".$this->forum."&topic=".$this->topic."\"><img src=\"images/topic_reply.gif\" border=\"0\"></a>";
        }
}

function setpagelinks () {
        $this->pagelinks = "";
        for ($i=1;count($this->posts)>=$i*$this->postsperpage-$this->postsperpage+1;$i++){
                if ($i == $this->page) {
                        $b = " <b>";
                        $r = "</b>";
                } else {
                        $b = " <a href=\"index.php?a=topic&forum=".$this->forum."&topic=".$this->topic."&page=".$i."\">";
                        $r = "</a>";
                }
                $this->pagelinks = $this->pagelinks.$b.$i.$r;
        }
}

function displaytopicinfotop () {
	if ($this->listtopic[7] == "1") {
		$topicname = "Closed: " . $topicname;
	} ?>
	<table width=100% cellpadding=2 cellspacing=1 class="bordertable">
	 <tr>
	  <td class="category" align="center" colspan="2">Topic</td>
	 </tr>
	 <tr>
	  <td class="ctable1" width="100%"><span class="regular">Viewing Topic: <?=$this->listtopic[1]?></span></td>
	  <td class="ctable2" nowrap rowspan="2"><span class="regular"><a href="index.php?a=post&forum=<?=$this->forum?>"><img src="images/topic_new.gif" border="0"></a> <?=$this->replylink?></span></td>
	 </tr>
	 <tr>
	  <td class="ctable1">[ <?=$this->pagelinks?> ]</td>
	 </tr>
	</table>
	<br>
	<?
}

function displayposts () {
	$i = 1;
	?>
	<table width="100%" cellpadding=5 cellspacing=1 class="bordertable">
	<?
	$posts = $this->limit($this->posts, ($this->page-1)*$this->postsperpage, $this->postsperpage-1);
	foreach ($posts as $post) {
		$user = $this->select($this->file['users'], $post[4]);
		$usergroup = $this->select($this->file['groups'], $user[3]);
		$post[2] = $this->tags($post[2]);
		if ($user[5] != "") {
			$location = "$user[5]";
		} else {
			$location = "";
		}
		if ($user[12] != "") {
			$signature = "<br><hr size=\"1\">".$this->tags($user[12]);
		} else {
			$signature = "";
		}
		if ($user[14] != "") {
			$avatar = "<img src=\"".$user[14]."\">";
		} else {
			$avatar = "";
		}
		if ($i == 1) {
			$int = 9;
		        if ($user[1] == "Guest") {
				$suser = "guest";
			} else { 
				$suser = $post[4];
			}
		} else {
			$int = 10;
        		if ($user[1] == "Guest") {
				$suser = "guest";
			} else {
				$suser = $post[4];
			}
		}
		$this->title[$user[0]] = "";
		if (empty($user[11])) {
			$p = "Guest Post";
		} else {
			$p = $user[11];
		}
		$this->pip($user[11], $user[3], $user[0]);
?><tr>
<td class="ctable1" valign="top" width="18%" <? if ($user[10] != "disabled") { $this->trfx($a, ($user[0])); }?>>
<a href="index.php?a=member&type=viewprofile&userid=<?=$user[0]?>"><?=$user[1]?></a><br>
<span class="small">
<?
if ($user[10] == "disabled") {
        echo "This user has been <b>Disabled</b>.\n";
} else {
        echo "<b><?=$this->title[$user[0]]?></b><br><b>".$usergroup[1]."</b><br>\n";
	if (!empty($avatar)) { echo $avatar."<br>"; }
?>
<?=$this->pip?>
<?	if (!empty($location)) { echo "Location: ".$location."<br>"; } ?>
Posts: <?=$p?><? } ?></span>
</td>
<td class="ctable2" valign="top" width="82%" colspan="2"><Table width="100%" align="center"> 
<TR> 
<TD valign="middle" align="left" width="60%"><div align="left"> 
<span class="small">Subject: <?=$post[1]?></span></div></td> 
<TD align="right" width="40%"><div align="right">
<a href="index.php?a=post&forum=<?=$this->forum?>&topic=<?=$this->topic?>&quote=<?=$post[0]?>"><img src="images/quote.gif" border="0"></a> 
<a href="index.php?a=moderate&type=edit&forum=<?=$this->forum?>&topic=<?=$this->topic?>&post=<?=$post[0]?>"><img src="images/edit.gif" border="0"></a> 
<a href="index.php?a=moderate&type=postdelete&forum=<?=$this->forum?>&topic=<?=$this->topic?>&post=<?=$post[0]?>"><img src="images/delete.gif" border="0"></a>
<? if ($this->validated == 1) { ?><a href="index.php?a=moderate&type=ban&session=<?=$post[5]?>&id=<?=$user[0]?>"><img src="images/ban.gif" border="0"></a><? } ?>
</div></td></tr> 
<TR><TD colspan="2" height="1" class="bordertable"></td></tr> 
<TR><TD colspan="2"><span class="regular">
<?=$post[2]?><br><?
if (!empty($post[$int])) {?>
<hr size="1">
<table width="80%" class="ctable2" border="0" cellpadding="0" cellspacing="0">
 <tr><td class="small" width="*" align="right"><b>Attachment</b>:</td></tr>
 <tr>
  <td rowspan="2" width="22"><image src="images/forumnew.gif"></td>
  <td class="small" width="*"><? echo "File Location: <a href='".$post[$int]."'><i>".$post[$int]."</i></a>"; ?></td>
 </tr>
 <tr>
  <td class="small" width="*"><? echo "File Type: <i>".$post[($int+1)]."</i>"; ?></td>
 </tr>
</table><br>
<?
}?><?=$signature?> 
</span></td></tr></table></td>
</tr>
<td class="ctable1" align="center" width="18%">
<?=$this->bigpip?>
<td class="ctable2" align="left" width="60%"><span class="small">
<?
//Display profile buttons for each messaging method if applicable
$buttons = "<a href=\"index.php?a=messenger&type=send&to=$user[1]\"><img src=\"images/pb_msg.gif\" border=\"0\"></a> ";
if ($user[4] != "") {
$buttons = $buttons . "<a href=\"mailto:$user[4]\"><img src=\"images/pb_mail.gif\" border=\"0\"></a> ";
}
if ($user[6] != "") {
$buttons = $buttons . "<a href=\"aim:goim?screenname=".$user[6]."&message=Hi.+Are+you+there?\"><img src=\"images/pb_aim.gif\" border=\"0\"></a> ";
}
if ($user[7] != "") {
$buttons = $buttons . "<a href='http://members.msn.com/default.msnw?mem=$user[7]' TARGET='new'><img src=\"images/pb_msn.gif\" border=\"0\"></a> ";
}
if ($user[8] != "") {
$buttons = $buttons . "<a href='http://edit.yahoo.com/config/send_webmesg?.target=$user[8]& ;.src=pg' TARGET='new'><img src=\"images/pb_yim.gif\" border=\"0\"></a> ";
}
if ($user[9] != "") {
$buttons = $buttons . "<a href='http://web.icq.com/wwp?Uin=$user[9]' TARGET='new'><img src=\"images/pb_icq.gif\" border=\"0\"></a> ";
}
if ($user[13] != "") {
$buttons = $buttons . "<a href='$user[13]' TARGET='new'><img src=\"images/pb_www.gif\" border=\"0\"></a> ";
}
if ($user[10] != "disabled") {
        echo $buttons;
} else {
	echo "<span style='display: none;'>.</span>";
}
?>
</span></td>
<td class="ctable2" align="center" width="22%"><?=$this->bdate($this->timeformat, $post[3])?></td>
</tr>
<tr>
<td class="darkspacer" height="4" colspan="3"></td>
</tr>

<?
$i++;
}
}


function displaytopicinfobottom () {
        $forum = $this->select("forums/forums.cgi", $this->forum);
        ?>
        </table>
        <br>
        <table width=100% cellpadding=2 cellspacing=1 class="bordertable">
         <tr>
          <td class="ctable1">[ <?=$this->pagelinks?> ]</td>
           <td class="ctable2" nowrap rowspan="2"><a href="index.php?a=post&forum=<?=$this->forum?>"><img src="images/topic_new.gif" border="0"></a> <?=$this->replylink?></td>
         </tr>
         <tr>
          <td class="ctable1" width="100%">Moderate Topic ( <? if ($this->validated == 1) { ?><a href="index.php?a=moderate&type=close&forum=<?=$this->forum?>&topic=<?=$this->topic?>">Close/Open</a> | <a href="index.php?a=moderate&type=topicdelete&forum=<?=$this->forum?>&topic=<?=$this->topic?>">Delete</a> | <a href="index.php?a=moderate&type=move&forum=<?=$this->forum?>&topic=<?=$this->topic?>">Move</a> | <a href="index.php?a=moderate&type=pin&forum=<?=$this->forum?>&topic=<?=$this->topic?>">Pin/Unpin</a> | <a href="index.php?a=moderate&type=rename&forum=<?=$this->forum?>&topic=<?=$this->topic?>">Rename</a> <? } else { ?>You may not moderate a topic.<? } ?> )</td>
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
?>