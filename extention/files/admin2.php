<?
if ($this->validate(14) == 1) {
	if ($setting == 1) {
        $titles = $this->selectall($this->file['titles']);
?>
       <table width="100%" cellpadding=2 cellspacing=1 class="bordertable">
         <tr>
          <td class="ctable1" align="center" width="4%"></td>
          <td class="ctable1" width="53%">Title</td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="7%"></td>
          <td class="ctable1" align="center" width="29%"></td>
         </tr>
         <tr>
          <td colspan=5 class="category"><?=$this->boardname?> Titles [ <a class="catlink" href="index.php?a=admin&type=title3" class="catlink">Add Title</a> ]</td>
         </tr>
         <tr>
          <td colspan=5 class="ctable2">If a title is set to :UserSet: then the user will be able to set their own title.<br>
	  If a title is set to :Previous: then the previous title will remain in effect, but 
	  there is a max of two that can be consecutive.</td>
         </tr>
<?
	$save = $this->bt."Save".$this->be;
	$rename = $this->bt."Rename".$this->be;
        $current = 1;
        foreach ($titles as $title) {
		$remove = "<a href='index.php?a=admin&type=title5&titleid=$title[0]'>Delete Title</a>";
?>
	 <form method="post" action="index.php">
	 <input type="hidden" name="a" value="admin">
	 <input type="hidden" name="type" value="title4">
	 <input type="hidden" name="titleid" value="<?=$title[0]?>">
         <tr>
          <td class="ctable<?=$current?>" align="center" width="4%"><img src="images/forumnew.gif"></td>
          <td class="ctable<?=$current?>" colspan="4" width="53%">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="regular">
                 <tr>
                  <td><input type="text" size="20" name="title" value="<?=$title[2]?>"> [ <?=$rename?> or <?=$remove?> ]</td>
	 </form>
	 <form method="post" action="index.php">
	 <input type="hidden" name="a" value="admin">
	 <input type="hidden" name="type" value="title2">
	 <input type="hidden" name="titleid" value="<?=$title[0]?>">
                  <td align="right">[ Required Number of Posts: <input type="text" name="posts" size="5" value="<?=$title[1]?>"> | Number of Pips: <input type="text" name="pips" size="2" value="<?=$title[3]?>"> <?=$save?> ]</td>
                 </tr>
              </table>
          </td>
	 </tr>
	 </form>
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
	} elseif ($setting == 2) {
		$titledb = $this->select($this->file['titles'], $titleid);
		$titledb[1] = $this->inputize($posts);
		$titledb[3] = $this->inputize($pips);
		$this->update($this->file['titles'], $titleid, $titledb);
		$this->displaymessage("Edit Title", "Title's posts set to ".$posts." and pips set to ".$pips.".");
	} elseif ($setting == 3) {
		global $state;
		if ($state == 1) {
			$newtitle = array($this->inputize($posts), $this->inputize($title), null);
			$this->insert($this->file['titles'], $newtitle);
			$this->displaymessage("Create Title", "Title ".$title." created.");
		} else {
		        $form =        "<form action=\"index.php\" method=\"post\">\n";
	        	$form = $form . "<input type=\"hidden\" name=\"a\" value=\"admin\">\n";
		        $form = $form . "<input type=\"hidden\" name=\"type\" value=\"title3\">\n";
	        	$form = $form . "<input type=\"hidden\" name=\"state\" value=\"1\">\n";
		        $form = $form . "Title Name: <input name=\"title\" size=\"25\"><br>\n";
	        	$form = $form . "Posts Required for this Title: <input name=\"posts\" size=\"25\" value=\"0\"><br>\n";
	        	$form = $form . "<@=$this->button(Create Title)@></form>\n";
		        $this->displaymessage("Create Title", $form);
		}
	} elseif ($setting == 4) {
		$titledb = $this->select($this->file['titles'], $titleid);
		if (strtoupper($title) == ":USERSET:") { $title = ":UserSet:"; }
		if (strtoupper($title) == ":PREVIOUS:") { $title = ":Previous:"; }
		$titledb[2] = $this->inputize($title);
		$this->update($this->file['titles'], $titleid, $titledb);
		$this->displaymessage("Rename Title", "Title renamed to ".$title.".");
	}	
} else {
	$this->displaymessage("Error", "You may not access the Administrator's Control Panel");
	$this->footer();
	die();
}
?>