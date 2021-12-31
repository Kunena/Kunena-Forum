<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    HTML
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 *
 * Taken from Joomla Platform 11.1
 * @copyright     Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Utility class for creating HTML Grids
 * @since Kunena
 */
abstract class JHtmlKunenaGrid
{
	/**
	 * Display a boolean setting widget.
	 *
	 * @param   integer $i       The row index.
	 * @param   integer $value   The value of the boolean field.
	 * @param   string  $taskOn  Task to turn the boolean setting on.
	 * @param   string  $taskOff Task to turn the boolean setting off.
	 *
	 * @return  string   The boolean setting widget.
	 * @since Kunena
	 */
	public static function boolean($i, $value, $taskOn = null, $taskOff = null)
	{
		// Load the behavior.
		self::behavior();

		// Build the title.
		$title = ($value) ? Text::_('COM_KUNENA_YES') : Text::_('COM_KUNENA_NO');
		$title .= '::' . Text::_('COM_KUNENA_LIB_CLICK_TO_TOGGLE_STATE');

		// Build the <a> tag.
		$bool   = ($value) ? 'true' : 'false';
		$task   = ($value) ? $taskOff : $taskOn;
		$toggle = (!$task) ? false : true;

		if ($toggle)
		{
			return '<a class="grid_' . $bool . ' hasTip" title="' . $title . '" rel="{id:\'cb' . $i . '\', task:\'' . $task . '\'}" href="#toggle"></a>';
		}

		return '<a class="grid_' . $bool . '" rel="{id:\'cb' . $i . '\', task:\'' . $task . '\'}"></a>';
	}

	/**
	 * @since Kunena
	 * @return void
	 */
	public static function behavior()
	{
		static $loaded = false;

		if (!$loaded)
		{
			HTMLHelper::_('behavior.tooltip');

			// Build the behavior script.
			$js = '
		window.addEvent(\'domready\', function(){
			actions = $$(\'a.move_up\');
			actions.combine($$(\'a.move_down\'));
			actions.combine($$(\'a.grid_true\'));
			actions.combine($$(\'a.grid_false\'));
			actions.combine($$(\'a.grid_trash\'));
			actions.combine($$(\'a.grid_action\'));
			actions.each(function(a){
				a.addEvent(\'click\', function(){
					args = JSON.decode(this.rel);
					listItemTask(args.id, args.task);
				});
			});
			$$(\'input.check-all-toggle\').each(function(el){
				el.addEvent(\'click\', function(){
					if (el.checked) {
						document.id(this.form).getElements(\'input[type=checkbox]\').each(function(i){
							i.checked = true;
						})
					}
					else {
						document.id(this.form).getElements(\'input[type=checkbox]\').each(function(i){
							i.checked = false;
						})
					}
				});
			});
		});';

			// Add the behavior to the document head.
			$document = Factory::getDocument();
			$document->addScriptDeclaration($js);

			$loaded = true;
		}
	}

	/**
	 * @param   string      $title         The link title
	 * @param   string      $order         The order field for the column
	 * @param   string      $direction     The current direction
	 * @param   string|int  $selected      The selected ordering
	 * @param   string|null $task          An optional task override
	 * @param   string      $new_direction An optional direction for the new column
	 * @param   string|null $form          form
	 *
	 * @return  string
	 * @since Kunena
	 */
	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc', $form = null)
	{
		$direction = strtolower($direction);

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$html = '<a href="javascript:kunenatableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\',\'' . $form . '\');" title="' . Text::_('COM_KUNENA_LIB_CLICK_TO_SORT_THIS_COLUMN') . '">';
		$html .= Text::_($title);

		if ($order == $selected)
		{
			$html .= '<span class="grid_' . $direction . '"></span>';
		}

		$html .= '</a>';

		return $html;
	}

	/**
	 * @param   mixed   $row        row
	 * @param   integer $i          i
	 * @param   string  $identifier identifier
	 *
	 * @return string
	 * @since Kunena
	 */
	public static function checkedOut($row, $i, $identifier = 'id')
	{
		$userid = Factory::getUser()->get('id');

		if ($row instanceof \Joomla\CMS\Table\Table)
		{
			$result = $row->isCheckedOut($userid);
		}
		else
		{
			$result = false;
		}

		if ($result)
		{
			return self::_checkedOut($row);
		}

		if ($identifier == 'id')
		{
			return self::id($i, $row->$identifier);
		}

		return self::id($i, $row->$identifier, $result, $identifier);
	}

	/**
	 * @param   mixed $row     row
	 * @param   int   $overlib overlib
	 *
	 * @return string
	 * @since Kunena
	 */
	protected static function _checkedOut(&$row, $overlib = 1)
	{
		$hover = '';

		if ($overlib)
		{
			$text = addslashes(htmlspecialchars($row->editor, ENT_COMPAT, 'UTF-8'));

			$date = HTMLHelper::_('date', $row->checked_out_time, Text::_('DATE_FORMAT_LC1'));
			$time = HTMLHelper::_('date', $row->checked_out_time, 'H:i');

			$hover = '<span class="editlinktip hasTip" title="' . Text::_('COM_KUNENA_LIB_CHECKED_OUT') . '::' . $text . '<br />' . $date . '<br />' . $time . '">';
		}

		$checked = $hover . HTMLHelper::_('image', 'admin/checked_out.png', null, null, true) . '</span>';

		return $checked;
	}

	/**
	 * @param   integer $rowNum     The row index
	 * @param   integer $recId      The record id
	 * @param   boolean $checkedOut checkout
	 * @param   string  $name       The name of the form element
	 *
	 * @return  string
	 * @since Kunena
	 */
	public static function id($rowNum, $recId, $checkedOut = false, $name = 'cid')
	{
		if ($checkedOut)
		{
			return '';
		}

		return '<input type="checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId . '" onclick="Joomla.isChecked(this.checked);" title="' . Text::sprintf('COM_KUNENA_LIB_CHECKBOX_ROW_N', ($rowNum + 1)) . '" />';
	}

	/**
	 * @param   integer $i         i
	 * @param   mixed   $value     Either the scalar value, or an object (for backward compatibility, deprecated)
	 * @param   string  $prefix    An optional prefix for the task
	 * @param   bool    $bootstrap bootstrap
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public static function published($i, $value, $prefix = '', $bootstrap = false)
	{
		if (is_object($value))
		{
			$value = $value->published;
		}

		$task   = $value ? 'unpublish' : 'publish';
		$alt    = $value ? Text::_('COM_KUNENA_PUBLISHED') : Text::_('COM_KUNENA_UNPUBLISHED');
		$action = $value ? Text::_('COM_KUNENA_LIB_UNPUBLISH_ITEM') : Text::_('COM_KUNENA_LIB_PUBLISH_ITEM');
		$class  = $task == 'unpublish' ? 'publish' : 'unpublish';

		$title = $inactive_title = $alt . '::' . $action;

		return self::action($i, $task, $prefix, $alt, $title, $class, $bootstrap);
	}

	/**
	 * Returns an action on a grid
	 *
	 * @param   integer      $i         The row index
	 * @param   string       $task      The task to fire
	 * @param   string|array $prefix    An optional task prefix or an array of options
	 * @param   string       $alt       alt
	 * @param   string       $title     An optional title
	 * @param   string       $class     An optional active HTML class
	 * @param   boolean      $bootstrap An optional setting for to know if it the link will be used in bootstrap.
	 * @param   string       $img       An optinal img HTML tag
	 * @param   string       $checkbox  An optional prefix for checkboxes.
	 *
	 * @return string The Html code
	 *
	 * @throws Exception
	 * @internal param string $text An optional text to display
	 * @since    3.0
	 */
	public static function action($i, $task, $prefix = '', $alt = '', $title = '', $class = '', $bootstrap = false, $img = '', $checkbox = 'cb')
	{
		if (is_array($prefix))
		{
			$options        = $prefix;
			$text           = array_key_exists('text', $options) ? $options['text'] : $text;
			$active_title   = array_key_exists('active_title', $options) ? $options['active_title'] : $active_title;
			$inactive_title = array_key_exists('inactive_title', $options) ? $options['inactive_title'] : $inactive_title;
			$active_class   = array_key_exists('active_class', $options) ? $options['active_class'] : $active_class;
			$inactive_class = array_key_exists('inactive_class', $options) ? $options['inactive_class'] : $inactive_class;
			$enabled        = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$translate      = array_key_exists('translate', $options) ? $options['translate'] : $translate;
			$checkbox       = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix         = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}

		$active        = $task == 'publish' ? 'active' : '';
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($bootstrap && $topicicontype == 'B2')
		{
			$html[] = '<a class="btn btn-micro ' . $active . '" ';
			$html[] = ' href="javascript:void(0);" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
			$html[] = ' title="' . $title . '">';
			$html[] = '<i class="icon-' . $class . '">';
			$html[] = '</i>';
			$html[] = '</a>';
		}
		elseif ($bootstrap && $topicicontype == 'B3')
		{
			if ($class == 'publish')
			{
				$class = 'ok';
			}

			if ($class == 'unpublish')
			{
				$class = 'remove';
			}

			if ($class == 'delete')
			{
				$class = 'trash';
			}

			$html[] = '<a class="btn btn-default btn-xs ' . $active . '" ';
			$html[] = ' href="javascript:void(0);" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
			$html[] = ' title="' . $title . '">';
			$html[] = '<i class="glyphicon glyphicon-' . $class . '">';
			$html[] = '</i>';
			$html[] = '</a>';
		}
		elseif ($bootstrap && $topicicontype == 'fa')
		{
			if ($class == 'publish')
			{
				$class = 'check';
			}

			if ($class == 'unpublish')
			{
				$class = 'times';
			}

			if ($class == 'edit')
			{
				$class = 'pencil-alt';
			}

			if ($class == 'delete')
			{
				$class = 'trash';
			}

			$html[] = '<a class="btn btn-default btn-xs ' . $active . '" ';
			$html[] = ' href="javascript:void(0);" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
			$html[] = ' title="' . $title . '">';
			$html[] = '<i class="fa fa-' . $class . '" aria-hidden="true">';
			$html[] = '</i>';
			$html[] = '</a>';
		}
		else
		{
			if ($task == 'publish')
			{
				$img = '<img src="media/kunena/images/unpublish.png"/>';
			}

			if ($task == 'unpublish')
			{
				$img = '<img src="media/kunena/images/tick.png"/>';
			}

			if ($task == 'edit')
			{
				$img = '<img src="media/kunena/images/edit.png"/>';
			}

			if ($task == 'delete')
			{
				$img = '<img src="media/kunena/images/delete.png"/>';
			}

			$html[] = '<a class="grid_' . $task . ' hasTip" alt="' . $alt . '"';
			$html[] = ' href="#" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
			$html[] = 'title="' . $title . '">';
			$html[] = $img;
			$html[] = '</a>';
		}

		return implode($html);
	}

	/**
	 * @param   integer $i         i
	 * @param   string  $img       Image for a positive or on value
	 * @param   string  $alt       alt
	 * @param   string  $task      task
	 * @param   string  $prefix    An optional prefix for the task
	 * @param   bool    $bootstrap bootstrap
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public static function task($i, $img, $alt, $task, $prefix = '', $bootstrap = false)
	{
		return self::action($i, $task, $prefix, $alt, '', $task, $bootstrap, '<img src="' . KunenaFactory::getTemplate()->getImagePath($img) . '" alt="' . $alt . '" title="' . $alt . '" />');
	}

	/**
	 * @param   mixed  $rows  rows
	 * @param   string $image image
	 * @param   string $task  task
	 *
	 * @return string
	 * @since Kunena
	 */
	public static function order($rows, $image = 'filesave.png', $task = 'saveorder')
	{
		$href = '<a href="javascript:saveorder(' . (count($rows) - 1) . ', \'' . $task . '\')" class="saveorder" title="' . Text::_('COM_KUNENA_LIB_SAVE_ORDER') . '"></a>';

		return $href;
	}

	/**
	 * @param   mixed  $i       i
	 * @param   mixed  $task    task
	 * @param   bool   $enabled enabled
	 * @param   string $alt     alt
	 *
	 * @return string
	 * @since Kunena
	 */
	public static function orderUp($i, $task, $enabled = true, $alt = 'COM_KUNENA_LIB_MOVE_UP')
	{
		$alt = Text::_($alt);

		if ($enabled)
		{
			return '<a class="move_up" href="#order" rel="{id:\'cb' . $i . '\', task:\'' . $task . '\'}" title="' . $alt . '"></a>';
		}

		return '<span class="move_up"></span>';
	}

	/**
	 * @param   mixed  $i       i
	 * @param   mixed  $task    task
	 * @param   bool   $enabled enabled
	 * @param   string $alt     alt
	 *
	 * @return string
	 * @since Kunena
	 */
	public static function orderDown($i, $task, $enabled = true, $alt = 'COM_KUNENA_LIB_MOVE_DOWN')
	{
		$alt = Text::_($alt);

		if ($enabled)
		{
			return '<a class="move_down" href="#order" rel="{id:\'cb' . $i . '\', task:\'' . $task . '\'}" title="' . $alt . '"></a>';
		}

		return '<span class="move_down"></span>';
	}
}
