<?php

include "php 4-2+.php";

class module extends fpboard {

function execute () {
        global $type, $d;
	if (isset($d)) {
		chmod("install.php", 0777);
		chmod("upgrade.php", 0777);
		unlink("install.php");
		unlink("upgrade.php");
	}
        if ($type == "login2") {
                $this->login2();
        } elseif ($type == "logout") {
                $this->logout();
        } elseif ($type == "changepass2") {
                $this->changepass2();
        } else {
                $this->header();
                if ($type == "login") {
                        $this->login();
                } elseif ($type == "register") {
                        $this->register();
                } elseif ($type == "register2") {
                        $this->register2();
                } elseif ($type == "profile") {
                        $this->profile();
                } elseif ($type == "profile2") {
                        $this->profile2();
                } elseif ($type == "viewprofile") {
                        $this->viewprofile();
                } elseif ($type == "list") {
                        $this->memberlist();
                } elseif ($type == "changepass1") {
                        $this->changepass1();
                }
                $this->footer();
        }
}

function changepass2 () {
        global $oldpassword, $newpassword, $newpassword2;
        if ($this->user[0] == 0) {
                $message = "You are a guest and have no password to change.";
        } elseif ($this->user[2] != $this->encrypt($oldpassword)) {
                $message = "You entered an incorrect old password so the board could not change your password.";
        } elseif ($newpassword != $newpassword2) {
                $message = "Your new passwords did not match.";
        } else {
                $user = $this->select($this->file['users'], $this->user[0]);
                $user[2] = $this->encrypt($newpassword);
                $this->update($this->file['users'], $this->user[0], $user);
                setcookie("cpass", $this->encrypt($newpassword), time()+5184000);
                $message = "Your password has been changed and your borwser cookies have been updated.";
        }
        $this->header();
        $this->displaymessage("Change Password", $message);
        $this->footer();
}

function changepass1 () {
        if ($this->user[0] == 0) {
        $this->displaymessage("Change Password", "You are a guest, you have no password to change, go away :P.");
        } else {
?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post">
        <input type="hidden" name="a" value="member">
        <input type="hidden" name="type" value="changepass2">
     <tr>
          <td colspan=2 class="category" align="center">Changing Password</td>
         </tr>
         <tr>
          <td class="ctable1" width=150>Logged in as:</td>
          <td class="ctable1"><?=$this->user[1]?></td>
         </tr>
         <tr>
          <td class="ctable2" width=150>Old Password</td>
          <td class="ctable2"><input name="oldpassword" type="password"></td>
         </tr>
         <tr>
          <td class="ctable1" width=150>New Password:</td>
          <td class="ctable1"><input name="newpassword" type="password"></td>
         </tr>
         <tr>
          <td class="ctable2" width=150>New Password Again:</td>
          <td class="ctable2"><input name="newpassword2" type="password"></td>
         </tr>
         <tr>
          <td colspan=2 align=center class="ctable1"><?$this->button("Change Password")?></td>
         </tr>
        </form>
        </table>
<?
}
}

function memberlist () {
        global $page, $sort;
        if (($sort == "") || ($sort == "username")) {
                $users = $this->selectsort($this->file['users'], 1);
        } elseif ($sort == "posts") {
                $users = $this->selectsort($this->file['users'], 11);
                $users = array_reverse($users);
        }
	$memcount = count($users) - 2;
        $pagelinks = "";
        if ($page == "") {$page = 1;}
        if (($sort == "") || ($sort == "username")) {
                for ($i=1;count($users)>=$i*$this->membersperpage-$this->membersperpage+1;$i++) {
			if ($i == $page) {
				$b = " <b>";
				$r = "</b>";
			} else {
				$b = " <a href=\"index.php?a=member&type=list&page=".$i."\">";
				$r = "</a>";
			}
                        $pagelinks = $pagelinks.$b.$i.$r;
                }
        }
        elseif ($sort == "posts") {
                for ($i=1;count($users)>=$i*$this->membersperpage-$this->membersperpage+1;$i++) {
			if ($i == $page) {
				$b = " <b>";
				$r = "</b>";
			} else {
				$b = " <a href=\"index.php?a=member&type=list&sort=posts&page=".$i."\">";
				$r = "</a>";
			}
                        $pagelinks = $pagelinks.$b.$i.$r;
                }
        }
        $users = $this->limit($users, ($page-1)*$this->membersperpage, $this->membersperpage-1);
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1>
         <tr><form action="index.php">
          <td align="left"><input type="hidden" name="a" value="member"><input type="hidden" name="type" value="list"><span class="regular">Sort By: <select name="sort"><option value="username">Username</option><option value="posts"># of Posts</option></select> <?$this->button("Sort")?></span></td></form>
          <td align="right"><span class="regular">[ <?=$pagelinks?> ]<br>
	  Viewing page <b><?=$page?></b> with <b><?=$this->membersperpage?></b> members per page. There is a total of <?=$memcount?> members. </span></td>
         </tr>
        </table>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
     <tr>
          <td class="category" colspan="7" class="category" align="center">Member List</td>
         </tr>
         <tr>
          <td class="ctable1">Username</td>
          <td class="ctable2" align="center">Posts</td>
          <td class="ctable1" align="center">Level</td>
          <td class="ctable2" width="40" align="center">AIM</td>
          <td class="ctable1" width="40" align="center">MSN</td>
          <td class="ctable2" width="40" align="center">YIM</td>
          <td class="ctable1" width="40" align="center">ICQ</td>
         </tr>
        <?
        foreach ($users as $user) {
		if ($user[1] != "Guest" && $user[1] != "") {
			if ($user[0] != "-1" && $user[0] != "") {
        			if ($user[6] == "") {
        			        $aim = "No";
        			} else {
                			$aim = "<a href=\"aim:goim?screenname=".$user[6]."&message=Hi.+Are+you+there?\">Yes</a>";
        			}
        			if ($user[7] == "") {
        			        $msn = "No";
        			} else {
        			        $msn = "<a href='http://members.msn.com/default.msnw?mem=".$user[7]."' TARGET='new'>Yes</a>";
        			}
        			if ($user[8] == "") {
        			        $yim = "No";
        			} else {
        			        $yim = "<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user[8]."& ;.src=pg' TARGET='new'>Yes</a>";
        			}
        			if ($user[9] == "") {
        			        $icq = "No";
        			} else {
        			        $icq = "<a href='http://web.icq.com/wwp?Uin=".$user[9]."' TARGET='new'>Yes</a>";
        			}
				$this->pip($user[11], $user[3], $user[0]);
				$pip = str_replace("<br>", "", $this->pip);
				$bigpip = str_replace("<br>", "", $this->bigpip);
        			if ($user[10] != "disabled") {
                ?>
         <tr>
          <td class="ctable1"><a href="index.php?a=member&type=viewprofile&userid=<?=$user[0]?>"><?=$user[1]?></a></td>
          <td class="ctable2" align="center"><?=$user[11]?></td>
          <td class="ctable1" align="center">
<table width="100%"><TR> 
<TD valign="middle" align="left" width="50%"><div align="left"> 
<?=$bigpip?>
</div></td> 
<TD align="right" width="50%"><div align="right">
<?=$pip?>
</div></td></tr></table>
</td>
          <td class="ctable2" width="40" align="center"><?=$aim?></td>
          <td class="ctable1" width="40" align="center"><?=$msn?></td>
          <td class="ctable2" width="40" align="center"><?=$yim?></td>
          <td class="ctable1" width="40" align="center"><?=$icq?></td>
         </tr>
                <?
				}
        		}
		}
	}
        ?>
        </table>
        <?
}

function viewprofile () {
        global $userid, $user;
        if (isset($userid)) {
                $user = $this->select($this->file['users'], $userid);
        } else {
                $user = $this->selectwhere($this->file['users'], 1, $user);
                $user = $user[0];
        }
        if ($user[14] != "") {
                $avatar = "<img src=\"".$user[14]."\">";
        } else {
                $avatar = "None";
        }
        $usergroup = $this->select($this->file['groups'], $user[3]);
        if ($this->validate(14) == 1) {
                $groups = $this->selectall($this->file['groups']);
                $groupshtml = "<br>New Group <input type=\"hidden\" name=\"a\" value=\"iai\"><input type=\"hidden\" name=\"type\" value=\"editgroup\"><input type=\"hidden\" name=\"user\" value=\"".$user[0]."\"><select name=\"group\">";
                foreach ($groups as $group) {
			if ($group[0] != 0) {
                        	$groupshtml = $groupshtml . "<option value=\"".$group[0]."\">".$group[1]."</option>";
			}
                }
                $groupshtml = $groupshtml . "</select> ".$button;
                if ($user[10] == "disabled") {
                        $statusadmin = "(<a href=\"index.php?a=iai&type=enableuser&userid=".$user[0]."\">Activate User</a>)";
                } else {
                        $statusadmin = "(<a href=\"index.php?a=iai&type=disableuser&userid=".$user[0]."\">Disable User</a>)";
                }
                $changepasshtml = "<tr><td class=\"ctable1\" width=\"120\">Change Password:</td><td class=\"ctable1\"><a href=\"index.php?a=iai&type=changepassword&userid=".$user[0]."\">Change Password</a></td></tr>";
        }
        if ($user[10] == "disabled") {
                $status = "Disabled";
        } else {
                $status = "Active";
        }
	$this->pip($user[11], $user[3], $user[0]);
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
     <tr>
          <td colspan=2 class="category" align="center">Viewing Profile</td>
         </tr>
         <tr>
          <td class="ctable2" width=120>Username:</td>
          <td class="ctable2"><?=$user[1]?></td>
         </tr>
         <tr>
          <td class="ctable1" width=120>User Status:</td>
          <td class="ctable1"><?=$status?> <?=$statusadmin?></td>
         </tr>
        <?=$changepasshtml?>
<?
        if ($user[10] != "disabled") {
?>
         <tr>
          <td class="ctable2" width=120>Messaging:</td>
          <td class="ctable2"><a href="index.php?a=messenger&type=send&to=<?=$user[1]?>">Send a Message</a></td>
         </tr>
         <tr>
          <td class="ctable1" width=120>Usergroup:</td>
          <form action="index.php"><td class="ctable1"><b><?=$usergroup[1]?></b><?=$groupshtml?><?
	if ($this->validate(14) == 1) {
		echo $this->button("Change Group");
		echo "</form>";
	}
?></td>
         </tr>
         <tr>
          <td class="ctable2" width=120>Level:</td>
          <td class="ctable2"><?=$this->bigpip?><?=$this->pip?></td>
         </tr>
         <tr>
          <td class="ctable1" width=120>Avatar:</td>
          <td class="ctable1"><?=$avatar?></td>
         </tr>
         <tr>
          <td class="ctable2" width=120>Email:</td>
          <td class="ctable2"><a href="mailto:<?=$user[4]?>"><?=$user[4]?></a></td>
         </tr>
         <tr>
          <td class="ctable1" width=120>Posts:</td>
          <td class="ctable1"><?=$user[11]?></td>
         </tr>
         <tr>
          <td class="ctable2" width=120>Last Active:</td>
          <td class="ctable2"><?=$this->bdate($this->timeformat, $user[10])?></td>
         </tr>
         <tr>
          <td class="ctable1" width=120>Location:</td>
          <td class="ctable1"><?=$user[5]?></td>
         </tr>
         <tr>
          <td class="ctable2" width=120>Web Page:</td>
          <td class="ctable2"><a href="<?=$user[13]?>"><?=$user[13]?></a></td>
         </tr>
         <tr>
          <td class="ctable1" width=120>AIM:</td>
          <td class="ctable1"><a href="aim:goim?screenname=<?=$user[6]?>&message=Hi.+Are+you+there?"><?=$user[6]?></a></td>
         </tr>
         <tr>
          <td class="ctable2" width=120>MSN:</td>
          <td class="ctable2"><a href='http://members.msn.com/default.msnw?mem=<?=$user[7]?>' TARGET='new'><?=$user[7]?></a></td>
         </tr>
         <tr>
          <td class="ctable1" width=120>YIM:</td>
          <td class="ctable1"><a href='http://edit.yahoo.com/config/send_webmesg?.target=<?$user[8]?>& ;.src=pg' TARGET='new'><?=$user[8]?></a></td>
         </tr>
         <tr>
          <td class="ctable2" width=120>ICQ:</td>
          <td class="ctable2"><a href='http://web.icq.com/wwp?Uin=<?=$user[9]?>' TARGET='new'><?=$user[9]?></a></td>
         </tr>
         <tr>
          <td class="ctable1" width=120>Signature:</td>
          <td class="ctable1"><?=$this->tags($user[12])?></td>
         </tr>
        </table>
        <?
	}
}

function profile2 () {
        global $email, $title, $location, $aim, $msn, $yim, $icq, $signature, $webpage, $avatar;
        if ($avatar != "") {$image = getimagesize($avatar);}
                if ($image[0] > $this->awidth) {
                        $this->displaymessage("Error", "Image too wide. Maximum width: ".$this->awidth);
                } elseif ($image[1] > $this->aheight) {
                        $this->displaymessage("Error", "Image too tall. Maximum height: ".$this->aheight);
                } else {
                        $user = $this->select($this->file['users'], $this->user[0]);
                        $user[4] = $this->inputize($email, 60);
                        $user[5] = $this->inputize($location, 60);
                        $user[6] = $this->inputize($aim, 35);
                        $user[7] = $this->inputize($msn, 60);
                        $user[8] = $this->inputize($yim, 35);
                        $user[9] = $this->inputize($icq, 35);
                        $user[12] = $this->inputize($signature, 450);
                        $user[13] = $this->inputize($webpage, 80);
                        $user[14] = $this->inputize($avatar, 80);
                        $user[15] = $this->inputize($title, 60);
                        $this->update($this->file['users'], $this->user[0], $user);
                        $this->displaymessage("Profile", "Profile Updated");
                }
}

function profile () {
        if ($this->user[0] == 0) {
        	$this->displaymessage("Profile", "You are a guest, you have no profile to edit, go away :P.");
        } else {
        	$usergroup = $this->select($this->file['groups'], $this->user[3]);
        ?>
        <table align=center width=100% cellpadding=2 cellspacing=1 class="bordertable">
        <form action="index.php" method="post" name=profile>
        <input type="hidden" name="a" value="member">
        <input type="hidden" name="type" value="profile2">
     <tr>
          <td colspan=2 class="category" align="center">Editing Profile</td>
         </tr>
         <tr>
          <td class="ctable2" width=150>Logged in as:</td>
          <td class="ctable2"><?=$this->user[1]?></td>
         </tr>
<?
		$this->pip($this->user[11], $this->user[3], $this->user[0]);
		if ($this->userset == true) {
			if (empty($this->user[15])) {
				$title = "Cornerstone Member";
			} else {
				$title = $this->user[15];
			}
?>         <tr>
          <td class="ctable1" width=150>Your Customized Title:</td>
          <td class="ctable1" valign="top"><span class="small">You have become advanced enough in this board to specify your own unique user title!</span><br>
	<input size=30 maxsize=60 name="title" value="<?=$title?>"></td>
         </tr><?
		}
?>
         <tr>
          <td class="ctable1" width=150>Password</td>
          <td class="ctable1"><a href="index.php?a=member&type=changepass1" target="_blank">Change Password</a></td>
         </tr>
         <tr>
          <td class="ctable2" width=150>Current Usergroup:</td>
          <td class="ctable2"><?=$usergroup[1]?></td>
         </tr>
         <tr>
          <td class="ctable1" width=150>Email:</td>
          <td class="ctable1"><input size=30 maxsize=60 name="email" value="<?=$this->user[4]?>"></td>
         </tr>
         <tr>
          <td class="ctable2" width=150>Location:</td>
          <td class="ctable2"><input size=30 maxsize=60 name="location" value="<?=$this->user[5]?>"></td>
         </tr>
         <tr>
          <td class="ctable1" width=150>Web Page:</td>
          <td class="ctable1"><input size=30 maxsize=80 name="webpage" value="<?=$this->user[13]?>"></td>
         </tr>
         <tr>
          <td class="ctable2" width=150>AIM:</td>
          <td class="ctable2"><input name="aim" maxsize=35 value="<?=$this->user[6]?>"></td>
         </tr>
         <tr>
          <td class="ctable1" width=150>MSN:</td>
          <td class="ctable1"><input name="msn" maxsize=60 value="<?=$this->user[7]?>"></td>
         </tr>
         <tr>
          <td class="ctable2" width=150>YIM:</td>
          <td class="ctable2"><input name="yim" maxsize=35 value="<?=$this->user[8]?>"></td>
         </tr>
         <tr>
          <td class="ctable1" width=150>ICQ:</td>
          <td class="ctable1"><input name="icq" maxsize=35 value="<?=$this->user[9]?>"></td>
         </tr>
         <tr>
          <td class="ctable2" width=150>Avatar:</td>
          <td class="ctable2">URL:<input size=30 maxsize=90 name="avatar" value="<?=$this->user[14]?>"><br>
	  Select: 
		<div height="80" width="120" style="overflow: scroll; height: 80; width: 120;">
		 <center>
		  <img src="images/avatars/av1.gif" width="80" height="64" onclick="profile.avatar.value='images/avatars/av1.gif'">
		  <img src="images/avatars/av2.gif" width="65" height="65" onclick="profile.avatar.value='images/avatars/av2.gif'">
 		  <img src="images/avatars/av3.gif" width="65" height="65" onclick="profile.avatar.value='images/avatars/av3.gif'">
 	 	  <img src="images/avatars/av4.jpg" width="65" height="65" onclick="profile.avatar.value='images/avatars/av4.jpg'">
		  <img src="images/avatars/av5.jpg" width="65" height="65" onclick="profile.avatar.value='images/avatars/av5.jpg'">
		  <img src="images/avatars/av6.jpg" width="65" height="65" onclick="profile.avatar.value='images/avatars/av6.jpg'">
		  <img src="images/avatars/av7.jpg" width="65" height="65" onclick="profile.avatar.value='images/avatars/av7.jpg'">
		  <img src="images/avatars/av8.jpg" width="66" height="65" onclick="profile.avatar.value='images/avatars/av8.jpg'">
		  <img src="images/avatars/av9.jpg" width="65" height="65" onclick="profile.avatar.value='images/avatars/av9.jpg'">
		  <img src="images/avatars/av10.jpg" width="65" height="65" onclick="profile.avatar.value='images/avatars/av10.jpg'">
		 </center>
		</div>
	  </td>
         </tr>
         <tr>
          <td class="ctable1" width=150 valign="top">Signature:<br>(max 600)</td>
          <td class="ctable1"><textarea name="signature" cols="60" rows="3"><?=$this->deinputize($this->user[12])?></textarea></td>
         </tr>
         <tr>
          <td colspan=2 align=center class="ctable1"><?$this->button("Edit Profile")?></td>
         </tr>
        </form>
        </table>
        <?
        }
}

function logout () {
	if ($this->currentstatus == 400 || $this->currentstatus == 401) {
        	$this->header();
        	$this->displaymessage("Error", "You cannot log out when the board is not open.");
        	$this->footer();
	} else {
        	$where = $this->selectwhere($this->file['online'], 1, $this->user[0]);
        	$this->delete($this->file['online'], $where[0][0]);
        	setcookie("cuser", "", time());
        	setcookie("cpass", "", time());
        	setcookie("sessionid", "", time());
        	$this->header();
        	$this->displaymessage("Logout", "You have been logged out.", "index.php");
        	$this->footer();
	}
}

function login2 () {
        global $username, $password, $guestcookie, $v;
        $user = $this->selectwhere($this->file['users'], 1, $username);
        if ($user[0][0] == 0) {
                $message = "Invalid username. Please go back and try again.<br>";
                } else {
                        if ($user[0][2] != $this->encrypt($password)) {
                                $message = "Invalid password. Please go back and try again.";
                        } elseif ($user[0][10] == "disabled") {
                                $message = "Your account has been disabled.  Contact the board administration to get it activated again.";
                        } else {
                                setcookie("cuser", $username, time()+5184000);
                                setcookie("cpass", $this->encrypt($password), time()+5184000);
                                // delete guest from who's online
                                $where = $this->selectwhere($this->file['online'], 3, $GLOBALS['REMOTE_ADDR']);
                                $this->delete($this->file['online'], $where[0][0]);
				if ($v == "1") {
					setcookie("invis", "1", time()+5184000);
				} elseif ($v == "0") {
					setcookie("invis", "0", time()+5184000);
					$this->whosonline();
				} else {
					setcookie("invis", "0", time()+5184000);
					$this->whosonline();
				}
                                $message = "You have been logged in.";
                                $redirect = "index.php";
                        }
                }
        $this->header();
        $this->displaymessage("Login", $message, $redirect);
        $this->footer();
}

function login () {
?>
<table align=center width=400 cellpadding=2 cellspacing=1 class="bordertable">
<form name"loginreg" action="index.php" method="post">
<input type="hidden" name="a" value="member">
<input type="hidden" name="type" value="login2">
 <tr>
  <td class="category" colspan="2" align="center">Login</td>
 </tr>
 <tr>
  <td class="ctable2">Username:</td>
  <td class="ctable1"><input type="text" name="username" size=30></td>
 </tr>
 <tr>
  <td class="ctable2">Password:</td>
  <td class="ctable1"><input type="password" name="password" size=30></td>
 </tr>
 <tr>
  <td class="ctable2" colspan="100%" align="center">
   <center>
    <select name="v">
     <option slected value="0">Let others see me (Visible)</option>
     <option value="1">Do not let others see me (Invisible)</option>
    </select>
   </center>
  </td>
 </tr>
  <td class="ctable2" colspan=2 align=center><?$this->button("Log In")?></td>
 </tr></form>
</table>
<?
}

function register2 () {
        global $username, $password, $password2, $email;
        $existingusername = $this->selectwhere_upcase($this->file["users"], 1, strtoupper($username));
	$back = ".  Please go <a href='index.php?a=member&type=register'>back</a> and try again.";
        if ($password != $password2) {
                $message = "Password did not match";
        } elseif (isset($existingusername[0][0])) {
                $message = "Sorry, that username is already taken";
        } elseif (strlen($username) > 35) {
                $message = "Username too long";
        } elseif ($username == "") {
                $message = "You did not enter a username";
        } elseif (strtoupper($username) == "GUEST") {
                $message = "You cannot use that username";
        } elseif (strtoupper($username) == "SYSTEM") {
                $message = "You cannot use that username";
	} elseif (strstr($username,">") || strstr($username,"<") || strstr($username,",")) {
		$message = "Your username contains invalid characters";
        } elseif ($password == "") {
                $message = "You did not enter a password";
        } else {
                $user = array($this->inputize($username, 35), $this->encrypt($password), 2, $this->inputize($email, 60), null, null, null, null, null, time(), 0);
                $u = $this->insert($this->file['users'], $user);
		if ($u == 1) {
			$user = $this->select($this->file['users'], $u);
			$user[3] = 1;
			$this->update($this->file['users'], $u, $user);
		}
                $message = "You have been registered.  You may now <a href=\"index.php?a=member&type=login\">login</a>.";
		$back = "";
		$this->systempm($u, 1);
        }
        $this->displaymessage("Register", $message.$back);
}

function register () {
?>
<table align=center width=400 cellpadding=2 cellspacing=1 class="bordertable">
<form action="index.php" method="post">
<input type="hidden" name="a" value="member">
<input type="hidden" name="type" value="register2">
 <tr>
  <td class="category" colspan="2" align="center">Register</td>
 </tr>
 <tr>
  <td class="ctable1" size="25">Username:</td>
  <td class="ctable1"><input size=30 name="username" maxsize=35></td>
 </tr>
 <tr>
  <td class="ctable2">Password:</td>
  <td class="ctable2"><input size=30 name="password" type="password"></td>
 </tr>
 <tr>
  <td class="ctable1">Password Again:</td>
  <td class="ctable1"><input size=30 name="password2" type="password"></td>
 </tr>
 <tr>
  <td class="ctable2">Email:</td>
  <td class="ctable2"><input size=35 maxsize=60 name="email"></td>
 </tr>
 <tr>
  <td class="ctable1" colspan=2 align=center><?$this->button("Register")?></td>
 </tr>
</form>
</table>
<?
}

}

?>