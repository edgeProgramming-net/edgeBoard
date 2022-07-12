<?php
/***********************************************************\
* +-------------------------------------------------------+ *
* | Developers: Deltawolf, Frost, ThUgZnHaRmOnY, nitsuj,  | *
* | CPlusPlusGuru, minipol, RM, MPHH, GreatNexus	  | *
* +-------------------------------------------------------+ *
* | © edge-programming.com				  | *
* | You may NOT alter this script for your own purposes,  | *
* | and you may not redistribute this script in any form. | *
* | This script is provided without any warranty of any	  | *
* | kind. Use at your own risk.				  | *
* +-------------------------------------------------------+ *
\***********************************************************/

ini_set ("register_globals", "1");
ini_set ("safe_mode", "0");
ini_set ("open_basedir", "0");
ob_start("gzhandler");

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

if ($a == "") {
	$a = "main";
}

$include = "extention/files/$a.php";

if (file_exists($include)) {	
	include "extention/exboard.php";
	include $include;
	$module = new module;
	$module->declarefiles();
	$module->declareoptions();
	$module->start();
	$module->writefiles();
}

ob_end_flush();
?>