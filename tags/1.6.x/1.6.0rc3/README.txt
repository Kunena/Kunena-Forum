/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2008-2010 Kunena All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

Kunena README

PLEASE READ THIS ENTIRE FILE BEFORE INSTALLING Kunena @kunenaversion@!

Introduction
============

The Kunena Team is excited to announce the release of Kunena 1.6, Release Candidate 3. This is the third and final RC 
release for Kunena 1.6 and is stable enough to be used on live production websites in limited usage (primarily for 
testing) as long as minor issues are tolerated during the time before it becomes a final release.
Since the second release candidate of Kunena 1.6, the Kunena team has been focused on fixing over 100 bugs and issues 
including CSS fixes, IE7 and IE8 compatibility, usability issues, API issues, html validation issues, new modules and 
plug-ins and updating languages translations.
Kunena 1.6 marks a major milestone for the Kunena Project. A long list of open source contributors have committed over 
2500 changes and have helped make Kunena the leading forum solution for both Joomla 1.5 and 1.6. A long list of new 
features in addition to various optimizations and code restructuring have resulted in the most advanced version of 
Kunena to date.
We continue to put a big emphasis on usability and ease of installation. Therefore, the installer has been further 
enhanced to allow for multiple installation and upgrade options. It performs incremental steps to avoid timeouts on 
slower hosts. It automatically performs actions such as taking the forum offline during the installation or upgrade and 
re-enables it after completing the install.
Kunena now supports Joomla 1.6 style languages files that are backward compatible with Joomla 1.5. Multiple languages 
are included in the base install packages and more will be available soon. They can also be installed separately. 
Kunena 1.6 also support multiple languages to be installed on a single system and, together with Joomla, allows users to 
select their preferred language.
Templates have now been separated from the underlying code, which allows for version-independent template development. 
Templates can embed various options that can be set via the included template manager (e.g. avatar position on left, 
right, top, or bottom in the new default template, Blue Eagle).
The Kunena Team is fully committed to Joomla 1.6 and beyond and a number of modules have been developed to lay the 
groundwork for future releases of Joomla.

What is New?
============

The new features in Kunena 1.6 include:
---------------------------------------

Improved Language Support:
--------------------------

Included languages: Catalan, Dutch, Finnish, French, German, Greek, Italian, Macedonian, Polish, Russian, Serbian, 
Spanish, Thai, Turkish, Yugoslavian, new in RC3: Chinese, Lithuanian, Hungarian, Taiwanese, Vietnamese (more to come)
Uses standard Joomla 1.6 language files (backwards compatible with Joomla 1.5.x)
Added support for installable language packs (using the Joomla Installer)

New Kunena Template Manager:
----------------------------
Centralized template system with installable templates
New default template Blue Eagle
Example template included for use as a base when creating new templates
Template parameters that allow more freedom for template designers and site administrators
Added support for icon sets allows different button and icon variations

New Integrated Polls Feature:
-----------------------------
Allow forum members to setup and participate in polls
Category specific: enable the feature from Category Manager
Global settings to modify the poll behavior
Migrate data from the Kunena 1.5 poll hack

Improved Topic and Post Moderation:
-----------------------------------
Ability to restore and purge deleted posts within the Trash Manager (backend)
Unapproved or deleted topics and posts can be seen in many views
Delete, restore and approve posts while reading topic
Simplified move, merge and split
Move current or newer posts into another category or (new) topic
Ability to move or delete all posts of a particular user (supports better spam management)
User profile access for moderators even if integrated with jomSocial, CB or others

New User Moderation Features:
-----------------------------
Users can be banned directly from their profile
Ban only from the forum (= read only access) or from the entire website
Ban user for a limited time (ban from the forum only)
Allow moderators to share ban reasons and to write comments on it
Ban history and show all previous bans on the user's profile
List banned users in the Ban Manager on the moderator's profile page

New Posting and End User Features:
----------------------------------
Multiple attachments support (AJAX-based)
Category subscriptions
New 'Thank you' system for thanking the poster for their message
Users can delete their own messages (configurable by time)
Anonymous Tipping: allow users to post topics in a defined category completely anonymously (no IP, no tracking, totally 
anonymous)
Google Maps integration - display basic maps inside of posts
New BBCode tags like [article], [attachment], [confidential], [map], [table] and others
Enhanced BBCode [quote] with reference to user and post
Selectable date/time format display options for all time displays and independent for tooltips (relative "ago" vs 
absolute)

Usability Improvements:
-----------------------
Improved RSS features
Color coded categories
Color coded usernames
Code highlighting with Geshi
Many improvements in administrator backend interface

Integration with Other Components:
----------------------------------
New integration system that allows easy integration of avatars, profiles and user lists. login and registration, private 
messaging, activity notifications and basic access control
Integrations included: AlphaUserPoints, Community Builder, JomSocial and UddeIM
New Router and Menu System
Uses Joomla menus to navigate the Kunena component
Easy to build your own custom Kunena menus with Joomla's Menu Item Manager
Option to have categories located outside main forum
Automatic super-routing uses best matching menu item for each page

New Installer:
--------------
Step-by-step details of the installation process
Fully automatic migration from Kunena 1.0.x and 1.5.x
Migrate from FireBoard 1.0.0 - 1.0.5 without using external tools
If migration fails, restore from backup or try again
Option to cleanly uninstall Kunena 1.6 completely from your site

Other Changes:
--------------
Converted from jQuery to MooTools 1.2 for better compatibility
CSS and JavaScript minification using the YUI compressor for faster loading (disabled when debug is turned on)
Uses CSS sprites for faster loading and fewer server hits
Almost all code moved out of templates
Basic API for Joomla modules and plug-ins
Now uses #__kunena prefixed tables instead of #__fb
Use /media/kunena instead of /images/fbfiles for avatars and new attachments
Latest version checking
Basic Joomla 1.6 support
Remember that you can test Kunena 1.6 with Joomla 1.6, but the support is not yet complete. For example, the Kunena menu 
will not be generated and there are some features, that do not work yet in Joomla 1.6. Kunena 1.6 will be ready for 
Joomla 1.6 once a final version is released.

System Requirements
===================

Joomla 1.5.20 Minimum Version Required (Mootools 1.2.x)
-------------------------------------------------------
Kunena 1.6 no longer uses jQuery and requires MooTools 1.2+. Since the release of Joomla 1.5.20, the Mootools 1.2.4 
system plug-in is now included with Joomla 1.5 and this Joomla! contains various fixes on router mecanism. The MooTools 
plug-in that was included in previous development versions of Kunena is no longer needed. Going forward, Joomla 1.6 will 
include MooTools 1.2.4 and Kunena is already compatible with this version.

PHP 5.2.3
---------
Kunena 1.6 requires PHP 5.2.3 or higher. We have also tested it on PHP 5.3.1. In general we currently recommend PHP 
5.2.x due to its maturity.

MySQL 5.0.0
-----------
Kunena in general and especially version 1.6 rely on advanced SQL features introduced in MySQL 5.0.x. MySQL 4.x is no 
longer support even though Joomla 1.5 itself can be run on certain versions of MySQL 4.x. The main difference is the 
amount of database workload generated by a forum component compared to the basic CMS functionality.

Third party components requirements
-----------------------------------
If you want running with Kunena 1.6, Jomsocial, Community Builer, UddeIm or Alpha User Points, you need to have the 
following versions of these components to avoid any issues :

Jomsocial 1.6.x
Community Builer 1.2.3
UddeIm 2.1
Alpha User Points 1.5.11

Downloading Kunena
==================

Find the latest package files here: http://joomlacode.org/gf/project/kunena/frs/

We use the power of JoomlaCode as our main repository for the public SVN. We encourage all users and open source 
developers to help develop fixes, features and enhancements to the existing code base. If you are interested in 
contributing, please provide a patch or enhanced branch and we will be happy to consider it for inclusion in the main 
code branch (quality and feature completeness permitting).
Find the SVN source code repository here: http://joomlacode.org/gf/project/kunena/scmsvn/

The only official Kunena distributions are available from the links above or directly at http://www.kunena.com. There 
are currently no officially supported alternative download mirrors supported and users are encouraged to visit 
www.kunena.com for the latest version of all available Kunena packages.

Compatibility with Third-party Kunena Templates
===============================================

Templates designed for Kunena 1.5.x will not work with Kunena 1.6 due to major changes in the code and structure.

All the logic has been removed from the template to make it much easier for template designers to create Kunena 
templates.
Check out the short guide on how to quickly customize a Kunena 1.6 template in just a few minutes: 
http://www.kunena.com/forum/159-common-questions/53519-the-5-minute-quick-and-dirty-kunena-16-template
More tutorials and instructions will be available on the final release of Kunena 1.6.

Compatibility with Third-party Kunena Modules
=============================================

Kunena 1.6 is not compatible with modules or plug-ins designed for previous versions and we recommend you uninstall all 
Kunena 1.5 extensions before installing or upgrading.

Kunena 1.6 now provides an updated API for easily creating modules or plug-ins. The Kunena team has created a few new 
plug-ins and modules which leverage the new API and can be used with Kunena 1.6. These extensions can be found in the 
Kunena Extension Directory: http://www.kunena.com/ked

Installing or Upgrading Kunena
==============================

Be sure your setup meets or exceeds the minimum Technical Requirements or the install/upgrade process will fail. If it 
fails, your data will be left untouched.
To install Kunena, make sure you read our Installation Instructions before beginning. To upgrade Kunena from previous 
versions (including FireBoard), make sure you read the Upgrade Instructions.
The Kunena installer now handles both installs and upgrades of existing installations without the need for user 
intervention, manual SQL execution, or migration procedures. It also keeps a detailed log of all Kunena versions 
installed on your site in order to perform incremental upgrades as necessary. This means that a site administrator 
doesn't have to worry about complicated installation or upgrade procedures.
We would like to underscore the importance of backups before and after any upgrade process. Never perform an install or 
upgrade without a full backup.

Credits
=======

An open source project like Kunena requires the dedication and investment of personal time from various contributors. 
This version of Kunena Forum has been made possible by the following contributors:

fxstein Kunena developer and admin of http://www.starVmax.com/forum/
Matias Kunena developer
severdia Kunena developer
xillibit Kunena developer
@quila Kunena contributor
810 Kunena contributor
alakentu Kunena moderator
LDA Kunena contributor
Rich Kunena moderator
sozzled Kunena moderator

Special thanks go to Beat and the CB Testing team, Cerberus, DTP2, LittleJohn and JoniJnm for significant contributions 
to Kunena. In addition many members of http://www.Kunena.com have contributed and helped make this a more stable and 
bugfree version. Our thanks go out to all contributors of Kunena!

Language credits
English translation was made by Kunena Team with many suggestions and corrections from our community. We also like to 
give special thanks to all our translators, who have worked hard to localise Kunena to their own languages.

ONLINE README
=============

For details about this release and how to install or upgrade to it please see:

http://docs.kunena.com/index.php/Kunena_@kunenaversion@_Read_Me

END OF README
=============
