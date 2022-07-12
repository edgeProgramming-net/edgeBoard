<?php
/*
* Some simple functions for working with a flat-file database. 
* You may modify this script for your own use, but please do not re-distribute it.
* Copyright edge-programming.com
*/

class flatfile {

var $filelock, $tfiles, $files;

function setdb () {
	$this->filelock = true;
	$this->files = array();
	$this->tfiles = array();
	$this->chars = array(
				'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 
				'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 
				'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 
				'y', 'z', 
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 
				'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 
				'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 
				'Y', 'Z', 
				'1', '2', '3', '4', '5', '6', '7', '8', 
				'9', '0'
				);
	$this->unchars = array(
				'|', '/', '\\', '<', '>', ',', ':', ';',
				);
	$this->db['users'] = "data/users.php";
	$this->db['messages'] = "data/messages.php";
	$this->db['online'] = "data/online.php";
	$this->db['groups'] = "data/groups.php";
	$this->db['settings'] = "data/settings.php";
	$this->db['titles'] = "data/titles.php";
	$this->db['sessions'] = "data/sessions.php";
	$this->db['counter'] = "data/counter.php";
	$this->db['session_archive'] = "data/archive_sessions.php";
	$this->db['banned'] = "data/banned.php";
	$this->db['notes'] = "data/notes.php";
	$this->db['cache'] = "data/cache.php";
	$this->db['errors'] = "data/errors.php";
	$this->read_cache();
}

function read_cache () {
	$cache = file($this->db['cache']);
	array_shift($cache);
	$cache = implode("", $cache);
	$this->cache = @unserialize($cache);
}

function touchfile ($filename) {
	if (! in_array($filename, $this->tfiles)) {
		array_push($this->tfiles, $filename);
	}
}

function writefiles ($files="all") {
	if ($files == "all") {
		if (isset($this->temails)) {
			foreach ($this->temails as $email) {
				$this->sendemail($email);
			}
		}
		foreach ($this->tfiles as $filename) {
			$this->write($filename);
		}
	} else {
		$this->write($files);
		$this->files[$filename] = 0;
		$this->openfile[$filename];
	}
}

function email ($destination, $subject, $message) {
	if (!isset($this->temails)) {
		$this->temails = array();
	}
	array_push ($this->temails, array($destination, $subject, $message));
}

function sendemail ($emailinfo) {
	$headers = "From: ".$this->settings['edgebrd_name']." <".$this->settings['eboard_email'].">";
	$to = $emailinfo[0];
	$subject = $emailinfo[1];
	$message= $emailinfo[2];
	if (!@mail($to, $subject, $message, $headers)) {
		$email = $this->inputize2(array(serialize("email"), serialize(time()), serialize($to), serialize($subject), serialize($message), serialize($headers)));
		$this->insert($this->db['errors'], $email);
	}
}

function write ($file) {
	$filename = $file;
	if ($this->files[$filename] == 0) {
		$this->files[$filename] = array();
	}
	$file = $this->files[$filename];
	$newfile = "";
	$l = 0;
	for ($i=0; $line=$file[$i]; $i++) {
		if ($l == 0) {
			$newfile = "<? die('edgeBoard Security: Please do not request this file!'); ?>\r\n";
			$l = 1;
		}
		for ($i=0;(($i < count($line)));$i++) {
			if ($i == 0) {
				$newfile = $newfile . $line[$i];
			} else {
				$newfile = $newfile . "|" . $line[$i];
			}
		}
		$newfile = $newfile . "\r\n";
	}
	$fhandle = fopen($filename, "w");
	if ($this->filelock) {@flock ($fhandle, LOCK_EX);}
	fwrite($fhandle, $newfile);
	fclose ($fhandle);
	if ($this->filelock) {@flock ($fhandle, LOCK_UN);}
	@chmod($filename, 0777);
}

function openfile ($filename, $implode=false, $implodekey="") {
	if (!is_array($filename) && ($implode == false)) {
		$this->openl2($filename);
	} elseif (!is_array($filename) && ($implode == true)) {
		$this->openl2($filename, $implodekey);
	} elseif (is_array($filename) && ($implode == true)) {
		$this->openl1($filename, $implodekey);
	}
}

function openl1 ($filename, $implode) {
	if (!empty($this->files[$filename])) {
		foreach ($this->files[$filename] as $line) {
			$rawline = implode("|", $line);
			$istr .= $rawline."\r\n";
		}
	} else {
		$istr = "";
	}
	$this->selectall[$implode] = $istr;
}

function openl2 ($filename, $implode="") {
	if (empty($this->files[$filename])) {
		if (file_exists($filename) && is_file($filename)) {
			$file = file($filename);
			$lines = array();
			foreach ($file as $rawline) {
				$rawline = chop($rawline);
				if (!strstr($rawline,"<? die(); ?>") &&
					!strstr($rawline,"<? die('edgeBoard Security: Please do not request this file!'); ?>")) {
					if (!empty($implode)) { 
						$istr .= $rawline."\r\n";
					}
					$line = explode("|", $rawline);
					array_push($lines, $line);
				}
			}
			$this->files[$filename] = $lines;
		} else {
			$this->files[$filename] = array();
		}
	}
	if (!empty($implode)) { 
		$this->selectall[$implode] = $istr;
	}
}

function findselect ($file, $what, $bool=false) {
	$got = 0;
	$data = 0;
	if ((is_numeric($what)) || (is_int($what))) {
		$data = $this->select($file, $what);
		$got = 1;
	}
	if (empty($data)) {
		$data = $this->selectwhere($file, 1, $what);
		if ($data[0] != 0) {
			$data = $data[0];
			$got = 1;
		}
	}
	if ($bool) {
		return $got;
	} else {
		return $data;
	}
}

// returns requested line as array
function select ($filename, $lineid) {
	$this->openfile($filename);
	$line = array();
	foreach ($this->files[$filename] as $line) {
		if ($line[0] == $lineid) {
			return $line;
		}
	}
}

// returns multi-dimensional array
function selectall ($filename, $implode=false, $implodekey="") {
	if ($implode) {
		$this->openfile($filename, $implode, $implodekey);
	} else {
		$this->openfile($filename);
	}
	return $this->files[$filename];
}

function gethits ($haystack, $needle) {
	$terms = explode(" ", $needle);
	$hits["total"] = 0;
	if (!empty($haystack) && !empty($needle)) {
		$hits["phrase"] = substr_count($haystack, $needle);
		$hits["terms"] = 0;
		foreach ($terms as $term) { 
			if (!empty($term)) {
				$morehits = substr_count($haystack, $term);
				$hits["terms"] = $hits["terms"] + $morehits;
			}
		}
		$hits["total"] = ($hits["phrase"] * 10) + $hits["terms"];
	}
	return $hits["total"];
}

function search ($filename, $string, $ss=false, $cs=false, $partid="all") {
	$results = array();
	$file = $this->selectall($filename);
	$this->openl1($filename, "search");
	if (strstr(" ", $string)) {
		$terms = explode(" ", $srting);
	} else {
		$terms = array($string);
	}
	if ($cs == false) {
		foreach ($terms as $term) {
			$term = strtolower($term);
		}
		$string = strtolower($string);
		$this->selectall["search"] = strtolower($this->selectall["search"]);
	}
	$hits = $this->gethits($this->selectall["search"], $string);

	if ($hits > 0) {
		foreach ($file as $line) {
			$hitline = implode("¶", $line);
			if ($cs == false) {
				$hitline = strtolower($hitline);
			}
			$linehits = $this->gethits($hitline, $string);
			if ($linehits > 0) {
				if ($partid == "all") {
					foreach ($line as $part) {
						if ($cs == false) {
							$part = strtolower($part);
						}
						if ($ss == true) {
							if ($part == $string) {
								array_push($results, array($line, $filename, $linehits));
							}
						} else {
							if (stristr($part,$string)) {
								array_push($results, array($line, $filename, $linehits));
							}
						}
					}				
				} else {
					if ($cs == false) {
						$part[$partid] = strtolower($part[$partid]);
					}
					if ($ss == true) {
							if ($part[$partid] == $string) {
								array_push($results, array($line, $filename, $linehits));
							}
					} else {
						if (stristr($part[$partid],$string)) {
							array_push($results, array($line, $filename, $linehits));
						}
					}
				}
			}
		}
	}
	return $results;
}

// returns 0 if not found, or the lines found
function selectwhereNOT ($filename, $partid, $part, $both=false, $partid2="", $part2="") {
	$nl = 0;
	$al = 0;
	$this->openfile($filename);
	$not_lines = array();
	$are_lines = array();
	foreach ($this->files[$filename] as $line) {
		if (!empty($part2)) {
			if (($line[$partid] == $part) && ($line[$partid2] == $part2)) {
				array_push($are_lines, $line);
				$al = 1;
			} else {
				array_push($not_lines, $line);
				$nl = 0;
			}
		} else {
			if ($line[$partid] == $part) {
				array_push($are_lines, $line);
				$al = 1;
			} else {
				array_push($not_lines, $line);
				$nl = 0;
			}
		}
	}
	if ($al || $nl) {
		if ($both) {
			return array($not_lines, $are_lines);
		} else {
			return $not_lines;
		}
	} else {
		return 0;
	}
}

function selectwhere_byletter ($filename, $partid, $letter) {
	$this->openfile($filename);
	$al = 0;
	$lines = array();
	foreach ($this->files[$filename] as $line) {
		if (strtoupper($line[$partid]{0}) == strtoupper($letter)) {
			array_push($lines, $line);
			$al = 1;
		}
	}
	if ($al) {
		return $lines;
	} else {
		return 0;
	}
}

// returns 0 if not found, or the lines found
function selectwhere_upcase ($filename, $partid, $part) {
	$this->openfile($filename);
	$lines = array();
	foreach ($this->files[$filename] as $line) {
		if (strtoupper($line[$partid]) == strtoupper($part)) {
			$newline = array();
			foreach ($line as $seg) {
				$seg = strtoupper($seg);
				array_push($newline, $seg);
			}
			array_push($lines, $newline);
		}
	}
	if ($lines > 0) {
		return $lines;
	} else {
		return 0;
	}
}

// returns 0 if not found, or the lines found
function selectwhere ($filename, $partid, $part) {
	$this->openfile($filename);
	$lines = array();
	$ch = 0;
	foreach ($this->files[$filename] as $line) {
		if ($line[$partid] == $part) {
			array_push($lines, $line);
			$ch = 1;
		}
	}
	if ($ch) {
		return $lines;
	} else {
		return array();
	}
}

// returns 0 if not found, or the lines found
function super_selectwhere ($filename, $dbarray) {
	$this->openfile($filename);
	$lines = array();
	$ch = 0;
	foreach ($this->files[$filename] as $line) {
		$valid = 1;
		for ($i=0;($i < count($line));$i++) {
			if (isset($dbarray[$i])) {
				if (($dbarray[$i] == $line[$i]) && ($valid != "z")) {
					$valid == 1;
				} else {
					$valid = "z";
				}
			}
		}
		if ($valid != "z") {
			$ch = 1;
			array_push($lines, $line);
		}
	}
	if ($ch) {
		return $lines;
	} else {
		return array();
	}
}

function insertid($filename, $lineid, $newline) {
	$this->openfile($filename);
	$this->touchfile($filename);
	$this->delete($filename, $lineid);
	$this->openfile($filename);
	$this->touchfile($filename);
	$this->files[$filename][$lineid]=$newline;
	return $lineid;
}

function inserti($filename, $newline) {
	$this->openfile($filename);
	$this->touchfile($filename);
	array_push($this->files[$filename], $newline);
	if (is_array($newline)) {
		return $newline[0];
	}
}

function replace ($filename, $line) {
	$this->delete($filename, $line[0]);
	return $this->inserti($filename, $line);
}

// returns 0 if not found, or the lines found
function where ($table, $partid, $part) {
	$lines = array();
	foreach ($table as $row) {
		if ($row[$partid] == $part) {
			array_push($lines, $row);
		}
	}
	return $lines;
}

// returns multi-deminsional array
function selectsort ($filename, $sort) {
	$this->openfile($filename);
	$lines = $this->files[$filename];
	$lines = $this->sort($lines, $sort);
	return $lines;
}

// returns multi-deminsional array
function sort ($lines, $sort) {
	// this just seems to go better if we sort it twice.
	for ($t=0; $t<=count($lines); $t++) {
		// for each line, $i is the line
		for ($i=0; $i < count($lines); $i++) {
			// everytime line $i is less than the next line
			while (strtolower($lines[$i][$sort]) < strtolower($lines[$i-1][$sort])) {
				// make a place to temporarily store $i.
				$temp = array();
				$temp = $lines[$i];
				// line $i becomes $i++
				$lines[$i] = $lines[$i-1];
				// line $i++ becomes line $i
				$lines[$i-1] = $temp;
				// this process repeats until all values greater than line i
				// are above line i
			}
		}
	}
	return $lines;
}

function moveoneup ($filename, $line) {
	$this->openfile($filename);
	$this->touchfile($filename);
	// for each line, $i is the line
	$continue = True;
	for ($i=1; $continue == True; $i++) {
		// everytime line $i is less than the next line
		if ($this->files[$filename][$i][0] == $line) {
			// make a place to temporarily store $i++.
			$temp = array();
			$temp = $this->files[$filename][$i-1];
			// line $i becomes $i++
			$this->files[$filename][$i-1] = $this->files[$filename][$i];
			// line $i becomes line $i++
			$this->files[$filename][$i] = $temp;
			$continue = False;
		}
		if ($i > count($this->files[$filename])) {$continue = False;}
	}
}

function moveonedown ($filename, $line) {
	$this->openfile($filename);
	$this->touchfile($filename);
	// for each line, $i is the line
	$continue = True;
	for ($i=0; $continue == True; $i++) {
		// everytime line $i is less than the next line
		if ($this->files[$filename][$i][0] == $line) {
			// make a place to temporarily store $i++.
			$temp = array();
			$temp = $this->files[$filename][$i+1];
			// line $i becomes $i--
			$this->files[$filename][$i+1] = $this->files[$filename][$i];
			// line $i becomes line $i++
			$this->files[$filename][$i] = $temp;
			$continue = False;
		}
		if ($i >= count($this->files[$filename])-2) {$continue = False;}
	}
}

// returns 1 when complete
function update ($filename, $lineid, $newline) {
	$this->openfile($filename);
	$this->touchfile($filename);
	for ($i=0; $i < count($this->files[$filename]); $i++) {
		if ($this->files[$filename][$i][0] == $lineid) {
			$this->files[$filename][$i] = $newline;
		}
	}
	return 1;
}

// replaces an entire db with $newfile [$newfile is an array ;)]
function updatefile ($filename, $newfile) {
	$this->openfile($filename);
	$this->touchfile($filename);
	$this->files[$filename] = $newfile;
	$this->writefiles($filename);
	$this->openfile($filename);
	return 1;
}

// returns 1 when complete
function insert_alt ($filename, $newline, $id) {
	$this->openfile($filename);
	$this->touchfile($filename);
	array_unshift($newline, $id);
	$i = 1;
	foreach ($newline as $line) {
		$array = array($i, $line);
		array_push($this->files[$filename], $array);
		$i++;
	}
	return 1;
}

function update_alt ($filename, $newlines) {
	$this->touchfile($filename);
	$lines = $this->selectall($filename);
	$i = 0;
	foreach ($newlines as $newline) {
		$lines[$i][1] = $newline;
		$i++;
	}
	$this->files[$filename] = $lines;
	return 1;
}
	
// returns $lineid when complete
function insert($filename, $newline) {
	$this->openfile($filename);
	$this->touchfile($filename);
	if (count($this->files[$filename]) != 0) {
		$lineid = $this->files[$filename][count($this->files[$filename])-1][0] + 1;
	} else {
		$lineid = 1;
	}
	array_unshift($newline, $lineid);
	array_push($this->files[$filename], $newline);
	return $lineid;
}
	
// returns 1 when complete
function delete($filename, $lineid) {
	$this->openfile($filename);
	$this->touchfile($filename);
	for ($i=0; $i<count($this->files[$filename]);$i++) {
		if ($this->files[$filename][$i][0] == $lineid) {
			array_splice($this->files[$filename], $i, 1);
		}
	}
	return 1;
}

function clear ($filename) {
	$this->openfile($filename);
	$this->touchfile($filename);
	$this->files[$filename] = array(array());
}

function inputize2 ($array) {
	$args = func_get_args();
	foreach ($array as $value) {
		if (isset($args[1]) && isset($args[2])) {
			$value = $this->inputize($value, $args[1], $args[2]);
		} elseif (isset($args[1])) {
			$value = $this->inputize($value, $args[1]);
		} else {
			$value = $this->inputize($value);
		}
	}
	return $array;
}

// arguments are
// $text to inputize, maximum length of text, maxsize of any one word
// $text is only required variable
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
	$text = str_replace("\n", "<BR>", $text);
	$text = str_replace("|", "&|4;", $text);
	return $text;
}

function deinputize ($text) {
	$text = str_replace("<BR>", "\n", $text);
	$text = str_replace("<RETURN>", "\n", $text);
	return $text;
}

function settemp ($var,$new,$val) {
	$this->tempvars['$var'] = $val;
	return $new;
}

function rettemp ($var) {
	return $this->tempvars['$var'];
}

function genstr ($stringlength, $offset=0, $maxoffset=0) {
	if ($maxoffset > 0) {
		$maxoffset = (-1*$maxoffset);
	}
	if ($offset < 0) {
		$offset = (-1*$maxoffset);
	}
	$chars = $this->chars;
	$max = count($chars) - 1 + $maxoffset;
	$offsettotal = (-1*$max) + $offset;
	if ($offsettotal > count($chars)-1) {
		$max = count($chars) - 1;
	}
	srand((double) microtime()*1000000);
	$rand_str = '';
	for ($i=1;$i<$stringlength;$i++) {
		$rand_str .= $chars[rand($offset, $max)];
	}
	return $rand_str;
}

}

//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////

class dimension {

function dimension () {
	$this->filelock = true;
	$this->files = array();
	$this->tfiles = array();
}

function touchfile ($filename) {
	if (! in_array($filename, $this->tfiles)) {
		array_push($this->tfiles, $filename);
	}
}

function writefiles ($files="all") {
	if ($files == "all") {
		foreach ($this->tfiles as $filename) {
			$this->write($filename);
		}
	} else {
		$this->write($files);
		$this->files[$filename] = 0;
		$this->openfile[$filename];
	}
}

function write ($file) {
	$filename = $file;
	if ($this->files[$filename] == 0) {
		$this->files[$filename] = array();
	}
	$file = $this->files[$filename];
	$newfile = serialize($file);
	$fhandle = fopen($filename, "w");
	if ($this->filelock) {@flock ($fhandle, LOCK_EX);}
	fwrite($fhandle, $newfile);
	fclose ($fhandle);
	if ($this->filelock) {@flock ($fhandle, LOCK_UN);}
}

function openfile ($filename) {
	if (!isset($this->files[$filename])) {
		$file = file($filename);
		$file = implode("", $file);
		if (empty($file)) {
			$file = array();
		} else {
			$file = unserialize($file);
		}
	}
	$this->files[$filename] = $file;
}

function findselect ($file, $what, $bool=false) {
	$got = 0;
	$data = 0;
	if ((is_numeric($what)) || (is_int($what))) {
		$data = $this->select($file, $what);
		$got = 1;
	}
	if (empty($data)) {
		$data = $this->selectwhere($file, 1, $what);
		if ($data[0] != 0) {
			$data = $data[0];
			$got = 1;
		}
	}
	if ($bool) {
		return $got;
	} else {
		return $data;
	}
}

// returns requested line as array
function select ($filename, $lineid) {
	$this->openfile($filename);
	$line = array();
	foreach ($this->files[$filename] as $line) {
		if ($line[0] == $lineid) {
			return $line;
		}
	}
}

// returns multi-dimensional array
function selectall ($filename) {
	$this->openfile($filename);
	return $this->files[$filename];
}

// returns 0 if not found, or the lines found
function selectwhereNOT ($filename, $partid, $part, $both=false, $partid2="", $part2="") {
	$nl = 0;
	$al = 0;
	$this->openfile($filename);
	$not_lines = array();
	$are_lines = array();
	foreach ($this->files[$filename] as $line) {
		if (!empty($part2)) {
			if (($line[$partid] == $part) && ($line[$partid2] == $part2)) {
				array_push($are_lines, $line);
				$al = 1;
			} else {
				array_push($not_lines, $line);
				$nl = 0;
			}
		} else {
			if ($line[$partid] == $part) {
				array_push($are_lines, $line);
				$al = 1;
			} else {
				array_push($not_lines, $line);
				$nl = 0;
			}
		}
	}
	if ($al || $nl) {
		if ($both) {
			return array($not_lines, $are_lines);
		} else {
			return $not_lines;
		}
	} else {
		return 0;
	}
}

function selectwhere_byletter ($filename, $partid, $letter) {
	$this->openfile($filename);
	$al = 0;
	$lines = array();
	foreach ($this->files[$filename] as $line) {
		if (strtoupper($line[$partid]{0}) == strtoupper($letter)) {
			array_push($lines, $line);
			$al = 1;
		}
	}
	if ($al) {
		return $lines;
	} else {
		return 0;
	}
}

// returns 0 if not found, or the lines found
function selectwhere ($filename, $partid, $part) {
	$this->openfile($filename);
	$lines = array();
	$ch = 0;
	foreach ($this->files[$filename] as $line) {
		if ($line[$partid] == $part) {
			array_push($lines, $line);
			$ch = 1;
		}
	}
	if ($ch) {
		return $lines;
	} else {
		return array();
	}
}

// returns 0 if not found, or the lines found
function super_selectwhere ($filename, $dbarray) {
	$this->openfile($filename);
	$lines = array();
	$ch = 0;
	foreach ($this->files[$filename] as $line) {
		$valid = 1;
		for ($i=0;($i < count($line));$i++) {
			if (isset($dbarray[$i])) {
				if (($dbarray[$i] == $line[$i]) && ($valid != "z")) {
					$valid == 1;
				} else {
					$valid = "z";
				}
			}
		}
		if ($valid != "z") {
			$ch = 1;
			array_push($lines, $line);
		}
	}
	if ($ch) {
		return $lines;
	} else {
		return array();
	}
}

function replace ($filename, $line) {
	$this->openfile($filename);
	$this->touchfile($filename);
	for ($i=0; $i<count($this->files[$filename]);$i++) {
		if ($this->files[$filename][$i][0] == $line[0]) {
			$this->files[$filename][$i] = $line;
		}
	}
	return 1;
}

// returns 0 if not found, or the lines found
function where ($table, $partid, $part) {
	$lines = array();
	foreach ($table as $row) {
		if ($row[$partid] == $part) {
			array_push($lines, $row);
		}
	}
	return $lines;
}

// returns multi-deminsional array
function selectsort ($filename, $sort) {
	$this->openfile($filename);
	$lines = $this->files[$filename];
	$lines = $this->sort($lines, $sort);
	return $lines;
}

// returns multi-deminsional array
function sort ($lines, $sort) {
	// this just seems to go better if we sort it twice.
	for ($t=0; $t<=count($lines); $t++) {
		// for each line, $i is the line
		for ($i=0; $i < count($lines); $i++) {
			// everytime line $i is less than the next line
			while (strtolower($lines[$i][$sort]) < strtolower($lines[$i-1][$sort])) {
				// make a place to temporarily store $i.
				$temp = array();
				$temp = $lines[$i];
				// line $i becomes $i++
				$lines[$i] = $lines[$i-1];
				// line $i++ becomes line $i
				$lines[$i-1] = $temp;
				// this process repeats until all values greater than line i
				// are above line i
			}
		}
	}
	return $lines;
}

function moveoneup ($filename, $line) {
	$this->openfile($filename);
	$this->touchfile($filename);
	// for each line, $i is the line
	$continue = True;
	for ($i=1; $continue == True; $i++) {
		// everytime line $i is less than the next line
		if ($this->files[$filename][$i][0] == $line) {
			// make a place to temporarily store $i++.
			$temp = array();
			$temp = $this->files[$filename][$i-1];
			// line $i becomes $i++
			$this->files[$filename][$i-1] = $this->files[$filename][$i];
			// line $i becomes line $i++
			$this->files[$filename][$i] = $temp;
			$continue = False;
		}
		if ($i > count($this->files[$filename])) {$continue = False;}
	}
}

function moveonedown ($filename, $line) {
	$this->openfile($filename);
	$this->touchfile($filename);
	// for each line, $i is the line
	$continue = True;
	for ($i=0; $continue == True; $i++) {
		// everytime line $i is less than the next line
		if ($this->files[$filename][$i][0] == $line) {
			// make a place to temporarily store $i++.
			$temp = array();
			$temp = $this->files[$filename][$i+1];
			// line $i becomes $i--
			$this->files[$filename][$i+1] = $this->files[$filename][$i];
			// line $i becomes line $i++
			$this->files[$filename][$i] = $temp;
			$continue = False;
		}
		if ($i >= count($this->files[$filename])-2) {$continue = False;}
	}
}

// returns 1 when complete
function update ($filename, $lineid, $newline) {
	$this->openfile($filename);
	$this->touchfile($filename);
	for ($i=0; $i < count($this->files[$filename]); $i++) {
		if ($this->files[$filename][$i][0] == $lineid) {
			$this->files[$filename][$i] = $newline;
		}
	}
	return 1;
}

// replaces an entire db with $newfile [$newfile is an array ;)]
function updatefile ($filename, $newfile) {
	$this->openfile($filename);
	$this->touchfile($filename);
	$this->files[$filename] = $newfile;
	return 1;
}
	
// returns $lineid when complete
function insert($filename, $newline) {
	$this->openfile($filename);
	$this->touchfile($filename);
	$lineid = (count($this->files[$filename])+1);
	array_unshift($newline, $lineid);
	array_push($this->files[$filename], $newline);
	return $lineid;
}
	
// returns 1 when complete
function delete($filename, $lineid) {
	$this->openfile($filename);
	$this->touchfile($filename);
	for ($i=0; $i<count($this->files[$filename]);$i++) {
		if ($this->files[$filename][$i][0] == $lineid) {
			$this->files[$filename][$i] = array($lineid);
		}
	}
	return 1;
}

function clear ($filename) {
	$this->openfile($filename);
	$this->touchfile($filename);
	$this->files[$filename] = array(array());
}

function inputize2 ($array) {
	$args = func_get_args();
	foreach ($array as $value) {
		if (isset($args[1]) && isset($args[2])) {
			$value = $this->inputize($value, $args[1], $args[2]);
		} elseif (isset($args[1])) {
			$value = $this->inputize($value, $args[1]);
		} else {
			$value = $this->inputize($value);
		}
	}
	return $array;
}

// $text to inputize, maximum length of text, maxsize of any one word
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
	return $text;
}

function deinputize ($text) {
	$text = str_replace("<BR>", "\n", $text);
	$text = str_replace("<BR>", "\n", $text);
	$text = str_replace("&lt;br&gt;", "\n", $text);
	$text = str_replace("&lt;BR&gt;", "\n", $text);
	$text = str_replace("<RETURN>", "\n", $text);
	return $text;
}

}

?>