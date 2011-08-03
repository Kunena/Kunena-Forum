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
	$contents = str_replace('_QQ_','"\""',$contents);
	$strings = (array) parse_ini_string($contents, false, INI_SCANNER_RAW);
}
$files = getTranslations('components/com_kunena/language');
foreach ($files as $file) {
	$contents = file_get_contents($file);
	$contents = str_replace('_QQ_','"\""',$contents);
	$strings = (array) parse_ini_string($contents, false, INI_SCANNER_RAW);
}
