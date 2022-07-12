<?php

include "php 4-2+.php";

class module extends fpboard {

function execute () {
        global $type, $res;
        $this->header();
        $this->checklogin();
        if (($type == "") || ($type == "inbox")) {
                $res = "Inbox";
                $this->displaylinks();
                $this->inbox();
        } elseif ($type == "outbox") {
                $res = "Outbox";
                $this->displaylinks();
                $this->outbox();
        } elseif ($type == "forward") {
                $res = "Forwarding";
                $this->displaylinks();
                $this->sendMessage();
        } elseif ($type == "forward2") {
                $res = "Forwarding";
                $this->displaylinks();
                $this->sendMessage2();
        } elseif ($type == "send") {
                $res = "Sending";
                $this->displaylinks();
                $this->sendMessage();
        } elseif ($type == "send2") {
                $res = "Sending";
                $this->displaylinks();
                $this->sendMessage2();
        } elseif ($type == "view") {
                $res = "Viewing";
                $this->displaylinks();
                $this->viewMessage();
        } elseif ($type == "delete") {
                $res = "Deleting...";
                $this->displaylinks();
                $this->delete1();
        } elseif ($type == "delete2") {
                $res = "Deleting...";
                $this->displaylinks();
                $this->delete2();
        } elseif ($type == "deletechecked") {
                $res = "Deleting...";
                $this->displaylinks();
                $this->deletechecked1();
        } elseif ($type == "deletechecked2") {
                $res = "Deleting...";
                $this->displaylinks();
                $this->deletechecked2();
        }
        $this->endmessenger();
        $this->footer();
}

function delete2 () {
        global $id;
        $message = $this->select($this->file['pm'], $id);
        ?></table><br><?
        if (($this->user[0] != $message[1]) && ($this->user[0]) != $message[2]) {
                $this->displaymessage("Error", "You may not delete this message.");
                $this->footer();
                die();
        } else {
		if ($message[1] == "System") {
			$message[6] = 3;
			$message[5] = "";
			$this->update($this->file['pm'], $id, $message);
		} else {
			$this->delete($this->file['pm'], $id);
		}
                $this->displaymessage("Delete Message", "Message Deleted");
        }
}


function delete1 () {
        global $id, $r;
	if ($r == "unsend") {
		$delete = "unsend and delete";
	} else {
		$delete = "delete";
	}
        $message = $this->select($this->file['pm'], $id);
        ?></table><br><?
        if (($this->user[0] != $message[1]) && ($this->user[0]) != $message[2]) {
                $this->displaymessage("Error", "You may not delete this message.");
                $this->footer();
                die();
        } else {
                $this->displaymessage("Delete Message", "Are you sure you wish to $delete this message? (<a href=\"index.php?a=messenger&type=delete2&id=".$id."\">Yes</a> | <a href=\"index.php?a=messenger&type=inbox\">No</a>)");
        }
}

function deletechecked2 () {
        global $message;
	$errcount = 0;
	$message = explode(",", $message);
	$mescount = count($message);
	foreach ($message as $id) {
        	$entry = $this->select($this->file['pm'], $id);
		if (($this->user[0] != $entry[1]) && ($this->user[0] != $entry[2])) {
			$errcount++;
		} else {
			if ($entry[1] == "System") {
				$entry[6] = 3;
				$entry[5] = "";
				$this->update($this->file['pm'], $id, $entry);
			} else {
				$this->delete($this->file['pm'], $id);
			}
		}
	}
	if ($errcount > 0) {
		$message = "Errors occurred during process.  You may not delete one or more of the messages you specified!";
	} else {
		$s = "s";
		if ($mescount == 1) {
			$s = "";
		}
		$message = "Message$s deleted.";
	}
        ?></table><br><?
        $this->displaymessage("Delete Message", $message);
}

function deletechecked1 () {
        global $message;
	if (count($message) == 0) {
        	$this->displaymessage("Delete Message", "You must select one or more messages to delete.");
		$this->footer();
		die();
	}
	$i = 0;
	$count = count($message);
	foreach ($message as $id) {
		$i++;
		if ($i == $count) {
			$dtext = $dtext.$id;
		} else {
			$dtext = $dtext.$id.",";
		}
	}
        ?></table><br><?
        $this->displaymessage("Delete Message", "Are you sure you wish to delete the selected item(s)? (<a href=\"index.php?a=messenger&type=deletechecked2&message=".$dtext."\">Yes</a> | <a href=\"index.php?a=messenger&type=inbox\">No</a>)");
}

function checklogin () {
        if ($this->user[0] == 0){
                $this->displaymessage("Error", "You are not logged in.");
                $this->footer();
                die();
        }
}

function viewMessage () {
        global $id, $res, $type, $cuser;
        $message = $this->select($this->file['pm'], $id);
        if (($this->user[0] != $message[1]) && ($this->user[0]) != $message[2]) {
                $this->displaymessage("Error", "You may not view this message.");
                $this->footer();
                die();
        } else {
                if (($this->user[0] == $message[2]) && ($message[6] != 1)) {
                        $message[6] = 1;
                        $this->update($this->file['pm'], $message[0], $message);
                }
		if ($message[1] == "System") {
			$from = array("", "System");
		} else {
                	$from = $this->select($this->file['users'], $message[1]);
		}
                $to = $this->select($this->file['users'], $message[2]);
                $message[5] = $this->tags($message[5]);
		if ($from[0] == $this->user[0]) {
			$delete = "Unsend";
		} else {
			$delete = "Delete";
		}
                ?>
                <tr>
                 <td class="ctable1" width="15%">From:</td>
                 <td class="ctable1" width="85%" colspan=4><?=$from[1]?></td>
                </tr>
                <tr>
                 <td class="ctable2" width="15%">To:</td>
                 <td class="ctable2" width="85%" colspan=4><?=$to[1]?></td>
                </tr>
                <tr>
                 <td class="ctable1" width="15%">Subject:</td>
                 <td class="ctable1" width="85%" colspan=4><?=$message[4]?></td>
                </tr>
                <tr>
                 <td class="ctable2" width="15%">Message:</td>
                 <td class="ctable2" width="85%" colspan=4><?=str_replace("&lt;br&gt;", "<br>\n", $message[5]);?></td>
                </tr>
                <tr>
                 <td class="ctable2" width="15%">Actions:</td>
                 <td class="ctable2" width="85%" colspan=4><a href="index.php?a=messenger&type=delete&<?if ($delete != "Delete") { ?>r=unsend&<? } ?>id=<?=$id?>"><?=$delete?></a> | <a href="index.php?a=messenger&type=send&to=<?=$from[1]?>&quote=<?=$message[0]?>">Reply</a>| <a href="index.php?a=messenger&type=forward&to=<?=$from[1]?>&quote=<?=$message[0]?>">Forward</a></td>
                </tr>
                <?
        }
}

function displaylinks () {
global $res;
?>
<table align="center" width="100%" cellpadding="2" cellspacing="1" border="0" class="bordertable">
 <tr>
  <td colspan="100%" class="category" align="center">Messenger (<?=$res?>)</td>
 </tr>
 <tr>
  <td colspan="100%" class="ctable1" align="center"><a href="index.php?a=messenger&type=inbox">Inbox</a> | <a href="index.php?a=messenger&type=outbox">Outbox</a> | <a href="index.php?a=messenger&type=send">Send A New Message</a></td>
 </tr>
<?
}

function inbox () {
        $messages = $this->selectwhere($this->file['pm'], 2, $this->user[0]);
        if ($messages[0][0] == "") {
                ?>
        <tr>
        <td class="ctable1" width="4%"></td>
        <td class="ctable1" width="4%"></td>
        <td class="ctable2" width="38%">Subject</td>
        <td class="ctable1" width="17%" align="center">From</td>
        <td class="ctable2" width="17%" align="center">To</td>
        <td class="ctable1" width="24%" align="center">Date</td>
        </tr>
                <tr>
                 <td colspan="100%" class="ctable2"><center>You have no messages.</center></td>
                </tr>
                <?
        } else {
                $this->displaymessages($messages);
        }
}

function displaymessages ($messages) {
        $messages = array_reverse($messages);
        ?>
	<form method="post" action="index.php">
	<input type="hidden" name="a" value="messenger">
	<input type="hidden" name="type" value="deletechecked">
        <tr>
        <td class="ctable1" width="4%"></td>
        <td class="ctable1" width="4%"></td>
        <td class="ctable2" width="38%">Subject</td>
        <td class="ctable1" width="17%" align="center">From</td>
        <td class="ctable2" width="17%" align="center">To</td>
        <td class="ctable1" width="24%" align="center">Date</td>
        </tr>
        <?
	$i = 0;
        foreach ($messages as $message) {
		if ($message[1] == "System") {
			$from = array("", "System");
		} else {
                	$from = $this->select($this->file['users'], $message[1]);
		}
                $to = $this->select($this->file['users'], $message[2]);
                if ($message[6] != 1) {
                        $folderimg = "foldernew.gif";
			$foldertitle = "New Message";
                } else {
                        $folderimg = "folderold.gif";
			$foldertitle = "Old Message";
                }
		if ($message[6] != 3) {
		$i++;
                ?>
                <tr>
                <td class="ctable1" width="4%"><img title="<?=$foldertitle?>" src="images/<?=$folderimg?>"></td>
		<td class="ctable1" width="4%"><input type="checkbox" name="message[]" value="<?=$message[0]?>"></td>
                <td class="ctable2"><a href="index.php?a=messenger&type=view&id=<?=$message[0]?>"><?=$message[4]?></a></td>
                <td class="ctable1" align="center"><?if ($from[1] != "System") { ?><a href='index.php?a=messenger&type=send&to=<?=$from[1]?>'><? } ?><?=$from[1]?><?if ($from[1] != "System") { ?></a><? } ?></td>
                <td class="ctable2" align="center"><a href='index.php?a=messenger&type=send&to=<?=$to[1]?>'><?=$to[1]?></a></td>
                <td class="ctable1" align="center"><?=$this->bdate($this->timeformat, $message[3])?></td>
                </tr>
		<?
		}
        }
		if ($i == 0) {
?>
                <tr>
                 <td colspan="100%" class="ctable2"><center>You have no messages.</center></td>
                </tr>
<?
	} else {
?>
                <td class="ctable1" colspan="100%"><center><?=$this->button("Delete Checked Messages");?></center></td>
                </tr>
                </table>
                <br></form>
<? } ?>        <table border="0" cellpadding="2" cellspacing="1" align="center" class="bordertable">
         <tr>
          <td align="center" class="ctable1">
           <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                 <td><span class="regular">Old Message</span></td>
                 <td><img src="images/folderold.gif"></td>
                </tr>
           </table>
          </td>
          <td align="center" class="ctable1">
           <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                 <td><span class="regular">New Message</span></td>
                 <td><img src="images/foldernew.gif"></td>
                </tr>
           </table>
          </td>
         </tr>
<?
}

function outbox () {
        $messages = $this->selectwhere($this->file['pm'], 1, $this->user[0]);
        if ($messages[0][0] == "") {
                ?>
                <tr>
                 <td colspan="100%" class="ctable2">You have no messages.</td>
                </tr>
                <?
        } else {
                $this->displaymessages($messages);
        }
}

function sendMessage2 () {
        global $subject, $message, $to;
	$mo = explode(", ", $to);
	$tousers = array();
	$nonsend = array();
	$i = 1;
	foreach ($mo as $tou) {
		if ($i > $this->options3[3]) {
			array_push($nonsend, $tou);
		} else {
        		array_push($tousers, $this->selectwhere($this->file['users'], 1, $tou));
		}
		$i++;
	}
        $redirect = "";
        if ($this->validate(15) == 0) {
                $message = "Could not validate user. Either you are not logged in, or you do not have permissions to post.";
        } elseif ($this->validate(15) == 2) {
                $message = "You are not logged in. Guest posting is not allowed.";
        } elseif ($message == "") {
                $message = "You did not type in a message.";
        } elseif ($subject == "") {
                $message = "You did not type in a subject.";
        } else {
                $subject = $this->inputize($subject, 25);
                $message = $this->inputize($message, $this->maxpostsize, $this->postwrap);
		foreach ($tousers as $touser) {
			if ($touser[0][0] != "") {
                		$newmessage = array($this->user[0], $touser[0][0], time(), $subject, $message, 0);
                		$this->insert($this->file['pm'], $newmessage);
			} else {
				$x = "  There was at least one invlaid username so the message may not have reached all recipiants.";
			}
		}
		if (count($nonsend) != 0) {
			$nmessage .= "\nThe message was not sent to the following ".count($nonsend)." of ".count($mo)." users because you have exceeded the max amount ";
			$nmessage .= "of recipiants (".(count($mo) - count($nonsend)).") for a private message!<br>\n";
			foreach ($nonsend as $user) {
				$nmessage .= $user."<br>\n";
			}
		}
                $message = "Message ".$subject." sent to '".$to."'.".$x."<br>".$nmessage;
        }
?></table><br><?
        $this->displaymessage("Send Message", $message);
}

function sendMessage () {
        global $to, $quote, $type;
	if ($type == "forward") {
		$to = "";
		$s = "Fwd: ";
		?>	  <script language=javascript><!--
	var i = 0;
	//--></script><?
	} elseif ($type == "send" && isset($quote)) {
		$s = "Re: ";
		?>	  <script language=javascript><!--
	var i = 1;
	//--></script><?
	} else {
		?>	  <script language=javascript><!--
	var i = 0;
	//--></script><?
	}
	if ($to == "System") {
		$this->displaymessage("Error", "You may not reply to System messages.");
		$this->footer;
		die();
	}
        if ($quote != "") {
                $quote = $this->select($this->file['pm'], $quote);
		if ($this->user[0] != $quote[2] && $this->user[0] != $quote[1]) {
			$this->displaymessage("Error", "You cannot access someone else's Private Messages.");
			$this->footer();
			$this->endmessenger();
			die();
		} else {
                	$reply = $s.$quote[4];
			if ($quote[1] == "System") {
				$user = array("", "");
				$to = "";
			} else {
				$user = $this->select($this->file['users'], $quote[1]);
			}
                	$quote = "\n\n[quote][i]Originally sent by[/i] [b]".$user[1]."[/b]\n".$quote[5]."[/quote]";
		}
	}
        $ulists = $this->selectsort($this->file['users'], 1);
        foreach ($ulists as $ulist) {
		if (($ulist[0] != "0") && ($ulist[0] != "-1") && ($ulist[1] != "") && ($ulist[1] != " ")) {
			if (!strstr($ulist[1],"?>")) {
	                $select = $select."\t<option value=\"".$ulist[1]."\">".$ulist[1]."</option>\n";
			}
		}
        }
?>
        <form action="index.php" name="sendmessage" method="post">
        <input type="hidden" name="a" value="messenger">
        <input type="hidden" name="type" value="send2">
     <tr>
          <td colspan=5 class="category" align="center">Sending A Message</td>
         </tr>
         <tr>
          <td class="ctable2" width=150>Logged in as:</td>
          <td class="ctable2" colspan="4"><?=$this->user[1]?></td>
         </tr>
         <tr>
          <td class="ctable1" width=150>To:</td>
          <td class="ctable1" colspan="4">
	  <table width="100%" border=0 cellpadding=0 cellspacing=0><tr><td width="50%">
	  <input size=30 value="<?=$to?>" name="to" onclick="char()" onKeyUp="char()"></td><td width="50%">
<?
// i wonder how nitsuj does this stuff ;)
?>
	  <script language=javascript>
<!--
function quickadd(qad) {
	if (i > 0) {
		document.sendmessage.to.value = document.sendmessage.to.value + ", " + qad;
	}
	if (i == 0) {
		document.sendmessage.to.value = document.sendmessage.to.value + qad;
	}
	i = i + 1;
}
function char(qad) {
	if (document.sendmessage.to.value == "") {
		i = 0;
	}
	if (document.sendmessage.to.value != "") {
		i = 1;
	}
}
//-->
	  </script>
          <select name="qa">
          <?=$select?></select> <?
	echo str_replace("submit", "button", $this->bt);
	echo "Quick Add   \" title=\"Click to add the currently selected user to the recipiants field.\" onclick=\"quickadd(document.sendmessage.qa.value);";
	echo str_replace("   ", "", $this->be);
?></td></tr></table></td>
         </tr>
         <tr>
          <td class="ctable1" width=150>Subject:</td>
          <td class="ctable1" colspan="4"><input size=30 maxlength="25" value="<?=$reply?>" name="subject"></td>
         </tr>
         <tr>
          <td class="ctable1" valign="top" width=150>Message</td>
          <td class="ctable1" colspan="4"><textarea name="message" cols=80 rows=8><?=$quote?></textarea></td>
         </tr>
         <tr>
          <td colspan=5 align=center class="ctable2"><?$this->button("Send")?></td>
         </tr>
        </form>
        </table>
<?
}

function endmessenger () {
?>
</table>
<?
}

}

?>