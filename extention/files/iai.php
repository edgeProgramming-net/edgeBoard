<?php

include "php 4-2+.php";

class module extends fpboard {

function execute() {
	global $type, $forum, $userid;
	$this->header();
	$this->doomsday(14);
	if ($type == "editgroup") {
		$this->editgroup();
	} elseif ($type == "movecategorydown") {
		$this->movecategorydown();
	} elseif ($type == "movecategoryup") {
		$this->movecategoryup();
	} elseif ($type == "moveforumup") {
		$this->moveforumup();
	} elseif ($type == "newforum") {
		$this->newforum();
	} elseif ($type == "newforum2") {
		$this->newforum2();
	} elseif ($type == "deleteforum") {
		$this->deleteforum();
	} elseif ($type == "deleteforum2") {
		$this->deleteforum2();
	} elseif ($type == "moveforumdown") {
		$this->moveforumdown();
	} elseif ($type == "moveforumup") {
		$this->moveforumup();
	} elseif ($type == "editforum") {
		$this->editforum();
	} elseif ($type == "editforum2") {
		$this->editforum2();
	} elseif ($type == "changepassword") {
		$this->changepassword();
	} elseif ($type == "changepassword2") {
		$this->changepassword2();
	} elseif ($type == "disableuser") {
		$this->disableuser();
	} elseif ($type == "disableuser2") {
		$this->disableuser2($userid);
		$message = $user[1]."'s account has been disabled.";
		$this->displaymessage("Disable Account", $message);
	} elseif ($type == "enableuser") {
		$this->enableuser();
	} elseif ($type == "enableuser2") {
		$this->enableuser2();
	} elseif ($type == "reconstruct") {
		$fl = array();
		if ($forum == "all") {
			$ff = $this->selectall("forums/forums.cgi");
			foreach ($ff as $fs) {
				$this->reconstruct($fs[0]);
				$message  = "The selected forum listing has been reconstructed.";
				$this->displaymessage("Reconstruct Forum", $message);
			}
		} else {
			$this->reconstruct($forum);
			$message  = "The selected forum listing has been reconstructed.";
			$this->displaymessage("Reconstruct Forum", $message);
		}
	}
	$this->footer();
}

function editgroup () {
	global $user, $group;
	if ($this->validate(14) == 1) {
		$newuser = $this->select($this->file['users'], $user);
		$newuser[3] = $group;
		$this->systempm($user, 3);
		$this->update($this->file['users'], $newuser[0], $newuser);
		$this->displaymessage ("Edit Group", "<a href=\"index.php?a=member&type=viewprofile&userid=".$newuser[0]."\">".$newuser[1]."</a>'s group has been edited.");
	}
}

function movecategoryup () {
	global $category;
	$this->moveoneup("forums/categories.cgi", $category);
	$this->displaymessage("Category Moved Up", "The selected category has been moved up.");
}

function movecategorydown () {
	global $category;
	$this->moveonedown("forums/categories.cgi", $category);
	$this->displaymessage("Category Moved Down", "The selected category has been moved down.");
}

function moveforumup () {
	global $forum;
}

function newforum () {
	global $category;
	$form =	"<form action=\"index.php\" method=\"post\">\n";
	$form = $form . "<input type=\"hidden\" name=\"a\" value=\"iai\">\n";
	$form = $form . "<input type=\"hidden\" name=\"type\" value=\"newforum2\">\n";
	$form = $form . "<input type=\"hidden\" name=\"category\" value=\"".$category."\">\n";
	$form = $form . "Forum Name: <input name=\"forumname\" size=\"25\"><br>\n";
	$form = $form . "Forum Description: <input name=\"forumdescription\" size=\"45\"><br>\n";
	$form = $form . '<center>'.$this->bt.'Create Forum'.$this->be.'</center></form>';
	$this->displaymessage("New Forum", $form);
}

function newforum2 () {
	global $category, $forumname, $forumdescription;
	$forum = array($forumname, 0, 0, "", "", "", "", $forumdescription);
	$forumid = $this->insert("forums/forums.cgi", $forum);
	$cat = $this->select("forums/categories.cgi", $category);
	$forums = explode (",", $cat[2]);
	array_push($forums, $forumid);
	$lalahaha = "";
	for ($i=0; $i<count($forums);$i++) {
		if ($i == 0) {
				$lalahaha = $forums[$i];
		} else {
			$lalahaha = $lalahaha . "," . $forums[$i];
		}
	}
	$cat[2] = $lalahaha;
	$this->update("forums/categories.cgi", $cat[0], $cat);
	mkdir("forums/".$forumid, 0777);
	chmod("forums/".$forumid, 0777);
	$this->displaymessage("Add Forum", $forumname . " added.");
}

function deleteforum () {
	global $forum, $category;
	$dbforum = $this->select("forums/forums.cgi", $forum);
	$this->displaymessage ("Delete Forum", "Are you sure you wish to delete forum <b>" . $dbforum[1] . "</b>? This action can not be undone.<br>( <a href=\"index.php?a=iai&type=deleteforum2&forum=".$forum."&category=".$category."\">Yes</a> | <a href=\"index.php?a=main\">No</a> )");
}

function deleteforum2 () {
	global $forum, $category;
	$cat = $this->select("forums/categories.cgi", $category);
	$forums = explode(",", $cat[2]);
	$lalahaha = "";
	for ($i=0; $i<count($forums);$i++) {
		if ($forums[$i] != $forum) {
			if ($lalahaha == "") {
					$lalahaha = $forums[$i];
			} else {
				$lalahaha = $lalahaha . "," . $forums[$i];
			}
		}
	}
	$cat[2] = $lalahaha;
	$this->update("forums/categories.cgi", $cat[0], $cat);
	$this->delete("forums/forums.cgi", $forum);
	$dhandle = opendir("forums/".$forum."/");
	while ($file = readdir($dhandle)) {
		if (($file == ".") || ($file == "..")) {
		} else {
			unlink($file);
		}
	}
	rmdir("forums/".$forum);
	$this->displaymessage("Delete Forum", "Forum Deleted");
}

function moveforumup () {
	global $forum, $category;
	$cat = $this->select("forums/categories.cgi", $category);
	$forums = explode(",", $cat[2]);
	// an edited version of $flatfile->moveoneup
	$continue = True;
	for ($i=0; $continue == True; $i++) {
		if ($forums[$i] == $forum) {
			// make a place to temporarily store $i++.
			$temp = array();
			$temp = $forums[$i-1];
			// line $i becomes $i++
			$forums[$i-1] = $forums[$i];
			// line $i becomes line $i++
			$forums[$i] = $temp;
			$continue = False;
		}
		if ($i > count($forums)) {$continue = False;}
	}
	$lalahaha = "";
	for ($i=0; $i<count($forums);$i++) {
		if ($lalahaha == "") {
				$lalahaha = $forums[$i];
		} else {
			$lalahaha = $lalahaha . "," . $forums[$i];
		}
	}
	$cat[2] = $lalahaha;
	$this->update("forums/categories.cgi", $cat[0], $cat);
	$this->displaymessage("Move Forum", "Forum Moved");
}

function moveforumdown () {
	global $forum, $category;
	$cat = $this->select("forums/categories.cgi", $category);
	$forums = explode(",", $cat[2]);
	// an edited version of $flatfile->moveoneup
	$continue = True;
	for ($i=0; $continue == True; $i++) {
		if ($forums[$i] == $forum) {
			// make a place to temporarily store $i++.
			$temp = array();
			$temp = $forums[$i+1];
			// line $i becomes $i++
			$forums[$i+1] = $forums[$i];
			// line $i becomes line $i++
			$forums[$i] = $temp;
			$continue = False;
		}
		if ($i >= count($forums)-2) {$continue = False;}
	}
	$lalahaha = "";
	for ($i=0; $i<count($forums);$i++) {
		if ($lalahaha == "") {
				$lalahaha = $forums[$i];
		} else {
			$lalahaha = $lalahaha . "," . $forums[$i];
		}
	}
	$cat[2] = $lalahaha;
	$this->update("forums/categories.cgi", $cat[0], $cat);
	$this->displaymessage("Move Forum", "Forum Moved");
}

function editforum () {
/*
0 |1   |2     |3    |4         |5          |6          |7
id|name|topics|posts|lastpostid|view groups|post groups|reply groups
*/
	global $forum, $listgroups, $num, $findgs, $findg;
	$i = 0;
	$indent = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$findgs = $this->selectall($this->file['groups']);
	$dbforum = $this->select("forums/forums.cgi", $forum);
	$postgs = explode(",", $dbforum[6]);
	$replgs = explode(",", $dbforum[7]);
	$viewgs = explode(",", $dbforum[5]);
	foreach ($findgs as $findg) {
		if ($findg[0] != 0) {
			if (count($replgs) == 1 || count($replgs) == 0) { if (empty($replgs[0])) { $r = "CHECKED "; } }
			if (count($viewgs) == 1 || count($viewgs) == 0) { if (empty($viewgs[0])) { $v = "CHECKED "; } }
			if (count($postgs) == 1 || count($viewgs) == 0) { if (empty($postgs[0])) { $p = "CHECKED "; } }
			foreach ($postgs as $postg) {
				if ($postg == $findg[0]) {
					$postgroups .= $indent."<input type='checkbox' ".$p."CHECKED name='forumpostgroups[]'";
	     	 			$postgroups .= " value='".$findg[0]."'>".$findg[1]."<br>\n";
					$this->written1[$findg[0]] = 1;
				}
			}
			foreach ($postgs as $postg) {
				if ($postg != $findg[0]) {
					if ($this->written1[$findg[0]] != 1) {
						$postgroups .= $indent."<input type='checkbox' ".$p."name='forumpostgroups[]'";
	     	 				$postgroups .= " value='".$findg[0]."'>".$findg[1]."<br>\n";
						$this->written1[$findg[0]] = 1;
					}
				}
				$this->written1[$findg[0]] = 1;
			}
			foreach ($replgs as $replg) {
				if ($replg == $findg[0]) {
					$replgroups .= $indent."<input type='checkbox' ".$r."CHECKED name='forumreplgroups[]'";
	     	 			$replgroups .= " value='".$findg[0]."'>".$findg[1]."<br>\n";
					$this->written2[$findg[0]] = 1;
				}
			}
			foreach ($replgs as $replg) {
				if ($replg != $findg[0]) {
					if ($this->written2[$findg[0]] != 1) {
						$replgroups .= $indent."<input type='checkbox' ".$r."name='forumreplgroups[]'";
	     	 				$replgroups .= " value='".$findg[0]."'>".$findg[1]."<br>\n";
						$this->written2[$findg[0]] = 1;
					}
				}
				$this->written2[$findg[0]] = 1;
			}
			foreach ($viewgs as $viewg) {
				if ($viewg == $findg[0]) {
					$viewgroups .= $indent."<input type='checkbox' ".$v."CHECKED name='forumviewgroups[]'";
	     	 			$viewgroups .= " value='".$findg[0]."'>".$findg[1]."<br>\n";
					$this->written3[$findg[0]] = 1;
				}
			}
			foreach ($viewgs as $viewg) {
				if ($viewg != $findg[0]) {
					if ($this->written3[$findg[0]] != 1) {
						$viewgroups .= $indent."<input type='checkbox' ".$v."name='forumviewgroups[]'";
	     	 				$viewgroups .= " value='".$findg[0]."'>".$findg[1]."<br>\n";
						$this->written3[$findg[0]] = 1;
					}
				}
				$this->written3[$findg[0]] = 1;
			}
		}
	}
	$form =	 "<form action=\"index.php\" method=\"post\">\n";
	$form = $form . "<input type=\"hidden\" name=\"a\" value=\"iai\">\n";
	$form = $form . "<input type=\"hidden\" name=\"type\" value=\"editforum2\">\n";
	$form = $form . "<input type=\"hidden\" name=\"forum\" value=\"".$forum."\">\n";
	$form = $form . "Forum Name: <input name=\"forumname\" size=\"25\" value=\"".$dbforum[1]."\"><br>\n";
	$form = $form . "Forum Description: <input name=\"forumdescription\" size=\"45\" value=\"".$dbforum[8]."\"><br>\n";
	$form = $form . "<br>Forum Post Groups:<br>\n";
	$form = $form . "\\ (which groups can view this forum?)<br>\n";
	$form = $form . $postgroups;
	$form = $form . "<br>Forum View Groups:<br>\n";
	$form = $form . "\\ (which groups can post in this forum?)<br>\n";
	$form = $form . $viewgroups;
	$form = $form . "<br>Forum Reply Groups:<br>\n";
	$form = $form . "\\ (which groups can reply to existing topics in this forum?)<br>\n";
	$form = $form . $replgroups;
	$form = $form . '<br><center>'.$this->bt.'Edit Forum'.$this->be.'</center></form>';
	$this->displaymessage("Edit Forum", $form);
}

function editforum2 () {
/*
0 |1   |2     |3    |4         |5          |6          |7
id|name|topics|posts|lastpostid|view groups|post groups|reply groups
*/
	global $forum, $forumname, $forumdescription, $forumpostgroups, $forumviewgroups, $forumreplgroups;
	$dbforum = $this->select("forums/forums.cgi", $forum);
	$dbforum[1] = $forumname;
	$postgs = explode(",", $dbforum[6]);
	$vgs = $dbforum[6];
	$viewgs = explode(",", $dbforum[5]);
	$pgs = $dbforum[5];
	$replgs = explode(",", $dbforum[7]);
	$rgs = $dbforum[7];
	$cv = count($postgs);
	$cp = count($viewgs);
	$cr = count($replgs);
	$gfile = $this->selectall($this->file['groups']);
	$tcount = 0;
	foreach ($gfile as $g) {
		if ($g[0] != 0) { ++$tcount; }
	}
	$pcount = count($forumpostgroups);
	$i = 0;
	if (count($forumpostgroups) != 0) {
		foreach ($forumpostgroups as $id) {
			$i++;
			if ($id != 0) {
				if ($count == $i) {
					if ($i == 1) {
						$dbforum[6] = $id;
					} else {
						$dbforum[6] = $dbforum[6].",".$id;
					}
				} else {
					if ($i == 1) {
						$dbforum[6] = $id;
					} else {
						$dbforum[6] = $dbforum[6].",".$id;
					}
				}
			}
		}
	} else {
		$dbforum[6] = "z";
	}
	if ($pcount == $tcount) { $dbforum[6] = ""; }
	$vcount = count($forumviewgroups);
	$i = 0;
	if (count($forumviewgroups) != 0) {
		foreach ($forumviewgroups as $id) {
			$i++;
			if ($id != 0) {
				if ($count == $i) {
					if ($i == 1) {
						$dbforum[5] = $id;
					} else {
						$dbforum[5] = $dbforum[5].",".$id;
					}
				} else {
					if ($i == 1) {
						$dbforum[5] = $id;
					} else {
						$dbforum[5] = $dbforum[5].",".$id;
					}
				}
			}
		}
	} else {
		$dbforum[5] = "z";
	}
	if ($vcount == $tcount) { $dbforum[5] = ""; }

	$rcount = count($forumreplgroups);
	$i = 0;
	if (count($forumreplgroups) != 0) {
		foreach ($forumreplgroups as $id) {
			$i++;
			if ($id != 0) {
				if ($count == $i) {
					if ($i == 1) {
						$dbforum[7] = $id;
					} else {
						$dbforum[7] = $dbforum[7].",".$id;
					}
				} else {
					if ($i == 1) {
						$dbforum[7] = $id;
					} else {
						$dbforum[7] = $dbforum[7].",".$id;
					}
				}
			}
		}
	} else {
		$dbforum[7] = "z";
	}
	if ($rcount == $tcount) { $dbforum[7] = ""; }

	$dbforum[8] = $forumdescription;
	$this->update("forums/forums.cgi", $forum, $dbforum);
	$this->displaymessage("Edit Forum", "Forum '$dbforum[1]' Edited");
}

function changepassword2 () {
	global $userid, $newpassword, $newpassword2;
	clearstatcache();
	if ($newpassword != $newpassword2) {
		$message = "The new passwords did not match.";
	} else {
		$user = $this->select($this->file['users'], $userid);
		$user[2] = $this->encrypt($newpassword);
		$this->update($this->file['users'], $userid, $user);
		$message = $user[1]."'s password has been changed.";
	}
	$this->displaymessage("Change Password", $message);
}

function changepassword () {
	global $userid;
	$user = $this->select($this->file['users'], $userid);
?>
	<table align=center width=65% cellpadding=2 cellspacing=1 class="bordertable">
	<form action="index.php" method="post">
	<input type="hidden" name="a" value="iai">
	<input type="hidden" name="type" value="changepassword2">
	<input type="hidden" name="userid" value="<?=$userid?>">
	<tr>
	<td class="category" colspan="2" align="center">Changing <?=$user[1]?>'s Password</td>
	</tr>
	<tr>
	<td class="ctable1">New Password:</td>
	<td class="ctable1"><input name="newpassword" type="password"></td>
	</tr>
	<tr>
	<td class="ctable2">New Password Again:</td>
	<td class="ctable2"><input name="newpassword2" type="password"></td>
	</tr>
	<tr>
	<td colspan=2 align=center class="ctable1"><?=$this->button("Change Password")?></td>
	</tr>
	</form>
	</table>
<?
}

function disableuser () {
	global $userid;
	$user = $this->select($this->file['users'], $userid);
?>
	<table align=center width=65% cellpadding=2 cellspacing=1 class="bordertable">
	<form action="index.php" method="post">
	<input type="hidden" name="a" value="iai">
	<input type="hidden" name="type" value="disableuser2">
	<input type="hidden" name="userid" value="<?=$userid?>">
     <tr>
	  <td colspan=2 class="category" align="center">Disabling <?=$user[1]?>'s Account</td>
	 </tr>
	 <tr>
	  <td class="ctable2" width=65%>Are you sure you want to disable this account?</td>
	 </tr>
	 <tr>
	  <td colspan=2 align=center class="ctable1"><?$this->button("Disable Account")?></td>
	 </tr>
	</form>
	</table>
<?
}

function enableuser2 () {
	global $userid;
	$user = $this->select($this->file['users'], $userid);
	$user[10] = time();
	$this->update($this->file['users'], $user[0], $user);
	$message = $user[1]."'s account has been enabled.";
	$this->displaymessage("Enable Account", $message);
}

function enableuser () {
	global $userid;
	$user = $this->select($this->file['users'], $userid);
?>
	<table align=center width=65% cellpadding=2 cellspacing=1 class="bordertable">
	<form action="index.php" method="post">
	<input type="hidden" name="a" value="iai">
	<input type="hidden" name="type" value="enableuser2">
	<input type="hidden" name="userid" value="<?=$userid?>">
     <tr>
	  <td colspan=2 class="category" align="center">Enabling <?=$user[1]?>'s Account</td>
	 </tr>
	 <tr>
	  <td class="ctable2" width=65%>Are you sure you want to enable this account?</td>
	 </tr>
	 <tr>
	  <td colspan=2 align=center class="ctable1"><?$this->button("Enable Account")?></td>
	 </tr>
	</form>
	</table>
<?
}

}
?>