<?php
/**
* @version $Id: klink.php 1195 2009-11-22 10:13:34Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

defined('_JEXEC') or die('Invalid Request.');

/**
 * HTML helper class for handling Kunena config rendering.
 *
 * @author 		fxstein
 * @package 	Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
abstract class JHtmlKConfig
{
	/**
	 * Method to generate a section of the config screen. A table that embeds all settings of that section.
	 *
	 * <code>
	 *	<?php echo JHtml::_('kconfig.section', $title, $settings ); ?>
	 * </code>
	 *
	 * ... where $settings is a concatination of individual settings outputs - see setting() function
	 *
	 * @param $title	string	section title of the config section to get rendered
	 * @param $settings	string	the individual settings output concatination
	 * @return	string	The html output for the config section to be rendered.
	 * @since	1.6
	 */
	public static function section($title, $settings)
	{
	    $output =  '<fieldset><legend>'.$title.'</legend>';
	    $output .= '<table class="admintable" cellspacing="1">';
	    $output .= ' <tbody>';
	    $output .= $settings;
	    $output .= ' </tbody>';
	    $output .= '</table></fieldset>';

	    return $output;
	}

	/**
	 * Method to generate an individual settings output for the settings screen.
	 *
	 * <code>
	 *	<?php echo JHtml::_('kconfig.setting', context, $setting, $title, $name, $type, $cols, $rows); ?>
	 * </code>
	 *
	 * @param $context	obj		usually $this
	 * @param $setting	string	the unqiue code name of a setting
	 * @param $title	string	the title of the setting as used oin the ToolTip
	 * @param $name	    string	the public name of the setting as seen by the user
	 * @param $type  	string	the type of setting, initially 'text', 'yes/no' and 'multiple'
	 * @param
	 * @return	string	The html output for the config section to be rendered.
	 * @since	1.6
	 */
	public static function setting($context, $setting, $title, $name, $type='text', $cols=5, $rows=1)
	{
	    $output =  '    <tr>';
	    $output .= '      <td width="40%" class="key">';
	    $output .= '        <label for="config_'.$setting.'" class="hasTip" title="'.$title.'">'.$name.'</label>';
	    $output .= '      </td>';
	    $output .= '      <td valign="top">';

	    switch ($type)
	    {
	        case 'text':
	            $output .= '      <input type="text" name="config['.$setting.']" id="config_'.$setting.'" value="'.$context->options->get($setting).'" size="'.$cols.'" />';

	            break;
	        case 'textarea':
				$output .= '      <textarea name="'.$setting.'" cols="'.$cols.'" rows="'.$rows.'">'.$context->options->get($setting).'</textarea>';

	            break;
	        case 'yes/no':
				$output .= '      '.JHTML::_('select.booleanlist' , $setting , null , $context->options->get($setting) , JText::_('Yes') , JText::_('No') );

	            break;
	        case 'multiple':
	            // TODO implement logic - will need an extra parameter after type that contains the array of choices

	            break;
	    }

	    $output .= '      </td>';
	    $output .= '    </tr>';

	    return $output;
	}

}
