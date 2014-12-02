<?php

if ($_SERVER['argc'] > 1) {
	$languages = explode(',',$_SERVER['argv'][1]);
} else {
	$languages = scandir('components/com_kunena/site/language');
	foreach ($languages as $i=>$language) {
		if ($language[0] == '.') unset($languages[$i]);
		elseif (!is_dir("components/com_kunena/site/language/{$language}")) unset($languages[$i]);
	}
}

$files = array(
	'components/com_kunena/admin/language/en-GB/en-GB.com_kunena.controllers.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.com_kunena.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.com_kunena.libraries.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.com_kunena.models.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.com_kunena.sys.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.com_kunena.views.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.plg_kunena_alphauserpoints.sys.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.plg_kunena_community.sys.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.plg_kunena_comprofiler.sys.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.plg_kunena_gravatar.sys.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.plg_kunena_joomla.sys.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.plg_kunena_kunena.sys.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.plg_kunena_uddeim.sys.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.plg_quickicon_kunena.sys.ini',
	'components/com_kunena/admin/language/en-GB/en-GB.plg_system_kunena.sys.ini',
	'components/com_kunena/site/language/en-GB/en-GB.com_kunena.controllers.ini',
	'components/com_kunena/site/language/en-GB/en-GB.com_kunena.ini',
	'components/com_kunena/site/language/en-GB/en-GB.com_kunena.models.ini',
	'components/com_kunena/site/language/en-GB/en-GB.com_kunena.templates.ini',
	'components/com_kunena/site/language/en-GB/en-GB.com_kunena.tpl_blue_eagle.ini',
	'components/com_kunena/site/language/en-GB/en-GB.com_kunena.views.ini'
);

echo "\nInitializing language files...\n\n";

foreach ($languages as $language) {
	if ($language == 'en-GB') continue;
	if (!is_dir("components/com_kunena/site/language/{$language}")) die("Error: Unknown language '{$language}'\n\n");

	// Save language files
	foreach ($files as $infile) {
		$outfile = preg_replace('|en-GB|', $language, $infile);
		if (!is_file($outfile)) {
			$contents = '; Sorry, this language file hasn\'t been translated yet.
;
; If you want to help us, please start by reading our documentation on translating Kunena:
; http://docs.kunena.org/index.php/Translating_Kunena
';

			echo "Create $outfile\n";
			$fp = fopen($outfile,'w');
			fwrite($fp, $contents);
			fclose($fp);
		}
	}
}
