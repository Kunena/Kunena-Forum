<?php

$language = 'fi-FI';
if ($_SERVER['argc'] > 1) {
	$language = $_SERVER['argv'][1];
}
if (!is_dir("components/com_kunena/language/{$language}")) die("Error: Unknown language '{$language}'\n\n");

// Find all language files
$filter = '/^en-GB\.com_kunena.*\.ini$/';
$files = checkdir('administrator', $filter);
$files += checkdir('components', $filter);

echo "\nLoading {$language} language strings...\n\n";

// Load all translations
global $translations;
$translations = array();
foreach ($files as $file=>$dummy) {
	$file = preg_replace('|en-GB|', $language, $file);
	$translations += loadTranslations($file);
}

echo "\nSaving {$language} language strings...\n\n";

// Save language files
foreach ($files as $infile=>$dummy) {
	$outfile = preg_replace('|en-GB|', $language, $infile);
	saveLang($infile, $outfile);
}

function checkdir($dir, $filter = '/(\.php|\.xml|\.js)$/') {
	$checklist = array();
	$files = scandir($dir);
	foreach ($files as $file) {
		if ($file[0] == '.') continue;
		$path = "$dir/$file";
		if (is_dir($path)) {
			$checklist += checkdir($path, $filter);
		} elseif (preg_match($filter, $file)) {
			$checklist[$path] = 1;
		}
	}
	return $checklist;
}

function loadTranslations($file) {
	if (!file_exists($file)) return array();
	echo "Load $file\n";
	$contents = file_get_contents($file);
	// Put commented out translations back so that we do not loose them
	$contents = preg_replace('|;\s*(COM_)|','\1',$contents);
	$strings = (array) parse_ini_string($contents);
	return $strings;
}

function saveLang($infile, $outfile) {
	echo "Save $outfile\n";
	$contents = file_get_contents($infile);
	$contents = preg_replace_callback('|^(; )?([A-Z0-9_]+)=".*"$|m', 'translate', $contents);

	$fp = fopen($outfile,'w');
	fwrite($fp, $contents);
	fclose($fp);
}

function translate($matches) {
	global $translations;
	$string = isset($translations[$matches[2]]) ? $translations[$matches[2]] : '';
	$commentout = ( !empty($matches[1]) ? $matches[1] : ( $string ? '' : '; ' ) );
	return $commentout.$matches[2].'="'.$string.'"';
}
