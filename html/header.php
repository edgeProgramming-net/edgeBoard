<HTML>
<HEAD>
<TITLE><?=$this->boardname?> :: edgeBoard<?php 
if (isset($this->listtopic[1])) {
	echo(" :: ".$this->listtopic[1]); 
} elseif (isset($this->forum)) { 
	$forum = $this->select("forums/forums.cgi", $this->forum); 
	echo(" :: ".$forum[1]); 
}
?></TITLE>
<?=$this->head()?>
</HEAD>
<body onunload="exit()" bgcolor="#FFFFFF" text="#000000" link="#CC0000" vlink="#CC0000" alink="#CC0000">
<table width=100% cellpadding=0 cellspacing=0 border=0>

<table width=100% cellpadding=0 cellspacing=0 border=0>

<TR>
<td align="center" valign="middle" width="75%"><img src="images/logo.gif"><br>
<span class="regular"><?=$this->toplinks()?></span>
</td>

<TD align="right">

<table width="240" cellspacing="0" cellpadding="10" background="images/usercpbg.gif" height="130">
<TR>
<TD height="5">
</td>
</tr>

<TR>
<TD>
<b><span class="usercp">
<?$this->usercp()?>
</span>
</td>
</tr>
</table>

</td>
</tr>
</table>
