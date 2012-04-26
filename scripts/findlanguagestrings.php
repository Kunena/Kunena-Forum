<?php

$header = '; License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
; Note : All ini files need to be saved as UTF-8 - No BOM

; Using new string format for Joomla 1.6.x

';

echo "\nFinding language strings from all php and xml files...\n";

$checklist = checkdir('administrator');
$checklist += checkdir('components');
$checklist += checkdir('modules');

$keys = array();
foreach ($checklist as $file => $dummy) {
	$strings = findStrings($file);
	foreach ($strings as $string) {
		if (!isset($keys[$string])) $keys[$string] = $file;
		else $keys[$string] = strprefix($keys[$string], $file);
	}
}
$files = array();
foreach ($keys as $key => $location) {
	if (empty($location)) {
		$files['site/com_kunena.common'][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/install#', $location)) {
		$files['admin/com_kunena.install'][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/libraries#', $location)) {
		$files['admin/com_kunena.libraries'][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/controllers#', $location)) {
		$files['admin/com_kunena.controllers'][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/models#', $location)) {
		$files['admin/com_kunena.models'][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/views#', $location)) {
		$files['admin/com_kunena.views'][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/media/kunena/topicicons/default/topicicons.xml#', $location)) {
		$files['site/com_kunena.topicicons'][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/kunena.#', $location)) {
		$files['admin/com_kunena.sys'][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena#', $location)) {
		$files['admin/com_kunena'][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/views/.*\.xml#', $location)) {
		$files['admin/com_kunena.sys'][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/controllers#', $location)) {
		$files['site/com_kunena.controllers'][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/models#', $location)) {
		$files['site/com_kunena.models'][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/views#', $location)) {
		$files['site/com_kunena.views'][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/template#', $location)) {
		if (preg_match('#/mirage/#', $location))
			$files['site/com_kunena.tpl_mirage'][$key] = $location;
		if (preg_match('#/blue_eagle/#', $location))
			$files['site/com_kunena.tpl_blue_eagle'][$key] = $location;
		else
			$files['site/com_kunena.templates'][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/lib#', $location)) {
		$files['site/com_kunena.legacy'][$key] = $location;
	} else {
		$files['site/com_kunena'][$key] = $location;
	}
}

echo "Loading existing language strings...\n\n";

$filter = '/^en-GB\.com_kunena.*\.ini$/';
$langfiles = checkdir('administrator', $filter);
$langfiles += checkdir('components', $filter);
$translations = loadTranslations(array_keys($langfiles));

foreach ($files as $filename=>$keys) {
	asort($keys);
	saveLang($keys, $translations, $filename, $header);
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

function strprefix( $s1, $s2 ) {
	$prefix = $s1^$s2;
	return substr($s1, 0, strlen($prefix)-strlen(ltrim($prefix,chr(0))));
}

function findStrings($filename) {
	$file = file_get_contents("$filename");
	preg_match_all('/COM_KUNENA_[\w\d_]*/u', $file, $matches);
	return $matches[0];
}

function saveLang(&$keys, &$translations, $outfile, $header) {
	$outfile = preg_replace(array('|^site/|', '|^admin/|'), array('lang/site/en-GB.','lang/admin/en-GB.'), $outfile).'.ini';
	$out = $header;
	$comment = '';
	foreach ($keys as $key=>$string) {
		if ($comment != $string) {
			$comment = $string;
			$out.= "\n; $comment\n\n";
		}
		if (!isset($translations[$key])) {
			$translations[$key] ='';
			//echo "Missing $key\n";
		}
		$out.= "{$key}=\"{$translations[$key]}\"\n";
	}
	// Saving temporary language file
	$fp = fopen($outfile,'w');
	fwrite($fp,$out);
	fclose($fp);
}

function &loadTranslations($files) {
	$translations = array();
	foreach ($files as $file) {
		if (preg_match('/com_kunena\.menu\.ini$/', $file)) continue;
		echo "Load $file\n";
		$contents = file_get_contents($file);
		$strings = (array) parse_ini_string($contents, false, INI_SCANNER_RAW);
		if (!$strings) echo "ERROR LOADING $file!\n";
		foreach ($strings as $key => $str) {
			if (!isset($translations[$key])) $translations[$key] = $str;
			elseif ($translations[$key] != $str) echo "Conflict: $key=\"{$translations[$key]}\" <==> \"$str\"\n";
		}
	}
	return $translations;
}
