<?php
function getTranslations($path) {
	$languages = array();
	$dirs = scandir($path);
	foreach ($dirs as $dir) {
		if ($dir && $dir != '.' && $dir != '..' && $dir != 'en-GB' && is_dir($path.'/'.$dir)) {
			$files = scandir($path.'/'.$dir);
			foreach ($files as $file)
				if (preg_match('/\.ini$/', $file)) $languages[] = $path.'/'.$dir.'/'.$file;
		}
	}
	return $languages;
}

echo "\nStarting language file check.\n\n";

$files = getTranslations('administrator/components/com_kunena/language');
foreach ($files as $file) {
	$contents = file_get_contents($file);
	echo "$file:\n";
	$contents = str_replace('_QQ_','"\""',$contents);
	$lines = explode("\n", $contents );
	if (!strpos($file, 'menu')) foreach ($lines as $line) {
		if (!preg_match('/^(;.*|\s*|[A-Z0-9_\.]+="[^"]*")$/u', $line)) echo $line."\n";
	}
	$strings = (array) parse_ini_string($contents, false, INI_SCANNER_RAW);
	if (!$strings) echo "ERROR!\n";
	foreach ($strings as $key => $str) {
		if (preg_match('/[^A-Z0-9_\.]+/u', $key)) echo "ERROR: $key!\n";
		//if (preg_match('/"/u', $str)) echo "ERROR: $key=\"$str\"\n";
	}
}
$files = getTranslations('components/com_kunena/language');
foreach ($files as $file) {
	$contents = file_get_contents($file);
	echo "$file:\n";
	$contents = str_replace('_QQ_','"\""',$contents);
	$lines = explode("\n", $contents );
	if (!strpos($file, 'menu')) foreach ($lines as $line) {
		if (!preg_match('/^(;.*|\s*|[A-Z0-9_\.]+="[^"]*")$/u', $line)) echo $line."\n";
	}
	$strings = (array) parse_ini_string($contents, false, INI_SCANNER_RAW);
	if (!$strings) echo "ERROR!\n";
	foreach ($strings as $key => $str) {
		if (preg_match('/[^A-Z0-9_]+/u', $key)) echo "ERROR: $key!\n";
		//if (preg_match('/"/u', $str)) echo "ERROR: $key=\"$str\"\n";
	}
}
