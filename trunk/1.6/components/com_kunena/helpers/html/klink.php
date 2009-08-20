<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

defined('_JEXEC') or die('Invalid Request.');

/**
 * HTML helper class for handling Kunena link rendering.
 *
 * @package 	Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
abstract class JHtmlKLink
{
	/**
	 * Method to generate an simple (X)HTML link as an <a> tag.
	 * 
	 * <code>
	 *	<?php echo JHtml::_('klink.simple', $url, $text); ?>
	 * </code>
	 * 
	 * @param	string	The URL for the href attribute of the <a> tag.
	 * @param	string	The text for the contents of the <a> tag.
	 * @return	string	The link as an <a> tag.
	 * @since	1.6
	 */
	public static function simple($url, $text = '')
	{
		return'<a href="'.$url.'">'.$text.'</a>';
	}

	/**
	 * Method to generate an (X)HTML search engine friendly link as an <a> tag.
	 * 
	 * <code>
	 *	<?php echo JHtml::_('klink.sef', $url, $text, ...); ?>
	 * </code>
	 * 
	 * @param	string	The URL for the href attribute of the <a> tag.
	 * @param	string	The text for the contents of the <a> tag.
	 * @return	string	The link as an <a> tag.
	 * @since	1.6
	 */
	public static function sef($url, $text, $title, $rel, $class ='', $anker='', $attr='')
	{
		return '<a '.($class ? 'class="'.$class.'" ' : '').'href="'.JRoute::_($url).($anker?('#'.$anker):'').'" title="'.$title.'"'.($rel ? ' rel="'.$rel.'"' : '').($attr ? ' '.$attr : '').'>'.$text.'</a>';
	}

	/**
	 * Method to generate an (X)HTML search engine friendly link to the credits.
	 * 
	 * <code>
	 *	<?php echo JHtml::_('klink.credits'); ?>
	 * </code>
	 * 
	 * @return	string	The link as an <a> tag.
	 * @since	1.6
	 */
    function credits()
    {
        return self::sef('http://www.kunena.com', 'Kunena', 'Kunena', 'follow', NULL, NULL, 'target="_blank"');
    }

    function teamCredits($catid, $name='')
    {
        return self::sef(KUNENA_LIVEURLREL.'&amp;func=credits&amp;catid='.$catid, $name, '', 'follow');
    }

    function kunena($name , $rel='follow')
    {
        return self::sef(KUNENA_LIVEURLREL, $name, '', $rel);
    }
}
