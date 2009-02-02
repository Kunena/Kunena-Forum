/**
* @version $Id: README.txt 1037 2008-09-01 04:12:50Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2008 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

FireBoard README

PLEASE DO READ THIS ENTIRE FILE BEFORE INSTALLING FIREBOARD @fbversion@!

KNOWN ISSUES
============

Unfortunately there are a handful of known issues that are still being worked on:

Corrupt GIF icons on some Joomla hosts
--------------------------------------

For some unknown reason, some Joomla 1.0.x installs will corrupt some of the small 
GIF icons during the install. This is believed to be a Joomla 1.0.x issue but we have
not been able to get confirmation on this. 
This problem will result in some of the template icons to be missing in the front end.
The problem has only been reproduced with the zip archive of the package. You might
want to check whether your platform supports other compressed file types.
In case you run into this issue, simply unzip the package file on your local computer
and manually upload the missing files to the required location. 
The GIF icons in the package are ok, only the copies created by Joomla during the install
might get corrupted.

International special characters in URLs
----------------------------------------

The new URL auto-linking feature does not support special characters in domain names of 
URLs. For example domain names with German Umlaute will brake the auto linking process.
Domains with such special characters are very rare.

30 sec timeout on Joomla 1.5.x
------------------------------

Joomla 1.5.x requires a lot more resources than Joomla 1.0.x. Because of that 
the upload and install of FireBoard can take longer than 30 sec on a shared hosting
environment. Unfortunately this is a very common php timeout setting. If you run into
this issue you can increase the php time out from 30 to 60 sec. Please see your hosting
provider for details.
Alternatively you may upload the entire package through ftp into a temporary directory.
Unpack the files an use Joomla's feature to install from a directory. This will 
eliminate the upload and decompress time from the install so it will easily run in less
than 30 sec.
Should you hit this timeout, you first must uninstall the partial FireBoard setup.
Depending on where the timeout occurred, you might be required to delete the 
components/com_fireboard and administartor/components/com_fireboard directories 
by hand through either ftp or ssh access of your hosting environment.

INSTALLATION
============

BEFORE YOU GO ANY FURTHER MAKE SURE YOU HAVE REDUNDANT BACKUPS OF YOUR SITE. As with any
software you might experience issues. We have tested FireBoard 1.0.5 better than any prior
release but we cannot guarantee that everything works just fine in your individual 
configuration. THE ONLY WAY for you to proceed is to make sure you have the most current
backup of your site ready - in case you need to roll back.
SO BACKUP - BACKUP - BACKUP!

Please make sure you have the latest build release of FireBoard. You can find the latest 
packages on JoomlaCode.org: http://joomlacode.org/gf/project/fireboard/frs/

We have started to use a new file naming standard:
For EXAMPLE: com_fireboard_v1.0.5RC1_b639_2008-08-30.zip
(You actual package might be newer and contain different numbers and dates)

The file name now contains, version number (1.0.5), version suffix (in this case RC1), 
build number (b639) and the date the build was created.
Very minor bug fixes only will be posted as new builds without changing the version.

Download the appropriate package for your target system. DO NOT uncompress it unless
you have a need to access individual files locally. The package is a valid Joomla install
package and needs to remain in its structure if you want to leverage Joomla's built in 
installation component. 

For known issues please visit http://www.BestOfJoomla.com and its community Forum.

We assume you are familiar with the Administration Back-end of Joomla. You will require
full admin access in order to install FireBaord and configure it to your individual 
needs.

FIRST TIME / FRESH INSTALL
==========================

Installation of FireBoard 1.0.5 is simple and easy. The Installation process is fully
automated and should not require manual intervention. The install process has been tested
on both Joomla 1.0.15 as well as 1.5.5. If by the time you install this package, there are
newer versions of Joomla available, please see http://www.BestOfJoomla.com for additional
information. 

FIRST TIME / FRESH INSTALL FIREBOARD 1.0.5 ON JOOMLA 1.0.x
==========================================================

Log into the Administrator back-end of Joomla 1.0.x.
Select "Installers/Components" in the main menu. 

Once you are on the installation screen you have multiple options on how to install the 
package. In this README we will only focus on the most commonly used option.

First of all check the list of currently installed components. Make sure FireBoard is not 
in that list. If it is, please check the UPGRADE section of this document. Joomla does
NOT support the installation of an identical package name more than once. 

In the first section "Upload Package File" select the local FireBoard package by hitting
the "Choose File" button. Once selected click "Upload File & Install"

The installation process will produce some output. It should not contain any error message.
If you do see error messages, please check http://www.BestOfJoomla.com and its
community forum for possible solutions to your problem.

Once the installation process has finished please proceed to perform an initial 
configuration of FireBaord.  Select "Components/FireBoard Forum" in the main menu.

Before you do anything else, please got to "FireBoard Configuration". Adjust the default
settings to your individual needs and click the "Save" button on the top right corner
of the admin screen. This will force the configuration to be written into the database
and the creation of a legacy config file for use with components and modules written
for previous version of FireBoard. This legacy support will only get created when you
hit the "Save" button. 

On initial install the Forum will be empty and will not contain any categories, sub
forums or post. As such the front end will not display any content.
The FireBaord configuration allows you to insert some sample data, to give you a few
examples on how you might want to start your install setup.

IMPORTANT: DO NOT MODIFY THE SAMPLE DATA AND ADOPT IT FOR YOUR OWN NEEDS. But rather
create new categories and structures. The sample data uses special ids, that allow it
to be removed from your system. Once you are satisfied with your setup, simply remove
the sample data or delete sample posts and categories manually through the FireBoard 
admin back-end.

The last step to enable FireBaord on your system is to create a menu link on Joomla.
This will allow your users to actually see the forum component on the front end.
In order to do so select "Menu/Menu Manager" from the Joomla admin back-end main menu.

In there click on the "Menu Items" icon of the menu you would like to add FireBoard to.
In most cases this will be "mainmenu". Once selected, hit the "New" button in the top
right corner. Select "Component" and hit "Next". Choose a name for the menu entry
for example: "Forum" and select the "FireBoard Forum" component in the list of available
components. Select the other options as per your individual requirements and finish
the menu item by clicking the "Save" button in the top right corner of your Joomla
admin back-end.

By now you are ready to go and use FireBoard. For additional information on how to 
setup FireBaord, questions about configuration and templates please visit
http://www.BestOfJoomla.com
 
FIRST TIME / FRESH INSTALL FIREBOARD 1.0.5 ON JOOMLA 1.5.x
==========================================================

The installation of Fireboard 1.0.5 on Joomla 1.5.x is very similar to installing it 
on Joomla 1.0.x. The main difference is that you first have to enable Joomla 1.0.x
legacy support as FireBoard 1.0.5 is a native Joomla 1.0.x application.

To do so log into the Administrator back-end of Joomla 1.5.x.
Select "Extensions/Plugin Manager" from the main menu. 
In the list of installed plugins search for "System - Legacy". On a standard Joomla
1.5.x install that might be on the second page of all listed plugins.
If this plugin is marked with a red X (as in disabled) simply click on the icon and 
the back-end will mark the plugin as enabled.

Once you have enabled Legacy support, the install is identical to Joomla 1.0.x

Select "Extensions/Install/Uninstall" choose the local package file by clicking the 
"Choose File" button and hit Upload File & Install.

The remainder of the initial instal and setup is identical to the Joomla 1.0.x 
process. Please refer to that section (above) for more information on the initial 
setup.

NOTE: Joomla 1.5.x requires a lot more resources than Joomla 1.0.5. Because of that 
the upload and install of FireBoard can take longer than 30 sec on a shared hosting
environment. Unfortunately this is a very common php timeout setting. If you run into
this issue you can increase the php time out from 30 to 60 sec. Please see your hosting
provider for details.
Alternatively you may upload the entire package through ftp into a temporary directory.
Unpack the files an use Joomla's feature to install from a directory. This will 
eliminate the upload and decompress time from the install so it will easily run in less
than 30 sec.

UPGRADE INSTALL
===============

Upgrading an existing installation of FireBoard to  1.0.5 is simple and easy. 
We have spent considerable resources and time to fully automate the process. Upgrading
FireBoard 1.0.0 - 1.0.4 to the latest 1.0.5 version is simple process that no longer 
requires you to execute any special scripts or SQL statements by hand.
Going forward all new versions of FireBoard will come with an automated upgrade process.
The upgrade process has been tested on both Joomla 1.0.15 as well as 1.5.5. If by the 
time you install this package, there are newer versions of Joomla available, please see
http://www.BestOfJoomla.com for additional information. 

There is a ONE TIME migration requirement for any prior version of FireBaord to 1.0.5.
In order to facilitate that one time migration we have released a stand alone component
com_fbconverter. It migrates the existing configuration settings of an existing 
FireBoard install (versions 1.0.0 to 1.0.4) into the database before the upgrade.

Make sure you download the latest package from JoomlaCode.org: 
http://joomlacode.org/gf/project/fireboard/frs/

Look for a package like: com_fbconverter_v1.0.5RC2_b159_2008-07-15.zip
It has the same new naming convention like all new FireBoard packages. By the time you 
read this there might be a newer version and build of it out there. Get the latest.  

The converter is only required if you want to preserve the existing settings of
FireBoard. All categories and posts are independent from that and do not require
the converter. Only the settings that used to be stored in fireboard_config.php
are getting translated by com_fbconverter and written into the database.

Just to be sure we mention it again: BACKUP - BACKUP - BACKUP!

UPGRADE INSTALL FIREBOARD 1.0.5 ON JOOMLA 1.0.x
===============================================

In order to upgrade your existing FireBoard install (prior versions 1.0.0 to 1.0.4)
you will need to execute above mentioned converter to preserve the existing configuration
settings. This is not mandatory but will make the upgrade process a little but easier
as you don't have to re-enter the config settings in the FireBaord back-end. This
is not required to maintain the existing category and thread hierarchy as all as
all posts and profiles.

If you are already on an earlier build of 1.0.5 you MUST not run this converter. It is
only used for the very first time getting onto 1.0.5. Starting with this version
FireBoard keeps its configuration inside the database, which does not require a
converter for future upgrades.

To perform the one time conversion of the configuration simply install the latest
com_fbconverter component onto your target system that runs the existing FireBoard
installation.

Log into the Administrator back-end of Joomla 1.0.x.
Select "Installers/Components" in the main menu. ("Extensions/Install/Uninstall" for 
Joomla 1.5.x)

Once you are on the installation screen you have multiple options on how to install the 
package. In this README we will only focus on the most commonly used option.

First of all check the list of currently installed components. Make sure FireBoard 
Converter is not in that list. If it is, please uninstall it first before you proceed.
Joomla does NOT support the installation of an identical package name more than once. 

In the first section "Upload Package File" select the local com_fbconverter package by 
hitting the "Choose File" button. Once selected click "Upload File & Install"

The installation process will produce some output. It should not contain any error message.
If you do see error messages, please check http://www.BestOfJoomla.com and its
community forum for possible solutions to your problem.

Once installed the FireBoard configuration is already converted as part of the install.
You may uninstall the Converter immediately. As it will serve no other purpose.
If you have to run the conversion again for various reasons, simply install the latest
converter package again.

That was the 'difficult' part of the upgrade - if you did choose to do so.

In order to upgrade FireBoard itself you now simply uninstall the existing FireBoard
component and re-install the latest version.

Log into the Administrator back-end of Joomla 1.0.x.
Select "Installers/Components" in the main menu. ("Extensions/Install/Uninstall" for 
Joomla 1.5.x)

To uninstall any existing FireBoard component select FireBoard Forum from the list of 
installed components and click the "Uninstall" button on the top right corner.
Uninstalling the component will not remove any database content from your server.

Once uninstalled successfully proceed to the very same steps as outlined in the  
FIRST TIME / FRESH INSTALL FIREBOARD 1.0.5 ON JOOMLA 1.0.x section. 

The built in installer will automatically detect the presence of prior releases data
and will perform the necessary upgrade steps to convert that data to the latest 1.0.5
format.


UPGRADE INSTALL FIREBOARD 1.0.5 ON JOOMLA 1.5.x
================================================

Since you are upgrading form a prior FireBoard version on Joomla 1.5.x it is assumed
that Joomla legacy support is already enabled on your system or FireBaord would not
be able to run. In case you are not sure see the First Time Install section for 
Joomla 1.5.x for further information on how to check and enable Joomla Legacy support.

Upgrading to FireBoard 1.0.5 on Joomla 1.5.x is virtually identical to the upgrade 
process for Joomla 1.0.x. Please refer to the Upgrade section for Joomla 1.0.x for
details on if and how to run the legacy com_fbconverter and the remainder of the 
upgrade process. 

The only difference to Joomla 1.0.x is that the component installer is located at 
"Extensions/Install/Uninstall" of the main menu. All other steps are identical.

Please refer to the Upgrade FireBoard 1.0.5 on Joomla 1.0.x section for the full 
process.


END OF README
=============