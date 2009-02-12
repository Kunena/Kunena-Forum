<?php
// Translate from text
// Noel Hunter, Feb 9, 2009
//
// This script takes a file of tab-delimited defines and translations,
// such as created by the script build_translate_txt.sh, and substitutes
// the translated text for the English text.  It can be called as follows:
//
// php translate_from_text.php > kunena.yourlanguage.php
//
// The files kunena.english.php and translate.txt should be in the same
// direcory as this script
//
// To verify operation before translating, run with English values, and
// then compare output to original.  There should be no changes
// For example:
// php translate_from_text.php > kunena.test.php
// diff kunena.english.php kunena.test.php

$filename = "kunena.english.php"; 
$filename2 = "translation_combined.txt"; 

$english = file( $filename );	// Read the English into an array
$words = file( $filename2 ); 	// Read translation into an array

$numenglish = count( $english );
$numwords = count( $words ); 

for($e=0; $e<$numenglish; $e++)
    {
    // Loop through lines in kunena.english.php
	// Set the old define and old value (English), by looking for ',
    	list($olddefine,$oldvalue)=split("',",$english[$e]);
	// Remove the ' from old value
	list($junk1,$oldvalue)=split("'",$oldvalue);
	// Enclose old value in single quotes
	$oldvalue="'".$oldvalue."'";

	for($w=0; $w<$numwords; $w++) 
	{
	// Loop through the translation text
		// Set the new define and new value, delimited by tab
   		list($newdefine,$newvalue)=split("\t",$words[$w]);
		// Trim newlines at end
		$newvalue=trim($newvalue,"\n");
		// Enclose in single quotes
		$newvalue="'".$newvalue."'";
		$newdefine="'".$newdefine."'";

		// If the english line we are processing contains
		// the new define, replace the value with translated value
		if(stristr($english[$e],$newdefine)) {
            		$english[$e] = str_replace($oldvalue,$newvalue,$english[$e]);
		}
    	}
}

// Write out the translated file to standard output
for($e=0; $e<$numenglish; $e++) {
	echo "$english[$e]";
}
?>
