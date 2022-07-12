<?

/*
* Copyright edge-programming.com
* No reproduction or redistribution of this script is allowed!
* Contact deltawolf@deltawolf.com or nitsuj24017@aol.com for informaion.
* Most flatfile functions done by lordanthron@yahoo.com
* The edgeTeam thanks Anthron for allowing us to continue his work.
* edgeBoard is the best!
*/

include "php 4-2+.php";

include "extention/modules/flatfile.php";

class fpboard EXTENDS functions {

var $timeformat, $boardname, $user, $usergroup, $cpass, $sessionid, $welcome, $executestart, $executetime;
var $maxpostsize, $postwrap, $topicsperpage, $postsperpage, $membersperpage, $posttime, $version;
var $topiclength, $nitsuser, $allowguestposting, $rollovercolor, $allowrollovereffect;

function fpboard () {
}

// thanks to joe@cfcl.com for this variable importing function
// import($var) will make $var available in current scope
// mod by nitsuj
function import () {
	global $HTTP_COOKIE_VARS;
	$args = func_get_args();
	foreach ($args as $var) {
		if(isset($_GET[$var])) {
			$GLOBALS[$var] = $_GET[$var];
		} elseif(isset($_POST[$var])) {
			$GLOBALS[$var] = $_POST[$var];
		}
	}
}

function declarefiles () {
	$ini = "data/install.ini";
	$this->file['access'] = "data/logs/access.php";
	$this->file['banned'] = "data/banned.php";
	$this->file['counter'] = "data/counter.php";
	$this->file['footer'] = "html/footer.php";
	$this->file['groups'] = "data/groups.php";
	$this->file['header'] = "html/header.php";
	$this->file['ip'] = "data/logs/ip.php";
	$this->file['online'] = "data/online.php";
	$this->file['pm'] = "data/pm.php";
	$this->file['session'] = "data/logs/session.php";
	$this->file['settings'] = "data/settings.php";
	$this->fire['spm'] = "data/system_messages.php";
	$this->file['status'] = "data/status.php";
	$this->file['users'] = "data/users.php";
	$this->file['style'] = "data/style.php";
	$this->file['titles'] = "data/titles.php";
	$this->file['version'] = "data/boardver.php";
	if ($ini[0] == "cgi") {
		foreach ($this->file as $file) {
			$file = str_replace(".php", ".cgi", $file);
		}
	}
	$this->directory['data/'] = "data/";
	$this->directory['copy/'] = "copy/";
	$this->directory['backup/'] = "backup/";
	$this->directory['html/'] = "html/";
	$this->directory['backup/data/'] = "backup/data/";
	$this->directory['backup/forums/'] = "backup/forums/";
	$this->directory['data/logs/'] = "data/logs/";
}

function declareoptions () {
	$this->options1 = $this->select($this->file['settings'], 1);
	$this->options2 = $this->select($this->file['settings'], 2);
	$this->options3 = $this->select($this->file['settings'], 3);
	$this->options4 = $this->select($this->file['settings'], 4);
	$this->options5 = $this->select($this->file['settings'], 5);
	include($this->file['version']);
	$this->version = $version;
	$this->aheight = $this->options1[3];
	$this->awidth = $this->options1[2];
	$this->topiclength = $this->options1[4];
	$this->timeformat = $this->options1[5];
	$this->boardname = $this->options1[1];
	$this->maxpostsize = $this->options1[6];
	$this->postwrap = $this->options1[7];
	$this->topicsperpage = $this->options1[8];
	$this->allowtablerollover = $this->options2[8];
	$this->postsperpage = $this->options2[1];
	$this->membersperpage = $this->options2[2];
	$this->posttime = $this->options2[3];
	$this->bt = "<input type=\"submit\" value=\"   ";
	$this->be = "   \" style=\"cursor: hand;\">";
	$this->allowguestposting = $this->options2[6];
	$this->toplinks['member'] = $this->options4[1];
	$this->polllength = $this->options3[5];
	$this->toplinks['guest'] = $this->options5[1];
	$status = file($this->file['status']);
	$this->currentstatus = $status[0];
	$banned = $this->selectall($this->file['banned']);
	foreach ($banned as $canned) {
		if (strstr($canned[2],$GLOBALS['REMOTE_ADDR'])) {
			$this->banned = 1;
		}
	}
}

function start () {
	global $nitsuser, $rollovercolor, $allowguestposting, $cuser, $cpass;
	$this->executestart = $this->getmicrotime();
	$this->setdb();
	$fc = "Forums Closed to the General Public";
	if ($cuser == "") {
		$this->user = array(0, "Guest",'',7);
		$this->in = false;
		$nitsuser = "Guest";
	} else {
		$user = $this->selectwhere($this->file['users'], 1, $cuser);
		$this->user = $user[0];
		$this->cpass = $cpass;
		$this->cuser = $cuser;
		if ($cpass != $this->user[2]) {
			$this->displaymessage("Error", "You are not logged in as the user. You are trying to hijack.");
			$this->footer();
			die();
		}
		$messages = $this->selectwhere($this->file['pm'], 2, $this->user[0]);
		$this->in = true;
		$messages = $this->where($messages, 6, 0);
		if (count($messages) != 1) { $s = "s"; }
	}
	$this->usergroup = $this->select($this->file['groups'], $this->user[3]);
	$this->validated = $this->validate(14);
	$this->whosonline();
	if ($this->banned == 1) {
		$this->header();
		$this->displaymessage("Error","We're sorry, but you have been banned form the board.");
		$this->footer();
	} else {
		if (strstr($this->currentstatus,"000")) {
			$this->execute();
		} elseif (strstr($this->currentstatus,"400")) {
			if ($this->validated == 1) {
				$this->displaymessage($fc,"Sorry, the forums have been closed, but you are an administrator, you can access the board.");
				$this->execute();
			} else {
				$this->header();
				$this->displaymessage($fc,"Sorry, the forums have been closed.");
				$this->footer();
				die();
			}
		} elseif (strstr($this->currentstatus,"401")) {
			if ($this->validated == 1) {
				$this->displaymessage($fc,"Sorry, the forums have been temporarily closed, but you are an administrator, you can access the board.");
				$this->execute();
			} else {
				$this->header();
				$this->displaymessage($fc,"Sorry, the forums have been temporarily closed.  Please retrun at a later time.");
				$this->footer();
				die();
			}
		} else {
			$this->execute();
		}
	}
}

function bdate ($timeformat, $time) {
	if (empty($this->options3[4])) { $this->options3[4] = 0; }
	return date($this->timeformat, ($time + $this->options3[4]));
}

function button ($text) {
	$this->import($this->bt, $this->be);
?><?=$this->bt?><?=$text?>   " title="   Click here to <?=$text?><?=$this->be?>
<?
}

/*******************************************
* the following was taken from class edge. *
*******************************************/
// Some simple tag functions.
// string $this->tags(string);
function tags ($text) {
	if (substr_count($text, "[quote]") == substr_count($text, "[/quote]")) {
		$text = str_replace("[quote]", "<table width=\"98%\" cellpadding=\"4\" cellspacing=\"0\" border=\"0\" align=\"center\" class=\"quote\"><tr><td>", $text);
		$text = str_replace("[/quote]", "</tr></td></table>", $text);
	}
	$text = preg_replace("/\[url\](http:\/\/|)(.*?)\[\/url\]/i", "<a href=\"http://\\2\" target=\"_blank\">\\1\\2</a>", $text);
	$text = preg_replace("/\[url=(http:\/\/|)(.*?)\](.*?)\[\/url\]/i", "<a href=\"http://\\2\" target=\"_blank\">\\3</a>", $text);
	$text = preg_replace("/\[img\](.*?)\[\/img\]/i", "<img alt=\"Posted Image from User\" src=\"\\1\" border=\"0\">", $text);
	$text = preg_replace("/\[url=(http:\/\/|)(.*?)\](.*?)\[\/url\]/i", "<a href=\"http://\\2\" target=\"_blank\">\\3</a>", $text);
	$text = preg_replace("/\[email\](.*?)\[\/email\]/i", "<a href=\"mailto:\\1\">\\1</a>", $text);
	$text = preg_replace("/\[email=(.*?)\](.*?)\[\/email\]/i", "<a href=\"mailto:\\1\">\\2</a>", $text);
	$text = preg_replace("/\[font (.*?)\](.*?)\[\/font\]/i", "<font \\1>\\2</font>", $text);
	$text = preg_replace("/\[b\](.*?)\[\/b\]/i", "<b>\\1</b>", $text);
	$text = preg_replace("/\[i\](.*?)\[\/i\]/i", "<i>\\1</i>", $text);
	$text = preg_replace("/\[u\](.*?)\[\/u\]/i", "<u>\\1</u>", $text);
	$text = preg_replace("/\[small\](.*?)\[\/small\]/i", "<span class=\"small\">\\1</span>", $text);
	$text = preg_replace("/\[center\](.*?)\[\/center\]/i", "<center>\\1</center>", $text);
	$text = str_replace(":login:", "<a href=\"index.php?a=member&type=login\"><img border=\"0\" alt=\"Login\" src=\"images/login.gif\"></a>", $text);
	$text = str_replace(":memberlist:", "<a href=\"index.php?a=member&type=list\"><img border=\"0\" alt=\"Member List\" src=\"images/memberlist.gif\"></a>", $text);
	$text = str_replace(":profile:", "<a href=\"index.php?a=member&type=profile\"><img border=\"0\" alt=\"Edit Profile\" src=\"images/profile.gif\"></a>", $text);
	$text = str_replace(":register:", "<a href=\"index.php?a=member&type=register\"><img border=\"0\" alt=\"Register\" src=\"images/register.gif\"></a>", $text);
	$text = str_replace(":logout:", "<a href=\"index.php?a=member&type=logout\"><img border=\"0\" alt=\"Logout\" src=\"images/logout.gif\"></a>", $text);
	$text = str_replace(":ebhome:", "<a href=".$this->options3[2]."><img border=\"0\" alt=\"Home\" src=\"images/home.gif\"></a>", $text);
	$text = str_replace(":search:", "<a href=\"index.php?a=search\"><img border=\"0\" alt=\"Search\" src=\"images/search.gif\"></a>", $text);
	$text = str_replace(":boardname:", $this->boardname, $text);
	$text = str_replace("<s", "<;s", $text);
	$text = str_replace("<S", "<;S", $text);
//  Emoticon functions
// "hey, who made these emoticons???" - Anthron
	$text = str_replace(":)", "<img alt='Smile' src='./images/emoticons/smile.gif'>", $text);
	$text = str_replace(":(", "<img alt='Sad' src='./images/emoticons/sad.gif'>", $text);
	$text = str_replace(":D", "<img alt='Big Grin' src='./images/emoticons/biggrin.gif'>", $text);
	$text = str_replace(":p", "<img alt='Tongue' src='./images/emoticons/tongue.gif'>", $text);
	$text = str_replace(":lol:", "<img alt='LOL' src='./images/emoticons/chuckle.gif'>", $text);
	$text = str_replace(":rolleyes:", "<img alt='' src='./images/emoticons/rolleyes.gif'>", $text);
	$text = str_replace(":unsure:", "<img alt='Unsure' src='./images/emoticons/unsure.gif'>", $text);
	$text = str_replace(";)", "<img alt='Wink' src='./images/emoticons/wink.gif'>", $text);
	$text = str_replace(":-)", "<img alt='Smile' src='./images/emoticons/smile.gif'>", $text);
	$text = str_replace(":-(", "<img alt='Sad' src='./images/emoticons/sad.gif'>", $text);
	$text = str_replace(":-D", "<img alt='Big Grin' src='./images/emoticons/biggrin.gif'>", $text);
	$text = str_replace(":-p", "<img alt='Tongue' src='./images/emoticons/tongue.gif'>", $text);
	$text = str_replace(":-/ ", "<img alt='Unsure' src='./images/emoticons/unsure.gif'> ", $text);
	$text = str_replace(";-)", "<img alt='Wink' src='./images/emoticons/wink.gif'>", $text);
	$text = str_replace("pt:", "pt;", $text);
	$text = str_replace("pT:", "pT;", $text);
	$text = str_replace("Pt:", "Pt;", $text);
	$text = str_replace("PT:", "PT;", $text);
	$text = preg_replace("/\ http:\/\/(.*?)\/ /i", " <a href='http://\\1' target=\"_blank\">http://\\1/</a> ", $text);
	$text = preg_replace("/\ http:\/\/(.*?)<br>/i", " <a href='http://\\1' target=\"_blank\">http://\\1</a><br>", $text);
	$text = preg_replace("/\ http:\/\/(.*?) /i", " <a href='http://\\1' target=\"_blank\">http://\\1</a> ", $text);
	$text = preg_replace("/\ www.(.*?)\/ /i", " <a href='http://www.\\1' target=\"_blank\">www.\\1/</a> ", $text);
	$text = preg_replace("/\ www.(.*?)<br>/i", " <a href='http://www.\\1' target=\"_blank\">www.\\1</a><br>", $text);
	$text = preg_replace("/\ www.(.*?) /i", " <a href='http://www.\\1' target=\"_blank\">www.\\1</a> ", $text);
	return $text;
}

//sends a system private message to a specific user
function systempm ($user, $messageid) {
	$pms = $this->selectall($this->file['pm']); //PMS...lol.  its PM's, you perv!
	$dbm1 = array("Welcome", "Welcome to :boardname:!  You are now a full-fledged member of the board!  Feel free to post as much as you like and if you have any questions or comments, don't hesitate to post them!  Adios, for now!");
	$dbm2 = array("Your Title Advancement", "You have become advanced enough in this board to specify your own unique user title!  Go <a href='index.php?a=member&type=profile'>here</a> to set it!");
	$dbm3 = array("User Group Change", "Your user group has been changed!  Look at your profile to see what group you are in now!");
	if ($messageid == 1) {
		$dbmessage = $dbm1;
	} elseif ($messageid == 2) {
		$dbmessage = $dbm2;
	} elseif ($messageid == 3) {
		$dbmessage = $dbm3;
	}
	$subject = $dbmessage[0];
	$message = $dbmessage[1];
	$continue = true;
	foreach ($pms as $pm) {
		if (($pm[2] == $user) && ($pm[1] == "System") && ($pm[4] == $subject)) {
			$continue = false;
		}
	}
	if ($continue == true) {
		$newmessage = array("System", $user, time(), $subject, $message, 0);
		$this->insert($this->file['pm'], $newmessage);
	}
}

// returns a series of links which are included in header.php and shown at the top of each page
function toplinks () {
	$this->import($loginlink, $membrlink, $rgstrlink, $profllink);
	if ($this->user[1] == "Guest") {
		 $top = $this->toplinks['guest'];
		stripslashes($top);
		$top = $this->tags($top);
		return stripslashes($top);
	} else {
		$top = $this->toplinks['member'];
		stripslashes($top);
		if ($this->validated == 1) {
			$top = str_replace("\n", "", $top);
			$top = $top."<a href=\"index.php?a=admin\"><img border=\"0\" src=\"images/admincp.gif\"></a>";
		}
		$top = $this->tags($top);
		return stripslashes($top);
	}
}

// Function modified by RM to allow Guest Posting
// returns 1 if user checks out ok, 0 on error, 2 if user is a Guest
function validate ($permission) {
	$equiv = $this->user[0];
	if ($this->user[0] == 0) {
	  if (strstr($this->allowguestposting, "true"))
	    $prevalid = true;
	  else
	    return 2;
	} elseif ($this->user[10] == "disabled") {
	    return 0;
	} else
		  $prevalid = ($this->cpass == $this->user[2]);
		return (int)(($prevalid)&&($this->usergroup[$permission] == 1));
}

//returns 1 if user is valid and is not a hijacker
function verify () {
	if ($this->cpass == $this->user[2] && $this->cuser == $this->user[1]) {
		return 1;
	}
}

// sets a unique sessionid to
function session () {
	$ipn = str_replace(".", "", ($GLOBALS['REMOTE_ADDR']));
	$x = round((time())*(rand(1,1000)), 20);
	$x = str_replace("+", "7", $x);
	$x = str_replace(" ", "7", $x);
	$x = str_replace("E", "7", $x);
	$x = str_replace(".", "0", $x);
	return $x.($ipn*2);
}

function set ($n, $g) {
	$ng = $this->select($this->file['groups'], $g);
	$v = $ng[1];
	$s = $ng[0];
	$d = $ng[18];
	$f = $ng[17];
	$c = $ng[18];
	if ($d == 1) {
		$d = "Admin";
	} elseif ($d == 2) {
		$d = "Registered";
	} elseif ($d == 3) {
		$d = "Team";
	} elseif ($d == 4) {
		$d = "Moderator";
	} elseif ($d == 0) {
		$d = "";
	}
	$list = $this->selectall($this->file['titles']);
	$pt = "Member";
	$pt1 = "Member";
	foreach ($list as $item) {
		if ($n >= $item[1]) {
			$t = $item[2];
			$p = $item[3];
		}
		if ($t == ":Previous:") {
			if ($pt1 == ":Previous:") {
				$t = $pt;
			} else {
				$t = $pt1;
			}
		}
		$pt = $pt1;
		$pt1 = $item[2];
	}
	$list = $this->selectall($this->file['pips']);
	foreach ($list as $item) {

	}
	if ($f == 1) {
 		$l = "Power ".$d;
	} elseif ($f == 0) {
		$l = "Level 1 ".$d;
	} elseif (($f == 0) && ($c == 0)) {
		$l = "None";
	}
	return array($t,$p,$c,$d,$l,$v,$f);
}

function pip ($n, $g, $u) {
	$q = $this->set($n, $g);
	if ($q[6] == 1) {
		$bigpip = "<img alt='$q[4]' src='images/pips/big".$q[2].".gif' border='0'><br>";
	}
	$i = 1;
	$newpip = "<img src='images/pips/".$q[2].".gif' border='0'>";
	while ($i <= $q[1]) {
		$pip = $pip.$newpip;
		if (($i == 6) || ($i == 12) || ($i == 18) || ($i == 24)) {
			$pip = $pip."<br>";
			$y = 1;
		} else {
			$y = 0;
		}
		$i++;
	}
	if (($pip == "")) {
		$pip = "<b><span class='small'> No Pips</span></b>";
	}
	if ($y == 0) { $pip = $pip."<br>"; }
	if ($q[0] == ":UserSet:") {
		if ($u == $this->user[0]) {
			$user = array();
			$user[15] = $this->user[15];
		} else {
			$user = $this->select($this->file['users'], $u);
		}
		$this->userset = true;
		$this->systempm($u, 2);
		if (empty($user[15])) {
			$t = "Cornerstone Member";
		} else {
			$t = $user[15];
		}
	} else {
		$t = $q[0];
	}
	$this->title[$u] = $t;
	$this->pip = $pip;
	$this->bigpip = $bigpip;
	$this->pipgroup = $q[4];
	$this->cusergroup = $q[5];
}

function doomsday ($part) {
	$permission = $this->validate($part);
	if ($permission != 1) {
		$this->displaymessage("Error", "You do not have the correct permissions to perform this action.");
		$this->footer();
		die();
	}
}

function usercp () {
	global $cuser, $a, $HTTP_COOKIE_VARS, $type;
	if ($HTTP_COOKIE_VARS['invis'] == 1) {
		$invtext = "| <a href='index.php?a=main&type=noninvis'>Make Visible</a> ";
	}
	if ($a == "main" && $type == "noninvis") {
		$invtext = "| Now Visible <script> window.location='index.php'; </script> ";
	}
	if ($HTTP_COOKIE_VARS['invis'] == "" || $HTTP_COOKIE_VARS['invis'] == "0") {
		$invtext = "| <a href='index.php?a=main&type=invis'>Make Invisible</a> ";
	}
	if ($a == "main" && $type == "invis") {
		$invtext = "| Now Invisible <script> window.location='index.php'; </script> ";
	}
	if ($this->user[1] == "Guest") {
		$this->user = $this->select($this->file['users'], 0);
		echo "<center>Welcome, Guest, please <a href=\"index.php?a=member&type=register\">register</a> or <a href=\"index.php?a=member&type=login\">login</a>.</center>";
	} else {
		if ($a == admin) {
			?><center>Please exit the ACP to have access to your user panel.</center><?
		} else {
			$this->pip($this->user[11], $this->user[3], $this->user[0]);
			$this->pip = str_replace("<span class='small'>", "", $this->pip);
			$this->pip = str_replace("</span>", "", $this->pip);
			$link = "<a href=\"index.php?a=member&type=profile\">";
			echo "<center>".$this->title[$this->user[0]].": <a href=\"index.php?a=member&type=viewprofile&user=".$this->user[1]."\">".$this->user[1]."</a><br>[ $link Edit Profile</a> ".$invtext."]</center>\n";
			$messages = $this->selectwhere($this->file['pm'], 2, $this->user[0]);
			$messages = $this->where($messages, 6, 0);
			if (count($messages) != 1) { $s = "s"; }
?><table cellpadding=0 cellspacing=0 border=0 class="usercp"><tr><td><b>Your Pips:&nbsp;</b></td><td><?=$this->pip?></td></tr></table><?
			echo "\nYour Level:  ".$this->pipgroup."<br>\n";
			echo "Your Group:  ".$this->cusergroup."<br>\n<center>";
			if (count($messages) > 0) {
				echo "<span class=\"small\">There's ".count($messages)." new message$s in your <a href='index.php?a=messenger'>inbox</a>!</span><br>\n";
			} else {
				echo "There are no new messages for you.<br>\n";
			}
			echo "[ <a href='index.php?a=messenger'>Inbox</a> | <a href='index.php?a=messenger&type=outbox'>Outbox</a> | <a href='index.php?a=messenger&type=send'>Send PM</a> ]</center>\n";
		}
	}
}

function whosonline () {
	global $gtime, $session, $HTTP_COOKIE_VARS;
	$whosonline = $this->selectall($this->file['online']);
	foreach ($whosonline as $member) {
		$curtime = (time()-900);
		if ($member[2] < $curtime) {
			$this->delete($this->file['online'], $member[0]);
		}
		$user = $this->select($this->file['users'], $member[1]);
		if ($this->user[10] == "disabled") {
			setcookie("cuser", "", time());
			setcookie("cpass", "", time());
			setcookie("sessionid", "", time());
		}
		if ($this->user[0] != 0 && $this->user[10] != "disabled") {
			$this->user[10] = time();
			$this->update($this->file['users'], $this->user[0], $this->user);
		}
	}
	if ($this->user[0] != 0) {
		$where = $this->selectwhere($this->file['online'], 1, $this->user[0]);
		if (count($where[0]) == 0) {
			if (!isset($gtime)) { $gtime = time(); }
			$whereip = $this->selectwhere($this->file['ip'], 2, $GLOBALS['REMOTE_ADDR']);
			if (count($whereip[0]) == 0) {
				$ip = array($this->user[0], $GLOBALS['REMOTE_ADDR'], $gtime);
				$this->insert($this->file['ip'], $ip);
			}
			$online = array($this->user[0], time(), $HTTP_COOKIE_VARS['invis']."00");
			$this->insert($this->file['online'], $online);
		} else {
			$whereip = $this->selectwhere($this->file['ip'], 2, $GLOBALS['REMOTE_ADDR']);
			if (count($whereip[0]) == 0) {
				$ip = array($this->user[0], $GLOBALS['REMOTE_ADDR'], $gtime);
				$this->insert($this->file['ip'], $ip);
			}
			$online = array($where[0][0], $where[0][1], time(), $HTTP_COOKIE_VARS['invis']."00");
			$this->update($this->file['online'], $where[0][0], $online);
		}
	} else {
		$session = $this->session();
		$guest = $this->selectwhere($this->file['online'], 3, $GLOBALS['REMOTE_ADDR']);
		$when = $this->selectwhere($this->file['ip'], 2, $GLOBALS['REMOTE_ADDR']);
		if (!isset($HTTP_COOKIE_VARS['sessionid'])) {
			setcookie("sessionid", $session, time()+5184000);
		} else {
			setcookie("sessionid", $HTTP_COOKIE_VARS['sessionid'], time()+5184000);
		}
		if (count($when[0]) == 0) {
			$ip = array("guest", $GLOBALS['REMOTE_ADDR'], $session);
			$this->insert($this->file['ip'], $ip);
		}
		if ($guest[0][3] != $GLOBALS['REMOTE_ADDR']) {
			$guest = array(0, time(), $GLOBALS['REMOTE_ADDR']);
			$this->insert($this->file['online'], $guest);
		} else {
			$guest[0][2] = time();
			$this->update($this->file['online'], $guest[0][0], $guest[0]);
		}
	}
}

function disableuser2 ($userid) {
	$user = $this->select($this->file['users'], $userid);
	$user[10] = disabled;
	$this->update($this->file['users'], $userid, $user);
}

function head () {
	$nitsuj = str_replace("\#", "", $this->options2[7]);
	$nitsuj = $this->inputize($nitsuj, 6);
?>
<style type="text/css">
<? include $this->file['style']; ?>
</style>
<script language="javascript">
<!--
var thecolor;

function emoticon(smilie) {
	document.Post.message.value = document.Post.message.value + smilie;
	document.Post.message.focus();
}

function storeCaret (textEl) {
	if (textEl.createTextRange)
		textEl.caretPos = document.selection.createRange().duplicate();
}

function insertAtCaret (textEl, text) {
	if (textEl.createTextRange && textEl.caretPos) {
		var caretPos = textEl.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
	} else
		textEl.value  = text;
}

function tableOver(color) {
	thecolor=color.style.backgroundColor;
	color.style.backgroundColor='<?=$nitsuj?>';
	color.style.cursor='hand';
}

function tableOut(color) {
	color.style.backgroundColor=thecolor;
	color.style.cursor='hand';
}
//-->
</script>
<?
}

function rfx ($page) {
	$args = func_get_args();
	if (strstr($this->allowtablerollover,"true")) {
		echo ('onMouseOver="tableOver(this);" onClick="document.location.href=\'index.php?a=forum&forum='.$args[1].'\'" onMouseOut="tableOut(this);" style="cursor: hand;"');
	}
}

function trfx ($page) {
	$args = func_get_args();
	if (strstr($this->allowtablerollover,"true")) {
		echo ('onMouseOver="tableOver(this);" onClick="document.location.href=\'index.php?a=member&type=viewprofile&userid='.$args[1].'\'" onMouseOut="tableOut(this);" style="cursor: hand;"');
	}
}

function frfx ($page) {
	$args = func_get_args();
	if (strstr($this->allowtablerollover,"true")) {
		echo ('onMouseOver="tableOver(this);" onClick="document.location.href=\'index.php?a=topic&forum='.$args[1].'&topic='.$args[2].'\'" onMouseOut="tableOut(this);" style="cursor: hand;"');
	}
}

// make sure $this->user can view $this->forum
// arguments are permissionid (5/6/7), display message (true/false), and forumid
function checkforumpermission ($permissionid, $displaymessage, $forumid) {
	$forum = $this->select("forums/forums.cgi", $forumid);
	$access = 1;
	if ($forum[$permissionid] != "") {
		$groups = explode(",", $forum[$permissionid]);
		$access = 0;
		foreach ($groups as $group) {
			if ($this->user[3] == $group) {
				$access = 1;
			}
		}
		if (($access == 0) && ($displaymessage == true))  {
			$this->displaymessage("Permissions Error.", "You do not have the nessecary permissions to perform that action in this forum.");
			$this->footer();
			die();
		}
	}
	return $access;
}

// used to display execution time
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

// arguments are message title, message text, [redirect]
function displaymessage () {
	$args = func_get_args();
	if (isset($args[2])) {
		$redirect = "<script language=\"javascript\">window.location=\"".$args[2]."\";</script>";
	} else {
		$redirect = "";
	}
	$argss1 = preg_replace("/\<@=$this->button\((.*?)\)@>/i", $this->bt.'\\1'.$this->be, $args[1]);
	?>
	<table width='65%' align=center cellpadding=2 cellspacing=1 class="bordertable">
	 <tr>
	  <td class="category"><?=$args[0]?></td>
	 </tr>
	 <tr>
	  <td class="ctable1"><?=$argss1?></td>
	 </tr>
	</table>
	<?
	print $redirect;
}

function addtopostcounter () {
	$user = $this->select($this->file['users'], $this->user[0]);
	$user[11]++;
	$this->update($this->file['users'], $this->user[0], $user);
}

function encrypt ($string) {
	return md5($string);
}

function header () {
	include $this->file['header'];
	$this->navbar();
}

function footer () {
	ini_set("display_errors", "0");
	if (file_exists("/proc/loadavg")) { 
		$serverload = fopen("/proc/loadavg", "r");
		$contents = fread($serverload,6);
		fclose($serverload);
		$load = explode(" ",$contents);
		$this->serverload = chop($load[0]);
	} else {
		$this->serverload = "Unavailable";
	}
	if (empty($this->serverload)) {
		$this->serverload = "Unavailable";
	}
	ini_restore("display_errors");
	$executeend = $this->getmicrotime();
	$executetime = $executeend - $this->executestart;
	$this->executetime = round($executetime, 4);
	include $this->file['footer'];
}

// returns an array starting from $start with no more than $max indexes or whatever.
// useful for pages.
function limit($array, $start, $max) {
	$lines = array();
	for ($i=$start; $i <= $start+$max; $i++) {
		if ($i == count($array)) {break;}
		array_push($lines, $array[$i]);
	}
	return $lines;
}

function deinputize ($text) {
	$text = str_replace("<br>", "\n", $text);
	return $text;
}

// arguments are
// $text to inputize, maximum length of text, maxsize of any one word
// $text is only required variable
function inputize () {
	$args = func_get_args();
	$text = $args[0];
	if ((isset($args[1])) && (strlen($text) > $args[1])) {
		$text = substr($text, 0, $args[1]);
	}
	if (isset($args[2])) {
	// the following loop was found at php.net
	// http://www.php.net/manual/en/ref.strings.php
	// created by heiko@individual-web.com
		$l = 0;
		$temp = "";
		for ($i = 0; $i < strlen($text); $i++) {
			$char = substr($text,$i,1);
			if ($char != " ") { $l++; }
			else { $l = 0; }
			if ($l == $args[2]) { $l = 0; $temp .= " "; }
			$temp .= $char;
		}
		$text = $temp;
	}
	$text = stripslashes($text);
	if (strstr($this->options3[1],"false")) {
		$text = str_replace(">", "&gt;", $text);
		$text = str_replace("<", "&lt;", $text);
	}
	$text = str_replace("\r\n", "<br>", $text);
	$text = str_replace("\n", "<br>", $text);
	$text = str_replace("|", "&#124;", $text);
	return $text;
}

//returns 0 on reconstruct
//returns 1 on successful verification
function verifyintegrity ($forumid) {
	$forum = $this->select("forums/forums.cgi", $forumid);
	$topic = $this->selectall("forums/".$forumid."/list.cgi");
	$last = count($topic) - 1;
	if (date("Y", $topic[$last][6]) == "1969") {
		if ($forum[3] > 0) {
			$this->reconstruct($forumid);
			return 0;
		} elseif ($forum[3] == 0) {
			return 1;
		}
	}
}

function navbar () {
	global $a, $type;
	$navbar = "&raquo; <a href=\"index.php\">".$this->boardname."</a>&nbsp;";
	if ($this->forum != "") {
		$forum = $this->select("forums/forums.cgi", $this->forum);
		$navbar = $navbar . "&raquo; <a href=\"index.php?a=forum&forum=".$this->forum."\">".$forum[1]."</a> ";
	}
	if ($this->topic != "") {
		if (substr($this->topic, 0, 1) != 0) {
			$topic = $this->select("forums/".$this->forum."/list.cgi", $this->topic);
		} else {
			$topic = $this->select("forums/".$this->forum."/pin.cgi", $this->topic);
		}
		$navbar = $navbar . "&raquo; <a href=\"index.php?a=topic&forum=".$this->forum."&topic=".$this->topic."\">".$topic[1]."</a> ";
	}
	if (($a == "admin")) {
		$navbar = $navbar . "&raquo; Administrator's Control Panel ";
	}
	if (($type == "register") || ($type == "register2")){
		$navbar = $navbar . "&raquo; Register ";
	}
	if (($type == "login") || ($type == "login2")){
		$navbar = $navbar . "&raquo; Login ";
	}
	if (($type == "general") || ($type == "general2")){
	       $navbar = $navbar . "&raquo; Editing Options ";
	}
	if (($type == "group") || ($type == "group2")){
		$navbar = $navbar . "&raquo; Editing Options ";
	}
	if (($type == "user") || ($type == "user2")){
		$navbar = $navbar . "&raquo; Editing Options ";
	}
	if ($type == "logout") {
		$navbar = $navbar . "&raquo; Logout ";
	}
	if (($a == "post") && ($this->topic == "")) {
		$navbar = $navbar . "&raquo; New Topic ";
	}
	if (($a == "post") && ($this->topic != "")) {
		$navbar = $navbar . "&raquo; Post Reply ";
	}
	if ($type == "edit") {
		$navbar = $navbar . "&raquo; Editing ";
	}
	if (($type == "postdelete") || ($type == "postdelete2")) {
		$navbar = $navbar . "&raquo; Deleting Post ";
	}
	if (($type == "topicdelete") || ($type == "topicdelete2")) {
		$navbar = $navbar . "&raquo; Deleting Topic ";
	}
	if (($type == "list")) {
		$navbar = $navbar . "&raquo; Memberlist ";
	}
	if (($type == "profile")) {
		$navbar = $navbar . "&raquo; Profile ";
	}
	?>
	<span class="small">Now is <?=$this->bdate($this->timeformat, time())?>.</span>
	<table width="100%" cellpadding=0 cellspacing=1 class="bordertable">
	 <tr>
	  <td>
	   <table width="100%" border=0 cellpadding=2 cellspacing=0>
	    <tr>
	     <td width="100%" class="ctable2"><?=$navbar?></td>
	    </tr>
	   </table>
	  </td>
	 </tr>
	</table>
	<br>
	<?
}

function reconstruct ($forumid) {
/*
list.cgi structure:
0 |1      |2      |3           |4              |5          |6             |7          |8                |9    |10
id|subject|replies|first poster|first timestamp|last poster|last timestamp|closed(0|1)|posticon filename|views|pinned(0|1)
topic structure
0 |1      |2      |3   |4     |5        |6          |7                |8    |9          |10                 |11
id|subject|message|time|poster|sessionid|closed(0|1)|posticon filename|views|pinned(0|1)|attachment filename|attachment description
*/
	$topics = array();
	$i = 1;
	$newlist = array();
	$forumdir = "forums/".$forumid."/";
	if ($handle = opendir($forumdir)) {
		while (false !== ($file = readdir($handle))) {
			if (strstr($file,".cgi")) {
				chmod($forumdir.$file, 0777);
				$file = str_replace(".cgi", "", $file);
			}
			$period = explode(".", $file);
			if (count($period) > 0) { $x == 1; } else { $x = 0; }
			if ($file != "list" && $file != ".." && $file != "." && $x == 0) {
				if ($i != $file) {
					rename($forumdir.$file.".cgi", $i.".cgi");
				}
				$newentry = array();
				$topic = $this->selectall($forumdir.$file.".cgi");
				$firstposter = $this->select($this->file['users'], $topic[0][4]);
				$lastposter = $this->select($this->file['users'], $topic[(count($topic) - 1)][4]);
				$closed = $topic[(count($topic) - 1)][6];
				$posticon = $topic[(count($topic) - 1)][7];
				$views = $topic[(count($topic) - 1)][8];
				$pinned = $topic[(count($topic) - 1)][9];
				if ($closed == "")	{ $closed = 0; }
				if ($posticon == "")	{ $posticon = null; }
				if ($views == "")	{ $views = count($topic) - 1;}
				if ($pinned == "")	{ $pinned = 0; }
				array_push($newentry, $topic[0][1]);//1
				array_push($newentry, (count($topic) - 1));//2
				array_push($newentry, $firstposter[1]);//3
				array_push($newentry, $topic[0][3]);//4
				array_push($newentry, $lastposter[1]);//5
				array_push($newentry, $topic[(count($topic) - 1)][3]);//6
				array_push($newentry, $closed);//7
				array_push($newentry, $posticon);//8
				array_push($newentry, $views);//9
				array_push($newentry, $pinned);//10
				array_push($topics, $newentry);
				$i++;
			}
		}
		closedir($handle); 
	}
	if (file_exists($forumdir."list.cgi")) { unlink($forumdir."list.cgi"); }
	foreach ($topics as $topic) {
		$this->insert($forumdir."list.cgi", $topic);
	}
}

}
?>