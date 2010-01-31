/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2010 Kunena All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

JFirePHP README

PLEASE READ THIS ENTIRE FILE BEFORE INSTALLING JFirePHP @kunenaversion@!

OVERVIEW
============

JFirePHP takes FirePHP integration to the next level. JFirePHP is a fully configurable 
Joomla 1.5 native system plugin that adds FirePHP debugging capabilities to your php software.
It allows developers to leverage the power of FirePHP inside of Joomla 1.5, configuring 
the level of integration in the Joomla backend for JFirePHP. 

Simply install the system plugin via the Joomla Extension Manager, make sure it is published.
Pick the user group level that should be able to see the console messages, set the various 
plugin options to your individual needs including the FirePHP setup options.

IMPORTANT
============

Add the firephp/firephp.defines.php to your project. You can either copy the content into 
your code (e.g. a global defines file) or copy the file into your project. Make sure it is 
included in your distribution. It ensures that all the FirePHP calls can stay in your code
without creating errors on systems without FirePHP.
Make sure you use the fb() or FB::* syntax on all FirePHP calls you place into your code.
DO NOT include any FirePHP files and classes directly. All of that is performed for you
as part of JFirePHP.

USE
============

Once JFirePHP has been installed and the jfirephp.defines.php file has been added to you project
you can leverage all of the JFirePHP (fb() and FB::*) functionality in your code. See the 
FirePHP Headquarters at http://www.firephp.org/HQ/Use.htm for detailed information about 
FirePHP and its usage.

For feedback and support please visit the forums at http://www.kunena.com/jfirephp

END OF README
=============
