<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

class install {

	function begin () {
		global $state;
		$this->dbname = "genesis.edb";
		if (file_exists("data/DO-NOT-DELETE-Genesis.php") ||
		   !file_exists($this->dbname)) {
			die();
		}
		$this->next_url = "#";
		$this->back_url = "#";
		$this->native = false;
		if ($state == "reg") {
			$this->next_url = "install.php?state=reg";
			$this->back_url = "#";
			$this->native = true;
			$this->header();
			$this->display_reg();
		} 
		elseif ($state == "reg2") {
			$this->next_url = "index.php?a=guest&type=login";
			$this->back_url = "#";
			$this->header();
			$this->reg2();
		} 
		else {
			$this->next_url = "install.php?state=reg";
			$this->back_url = "#";
			$this->set_file_system($this->dbname);
			$this->header();
			$this->display_welcome();
		}
		$this->footer();
	}

	function set_file_system ($db) {
		$db = file($db);
		$db = implode("", $db);
		$this->main_database = unserialize($db);
		foreach ($this->main_database as $minor_database) {
			$this->add_to_fs($minor_database[0], $minor_database[1]);
		}
	}
	
	function add_to_fs ($filename, $filecontents) {
		$filecontents = $this->deinputize($filecontents);
		$directory_structure = explode("/", $filename);
		foreach ($directory_structure as $structure) {
			$current_dir = $current_dir.$structure."/";
			if (strstr($structure, ".")) {
				$this->writefile($filename, $filecontents);
			} else {
				if (!file_exists($current_dir)) {
					mkdir($current_dir, 0777);
					chmod($current_dir, 0777);
				}
			}
		}
	}
	
	function deinputize ($text) {
		$text = str_replace("#gt", ">", $text);
		$text = str_replace("#lt", "<", $text);
		$text = str_replace("#12", "|", $text);
		return $text;
	}
	
	function writefile ($filename, $filecontents) {
		$handle = fopen($filename, "w");
		fwrite($handle, $filecontents);
		fclose($handle);
		chmod($filename, 0777);
	}
	
	function header () {
		$style = file("html/system/style.php");
		$style = implode("\t", $style);
		?>
<HTML>
<HEAD>
<TITLE>edgeBoard Installer</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<?=$style?>
</HEAD>
<BODY BGCOLOR=#FFFFFF LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>
<TABLE WIDTH="100%" HEIGHT="98%" BORDER="0" CELLSPACING="0" CELLPADDING="0">
  <TR>
	<TD><CENTER>
		<TABLE WIDTH=600 BORDER=0 CELLPADDING=0 CELLSPACING=0>
		  <TR> 
			<TD COLSPAN=3> <A HREF="http://www.edgeboard.net/"><IMG BORDER="0" SRC="images/installer_01.jpg" WIDTH=320 HEIGHT=107 ALT=""></A></TD>
			<TD COLSPAN=3> <IMG SRC="images/installer_02.jpg" WIDTH=280 HEIGHT=107 ALT=""></TD>
		  </TR>
		  <TR> 
			<TD ROWSPAN=3> <IMG SRC="images/installer_03.gif" WIDTH=8 HEIGHT=293 ALT=""></TD>
			<TD COLSPAN=2> <IMG SRC="images/installer_04.jpg" WIDTH=312 HEIGHT=19 ALT=""></TD>
			<TD COLSPAN=2> <IMG SRC="images/installer_05.jpg" WIDTH=269 HEIGHT=19 ALT=""></TD>
			<TD ROWSPAN=3> <IMG SRC="images/installer_06.gif" WIDTH=11 HEIGHT=293 ALT=""></TD>
		  </TR>
		  <TR> 
			<TD VALIGN="TOP" BACKGROUND="images/installer_07.gif" COLSPAN=4 WIDTH=581 HEIGHT=188><SPAN CLASS='regular'>
		<?
	}
	
	function footer () {
		$back_url = "javascript:alert('This has been disabled')";
		$next_url = $this->next_url;
		$foot_end = $this->footer_end;
		if (isset($this->next)) {
			$next = $this->next;
		} else {
			$next = '<A HREF="'.$next_url.'"><IMG ALT="Next ->" BORDER="0" SRC="images/installer_10.jpg" WIDTH=122 HEIGHT=86></A>';
		}
		?>
			</SPAN></TD>
		  </TR>
		  <TR> 
			<TD><A HREF="<?=$back_url?>"><IMG ALT="<- Back" BORDER="0" SRC="images/installer_08.jpg" WIDTH=122 HEIGHT=86></A></TD>
			<TD COLSPAN=2><A HREF="http://www.edgeprogramming.com/"><IMG BORDER="0" SRC="images/installer_09.jpg" WIDTH=337 HEIGHT=86 ALT=""></A></TD>
			<TD><?=$next?></TD>
		  </TR>
		  <TR> 
			<TD> <IMG SRC="images/spacer.gif" WIDTH=8 HEIGHT=1 ALT=""></TD>
			<TD> <IMG SRC="images/spacer.gif" WIDTH=122 HEIGHT=1 ALT=""></TD>
			<TD> <IMG SRC="images/spacer.gif" WIDTH=190 HEIGHT=1 ALT=""></TD>
			<TD> <IMG SRC="images/spacer.gif" WIDTH=147 HEIGHT=1 ALT=""></TD>
			<TD> <IMG SRC="images/spacer.gif" WIDTH=122 HEIGHT=1 ALT=""></TD>
			<TD> <IMG SRC="images/spacer.gif" WIDTH=11 HEIGHT=1 ALT=""></TD>
		  </TR>
		</TABLE>
	  </CENTER><SPAN CLASS="small">&copy; 2003 edgePrograming.com</SPAN></TD>
  </TR>
</TABLE><?=$foot_end?><BR>
</BODY>
</HTML>
		<?
	}
	
	function display_welcome () {
		?>
Welcome to edgeBoard, the first fearure-rich flat-file database Bulletin Board System!  edgeBoard 
was put together as an alternative to the unoppositioned MySQL board.  Now because of edgeBoard, webmasters and developers across the 
internet can have a feature-rich discussion board without paying their hosts tons of money for MySQL support. edgeBoard is now a 
Bulletin Board veteran because of fan support.<BR><BR>
Now it's your turn!  You now have your very own edgeBoard to use at your website.  edgeBoard is very versatile and is 
virtually garunteed to fit your discussion board needs.  This BBS (Bulletin Board System) includes the following and many more features:<BR>
Themes, ACP (Administrator's Control Panel), BBCodes, board status alerts, changeable toplinks, attachment uploads, 
IAI (Integrated Admin Inerface), moderators, user groups, configurable pips, user profiles, private message system, live IP address logging, 
user banishment, user management, access log, quoteable/editable/deleteable posts, polls, and much more!
		<?
	}
	
	function display_reg ($message="") {
		global $username, $pw1, $pw2, $email;
		?>
<? if ($this->native) { ?>Now we need to set your administration account.  Keep in mind that this username will be the Master username, 
that administrator permissions cannot be removed from!  No matter what usergroup you are a part of or moved to, you will still have 
administrator permissions.<BR><BR><? } else { echo $message."<BR><BR>"; } ?>
<FORM ACTION="install.php" METHOD="POST">
<INPUT TYPE="HIDDEN" NAME="state" VALUE="reg2">
Username: <INPUT TYPE="TEXT" NAME="username" VALUE="<?=$username?>"><BR>
Password: <INPUT TYPE="PASSWORD" NAME="pw1" VALUE="<?=$pw1?>"><BR>
Confirm Password: <INPUT TYPE="PASSWORD" NAME="pw2" VALUE="<?=$pw2?>"><BR>
Email Address: <INPUT TYPE="TEXT" NAME="email" VALUE="<?=$email?>">
</SPAN>
		<?
		$this->footer_end = "</FORM>";
		$this->next = <<<LONG
<INPUT TYPE="IMAGE" ALT="Next ->" BORDER="0" SRC="images/installer_10.jpg" WIDTH=122 HEIGHT=86>
LONG;
	}
	
	function reg2 () {
		global $username, $email, $pw1, $pw2;
		$errors = true;
		$message = "There was an error with your registration:<BR>\r\n";
		if (empty($email)) {
			$message .= "Your email must not be blank!";
		} elseif (empty($username)) {
			$message .= "Your username must not be blank!";
		} elseif (empty($pw1)) {
			$message .= "Your password must not be blank!";
		} elseif ($pw1 != $pw2) {
			$message .= "Your passwords do not match!";
		} else {
			$errors = false;
			$style = file("html/system/style.php");
			$style = implode("\t", $style);
			echo $style;
			include "lib/flatfile.php";
			$flatfile = new flatfile;
			$flatfile->setdb();
			$password = md5($pw1);
			$user = array($flatfile->inputize($username, 35), $password, 1, 
			$flatfile->inputize($email, 60), NULL, NULL, NULL, NULL, NULL, time(), 0, 
			NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, time(), "OPTIONAL");
			$flatfile->insert("data/users.php", $user);
			$flatfile->writefiles("data/users.php");
			?>
The installer has completed!  You now have your own edgeBoard!  You can now login
by clicking the "Next" button.  Enjoy!<BR>
<BR>
<B>Remember:  You can always get support by clicking the "Get Support" link under the tools menu of the 
Administrator's Control Panel.</B>
			<?
			copy ("index/index.php", "index.php");
			copy ("index/acp.php", "acp.php");
			copy ("index/ucp.php", "ucp.php");
			copy ("data/sessions/blank_archive.php", "data/DO-NOT-DELETE-Genesis.php");
			chmod("data/DO-NOT-DELETE-Genesis.php", 0777);
			@unlink($this->dbname);
		}
		if ($errors) {
			$this->display_reg($message);
		}
	}

}

$installer = new install;
$installer->begin();

?>