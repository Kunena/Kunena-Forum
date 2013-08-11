Introduction
============

This file contains all the languages which are currently available for Kunena Forum @kunenaversion@ for Joomla! 2.5.

We recommend that you install the most recent language package every time you have upgraded Kunena.

Kunena distribution package itself includes translations for installation, which will be used during installation process.

You can find the most recent language list from here:
http://www.kunena.org/download

Installing languages
====================

Just install this package by using Joomla installer. If you cannot install the package because of 2MB upload limit, you can either
install the package from URL or make the file smaller by removing a few languages you do not need.

Installer will detect which languages have been installed into your system. It will install or update only existing languages,
so If you add new languages after installing this package, you will need to install this language pack again.

Every language will be installed separately to allow you to uninstall them one by one if needed. You can find the language
packages by going to "Extension Manager: Manage", selecting type File and filtering results by "Kunena Language".

Additionally extension manager will have a package called "Kunena Language Pack". Uninstalling this package will also uninstall
all the languages that were added during installation.

How to make translations keys
=====================

The translations keys in language files need to follow the following rule:

<package-type>_<package-name>_<lang-type>_<object>_<atribute-in-object>_<preprefix(categorization in plural casses)-uniquename>

Which give by example :

COM_KUNENA_SYS_FIELDSET_LABEL_BASICS
COM_KUNENA_SYS_FIELD_LABEL_BASICSTIMETOCREATE
COM_KUNENA_SYS_FIELD_DESC_BASICSTIMETOCREATE
COM_KUNENA_LIB_FIELDSET_LABEL_BASICS

The unique name is bunched at the end of the key and the first part of the key take his name by following the directory structure
where the key is used.

Translating languages
=====================

Many of the languages in this package are missing translations. To see the status of your language, please visit our Transifex status page:
https://www.transifex.com/projects/p/Kunena/

If you want to help us to translate your language, please register into Transifex site and request a membership to a team.

Here are our wiki pages for the translators (please read them before starting your work):

http://docs.kunena.org/index.php/Transifex
http://docs.kunena.org/index.php/Translating_Kunena

More Information
================

For more information, please read:
http://docs.kunena.org/index.php/K_2.0_Language_Support
