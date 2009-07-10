/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2008-2009 Kunena All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

Kunena README

PLEASE READ THIS ENTIRE FILE BEFORE INSTALLING Kunena @fbversion@!

KNOWN ISSUES
============

There is a handful of known issues, which are still being worked on:

Corrupt GIF icons on some Joomla hosts
--------------------------------------

For some unknown reason, some Joomla 1.0.x installs will corrupt some of the small 
GIF icons during the install. This is believed to be a Joomla 1.0.x issue, but we have
not been able to get confirmation on this. 

This problem will result in some of the template icons to be missing in the front end.
The problem has only been reproduced with the ZIP archive of the package. You might
want to check whether your platform supports other compressed file types.
In case you run into this issue, simply unzip the package file on your local computer
and manually upload the missing files to the required location. 

The GIF icons in the package itself are OK, but the copies created by Joomla during the install
might get corrupted.


International special characters in URLs
----------------------------------------

The new URL auto-linking feature does not support special characters in domain names of 
URLs. For example, domain names with German umlaut will break the auto-linking process.
Domains with such special characters are very rare.


30-second timeout on Joomla 1.5.x
---------------------------------

Because Joomla 1.5.x requires a lot more resources than Joomla 1.0.x,  
the upload and installation of Kunena may take longer than 30 seconds in a shared hosting
environment. Unfortunately, this is a very common PHP timeout setting. If you run into
this issue, you can increase the PHP timeout period from 30 to 120 seconds. See your hosting
provider for additional details.

Alternately, you may upload the entire package via FTP into a temporary directory.
Then unpack the files and use Joomla's "Install from a directory" feature. This will 
eliminate the upload and decompress time from the install process so it will run in less
than 30 seconds.

Should you exceed this timeout period, you must first uninstall the partial Kunena setup.
Depending on at what point the timeout occurred, you might be required to delete the 
components/com_kunena and administrator/components/com_kunena directories 
by hand through either FTP or SSH access in your hosting environment.

ONLINE README
=============

For details about this release and how to install or upgrade to it please see:

http://docs.kunena.com/index.php/Kunena_1.0.11_Read_Me

END OF README
=============