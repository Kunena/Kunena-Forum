<?php
// This file is designed to be called by the rewrite rules in htaccess
// It will reqrite the URL and redirect
// Noel Hunter, Feb 6, 2009

// Get the querry string
$url=$_SERVER['QUERY_STRING'];

// Replace old string with new.  Add ?redirected in case you need to detect redirects
$url=str_replace('fireboard','kunena',$url); 
$url="index.php?redirected".$url;

// Go to new URL
header(sprintf("Location: %s", $url));
?>
