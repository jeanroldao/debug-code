<?php

function parseCsvString($csv) {
	$table =[];
	$row = [];
	
	$curVal = '';
	$inquotes = false;
	for ($i = 0; $i < strlen($csv); $i++) {
		$ch = $csv[$i];
		if ($inquotes) {
			if ($ch == '"') {
				$inquotes = false;
			} else {
				$curVal .= $ch;
			}
		} else {
			if ($ch == '"') {
				$inquotes = true;
				if (strlen($curVal) > 0) {
					//if this is the second quote in a value, add a quote
					//this is for the double quote in the middle of a value
					$curVal .= '"';
				}
			} else if ($ch == ',') {
				$row[] = $curVal;
				$curVal = '';
			} else if ($ch == "\n") {
				$row[] = $curVal;
				$curVal = '';
				$table[] = $row;
				$row = [];
			} else {
				$curVal .= $ch;
			}
		}
	}
	$row[] = $curVal;
	$table[] = $row;
	return $table;
}
function normalize($s) {
    // Normalize line endings
    // Convert all line-endings to UNIX format
    $s = str_replace("\r\n", "\n", $s);
    $s = str_replace("\r", "\n", $s);
    // Don't allow out-of-control blank lines
    $s = preg_replace("/\n{2,}/", "\n\n", $s);
    return $s;
}

$csv = normalize(file_get_contents($_SERVER['argv'][1]));
$table = parseCsvString(trim($csv));

echo "done! (" .count($table) ." convertidos!)\n";
fgets(STDIN);