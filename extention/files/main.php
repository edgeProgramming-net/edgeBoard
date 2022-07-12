<?php

include "php 4-2+.php";

class module extends fpboard {

var $topics, $posts, $validated;

function execute () {
        global $type;
        if ($type == "mark") {
		$this->markposts();
        }
        if ($type == "invis") {
		$this->in();
        }
        if ($type == "noninvis") {
		$this->ni();
        }
        $this->validated = $this->validate(14);
        $this->header();
        $this->displayforums();
        $this->displaywhosonline();
        $this->displaystats();
        $this->displaylogin();
        $this->displayforumimgs();
        $this->footer();
}

function ni () {
	setcookie("invis", "0", time()+5184000);
	$this->whosonline();
}

function in () {
	setcookie("invis", "1", time()+5184000);
	$this->whosonline();
}

function spacer () {
?>
     <tr>
          <td class="ctable2" colspan="5" height="12"></td>
         </tr>
<?
}

function markposts () {
	global $HTTP_COOKIE_VARS;
	$cuser = $HTTP_COOKIE_VARS['cuser'];
	$cpass = $HTTP_COOKIE_VARS['cpass'];
        $categories = $this->selectall("forums/categories.cgi");
        foreach ($categories as $category) {
		$forums = explode(",", $category[2]);
		foreach ($forums as $forumid) {
			$forumcookie = $forumcookie."$forumid,".time().":";
                }
        }
	setcookie("disanre", $forumcookie, time()+5184000);
	setcookie('cuser', $cuser, time()+5184000);
	setcookie('cpass', $cpass, time()+5184000);
	$redirect = "<script language=\"javascript\">window.location=\"index.php\";</script>";
	print $redirect;
}

function displayforums () {
	global $HTTP_COOKIE_VARS;
	if ($this->validated == 1) {
		$iaitop = "[ <a href=\"index.php?a=admin&type=newcategory\">New Category</a> ] [ <a href=\"index.php?a=iai&type=reconstruct&forum=all\">Reconstruct All</a> ]";
	}
        ?>
        <table width="100%" cellpadding=2 cellspacing=0>
         <tr>
          <td></td>
          <td align="right"><span class="regular">[ <a href="index.php?a=main&type=mark">Mark All Posts Read</a> ] <?=$iaitop?></span></td>
         </tr>
        </table>
        <?
	$cats = 0;
        $categories = $this->selectall("forums/categories.cgi");
        foreach ($categories as $category) {
		?>        <table width="100%" cellpadding=2 cellspacing=1 class="bordertable"><?
		$i = 0;
		if ($this->validated == 1) {
		        $iaicat = "[ <a href=\"index.php?a=iai&type=movecategoryup&category=".$category[0] ."\" class=\"catlink\">Up</a> | <a href=\"index.php?a=iai&type=movecategorydown&category=".$category[ 0]."\" class=\"catlink\">Down</a> ] [ <a href=\"index.php?a=iai&type=newforum&category=".$category[0]."\" class=\"catlink\">New Forum</a> ]";
		}?>
         <tr>
          <td colspan="100%" class="category"><?=$category[1]?> <?=$iaicat?></td>
         </tr>
         <tr>
          <td class="ctable1" align="center" width="2%" rowspan="100%"><span style="display: none;">.</span></td>
          <td class="ctable1" align="center" width="4%"></td>
          <td class="ctable1" width="49%"><span class="small">Forum Name</span></td>
          <td class="ctable1" align="center" width="7%"><span class="small">Topics</span></td>
          <td class="ctable1" align="center" width="7%"><span class="small">Posts</span></td>
          <td class="ctable1" align="center" width="29%"><span class="small">Last Post</span></td>
          <td class="ctable1" align="center" width="2%" rowspan="100%"><span style="display: none;">.</span></td>
         </tr><?
		$forums = explode(",", $category[2]);
		foreach ($forums as $forumid) {
			if ($forumid != "") {
				$this->verifyintegrity($forumid);
				$i++;
			        if ($this->checkforumpermission(5, false, $forumid) == 1) {
					$forumimg = "forumold.gif";
					$forum = $this->select("forums/forums.cgi", $forumid);
					$this->topics = $this->topics + $forum[2];
					$this->posts = $this->posts + $forum[3];
				        $lasttopic = $this->select("forums/".$forum[0]."/list.cgi", $forum[4]);
					if ($this->validated == 1) {
						$iaiforum = "</td><td align=\"right\">[ <a href=\"index.php?a=iai&type=editforum&forum=".$forum[0]."\">Edit</a> | <a href=\"index.php?a=iai&type=reconstruct&forum=".$forum[0]."\">Reconstruct</a> | <a href=\"index.php?a=iai&type=deleteforum&forum=".$forum[0]."&category=".$category[0]."\">Delete</a> ] [ <a href=\"index.php?a=iai&type=moveforumup&forum=".$forum[0]."&category=".$category[0]."\">Up</a> | <a href=\"index.php?a=iai&type=moveforumdown&forum=".$forum[0]."&category=".$category[0]."\">Down</a> ] ";
					}
					$flist = $HTTP_COOKIE_VARS['disanre'];
					$flist = explode(":", $flist);
					foreach ($flist as $frecords) {
						$flist2 = explode(",", $frecords);
						if ($flist2[0] == $forumid) {
							$flastactive = $flist2[1];
						}
					}
					if ($flastactive < $lasttopic[6]) {
						$forumimg = "forumnew.gif";
					} else {
						$forumimg = "forumold.gif";
					}
					if ($forum[3] != 0) {
						$lastpost  = "Last post in <a href=\"index.php?a=topic&forum=".$forum[0]."&topic=".$lasttopic[ 0]."\">" . $lasttopic[1] . "</a> ";
						$lastpost .= "by <a href=\"index.php?a=member&type=viewprofile&user=".$lasttopic[5]." \">".$lasttopic[5]."</a><br>";
						$lastpost .= $this->bdate($this->timeformat, $lasttopic[6]);
					} else {
						$lastpost  = "There are no posts in this forum right now.";
						$forumimg = "forumold.gif";
					}
       	                         ?>
                        <tr>
                         <td class="ctable2" align="center" width="4%"><img src="images/<?=$forumimg?>"></td>
                         <td class="ctable2" <?=$this->rfx($a, ($forum[0]));?>><table width="100%" cellpadding="0" cellspacing="0" border="0" class="regular"><tr><td><a href="index.php?a=forum&forum=<?=$forum[0]?>"><?=stripslashes($forum[1])?></a> <?=$iaiforum?></td></tr></table><span class="small"><?=stripslashes($forum[8])?></span></td>
                         <td class="ctable1" align="center" width="7%"><?=$forum[2]?></td>
                         <td class="ctable1" align="center" width="7%"><?=$forum[3]?></td>
                         <td class="ctable1" width="29%"><span class="small"><?=$lastpost?><br><?=$iaipost?></span></td>
                        </tr>
				<?
				} else {
					$i--;
				}
			}
			if ($i <= 0) { ?>
                         <td class="ctable1" colspan="5"><center>There are currently no forums in this category.</center></td>
                        </tr>
<?			}
		}
		?></table><br><?
        }
?><?
}



function displaywhosonline () {
	$guests = 0; $members = 0; $total = 0; $list = "";
	$whosonline = $this->selectall($this->file['online']);
	foreach ($whosonline as $user) {
		if ($user[1] == 0) {
			$guests++;
		} else {
			if (strstr($user[3],"100")) {
				$guests++;
			} else {
				$members++;
				$fuser = $this->select($this->file['users'], $user[1]);
				$march = $this->select($this->file['groups'], $fuser[3]);
				$color = $march[19];
				if ($color == ":default:") {
					$props = "<font class='small'><u>";
				} else {
					$props = "<font color='".$color."'><u>"; 
				}
				$list = $list . "&raquo; <a href=\"index.php?a=member&type=viewprofile&user=".$fuser[1]."\">".$props.$fuser[1]."</u></font></a> ";
			}
		}
	}
	$total = $guests + $members;
	?>
	<table width="100%" cellpadding=2 cellspacing=1 class="bordertable">
	 <tr>
	  <td colspan=5 class="ctable1" align="center">Board Info</td>
	 </tr>
	 <tr>
	  <td colspan=5 class="category">Who's Online</td>
	 </tr>
	 <tr>
 	  <td class="ctable2" align="center" width="4%"><img src="images/whosonline.gif"></td>
	  <td colspan=4 class="ctable1" width="96%"><span class="small">Guests: <?=$guests?> Members: <?=$members?> Total: <?=$total?><br><?=$list?>&nbsp;</span></td>
	 </tr>
	<?
	$this->total = $members + $guests;
	$this->spacer();
}

function displaystats () {
  $users = $this->selectall($this->file['users']);
?>
 <tr>
  <td colspan=5 class="category">Stats</td>
 </tr>
 <tr>
<?
	clearstatcache();
	if (file_exists($this->file['counter'])) {
		$counter_file = $this->file['counter'];
		$counter = $this->selectall($this->file['counter']);
		$countr = $this->select($this->file['counter'], 1);
		$count = 1 + $countr[1];
	  } else {
		$counter_file = "counter.cgi";
		$counter_file_line = file($counter_file);
		$count = 1 + $counter_file_line[0];
		$line1 = array($counter_file_line[0]);
		$line2 = array(0, time());
		$this->insert($this->file['counter'], $line1);
		$this->insert($this->file['counter'], $line2);
		unlink("counter.cgi");
		$counter_file = $this->file['counter'];
		$counter = $this->selectall($this->file['counter']);
	}
	$this->delete($this->file['counter'], 1);
	$this->delete($this->file['counter'], 2);
	if ($this->total > $counter[1][1]) {
		$most = $this->total;
		$date = time();
 	 } else {
		$most = $counter[1][1];
		$date = $counter[1][2];
  	}
  	$line1 = array($count);
  	$line2 = array($most, $date);
  	$this->insert($this->file['counter'], $line1);
  	$this->insert($this->file['counter'], $line2);
	$counter = $this->selectall($this->file['counter']);
?>
  <td class="ctable2" align="center" width="4%"><img src="images/stats.gif"></td>
  <td colspan=4 class="ctable1" width="96%"><span class="small">
  A total of <b><?=$this->posts?> posts</b> in <b><?=$this->topics?> topics</b>.<br>
  A total of <b><?=count($users)-2;?> registered members</b>.<br>
  This page has been accessed <b><?=$counter[0][1]?></b> times.<br>
  The most users ever online was <b><?=$counter[1][1]?></b> on <b><?=$this->bdate($this->timeformat, $counter[1][2])?></b>.<br>
<?
	if ($users[count($users)-1][1] == "Guest") {
?>
  There are currently no registered members.</span></td>
<? } else { ?>
  Our most recent registered member is <a href="index.php?a=member&type=viewprofile&user=<?=$users[count($users)-1][1];?>"><?=$users[count($users)-1][1];?></a>.</span></td>
<? } ?>
 </tr>
<?
$this->spacer();
}

function displaylogin () {
if ($this->user[0] == 0) {
?>

 <tr>
  <td colspan=5 class="category">Login or <a href="index.php?a=member&type=register" class="catlink">Register</a></td>
 </tr>
 <tr>
  <td class="ctable2" align="center" width="4%"><img src="images/loginico.gif"></td>
  <td colspan=4 class="ctable1" width="96%">
   <table width="100%" cellpadding="0" cellspacing="0" border="0">
           <tr width="60%">
           <td><table width="100%">
             <tr>
              <td width="30%" align="right"><span class="regular"><form name="loginreg" action="index.php" method="post">
<input type="hidden" name="a" value="member">
<input type="hidden" name="type" value="login2">Username:</span>&nbsp;</td>
              <td width="30%" align="left"><input type="text" name="username" size=30></td>
             </tr>
             <tr>
              <td width="30%" align="right"><span class="regular">Password:</span>&nbsp;</td>
              <td width="30%" align="left"><input type="password" name="password" size=30 ></td>
             </tr>
            </table></td>
            <td width="40%" align="left"><?$this->button("Log In")?></td>
           </tr>
    </table>
    </td>
    </tr></form>
<?
$this->spacer();
}

}

function displayforumimgs () {
?>
 <tr>
  <td colspan=5 class="category">Forum Graphics</td>
 </tr>
 <tr>
  <td colspan=2 class="ctable1" align="center" width="50%">
   <table width="100%" cellpadding="0" cellspacing="0" border="0">
           <tr>
            <td width="50%" align="right"><span class="regular">New Posts:</span>&nbsp;</td>
            <td width="50%" align="left"><img src="images/forumnew.gif"></td>
           </tr>
   </table>
  </td>
  <td colspan=3 class="ctable1" align="center" width="50%">
   <table width="100%" cellpadding="0" cellspacing="0" border="0">
           <tr>
            <td width="50%" align="right"><span class="regular">No New Posts:</span>&nbsp;</td>
            <td width="50%" align="left"><img src="images/forumold.gif"></td>
           </tr>
   </table>
  </td>
 </tr>
</table>
 </tr>
</table>

<?
}

}

?>