<?php

$header = '; Kunena Forum translation
;
; All translations can be found from https://www.transifex.net/projects/p/Kunena/r/Kunena2/
; Please join the translation team if you want to contribute your changes to the next release.
;
; License GNU General Public License version 3 or later; see LICENSE.txt
; Note : All ini files need to be saved as UTF-8 - No BOM

; Using new INI string format for Joomla! 2.5

';

echo "\nFinding language strings from all php and xml files...\n";

$checklist = checkdir('administrator');
$checklist += checkdir('components');
$checklist += checkdir('modules');

$keys = array('COM_KUNENA'=>'administrator/components/com_kunena/kunena.j25.xml');
$files = array();
foreach ($checklist as $file => $dummy) {
	$strings = findStrings($file);
	foreach ($strings as $string) {
		$prefix = isset($keys[$string]) ? strprefix($keys[$string], $file) : $file;
		if ($prefix != $file && preg_match('#^components/com_kunena/views/[^\.]+\.xml$#', $file)) {
			// Special case, use sys.ini file (2 locations if needed)
			$prefix = 'components/com_kunena/views/**/*.xml';
			$files['admin/en-GB.com_kunena.sys.ini'][$prefix][$string] = $prefix;
		} else {
			$keys[$string] = $prefix;
		}
	}
}
foreach ($keys as $key => $location) {
	if (empty($location)) {
		$files['site/en-GB.com_kunena.ini']['Common strings for both frontend and backend'][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/install/modules/#', $location)) {
		$files['site/en-GB.com_kunena.ini'][$location][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/install/plugins/#', $location)) {
		$files['site/en-GB.com_kunena.ini'][$location][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/install/#', $location)) {
		$files['admin/en-GB.com_kunena.install.ini'][$location][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/libraries/#', $location)) {
		$files['admin/en-GB.com_kunena.libraries.ini'][$location][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/controllers/#', $location)) {
		$files['admin/en-GB.com_kunena.controllers.ini'][$location][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/models/#', $location)) {
		$files['admin/en-GB.com_kunena.models.ini'][$location][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/views/#', $location)) {
		$files['admin/en-GB.com_kunena.views.ini'][$location][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/media/kunena/topicicons/default/topicicons.xml#', $location)) {
		$files['site/en-GB.com_kunena.templates.ini'][$location][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/kunena\.#', $location)) {
		$files['admin/en-GB.com_kunena.sys.ini'][$location][$key] = $location;
	} elseif (preg_match('#^administrator/components/com_kunena/#', $location)) {
		$files['admin/en-GB.com_kunena.ini'][$location][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/views/[^\.]+\.xml$#', $location)) {
		$files['admin/en-GB.com_kunena.sys.ini'][$location][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/controllers/#', $location)) {
		$files['site/en-GB.com_kunena.controllers.ini'][$location][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/models/#', $location)) {
		$files['site/en-GB.com_kunena.models.ini'][$location][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/views/#', $location)) {
		$files['site/en-GB.com_kunena.views.ini'][$location][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/template/#', $location)) {
		if (preg_match('#/blue_eagle/#', $location))
			$files['site/en-GB.com_kunena.tpl_blue_eagle.ini'][$location][$key] = $location;
		else
			$files['site/en-GB.com_kunena.templates.ini'][$location][$key] = $location;
	} elseif (preg_match('#^components/com_kunena/lib#', $location)) {
		$files['admin/en-GB.com_kunena.libraries.ini'][$location][$key] = $location;
	} else {
		$files['site/en-GB.com_kunena.ini'][$location][$key] = $location;
	}
}

echo "Loading existing language strings...\n\n";

$filter = '/^en-GB\.com_kunena.*\.ini$/';
$langfiles = checkdir('administrator', $filter);
$langfiles += checkdir('components', $filter);

list($translations, $filestrings) = loadTranslations(array_keys($langfiles));

foreach ($files as $filename=>$fkeys) {
	saveLang($keys, $fkeys, $translations, $filestrings, $filename, $header);
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

function saveLang(&$keys, &$fkeys, &$translations, &$filestrings, $outfile, $header) {
	$out = $header;
	ksort($fkeys);
	foreach ($fkeys as $location=>$list) {
		$autogen = array();
		$out.= "\n; JROOT/{$location}\n\n";
		ksort($list);
		foreach ($list as $key=>$string) {
			if ($key[strlen($key)-1] == '_') {
				foreach ($translations as $tkey=>$tstring) {
					if (strpos($tkey, $key) === 0 && !isset($keys[$tkey])) {
						unset($filestrings[$outfile][$tkey]);
						$autogen[$key][$tkey] = $translations[$tkey];
					}
				}
			} else {
				unset($filestrings[$outfile][$key]);
				if (!isset($translations[$key])) {
					// Missing key
					$translations[$key] = $key;
				}
				$out.= "{$key}=\"{$translations[$key]}\"\n";
			}
		}
		ksort($autogen);
		foreach ($autogen as $tkey=>$auto) {
			ksort($auto);
			$out.= "\n; Automatically generated strings for {$tkey}*\n\n";
			foreach ($auto as $genkey=>$genvalue) $out.= "{$genkey}=\"{$genvalue}\"\n";
			$out.= "\n; End of automatically generated strings for {$tkey}*\n";
		}
	}

	if (!empty($filestrings[$outfile])) {
		$out.= "\n; Removed strings\n\n";
		foreach ($filestrings[$outfile] as $key=>$string) {
			if (!isset($keys[$key])) $out.= "; {$key}=\"{$translations[$key]}\"\n";
		}
/*
		$out.= "\n; Moved strings\n\n";
		foreach ($filestrings[$outfile] as $key=>$string) {
			if (isset($keys[$key])) $out.= "{$key}=\"{$translations[$key]}\"\n";
		}
*/
	}

	// Saving temporary language file
	$outfile = preg_replace(array('|^site/|', '|^admin/|'), array('components/com_kunena/language/en-GB/','administrator/components/com_kunena/language/en-GB/'), $outfile);
	$fp = fopen($outfile,'w');
	fwrite($fp,$out);
	fclose($fp);
}

function loadTranslations($files) {
	$translations = array();
	$filestrings = array();
	foreach ($files as $file) {
		if (preg_match('/com_kunena\.menu\.ini$/', $file)) continue;
		echo "Load $file\n";
		$contents = file_get_contents($file);
		// Put commented out translations back so that we do not loose them
		$contents = preg_replace('|;\s*(COM_)|','\1',$contents);
		$strings = (array) parse_ini_string($contents);
		if (!$strings) echo "ERROR LOADING $file!\n";
		$file = preg_replace(array('|^components/.*/|', '|^administrator/.*/|'), array('site/','admin/'), $file);
		foreach ($strings as $key => $str) {
			if (!isset($translations[$key])) $translations[$key] = $str;
			elseif ($translations[$key] != $str) echo "Conflict: $key=\"{$translations[$key]}\" <==> \"$str\"\n";
		}
		$filestrings[$file] = $strings;
	}
	ksort($translations);
	return array($translations, $filestrings);
}
