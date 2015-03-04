# Crypsis Template Structure

## ./media

Will contain all the browser accessible media files.

## ./images

All images used in the template (Deprecated. TBD -> media).

## ./js

JavaScript files (Deprecated. TBD -> media).

## ./less

Less files that will be compiled into CSS.

## ./language

Custom language files for the template.

## ./widgets

Template files for widgets. Widgets are simple standalone objects like JHtml.

## ./layouts

Template files for layouts. Layouts are similar to those in Joomla 3.0+.

## ./modules

Template files for modules. Modules are layouts which have controller and can be called from outside without knowing
the implementation details.

## ./pages

Template files for pages. Pages are the traditional views.

## ./template.php

Main template class, which defines template overrides and custom method calls.

## ./config.xml

Template configuration options. Can be used both inside the template and less files.

##./kunena_tpml_crypsis.xml

Needed by Joomla installer. Yes, the new templates will be installed by Joomla Extension Manager.

## ./mapping.php

Provides basic mapping from the old files to the new ones. Will go away.

## ./template.xml

Legacy installer file for Kunena. Will go away.
