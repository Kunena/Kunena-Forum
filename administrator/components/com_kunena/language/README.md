Introduction
============

This file contains all the languages which are currently available for Kunena Forum @kunenaversion@ for Joomla! 1.5, 1.7 and 2.5.

We recommend that you install the most recent language package every time you have upgraded Kunena.

Kunena distribution package itself includes a few languages, which will be automatically installed or upgraded during installation process.
For those languages there is no need to install this package. You can find the most recent language list from here:
http://www.kunena.org/download

Installing languages in Joomla! 1.7 and 2.5
===========================================

Just install this package by using Joomla installer. All the available languages will be updated during the installation process.

Recommended way for Joomla! 1.7 and 2.5 is to have all language files inside the extension folders. This installer does just that,
but it also removes all the old Kunena language files from /language and /administrator/language folders.

WARNING: Do not attempt to install the language archive files by hand! They are Joomla! 1.5 installer files.
Installing those files in Joomla! 1.7 or 2.5 has some side effects, which may cause you to accidentally uninstall the whole language.
If you have already installed Kunena language files by hand, you can fix the issue by installing your Joomla! core language again.

Installing languages in Joomla! 1.5
===================================

In Joomla! 1.5 you have to extract the files from the archive and install them one by one.

For each language, there are two files that you need to install. For example, the files for the English language packs are:

    language/com_kunena.en_GB.admin_v@kunenaversion@.zip
    language/com_kunena.en_GB.site_v@kunenaversion@.zip

Using the standard Joomla installation procedure:

    Install the "admin" (backend) file
    Install the "site" (frontend) file

The first file contains the language strings for the administrator (backend) and the second file contains the language strings for the frontend.

Translating languages
=====================

Many of the languages in this package are missing translations. To see the status of your language, please visit our Transifex status page:
https://www.transifex.net/projects/p/Kunena/r/Core/

If you want to help us to translate your language, please register into Transifex site and request a membership to a team.

Here are our wiki pages for the translators (please read them before starting your work):

http://docs.kunena.org/index.php/Transifex
http://docs.kunena.org/index.php/Translating_Kunena

More Information
================

For more information, please read:
http://docs.kunena.org/index.php/K_1.7_Language_Support
