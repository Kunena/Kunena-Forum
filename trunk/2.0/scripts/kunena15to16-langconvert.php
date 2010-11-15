<?php
/*

Here is the directory structure that is needed to make the script to work:
en-GB/
	admin/
		en-GB.com_kunena.ini
		en-GB.com_kunena.install.ini
	site/
		en-GB.com_kunena.ini
xx-XX/
	kunena.mylanguage.php
yy-YY/
	kunena.otherlanguage.php

Script will generate the same file structure to your own language as in en-GB.

It will include all the strings from en-GB files:
- Most of your translations will be filled in
- Untraslated entries will be commented out with the English strings in them

Please review all the translations against English strings. We have made a lot of adjustments into them
and this script does not compare content -- it just converts old format into new.
*/


$languages = array();
$dirs = scandir('.');
foreach ($dirs as $dir) {
	if ($dir && $dir != '.' && $dir != '..' && $dir != 'en-GB' && is_dir('./'.$dir)) {
		$files = scandir('./'.$dir);
		foreach ($files as $file)
			if (preg_match('/\.php$/', $file)) $php = $file;
		$languages[$dir] = $php;
	}
}

$header = '; $Id$
; License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
; Note : All ini files need to be saved as UTF-8 - No BOM

; Using new string format for Joomla 1.6.x

; Translator comments come into here!

';

echo "\nStarting language file conversion.\n\n";

$en_admin = parse_ini_file("en-GB/admin/en-GB.com_kunena.ini", false);
$en_install = parse_ini_file("en-GB/admin/en-GB.com_kunena.install.ini", false);
$en_site = parse_ini_file("en-GB/site/en-GB.com_kunena.ini", false);
if (!$en_admin && !$en_install && !$en_site) die('* ERROR: cannot load English language files');

foreach ($languages as $id=>$langfile) {
	@mkdir("./$id/admin");
	@mkdir("./$id/site");

	kconvert($id, $langfile);

	// Load new INI
	$xx_lang = parse_ini_file("$id/$id.com_kunena.tmp.ini", false);
	if (!$xx_lang) die('* There are some issues in generated INI, you need to edit and fix it!');
	echo "* Load $id/$id.com_kunena.tmp.ini: SUCCESS\n";

	kmakelang($xx_lang, $en_admin, "$id/admin/$id.com_kunena.ini", $header);
	kmakelang($xx_lang, $en_install, "$id/admin/$id.com_kunena.install.ini", $header);
	kmakelang($xx_lang, $en_site, "$id/site/$id.com_kunena.ini", $header);
}

function kconvert($id, $langfile) {
	echo "Converting $id ($langfile)\n";
	if (!is_file("$id/$id.com_kunena.tmp.ini")) {
		// Converting file from php to ini format
		$lang = file_get_contents("$id/$langfile");
		$lang = preg_split('/DEFINE\s*\(\s*[\'"]/iu', $lang);
		array_shift($lang);
		$lang = preg_replace('/^([A-Za-z0-9_]+)\s*[\'"]\s*,\s*[\'"]\s*(.*?)\s*[\'"]\s*\)\s*;.*$/msu', '\\1=#QUOTE#\\2#QUOTE#', $lang);
		$pattern = array('/"/u', '/\\\'/u', '/&#32;/u', '/&#700;/u', '/&#39;/u', '/&#146/u');
		$replace = array('&quot;', '\'', '', '\'', '\'', '\'');
		$lang = preg_replace($pattern, $replace, $lang);
		$lang = preg_replace('/#QUOTE#/u', '"', $lang);
		$ini = implode("\n", $lang);

		// Changing to the new string format
		$pattern = array('/\n_COM_/u', '/\n_KUNENA_/u');
		$ini = preg_replace($pattern, "\nCOM_KUNENA_", $ini);
		$ini = preg_replace('/\n_/u', "\nCOM_KUNENA_", $ini);

		// Saving temporary language file
		$fp = fopen("$id/$id.com_kunena.tmp.ini",'w');
		fwrite($fp,$ini);
		fclose($fp);
		echo "* Generated $id/$id.com_kunena.tmp.ini\n";
	} else {
		echo "* Using existing $id/$id.com_kunena.tmp.ini\n";
	}
}

function kmakelang($xx_lang, $en_lang, $outfile, $header) {
	$out = $header;
	foreach ($en_lang as $key=>$string) {
		if (!empty($xx_lang[$key])) $out.= "$key=\"{$xx_lang[$key]}\"\n";
		else $out.= ";$key=\"{$string}\"\n";
	}
	// Saving temporary language file
	$fp = fopen("$outfile",'w');
	fwrite($fp,$out);
	fclose($fp);
	echo "* Generated $outfile\n";
}

