<?php

include "php 4-2+.php";

class module extends fpboard {

var $topics, $posts, $validated;

function execute () {
        global $type;
        if ($this->validate(14) == 1) {
                $this->header();
                $this->adminlinks();
                if ($type == "general") {
                        $this->general();
                } elseif ($type == "customize") {
                        $this->customize();
                } elseif ($type == "group") {
                        $this->group();
                } elseif ($type == "general2") {
                        $this->general2();
                } elseif ($type == "customize2") {
                        $this->customize2();
                } elseif ($type == "group2") {
                        $this->group2();
                } elseif ($type == "group3") {
                        $this->group3();
                } elseif ($type == "newgroup") {
                        $this->newgroup();
                } elseif ($type == "viewusers") {
                        $this->viewusers();
                } elseif ($type == "newgroup2") {
                        $this->newgroup2();
                } elseif ($type == "deletegroup") {
                        $this->deletegroup();
                } elseif ($type == "deletegroup2") {
                        $this->deletegroup2();
                } elseif ($type == "category") {
                        $this->category();
                } elseif ($type == "editcategory2") {
                        $this->editcategory2();
                } elseif ($type == "editcategory") {
                        $this->editcategory();
                } elseif ($type == "newcategory") {
                        $this->newcategory();
                } elseif ($type == "newcategory2") {
                        $this->newcategory2();
                } elseif ($type == "deletecategory") {
                        $this->deletecategory();
                } elseif ($type == "deletecategory2") {
                        $this->deletecategory2();
                } elseif ($type == "safety") {
                        $this->safety();
                } elseif ($type == "safety2") {
                        $this->safety2();
                } elseif ($type == "title") {
                        $this->pip();
                } elseif ($type == "title2") {
                        $this->pip2();
                } elseif ($type == "title3") {
                        $this->pip3();
                } elseif ($type == "title4") {
                        $this->pip4();
                } elseif ($type == "title5") {
                        $this->pip5();
                } elseif ($type == "pip") {
                        $this->pip6();
                } elseif ($type == "pip2") {
                        $this->pip7();
                } else {
                        $this->general();
                }
                $this->footer();
        } else {
                $this->header();
                $this->displaymessage("Error", "You may not access the Administrator's Control Panel");
                $this->footer();
        }
}

function spacer () {
?>
     </table>
<?
}

function links () {
?><center>
[ <a href="index.php?a=admin&type=general">General Options</a> ]
[ <a href="index.php?a=admin&type=group">Group Options</a> ]
[ <a href="index.php?a=admin&type=category">Category Options</a> ]
[ <a href="index.php?a=admin&type=customize">Customize eB</a> ]
[ <a href="index.php?a=admin&type=safety">Backup/Restore</a> ]
[ <a href="index.php?a=admin&type=title">User Title and Pip Options</a> ]
</center><?
}

function adminlinks () {
?>
       <table width="100%" cellpadding=2 cellspacing=0>
         <tr>
          <td><span class="regular"><?=$this->links()?></span></td>
         </tr>
        </table>
<?
}

function pip () {
	global $title, $titleid, $posts, $pips;
        $setting = 1;
        include("extention/files/admin2.php");
}

function pip2 () {
	global $title, $titleid, $posts, $pips;
        $setting = 2;
        include("extention/files/admin2.php");
}

function pip3 () {
	global $title, $titleid, $posts, $pips;
        $setting = 3;
        include("extention/files/admin2.php");
}

function pip4 () {
	global $title, $titleid, $posts, $pips;
        $setting = 4;
        include("extention/files/admin2.php");
}

function pip5 () {
	global $title, $titleid, $posts, $pips;
	$this->delete($this->file['titles'], $titleid);
	$this->displaymessage("Delete Title", "Title deleted.");
}

function safety () {
?>
       <table width="100%" cellpadding=2 cellspacing=1 class="bordertable">
         <tr>
          <td class="ctable1" align="center" width="4%"></td>
          <td class="ctable1" width="53%">Name</td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="29%"></td>
         </tr>
         <tr>
          <td colspan=5 class="category">Select a Method for Backup/Restore</td>
         </tr>
         <tr>
          <td class="ctable1" align="center" width="4%"><img src="images/forumnew.gif"></td>
          <td class="ctable1" colspan="4" width="53%">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="regular">
                 <tr>
                  <td>Backup:</td>
                  <td align="right">[ <a title="Backs up forums, categories, and topics." href="index.php?a=admin&type=safety2&selection=1">Category, Forum, and Topic Files</a> ] [ <a title="Backs up users, messages, options, styles, and groups." href="index.php?a=admin&type=safety2&selection=2">All Data and Settings</a> ]</td>
                 </tr>
              </table>
          </td>
         </tr>
         <tr>
          <td class="ctable1" align="center" width="4%"><img src="images/forumnew.gif"></td>
          <td class="ctable1" colspan="4" width="53%">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="regular">
                 <tr>
                  <td>Restore:</td>
                  <td align="right">[ <a title="Restores forums, categories, and topics." href="index.php?a=admin&type=safety2&selection=3">Category, Forum, and Topic Files</a> ] [ <a title="Restores users, messages, options, styles, and groups." href="index.php?a=admin&type=safety2&selection=4">All Data and Settings</a> ]</td>
                 </tr>
              </table>
          </td>
         </tr>
        </table>
<?
}

function safety2 () {
        global $selection;
        include("extention/files/safety.php");
}

function category () {
        $acats = file("forums/categories.cgi");
?>
       <table width="100%" cellpadding=2 cellspacing=1 class="bordertable">
         <tr>
          <td class="ctable1" align="center" width="4%"></td>
          <td class="ctable1" width="53%">Name</td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="29%"></td>
         </tr>
         <tr>
          <td colspan=5 class="category"><?=$this->boardname?> Categories [ <a class="catlink" href="index.php?a=admin&type=newcategory" class="catlink">New Category</a> ]</td>
         </tr>
<?
        $current = 1;
        foreach ($acats as $acat) {
                $acat = explode("|", $acat);
?>
         <tr>
          <td class="ctable<?=$current?>" align="center" width="4%"><img src="images/forumnew.gif"></td>
          <td class="ctable<?=$current?>" colspan="4" width="53%">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="regular">
                 <tr>
                  <td><?=$acat[1]?></td>
                  <td align="right">[ <a href="index.php?a=admin&type=editcategory&catnumber=<?=$acat[0]?>">Edit</a> ] [ <a href="index.php?a=admin&type=deletecategory&catnumber=<?=$acat[0]?>">Delete</a> ]</td>
                 </tr>
              </table>
          </td>
         </tr>
<?
       if ($current == 1) {
               $current = 2;
       } else {
               $current = 1;
       }

       }
?>
</table>
<?
}

function newcategory () {
        $form =        "<form action=\"index.php\" method=\"post\">\n";
        $form = $form . "<input type=\"hidden\" name=\"a\" value=\"admin\">\n";
        $form = $form . "<input type=\"hidden\" name=\"type\" value=\"newcategory2\">\n";
        $form = $form . "Category Name: <input name=\"catname\" size=\"25\"><br>\n";;
        $form = $form . "<center><@=$this->button(Create Category)@></center></form>\n";
        $this->displaymessage("New Category", $form);
}

function newcategory2 () {
        global $catname;
        $save = array($this->inputize($catname), "");
        $c = $this->insert("forums/categories.cgi", $save);
        $edit = "<a href=\"index.php?a=admin&type=editcategory&catnumber=".$c."\">edit</a>";
        $addnew = "<a href=\"index.php?a=iai&type=newforum&category=".$c."\">add new</a>";
        $this->displaymessage("Created", "Category, $d has been created.  You will have to ".$edit." this category to add existing forums.  You can also ".$addnew." forums to it.");
}

function editcategory () {
        global $category, $catnumber;
        $num = 1;
        $dbforum = $this->select("forums/categories.cgi", $catnumber);
        $fs = explode(",", $dbforum[2]);
        $forums = file("forums/forums.cgi");
        foreach ($forums as $forum) {
                 $forum = explode("|", $forum);
                 $listforums = $listforums . "| Forum number " . $forum[0] . " is " . $forum[1] . " ";
        }
        $form =        "<form action=\"index.php\" method=\"post\">\n";
        $form = $form . "<input type=\"hidden\" name=\"a\" value=\"admin\">\n";
        $form = $form . "<input type=\"hidden\" name=\"type\" value=\"editcategory2\">\n";
        $form = $form . "<input type=\"hidden\" name=\"category\" value=\"".$dbforum[0]."\">\n";
        $form = $form . "Category Name: <input name=\"catname\" size=\"25\" value=\"".$dbforum[1]."\"><br>\n";
        $form = $form . "Forums in this Category: <input name=\"list\" size=\"25\" value=\"".$dbforum[2]."\"><br>\n";
        $form = $form . "\\ (which forums do you want listed in this category? eg: 1,3,5)<br>\n";
        $form = $form . "\\ (<b>FORUMS:</b>\n";
        $form = $form . $listforums . "| )<br>\n";
        $form = $form . "<@=$this->button(Edit Category)@></form>\n";
        $this->displaymessage("Edit Forum", $form);
}

function editcategory2 () {
        global $category, $catname ,$list;
        $dbforum = $this->select("forums/categories.cgi", $category);
        $dbforum[1] = $this->inputize($catname);
        $dbforum[2] = $this->inputize($list);
        $this->update("forums/categories.cgi", $category, $dbforum);
        $this->displaymessage("Edit Category", "Category Edited");
}

function deletecategory () {
        global $catnumber;
        $acats = file("forums/categories.cgi");
        foreach ($acats as $acat) {
                 $acat = explode("|", $acat);
                 if ($acat[0] != $catnumber) {
                           $cattxt = $cattxt . "<option value=\"" . $acat[0] . "\">" . $acat[1] . "</option>\n";
                 }
        }
?>
<form action="index.php" method="post">
    <input type="hidden" name="a" value="admin">
        <input type="hidden" name="type" value="deletecategory2">
        <input type="hidden" name="catnumber" value="<?=$catnumber?>">
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Select the Group You Want the Forums in Category <?=$catnumber?> to Go:</font></td>
      <td class="ctable1" valign="top"><select name="resultcat"><?=$cattxt?></select></td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="ctable2"><?$this->button("Continue...")?></td>
  </form>
<?
}

function deletecategory2 () {
        global $catnumber, $resultcat;
        $movecats = $this->select("forums/categories.cgi", $catnumber);
        $list = $movecats[2];
        $destcats = $this->select("forums/categories.cgi", $resultcat);
        $prev = $destcats[2];
        $success = $this->delete("forums/categories.cgi", $catnumber);
        $s = ",";
        if (empty($list)) { $s = ""; }
        if (empty($prev)) { $s = ""; }
        $dbforum = $this->select("forums/categories.cgi", $resultcat);
        $dbforum[2] = $this->inputize($list.$s.$prev);
        $this->update("forums/categories.cgi", $resultcat, $dbforum);
        $this->spacer();
        if ($success == 1) {
                $this->displaymessage("Success", "Category $catnumber has been deleted, and all users have been moved to category $resultcat.");
        } else {
                $this->displaymessage("Failed", "edgeBoard failed to delete the group!");
        }
}

function newgroup () {
        global $dbgroup, $g1, $g2, $g3, $g4, $g5, $g6, $g7, $g8, $g9, $g10, $g11, $g12, $g13, $g14, $g15;
        include("extention/files/admin4.php");
}

function newgroup2 () {
        global $line, $agroups, $agroup, $a, $groupnumber, $dbgroup, $g1, $g2, $g3, $g4;
        global $i, $save, $g5, $g6, $g7, $g8, $g9, $g10, $g11, $g12, $g13, $g14, $g15;
	global $g17, $g18, $g19;
        $d = $this->inputize($g1);
        $e = $this->inputize($g2);
        $f = $this->inputize($g3);
        $g = $this->inputize($g4);
        $h = $this->inputize($g5);
        $s = $this->inputize($g6);
        $j = $this->inputize($g7);
        $k = $this->inputize($g8);
        $l = $this->inputize($g9);
        $m = $this->inputize($g10);
        $n = $this->inputize($g11);
        $o = $this->inputize($g12);
        $p = $this->inputize($g13);
        $q = $this->inputize($g14);
        $r = $this->inputize($g15);
        $s = "0";
        $t = $this->inputize($g17);
        $u = $this->inputize($g18);
        $v = $this->inputize($g19);
        $save = array($d, $e, $f, $g, $h, $s, $j, $k, $l, $m, $n, $o,  $p, $q, $r, $s, $t, $u, $v);
        $this->insert($this->file['groups'], $save);
        $this->displaymessage("Created", "Group, $d has been created.");
}

function group () {
        $agroups = file($this->file['groups']);
?>
       <table width="100%" cellpadding=2 cellspacing=1 class="bordertable">
         <tr>
          <td class="ctable1" align="center" width="4%"></td>
          <td class="ctable1" width="53%">Name</td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="29%"></td>
         </tr>
         <tr>
          <td colspan=5 class="category"><?=$this->boardname?> Groups [ <a class="catlink" href="index.php?a=admin&type=newgroup" class="catlink">New Group</a> ]</td>
         </tr>
<?
        $current = 1;
        foreach ($agroups as $agroup) {
                $agroup = explode("|", $agroup);
		if ($agroup[0] != 0) {
?>
         <tr>
          <td class="ctable<?=$current?>" align="center" width="4%"><img src="images/forumnew.gif"></td>
          <td class="ctable<?=$current?>" colspan="4" width="53%">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="regular">
                 <tr>
                  <td><?=$agroup[1]?></td>
                  <td align="right">[ <a href="index.php?a=admin&type=viewusers&groupnumber=<?=$agroup[0]?>">View Users</a> ] [ <a href="index.php?a=admin&type=group2&groupnumber=<?=$agroup[0]?>">Edit</a> ] [ <a href="index.php?a=admin&type=deletegroup&groupnumber=<?=$agroup[0]?>">Delete</a> ]</td>
                 </tr>
              </table>
          </td>
         </tr>
<?
        if ($current == 1) {
		$current = 2;
        } else {
		$current = 1;
        }

        }
	}
?>
</table>
<?
}

function group2 () {
        global $groupnumber;
        include("extention/files/admin4.php");
}

function group3 () {
        global $groupnumber, $dbgroup, $g1, $g2, $g3, $g4, $g5, $g6, $g7, $g8, $g9, $g10;
	global $g11, $g12, $g13, $g14, $g15, $g16, $g17, $g18, $g19;
        $dbgroup = $this->select($this->file['groups'], $groupnumber);
        $dbgroup[1] = $this->inputize($g1);
        $dbgroup[2] = $this->inputize($g2);
        $dbgroup[3] = $this->inputize($g3);
        $dbgroup[4] = $this->inputize($g4);
        $dbgroup[5] = $this->inputize($g5);
        $dbgroup[6] = $this->inputize($g6);
        $dbgroup[7] = $this->inputize($g7);
        $dbgroup[8] = $this->inputize($g8);
        $dbgroup[9] = $this->inputize($g9);
        $dbgroup[10] = $this->inputize($g10);
        $dbgroup[11] = $this->inputize($g11);
        $dbgroup[12] = $this->inputize($g12);
        $dbgroup[13] = $this->inputize($g13);
        $dbgroup[14] = $this->inputize($g14);
        $dbgroup[15] = $this->inputize($g15);
        $dbgroup[16] = $this->inputize($g18);
        $dbgroup[17] = $this->inputize($g17);
        $dbgroup[18] = $this->inputize($g18);
        $dbgroup[19] = $this->inputize($g19);
        $this->update($this->file['groups'], $groupnumber, $dbgroup);
        $this->spacer();
        $this->displaymessage("Saved", "Your group preferences have been saved.");
}

function viewusers () {
        global $groupnumber;
        $ausers = file($this->file['users']);
        foreach ($ausers as $auser) {
                $auser = explode("|", $auser);
                $num = 0;
                if ($auser[3] == $groupnumber) {
                        if ($num == 0) {
                                $no = "";
                        } else {
                                $no = "\n";
                        }
			if ($auser[10] == "disabled") {
				$link2 = "<a href=\"index.php?a=iai&type=enableuser&userid=".$auser[0]."\">Activate/Enable User</a>";
				$link1 = "<b>Disabled/Inactive</b>";
			} else {
				$link1 = "<b>Enabled/Active</b>";
				$link2 = "<a href=\"index.php?a=iai&type=disableuser&userid=".$auser[0]."\">Deactivate/Disable User</a>";
			}
                        $userlist = $userlist."<a href=\"index.php?a=member&type=viewprofile&user=".$auser[1]."\">".$auser[1]."</a>";
			$userlist = $userlist." ( ".$link1." | ";
			$userlist = $userlist.$link2." )";
			$userlist = $userlist."<br>".$no;
                        $num = $num + 1;
                }
        }
        $tg = $this->select($this->file['groups'], $groupnumber);
?>
       <table width="100%" cellpadding=2 cellspacing=1 class="bordertable">
         <tr>
          <td class="ctable1" align="center" width="4%"></td>
          <td class="ctable1" width="53%">Users</td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="29%"></td>
         </tr>
         <tr>
          <td colspan=5 class="category"><?=$tg[1]?> [ <a class="catlink" href="index.php?a=admin&type=viewusers&groupnumber=<?=$tg[0]?>">View Users</a> ] [ <a class="catlink" href="index.php?a=admin&type=group2&groupnumber=<?=$tg[0]?>">Edit</a> ] [ <a class="catlink" href="index.php?a=admin&type=deletegroup&groupnumber=<?=$tg[0]?>">Delete</a> ]</td>
         </tr>
         <tr>
          <td class="ctable1" valign="top" align="center" width="4%"><img src="images/forumnew.gif"></td>
          <td class="ctable1" colspan="4" width="53%">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="regular">
                 <tr>
                  <td><span class="regular"><?=$userlist?></span></td>
                 </tr>
              </table>
          </td>
         </tr>
</table>
<?
}

function deletegroup () {
        global $groupnumber;
	if ($groupnumber <= 7) {
		$this->displaymessage("Error", "You cannot delete a default group.  You can change their name and permissions though.");
	}
        $agroups = $this->selectall($this->file['groups']);
        foreach ($agroups as $agroup) {
                if ($agroup[0] != $groupnumber) {
			if ($agroup[0] != 0) {
				$grouptxt = $grouptxt . "<option value=\"" . $agroup[0] . "\">" . $agroup[1] . "</option>\n";
			}
              }
        }
	$cgroup = $this->select($this->file['groups'], $groupnumber);
?>
<form action="index.php" method="post">
    <input type="hidden" name="a" value="admin">
        <input type="hidden" name="type" value="deletegroup2">
        <input type="hidden" name="groupnumber" value="<?=$groupnumber?>">
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Select the group you want the users in group <?=$cgroup[1]?> to go:</font></td>
      <td class="ctable1" valign="top"><select name="resultgroup"><?=$grouptxt?></select></td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="ctable2"><?$this->button("Continue...")?></td>
  </form>   
<?
}

function deletegroup2 () {
        global $groupnumber, $resultgroup;
        $ausers = file($this->file['users']);
        foreach ($ausers as $auser) {
                $auser = explode("|", $auser);
                if ($auser[3] == $groupnumber) {
                           $duser = $this->select($this->file['users'], $auser[0]);
                           $duser[3] = $resultgroup;
                           $this->update($this->file['users'],  $auser[0], $duser);
                }
        }
	$cgroup = $this->select($this->file['groups'], $groupnumber);
        $success = $this->delete($this->file['groups'], $groupnumber);
        $this->spacer();
	$rgroup = $this->select($this->file['groups'], $resultgroup);
        if ($success == 1) {
                $this->displaymessage("Success", "The group ".$cgroup[1]." has been deleted, and all users have been moved to group ".$rgroup[1].".");
        } else {
                $this->displaymessage("Failed", "edgeBoard failed to delete the group!");
        }
}

function general () {
	include "extention/files/admin3.php";
}

function general2 () {
        global $brdnm, $avatw, $avath, $topcl, $timef, $pstsz, $wrdsz, $toppp, $pstpp, $mempp, $postt, $topl1, $topl2;
        global $allgu, $rcolo, $rollo, $ahtml, $hmurl, $maxpm, $stats, $offst, $polll, $wrdfl;
        $avatw = $this->inputize($avatw);
        $avath = $this->inputize($avath);
        $topcl = $this->inputize($topcl);
        $timef = $this->inputize($timef);
        $pstsz = $this->inputize($pstsz);
        $wrdsz = $this->inputize($wrdsz);
        $toppp = $this->inputize($toppp);
        $pstpp = $this->inputize($pstpp);
        $mempp = $this->inputize($mempp);
        $postt = $this->inputize($postt);
        $rollo = $this->inputize($rollo);
        $rcolo = $this->inputize($rcolo);
        $allgu = $this->inputize($allgu);
        $brdnm = $this->inputize($brdnm);
        $hmurl = $this->inputize($hmurl);
        $maxpm = $this->inputize($maxpm);
	$polll = $this->inputize($polll);
	$wrdfl = $this->inputize($wrdfl);
        $line1 = array($brdnm, $avatw, $avath, $topcl, $timef, $pstsz, $wrdsz, $toppp, $wrdfl);
        $line2 = array($pstpp, $mempp, $postt, $topl1, $topl2, $allgu, $rcolo, $rollo);
        $line3 = array($ahtml, $hmurl, $maxpm, $offst, $polll,   null,   null,   null  );
	$line4 = $this->select($this->file['settings'], 4);
	$line5 = $this->select($this->file['settings'], 5);
	$line4 = array($line4[1]);
	$line5 = array($line5[1]);
        $this->delete($this->file['settings'], 1);
        $this->delete($this->file['settings'], 2);
        $this->delete($this->file['settings'], 3);
	$this->delete($this->file['settings'], 4);
        $this->delete($this->file['settings'], 5);
        $this->insert($this->file['settings'], $line1);
        $this->insert($this->file['settings'], $line2);
        $this->insert($this->file['settings'], $line3);
        $this->insert($this->file['settings'], $line4);
        $this->insert($this->file['settings'], $line5);
	$file = fopen($this->file['status'], "w");
	fputs($file, $stats);
	fclose($file);
        $this->displaymessage("Saved", "Your options have been saved.");
}

function customize () {
?>

<table width="100%" cellpadding="2" cellspacing="1" class="bordertable">
    <tr>
      <td class="ctable2" width="50%"><form action="index.php" method="post">
        <input type="hidden" name="a" value="admin">
        <input type="hidden" name="type" value="customize2"><font SIZE="2">Toplinks for a Logged In User:<br>\\(<b>Link Codes:</b> :login: = Login Link | :search: = Search Link | :logout: = Logout Link | :memberlist: = Memberlist Link | :profile: = Profile Link | :register: = Register Link)</font>
<br><font SIZE="2">\\(AdminCP links will be automatically added to admin users)</font></td>
      <td class="ctable2" valign="top"><? $op1 = str_replace("\"", "'", $this->toplinks['member']); ?>
<input TYPE="TEXT" value="<?=$op1?>" NAME="toplinks" SIZE="40"></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Toplinks for a Guest:<br>\\(<b>Link Codes:</b> :login: = Login Link | :logout: = Logout Link | :search: = Search Link | :memberlist: = Memberlist Link | :profile: = Profile Link | :register: = Register Link)</font>
<br><font SIZE="2">\\(AdminCP links will be automatically added to admin users)</font></td>
      <td class="ctable1" valign="top"><? $op2 = str_replace("\"", "'", $this->toplinks['guest']); ?>
<input TYPE="TEXT" value="<?=$op2?>" NAME="botlinks" SIZE="40"></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%">
      <font SIZE="2">Style Sheet:</font></td>
      <td class="ctable1" valign="top"><textarea cols="80" ROWS="10" NAME="style"><?
        $styles = file($this->file['style']);
        foreach ($styles as $style) {
                echo ($style);
        }
      ?></textarea></td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="ctable1"><?$this->button("Save Changes")?></td>
    </tr>
  </form>
</table>
</table>
<?
}

function customize2 () {
        global $toplinks, $botlinks, $style;
	$this->delete($this->file['settings'], 4);
	$this->delete($this->file['settings'], 5);
	$line4 = array(stripslashes($toplinks));
	$line5 = array(stripslashes($botlinks));
	$this->insert($this->file['settings'], $line4);
	$this->insert($this->file['settings'], $line5);
        $save = fopen($this->file['style'], "w");
        fputs($save, stripslashes($style));
        fclose($save);
        $this->displaymessage("Saved", "Your customizations have been saved.");
}
}
?>