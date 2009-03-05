/**
* @version $Id: README.txt 1037 2008-09-01 04:12:50Z fxstein $
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


INSTALLATION
============

BEFORE YOU GO ANY FURTHER, MAKE SURE YOU HAVE REDUNDANT BACKUPS OF YOUR SITE. As with any
software, you might experience issues. We have tested Kunena 1.0.8 better than any prior
release, but we cannot guarantee everything works perfectly in your individual 
configuration. THE ONLY WAY you should proceed is to first make sure you have a current
backup of your site in case you need to revert.

SO BACKUP, BACKUP, BACKUP!

Please make sure you have the latest build release of Kunena. You can find the latest 
packages here on JoomlaCode.org: http://joomlacode.org/gf/project/Kunena/frs/

We have started to use a new file naming standard:
For example: com_kunena_v1.0.8_b1067_2009-02-09.zip
(The actual package might be newer and contain different numbers and dates)

The file name now contains, version number (1.0.8), version suffix, 
build number (b1067) and the date the build was created.
Only very minor bug fixes will be posted as new builds without changing the version.

Download the appropriate package for your target system. DO NOT uncompress it unless
you have a need to access individual files. The package is a valid Joomla install
package and needs to keep its structure intact if you want to utilize Joomla's built-in 
installation component. 

For known issues, please visit http://www.kunena.com and its community forum.

We'll assume you are familiar with the administration back-end of Joomla. You will require
full administrator access in order to install Kunena and configure it to your specific 
needs.


FIRST TIME / FRESH INSTALL
==========================

The installation of Kunena 1.0.8 is simple. The installation process is fully
automated and should not require manual intervention. The installation process has been tested
on both Joomla 1.0.15 as well as 1.5.9. If by the time you install this package, there are
newer versions of Joomla available, please see http://www.kunena.com for additional
information. 


FIRST TIME / FRESH INSTALL (Kunena 1.0.8 on Joomla 1.0.x)
=========================================================

Log into the administrator back-end of Joomla 1.0.x.

Select "Installers/Components" in the main menu. 

Once you are on the installation screen, you have several ways to install the 
package. In this README, we will only focus on the most commonly used option.

First of all, check the list of currently installed components. Make sure Kunena is not 
on that list. If it is, please check the UPGRADE section of this document. Joomla does
NOT support the installation of an identical package name more than once. 

In the first section "Upload Package File" select the local Kunena package by clicking
the "Choose File" button. Once selected, click "Upload File & Install."

The installation process should be confirmed. It should not contain any error message.
If you see an error messages, please check http://www.kunena.com and its
community forum for possible solutions to your issue.

Once the installation process has finished, proceed to perform an initial 
configuration of Kunena. Select "Components/Kunena Forum" in the main menu.

Before you do anything else, go to "Kunena Configuration." Adjust the default
settings to your individual needs and click the "Save" button in the top right corner
of the admin screen. This will force the configuration to be written into the database
and the creation of a legacy configuration file for use with components and modules written
for previous version of Kunena. This legacy support will only be created when you
hit the "Save" button. 

Upon initial install, the forum will be empty with no categories, subforums,
or posts—so the front end will not display any content.

The Kunena configuration allows you to insert some sample data to provide some
examples on how you might want to start your own setup.

IMPORTANT: DO NOT MODIFY THE SAMPLE DATA AND ADAPT IT FOR YOUR OWN NEEDS. But rather
create new categories and structures. The sample data uses special IDs which allow it
to be removed from your system. Once you are satisfied with your setup, simply remove
the sample data or delete sample posts and categories manually through the Kunena 
admin back-end.

The last step to enable Kunena on your system is to create a menu link on Joomla.
This will allow your users to actually see the forum component on the front-end.
In order to do, so select "Menu/Menu Manager" from the Joomla admin back-end main menu.
Then click on the "Menu Items" icon of the menu you would like to add Kunena to.
In most cases, this will be "mainmenu." Once selected, hit the "New" button in the top
right corner. Select "Component" and hit "Next." Choose a name for the menu entry—
for example "Forum" and select the "Kunena Forum" component in the list of available
components. Select the other options based on your individual requirements and then
click the "Save" button in the top right corner of your Joomla admin back-end.

You are now ready to use Kunena. For more information about how to 
setup Kunena, questions about configuration, and templates, visit
http://www.kunena.com
 

FIRST TIME / FRESH INSTALL (Kunena 1.0.8 on Joomla 1.5.x)
=========================================================

The installation of Kunena 1.0.8 on Joomla 1.5.x is very similar to installing it 
on Joomla 1.0.x. The main difference is that you first have to enable Joomla 1.0.x
legacy support since Kunena 1.0.8 is a native Joomla 1.0.x application.

To do so, log into the administrator back-end of Joomla 1.5.x. Select "Extensions/Plugin Manager"
from the main menu. In the list of installed plugins, search for "System - Legacy".
On a standard Joomla 1.5.x install that might be on the second page of plugins.
If the legacy plugin is marked with a red X (meaning it's disabled), simply click on 
the icon to enable the plugin.

Once legacy support is enabled, the installation process is identical to Joomla 1.0.x.

Select "Extensions/Install/Uninstall," choose the local package file by clicking the 
"Choose File" button, and hit "Upload File & Install."

The rest of the initial installation and setup is identical to the Joomla 1.0.x 
process. Please refer to that section (above) for more information.

NOTE: Because Joomla 1.5.x requires a lot more resources than Joomla 1.0.x,  
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



UPGRADE INSTALL
===============

Upgrading an existing installation of Kunena to 1.0.8 is simple and easy. 
We have spent considerable resources and time to fully automate the process. Upgrading
FireBoard 1.0.0-1.0.4 and Kunena 1.0.5-1.0.7 to the latest 1.0.8 version is simple process
that no longer requires you to execute any special scripts or SQL statements by hand.
In the future, all versions of Kunena will come with an automated upgrade process.
The upgrade process has been tested on both Joomla 1.0.15 and 1.5.9. If by the 
time you install this package there are newer versions of Joomla available, see
http://www.kunena.com for additional information. 

There is a ONE-TIME migration requirement for any prior version of FireBoard to Kunena 1.0.8.
In order to facilitate that one-time migration, we have released a standalone component
called com_fbconverter. It migrates the configuration settings of an existing 
Kunena install (versions 1.0.0 to 1.0.4) into the database before the upgrade.

Make sure you download the latest package from JoomlaCode.org: 
http://joomlacode.org/gf/project/Kunena/frs/

Look for the latest package (named like "com_fbconverter_v1.0.8_b159_2009-02-09.zip").
It has the same naming convention like all Kunena packages. By the time you 
read this there might be a newer version and build available. Download the latest.  

The converter is only required if you want to preserve the existing settings of
Kunena. All categories and posts are independent of that and do not require
the converter. Only the settings that were previously stored in the Kunena_config.php
file are =translated by com_fbconverter and written into the database.

BE SURE TO BACKUP, BACKUP, BACKUP!


UPGRADE INSTALL (Kunena 1.0.8 on Joomla 1.0.x)
==============================================

In order to upgrade your existing Kunena install (FireBoard versions 1.0.0 - 1.0.4),
you will need to run the abovementioned converter to preserve the existing configuration
settings. This is not mandatory but will make the upgrade process a little easier
so you don't have to re-enter the configuration settings in the Kunena back-end. This
is not required to maintain the existing category and thread hierarchy as well as
posts and profiles.

If you are already using an earlier build of Kunena (1.0.5 - 1.0.7), you MUST NOT run this 
converter. It is only used for the very first time when upgrading to 1.0.5. Starting with 
version 1.0.5, Kunena keeps its configuration inside the database so it does not require a
converter for future upgrades.

To perform the one time conversion of the configuration, simply install the latest
com_fbconverter component onto your target system where Kunena is installed.

Log into the Administrator back-end of Joomla 1.0.x.
Select "Installers/Components" in the main menu. ("Extensions/Install/Uninstall" for 
Joomla 1.5.x)

Once you are at the installation screen, you have several options to install the 
package. In this README we will only focus on the most commonly used option.

First of all, check the list of currently installed components. Make sure Kunena is not 
on that list. If it is, please check the UPGRADE section of this document. Joomla does
NOT support the installation of an identical package name more than once. 

In the first section "Upload Package File" select the local Kunena package by clicking
the "Choose File" button. Once selected, click "Upload File & Install."

The installation process should be confirmed. It should not contain any error message.
If you see an error messages, please check http://www.kunena.com and its
community forum for possible solutions to your issue.

Once installed, the Kunena configuration is converted as part of the install.
You can uninstall the converter immediately (it doesn't serve any other other purpose).
If you have to run the conversion again for any reason, simply re-install the latest
converter package.

In order to upgrade Kunena itself, simply uninstall the existing Kunena
component and re-install the latest version.

Log into the administrator back-end of Joomla 1.0.x.
Select "Installers/Components" in the main menu. ("Extensions/Install/Uninstall" for 
Joomla 1.5.x)

To uninstall any existing Kunena component, select Kunena Forum from the list of 
installed components and click the "Uninstall" button on the top right corner.
Uninstalling the component will not remove any database content from your server.

Once successfully uninstalled, perform the same steps outlined in the  
FIRST TIME / FRESH INSTALL (Kunena 1.0.8 on Joomla 1.0.x) section. 

The built-in installer will automatically detect the presence of any prior releases 
and perform the necessary upgrade steps, converting data to the latest 1.0.8
format.


UPGRADE INSTALL (Kunena 1.0.8 on Joomla 1.5.x)
==============================================

Since you are upgrading from a prior Kunena version on Joomla 1.5.x, it is assumed
that Joomla legacy support is already enabled on your system (otherwise Kunena would
not be able to run). If you are not sure, review the First Time Install section for 
Joomla 1.5.x for further information on how to check and enable legacy support in Joomla.

Upgrading to Kunena 1.0.8 on Joomla 1.5.x is virtually identical to the upgrade 
process for Joomla 1.0.x. Please refer to the upgrade section for Joomla 1.0.x for
details on how to run the legacy com_fbconverter (if needed) and the remaining steps  
of the upgrade process. 

The only difference to Joomla 1.0.x is the component installer is located at 
"Extensions/Install/Uninstall" of the main menu. All other steps are identical.

Please refer to the Upgrade Kunena 1.0.8 on Joomla 1.0.x section for the full 
process.


END OF README
=============