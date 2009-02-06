<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

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
