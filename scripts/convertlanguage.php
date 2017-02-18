<?php

if ($_SERVER['argc'] > 1) {
	$languages = explode(',',$_SERVER['argv'][1]);
} else {
	$languages = scandir('components/com_kunena/language');
	foreach ($languages as $i=>$language) {
		if ($language[0] == '.') unset($languages[$i]);
		elseif (!is_dir("components/com_kunena/language/{$language}")) unset($languages[$i]);
	}
}

// Find all language files
$filter = '/^en-GB\..*\.ini$/';
$files = checkdir('administrator/components/com_kunena/language', $filter);
$files += checkdir('components/com_kunena/language', $filter);
// Remove incompatible file
unset($files['administrator/components/com_kunena/language/en-GB/en-GB.com_kunena.menu.ini']);

global $translations;

foreach ($languages as $language) {
	if ($language == 'en-GB') continue;
	if (!is_dir("components/com_kunena/language/{$language}")) die("Error: Unknown language '{$language}'\n\n");

	echo "\nLoading {$language} language strings...\n\n";

	// Load all translations
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
	if (!is_file($file)) return array();
	echo "Load $file\n";
	$contents = file_get_contents($file);
	$contents = preg_replace('#\r\n#u',"\n",$contents);
	// Put commented out translations back so that we do not loose them
	$contents = preg_replace('#;\s*(COM_|PLG_|PKG_|MOD_)#u','\1',$contents);
	$strings = (array) parse_ini_string($contents);
	return $strings;
}

function saveLang($infile, $outfile) {
	$contents = file_get_contents($infile);
	$contents = preg_replace_callback('|^(; )?([A-Z0-9_]+)="(.*)"$|mu', 'translate', $contents);
	if (!preg_match('|^([A-Z0-9_]+)|mu', $contents)) {
		// Create dummy installation file
		$contents = '; Sorry, this language file hasn\'t been translated yet.
;
; If you want to help us, please start by reading our documentation on translating Kunena:
; https://docs.kunena.org/index.php/Translating_Kunena
';
	}

	echo "Save $outfile\n";
	$fp = fopen($outfile,'w');
	fwrite($fp, $contents);
	fclose($fp);
}

function translate($matches) {
	global $translations;
	$string = isset($translations[$matches[2]]) ? $translations[$matches[2]] : '';
	// if ($matches[3] == $string) $string = '';
	$commentout = ( !empty($matches[1]) ? $matches[1] : ( $string ? '' : '; ' ) );
	return $commentout.$matches[2].'="'.$string.'"';
}
