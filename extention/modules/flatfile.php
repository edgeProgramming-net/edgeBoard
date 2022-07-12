<?

/*
* Copyright edge-programming.com
* No reproduction or redistribution of this script is allowed!
* Contact deltawolf@deltawolf.com or nitsuj24017@aol.com for informaion.
* Most flatfile functions done by lordanthron@yahoo.com
* The edgeTeam thanks Anthron for allowing us to continue his work.
* edgeBoard is the best!
*/

/*
* The code below made by MPHH
* For PHP V. 4.2.0
*/
@reset($HTTP_GET_VARS); 
while(list($key, $val) = @each($HTTP_GET_VARS)) 
$$key = $val;
@reset($HTTP_POST_VARS); 
while(list($key, $val) = @each($HTTP_POST_VARS)) 
$$key = $val;
@reset($HTTP_COOKIE_VARS); 
while(list($key, $val) = @each($HTTP_COOKIE_VARS)) 
$$key = $val;

class functions {

var $filelock, $tfiles, $files;

function setdb () {
	$this->filelock = true;
	$this->files = array();
	$this->tfiles = array();
}

function touchfile ($filename) {
	if (! in_array($filename, $this->tfiles)) {
		array_push($this->tfiles, $filename);
	}
}

function writefiles () {
	global $type;
	foreach ($this->tfiles as $filename) {
		if (strstr($filename,"list.cgi")) {
			$this->listwrite($filename);
		} else {
			$this->filewrite($filename);
		}
	}
}

function listwrite($filename) {
	clearstatcache();
	$newfile = "";
	if (file_exists($filename)) {
		$oldf = $this->files[$filename];
		$o = count($oldf);
	} else {
		$oldf = array();
		$o = 0;
	}
	$this->filewrite($filename);
	$newf = file($filename);
	$n = count($newf);
	if ($o > $n) {
		 $fhandle = fopen($filename, "w");
		 if ($this->filelock) {flock ($fhandle, LOCK_EX);}
		 foreach ($oldf as $line) {
		      fwrite($fhandle, $line);
		 }
		 fclose ($fhandle);
		 $this->writeerror = 1;
	}
}


function filewrite($filename) {
	$newfile = "";
	foreach ($this->files[$filename] as $line) {
	      for ($i=0; $i<count($line);$i++) {
		      if ($i == 0) {
			      $newfile = $newfile . $line[$i];
		      } else {
			      $newfile = $newfile . "|" . $line[$i];
		      }
	      }
	      $newfile = $newfile . "\n";
	}
	$fhandle = fopen($filename, "w");
	if ($this->filelock) {flock ($fhandle, LOCK_EX);}
	fwrite($fhandle, $newfile);
	fclose ($fhandle);
}

function openfile ($filename) {
	clearstatcache();
	if (! isset($this->files[$filename])) {
		clearstatcache();
		if (file_exists($filename) && $filename !="") {
			$file = file($filename);
			$lines = array();
			foreach ($file as $rawline) {
				$rawline = chop($rawline);
				$line = explode("|", $rawline);
				array_push($lines, $line);
			}
			$this->files[$filename] = $lines;
		} else {
			$this->files[$filename] = array();
		}
	}
}

// returns requested line as array
function select ($filename, $lineid) {
	$this->openfile($filename);
	foreach ($this->files[$filename] as $line) {
		if ($line[0] == $lineid) {
			return $line;
		}
	}
}

// returns 0 if not found, or the lines found
function selectwhere ($filename, $partid, $part) {
	$this->openfile($filename);
	$lines = array();
	foreach ($this->files[$filename] as $line) {
		if ($line[$partid] == $part) {
			array_push($lines, $line);
		}
	}
	return $lines;
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
	return $lines;
}

//deletes a line in a file
//partid = section
//part identifies the contents of partid in the file
//where part and partid in the file are both true, the line is deleted
//arrayseparator is the character that defines different values in the array
function selectanddelete ($filename, $x, $part, $x) {
	$openfile = file($filename);
	$i = 0;
	$t = 1;
	$file = fopen($filename, "w");
	foreach ($openfile as $filay) {
		$filay = explode("|", $filay);
		if ($filay[0] != $part) {
			fputs($file, $openfile[$i]);
		}
		$i = $i + $t;
	}
	fclose($file);
	return 1;
}

// returns 0 if not found, or the lines found
function where ($file, $partid, $part) {
	$lines = array();
	foreach ($file as $line) {
		if ($line[$partid] == $part) {
			array_push($lines, $line);
		}
	}
	return $lines;
}

// returns multi-deminsional array
function selectsort ($filename, $sort) {
	$this->openfile($filename);
	$lines = $this->files[$filename];
	$sortedlines = $this->sort($lines, $sort);
	return $sortedlines;
}

// returns multi-deminsional array
function sort ($lines, $sort) {
	// this just seems to go better if we sort it twice.
	for ($t=0; $t<=count($lines); $t++) {
		// for each line, $i is the line
		for ($i=0; $i < count($lines); $i++) {
			// everytime line $i is less than the next line
			while ($lines[$i][$sort] < $lines[$i-1][$sort]) {
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

function find ($file, $partid, $part) {
	$openfile = $this->selectall($file);
	foreach ($openfile as $array) {
		if ($array[$partid] == $part) {
			$return = $array;
		}
	}
	if (isset($return)) {
		return $return;
	} else {
		return 0;
	}
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

// returns multi-dimensional array
function selectall ($filename) {
	$this->openfile($filename);
	return $this->files[$filename];
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

function inserti($filename, $newline) {
	$this->openfile($filename);
	$this->touchfile($filename);
	array_push($this->files[$filename], $newline);
}

// returns 1 when complete
function delete($filename, $lineid) {
	$this->openfile($filename);
	$this->touchfile($filename);
	$i = 0;
	$n = (count($this->files[$filename]));
	while ($i < $n) {
		if ($this->files[$filename][$i][0] == $lineid) {
			array_splice($this->files[$filename], $i, 1);
		}
		$i++;
	}
	return 1;
}

}

?>