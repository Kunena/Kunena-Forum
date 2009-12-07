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
	    $output =  '<fieldset><legend>'.$title.'</legend><table class="admintable" cellspacing="1"><tbody>';
	    $output .= $settings;
	    $output .= '</tbody></table></fieldset>';

	    return $output;
	}

	/**
	 * Method to generate an individual settings output for the settings screen.
	 *
	 * <code>
	 *	<?php echo JHtml::_('kconfig.setting', $var, $setting, $title, $name, $type, $extra1, $extra2); ?>
	 * </code>
	 *
	 * @param $var	    string	content of the variable that hold this setting
	 * @param $setting	string	the unqiue code name of a setting
	 * @param $title	string	the title of the setting as used oin the ToolTip
	 * @param $name	    string	the public name of the setting as seen by the user
	 * @param $type  	string	the type of setting, initially 'text', 'yes/no' and 'multiple'
	 * @param
	 * @return	string	The html output for the config section to be rendered.
	 * @since	1.6
	 */
	public static function setting($var, $setting, $name, $title, $type='text', $extra1=5, $extra2=1, $info='')
	{
	    $output =  '<tr><td width="40%" class="key">';
	    if ($type != 'info')
	    {
	        $output .= '<label for="config_'.$setting.'" class="hasTip" title="'.$name.'::'.$title.'">'.$name.'</label>';
	    }
	    $output .= '</td><td valign="top">';

	    switch ($type)
	    {
	        case 'text':
	            $output .= '<input type="text" name="config['.$setting.']" id="config_'.$setting.'" value="'.$var.'" size="'.$extra1.'" /> '.$info;

	            break;
	        case 'textarea':
				$output .= '<textarea name="'.$setting.'" cols="'.$extra1.'" rows="'.$extra2.'">'.$var.'</textarea> '.$info;

	            break;
	        case 'editor':
	            $editor =& JFactory::getEditor();
	            $output .= $editor->display( $setting,  htmlspecialchars($var, ENT_QUOTES), '100%', $extra2 * 10, $extra1, $extra2, false ).' '.$info ;

	            break;
	        case 'yes/no':
				$output .= JHTML::_('select.booleanlist' , $setting , null , $var , JText::_('Yes') , JText::_('No') ).' '.$info;

	            break;
	        case 'list':
	            $output .= JHTML::_('select.genericlist',  $extra1, $setting, 'class="inputbox" size="'.$extra2.'"', 'value', 'text', $var);

	            break;
	        case 'multiple':
	            $output .= JHTML::_('select.genericlist',  $extra1, $setting, 'class="inputbox" size="'.($extra2==0?count($extra1):$extra2).'" multiple="multiple"', 'value', 'text', $var);

	            break;
	        case 'info':
	            $output .= $title;

	            break;
	        default:
	            JError::raiseWarning( 0, 'JHtmlKConfig.setting() setting type: ' .$type. ' not supported' );
	    }

	    $output .= '</td></tr>';

	    return $output;
	}

}
