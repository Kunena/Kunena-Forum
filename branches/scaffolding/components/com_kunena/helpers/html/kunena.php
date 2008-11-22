<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

/**
 * Utility class for HTML output
 *
 * @static
 * @package 	Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class JHtmlKunena
{
	/**
	 * Displays an icon, or text, and link
	 *
	 * @params	array	An array of icons
	 * @params	string	The name of the icon to use
	 * @params	string	The href for the link
	 * @params	string	The text to use for alt, title or link text (where icon not defined)
	 * @params	string	The text to use for the link
	 * @params	string	The alt text to use for the image
	 * @params	array	An array of additional options (future usage)
	 */
	function iconlink( &$icons, $iconName, $link = '', $title, $text = '', $alt = '' )
	{
		$html	= JHtmlKunena::icon($icons, $iconName, $title, $text, $alt);
		$html	= '<a href="'.$link.'">'.$html.'</a>';

		return $html;
	}

	/**
	 * Displays an icon or text
	 *
	 * @params	array	An array of icons
	 * @params	string	The name of the icon to use
	 * @params	string	The text to use for alt, title or link text (where icon not defined)
	 * @params	string	The text to use for the link
	 * @params	string	The alt text to use for the image
	 * @params	array	An array of additional options (future usage)
	 */
	function icon( &$icons, $iconName, $title, $text = '', $alt = '' )
	{
		if ($text == '') {
			$text = $title;
		}
		if ($alt == '') {
			$alt = $text;
		}
		if (isset( $icons[$iconName] ) && $icons[$iconName])
		{
			$title	= htmlspecialchars( $title );
			$html	= '<img src="'./*JB_URLICONSPATH. */$icons[$iconName].'" border="0" alt="'.$alt.'"  title="'.$title.'" />';
		}
		else {
			$html	= $text;
		}

		return $html;
	}
}
