<?
if ($this->validate(14) == 1) {
        global $groupnumber;
?>
<table width="100%" cellpadding="2" cellspacing="1" class="bordertable">
        <form action="index.php" method="post">
        <input type="hidden" name="a" value="admin">
<?
	if (empty($groupnumber)) {
		$sg[19] = ":default:";
?>
        <input type="hidden" name="type" value="newgroup2">
<?
	} else {
        	$sg = $this->select($this->file['groups'], $groupnumber);
?>
        <input type="hidden" name="type" value="group3">
        <input type="hidden" name="groupnumber" value="<?=$groupnumber?>">
<?
	}
?>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Group Name:</font></td>
      <td class="ctable1" valign="top"><input NAME="g1" value="<?=$sg[1]?>" SIZE="20"></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Allow Posting?</font></td>
      <td class="ctable2" valign="top"><select name="g2">
					<option <? if ($sg[2] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[2] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Allow Replying?</font></td>
      <td class="ctable1" valign="top"><select name="g3">
					<option <? if ($sg[3] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[3] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Allow Edit Own Post?</font></td>
      <td class="ctable2" valign="top"><select name="g4">
					<option <? if ($sg[4] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[4] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Allow Edit All Posts?</font></td>
      <td class="ctable1" valign="top"><select name="g5">
					<option <? if ($sg[5] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[5] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Allow Delete Own Post?</font></td>
      <td class="ctable2" valign="top"><select name="g6">
					<option <? if ($sg[6] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[6] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Allow Delete All Posts?</font></td>
      <td class="ctable1" valign="top"><select name="g7">
					<option <? if ($sg[7] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[7] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Allow Delete Own Topic?</font></td>
      <td class="ctable2" valign="top"><select name="g8">
					<option <? if ($sg[8] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[8] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Allow Delete All Topics?</font></td>
      <td class="ctable1" valign="top"><select name="g9">
					<option <? if ($sg[9] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[9] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Allow Close Own Topic?</font></td>
      <td class="ctable2" valign="top"><select name="g10">
					<option <? if ($sg[10] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[10] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Allow Close All Topics?</font></td>
      <td class="ctable1" valign="top"><select name="g11">
					<option <? if ($sg[11] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[11] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Allow Move Own Topic?</font></td>
      <td class="ctable2" valign="top"><select name="g12">
					<option <? if ($sg[12] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[12] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Allow Move All Topics</font></td>
      <td class="ctable1" valign="top"><select name="g13">
					<option <? if ($sg[13] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[13] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Allow ACP and IAI (Integrated Admin Interface) Access?</font></td>
      <td class="ctable2" valign="top"><select name="g14">
					<option <? if ($sg[14] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[14] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Allow Send Messages?</font></td>
      <td class="ctable1" valign="top"><select name="g15">
					<option <? if ($sg[15] == "1") { ?>SELECTED <? } ?>value="1">Yes</option>
					<option <? if ($sg[15] == "0") { ?>SELECTED <? } ?>value="0">No</option>
</select></td>
    </tr>
    <tr>
      <td class="ctable2" width="50%"><font SIZE="2">Type:</font></td>
      <td class="ctable2" valign="top"><select name="g17">
        <option<? if ($sg[17] == "1") { ?> SELECTED<? } ?> value="1">Power (has large pip)</option>
        <option<? if ($sg[17] == "0") { ?> SELECTED<? } ?> value="0">Level 1 (has no large pip)</option>
       </select> <select name="g18">
        <option<? if ($sg[18] == "1") { ?> SELECTED<? } ?> value="1">Admin (Red) Pips</option>
        <option<? if ($sg[18] == "2") { ?> SELECTED<? } ?> value="2">Registered (Green) Pips</option>
        <option<? if ($sg[18] == "3") { ?> SELECTED<? } ?> value="3">Team (Blue) Pips</option>
        <option<? if ($sg[18] == "4") { ?> SELECTED<? } ?> value="4">Moderator (Yellow-Orange) Pips</option>
        <option<? if ($sg[18] == "0") { ?> SELECTED<? } ?> value="0">No Pips</option>
       </select></td>
    </tr>
    <tr>
      <td class="ctable1" width="50%"><font SIZE="2">Color:<br>//Defines the color of the users in this group in the who is online list.<br>
//:default: means default color.</font></td>
      <td class="ctable1" valign="top"><input NAME="g19" value="<?=$sg[19]?>" SIZE="20"></td>
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