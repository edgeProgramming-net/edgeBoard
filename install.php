<html>
<head>
<title>Install edgeBoard</title>
<style type="text/css">
<?
include "data/style.php";
?>
</style>
</head>
<body>
<span class="regular">
<?
function encrypt ($string) {
        return md5($string);
}

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
        $text = str_replace(">", "&gt;", $text);
        $text = str_replace("<", "&lt;", $text);
        $text = str_replace("\n", "<br>", $text);
        $text = str_replace("|", "&#124;", $text);
        return $text;
}

if (isset($a)) {
        global $username, $password, $password2, $email;
	$this->directory['data/'] = "data/";
	$this->directory['backup/'] = "backup/";
	$this->directory['html/'] = "html/";
	$this->directory['backup/data/'] = "backup/data/";
	$this->directory['backup/forums/'] = "backup/forums/";
	$this->directory['data/logs/'] = "data/logs/";
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
	foreach ($this->directory as $directory) {
		chmod($directory, 0777);
	}
	foreach ($this->file as $file) {
		chmod($file, 0777);
	}
        if ($password != $password2) {
                $message = "Password did not match. Please go back and try again.";
        } elseif (isset($otherusername[0][0])) {
                $message = "Sorry, that username is already taken.";
        } elseif (strlen($username) > 35) {
                $message = "Username too long.  Max 30 characters.";
        } elseif ($username == "") {
                $message = "You did not enter a username.";
        } elseif ($password == "") {
                $message = "You did not enter a password.";
        } elseif (strtoupper($username) == "GUEST") {
                $message = "Your username cannot be 'Guest'.";
        } elseif (strtoupper($username) == "SYSTEM") {
                $message = "Your username cannot be 'System'.";
        } else {
                $user = (inputize($username, 35)."|".encrypt($password)."|1|".inputize($email, 60)."|||||||0");
                $save = $save = fopen($this->file['users'], "a+");
                fputs($save, "1|".$user."\n");
                fclose($save);
                $message = "You have been registered. You may now <a href=\"index.php?a=member&type=login&d=1\">login</a>.";
        }
?>
<table align=center width=400 cellpadding=2 cellspacing=1 class="bordertable">
<form action="install.php" method="post">
<input type="hidden" name="a" value="newadmin">
 <tr>
  <td class="category" align="center">Install edgeBoard</td>
 </tr>
 <tr>
  <td class="ctable1"><?=$message?><br>
  <span class="regular">
	If you recieved any PHP script errors during install, you will have to consult the <a href="http://edgeboard.net/docs/f.php">edgeBoard CHMOD File List</a>!
  </span>
  </td>
 </tr>
</form>
</table>
<?
} else {
?>
<table align=center width=400 cellpadding=2 cellspacing=1 class="bordertable">
<form action="install.php" method="post">
<input type="hidden" name="a" value="newadmin">
 <tr>
  <td class="category" colspan="2" align="center">Install edgeBoard</td>
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
  <td class="ctable1" colspan=2 align=center><input type=submit value="   Register Administrator   "></td>
 </tr>
</form>
</table>
<?
}
?>
</span>
<?
        $this->executetime = "unavailable";
        include "html/footer.php";
?>
</body>
</html>