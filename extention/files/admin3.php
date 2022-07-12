<?
if ($this->validate(14) == 1) {
?>
<table width="100%" cellpadding="2" cellspacing="1" class="bordertable">
<form action="index.php" method="post">
    <input type="hidden" name="a" value="admin">
        <input type="hidden" name="type" value="general2">
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Board Name:</font></td>
      <td class="ctable1" valign="top"><input TYPE="TEXT" NAME="brdnm" value="<?=$this->options1[1]?>" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Change Board Status:</font></td>
      <td class="ctable1" valign="top"><select name="stats">
					<option <? if (strstr($this->currentstatus,"000")) { ?>SELECTED <? } ?>value="000">Currently Open</option>
					<option <? if (strstr($this->currentstatus,"401")) { ?>SELECTED <? } ?>value="401">Temporarily Closed</option>
					<option <? if (strstr($this->currentstatus,"400")) { ?>SELECTED <? } ?>value="400">Permanently Closed</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Allow Table Rollover Effect?</font></td>
      <td class="ctable1" valign="top"><select name="rollo">
					<option <? if ($this->options2[8] == "true") { ?>SELECTED <? } ?>value="true">Yes</option>
					<option <? if ($this->options2[8] == "false") { ?>SELECTED <? } ?>value="false">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Table Rollover Color: (add no "#" to indicate hex value)</font></td>
      <td class="ctable1" valign="top"><input TYPE="TEXT" NAME="rcolo" maxlegnth="6" value="<?=$this->options2[7]?>" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Allow Guest Posting?</font></td>
      <td class="ctable1" valign="top"><select name="allgu">
					<option <? if ($this->options2[6] == "true") { ?>SELECTED <? } ?>value="true">Yes</option>
					<option <? if ($this->options2[6] == "false") { ?>SELECTED <? } ?>value="false">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Maximum Private Message Recipiants:</font></td>
      <td class="ctable2" valign="top"><input TYPE="TEXT" value="<?=$this->options3[3]?>" NAME="maxpm" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Website Homepage Address:</font></td>
      <td class="ctable2" valign="top"><input TYPE="TEXT" value="<?=$this->options3[2]?>" NAME="hmurl" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Maximum Avatar Height:</font></td>
      <td class="ctable2" valign="top"><input TYPE="TEXT" value="<?=$this->options1[3]?>" NAME="avath" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Maximum Avatar Width:</font></td>
      <td class="ctable1" valign="top"><input TYPE="TEXT" value="<?=$this->options1[2]?>" NAME="avatw" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Maximum Length of a
      Topic's Subject:</font></td>
      <td class="ctable2" valign="top"><input TYPE="TEXT" value="<?=$this->options1[4]?>" NAME="topcl" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Bad Word Filter:</font><br><font SIZE="2">\\(the words you want to filter, seperated by commas "badword,anotherone,andanother".)</font></td>
      <td class="ctable2" valign="top"><input TYPE="TEXT" value="<?=$this->options1[9]?>" NAME="wrdfl" SIZE="20" ></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%">
      <font SIZE="2">Time Format:</font><br>\\(for help with
      this, visit: <a href="http://www.php.net/manual/en/function.date.php">
      <span style="color: #180080">this page</span></a>)</font></td>
      <td class="ctable1" valign="top"><input TYPE="TEXT" value="<?=$this->options1[5]?>" NAME="timef" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%">
      <font SIZE="2">Time Offset:</font><br>\\(use this when your 
       web server is not in your same time zone)</font></td>
      <td class="ctable1" valign="top"><select name="offst">
					  <option <? if ($this->options3[4] == "86400") { ?>SELECTED <? } ?>value="86400">+23 Hours</option>
					  <option <? if ($this->options3[4] == "82800") { ?>SELECTED <? } ?>value="82800">+22 Hours</option>
					  <option <? if ($this->options3[4] == "79200") { ?>SELECTED <? } ?>value="79200">+21 Hours</option>
					  <option <? if ($this->options3[4] == "75600") { ?>SELECTED <? } ?>value="75600">+20 Hours</option>
					  <option <? if ($this->options3[4] == "72000") { ?>SELECTED <? } ?>value="72000">+19 Hours</option>
					  <option <? if ($this->options3[4] == "64800") { ?>SELECTED <? } ?>value="64800">+18 Hours</option>
					  <option <? if ($this->options3[4] == "61200") { ?>SELECTED <? } ?>value="61200">+17 Hours</option>
					  <option <? if ($this->options3[4] == "57600") { ?>SELECTED <? } ?>value="57600">+16 Hours</option>
					  <option <? if ($this->options3[4] == "54000") { ?>SELECTED <? } ?>value="54000">+15 Hours</option>
					  <option <? if ($this->options3[4] == "50400") { ?>SELECTED <? } ?>value="50400">+14 Hours</option>
					  <option <? if ($this->options3[4] == "46800") { ?>SELECTED <? } ?>value="46800">+13 Hours</option>
					  <option <? if ($this->options3[4] == "43200") { ?>SELECTED <? } ?>value="43200">+12 Hours</option>
					  <option <? if ($this->options3[4] == "39600") { ?>SELECTED <? } ?>value="39600">+11 Hours</option>
					  <option <? if ($this->options3[4] == "36000") { ?>SELECTED <? } ?>value="36000">+10 Hours</option>
					  <option <? if ($this->options3[4] == "32400") { ?>SELECTED <? } ?>value="32400">+9 Hours</option>
					  <option <? if ($this->options3[4] == "28800") { ?>SELECTED <? } ?>value="28800">+8 Hours</option>
					  <option <? if ($this->options3[4] == "25200") { ?>SELECTED <? } ?>value="25200">+7 Hours</option>
					  <option <? if ($this->options3[4] == "21600") { ?>SELECTED <? } ?>value="21600">+6 Hours</option>
					  <option <? if ($this->options3[4] == "18000") { ?>SELECTED <? } ?>value="18000">+5 Hours</option>
					  <option <? if ($this->options3[4] == "14400") { ?>SELECTED <? } ?>value="14400">+4 Hours</option>
					  <option <? if ($this->options3[4] == "10800") { ?>SELECTED <? } ?>value="10800">+3 Hours</option>
					  <option <? if ($this->options3[4] == "7200") { ?>SELECTED <? } ?>value="7200">+2 Hours</option>
					  <option <? if ($this->options3[4] == "3600") { ?>SELECTED <? } ?>value="3600">+1 Hour</option>
					  <option <? if ($this->options3[4] == "0") { ?>SELECTED <? } ?>value="0">+0 Hours</option>
					<option <? if ($this->options3[4] == "-3600") { ?>SELECTED <? } ?>value="-3600">-1 Hour</option>
					<option <? if ($this->options3[4] == "-7200") { ?>SELECTED <? } ?>value="-7200">-2 Hours</option>
					<option <? if ($this->options3[4] == "-20800") { ?>SELECTED <? } ?>value="-10800">-3 Hours</option>
					<option <? if ($this->options3[4] == "-14400") { ?>SELECTED <? } ?>value="-14400">-4 Hours</option>
					<option <? if ($this->options3[4] == "-18000") { ?>SELECTED <? } ?>value="-18000">-5 Hours</option>
					<option <? if ($this->options3[4] == "-21600") { ?>SELECTED <? } ?>value="-21600">-6 Hours</option>
					<option <? if ($this->options3[4] == "-25200") { ?>SELECTED <? } ?>value="-25200">-7 Hours</option>
					<option <? if ($this->options3[4] == "-28800") { ?>SELECTED <? } ?>value="-28800">-8 Hours</option>
					<option <? if ($this->options3[4] == "-32400") { ?>SELECTED <? } ?>value="-32400">-9 Hours</option>
					<option <? if ($this->options3[4] == "-36000") { ?>SELECTED <? } ?>value="-36000">-10 Hours</option>
					<option <? if ($this->options3[4] == "-39600") { ?>SELECTED <? } ?>value="-39600">-11 Hours</option>
					<option <? if ($this->options3[4] == "-43200") { ?>SELECTED <? } ?>value="-43200">-12 Hours</option>
					<option <? if ($this->options3[4] == "-46800") { ?>SELECTED <? } ?>value="-46800">-13 Hours</option>
					<option <? if ($this->options3[4] == "-50400") { ?>SELECTED <? } ?>value="-50400">-14 Hours</option>
					<option <? if ($this->options3[4] == "-54000") { ?>SELECTED <? } ?>value="-54000">-15 Hours</option>
					<option <? if ($this->options3[4] == "-57600") { ?>SELECTED <? } ?>value="-57600">-16 Hours</option>
					<option <? if ($this->options3[4] == "-61200") { ?>SELECTED <? } ?>value="-61200">-17 Hours</option>
					<option <? if ($this->options3[4] == "-64800") { ?>SELECTED <? } ?>value="-64800">-18 Hours</option>
					<option <? if ($this->options3[4] == "-72000") { ?>SELECTED <? } ?>value="-72000">-19 Hours</option>
					<option <? if ($this->options3[4] == "-75600") { ?>SELECTED <? } ?>value="-75600">-20 Hours</option>
					<option <? if ($this->options3[4] == "-79200") { ?>SELECTED <? } ?>value="-79200">-21 Hours</option>
					<option <? if ($this->options3[4] == "-82800") { ?>SELECTED <? } ?>value="-82800">-22 Hours</option>
					<option <? if ($this->options3[4] == "-86400") { ?>SELECTED <? } ?>value="-86400">-23 Hours</option>
</select></td>    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Maximum Post Size:</font><br><font SIZE="2">\\(the maximum
      size, in characters, of each individual post.)</font></td>
      <td class="ctable2" valign="top"><input TYPE="TEXT" value="<?=$this->options1[6]?>" NAME="pstsz" SIZE="20" ></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%">
      <font SIZE="2">Maximum Word
      Size:</font><br><font SIZE="2">\\(the maximum
      size, in characters, of a single long word.)</font></td>
      <td class="ctable1" valign="top"><input TYPE="TEXT" value="<?=$this->options1[7]?>" NAME="wrdsz" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Topics Per Page:</font></td>
      <td class="ctable2" valign="top"><input TYPE="TEXT" value="<?=$this->options1[8]?>" NAME="toppp" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Posts/Search Results Per Page:</font></td>
      <td class="ctable1" valign="top"><input TYPE="TEXT" value="<?=$this->options2[1]?>" NAME="pstpp" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Members Per Page:</font></td>
      <td class="ctable2" valign="top"><input TYPE="TEXT" value="<?=$this->options2[2]?>" NAME="mempp" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%">
      <font SIZE="2">Post Time:</font></b><br><font SIZE="2">\\(number of
      seconds to wait between posts)</font></td>
      <td class="ctable1" valign="top"><input TYPE="TEXT" value="<?=$this->options2[3]?>" NAME="postt" SIZE="20"
    style=""></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%">
      <font SIZE="2">Allow HTML?</font></b></td>
      <td class="ctable1" valign="top"><select name="ahtml">
					<option <? if ($this->options3[1] == "true") { ?>SELECTED <? } ?>value="true">Yes</option>
					<option <? if ($this->options3[1] == "false") { ?>SELECTED <? } ?>value="false">No</option>
</select></td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="ctable1"><?$this->button("Save Changes")?></td>
    </tr>
  </form>
</table>
<?
} else {
	$this->displaymessage("Error", "You may not access the Administrator's Control Panel");
	$this->footer();
	die();
}
?>