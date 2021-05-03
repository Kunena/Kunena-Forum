<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   (C) 2011 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Site\Service\Html;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * Utility class for creating HTML Grids
 *
 * @since  6.0
 */
class Kunenagrid
{
	/**
	 * @param   integer  $i          i
	 * @param   mixed    $value      Either the scalar value, or an object (for backward compatibility, deprecated)
	 * @param   string   $prefix     An optional prefix for the task
	 * @param   bool     $bootstrap  bootstrap
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 */
	public static function published(int $i, $value, $prefix = '', $bootstrap = false): string
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
	 * @internal param string $text An optional text to display
	 *
	 * @param   integer       $i          The row index
	 * @param   string        $task       The task to fire
	 * @param   string|array  $prefix     An optional task prefix or an array of options
	 * @param   string        $alt        alt
	 * @param   string        $title      An optional title
	 * @param   string        $class      An optional active HTML class
	 * @param   boolean       $bootstrap  An optional setting for to know if it the link will be used in bootstrap.
	 * @param   string        $img        An optional img HTML tag
	 * @param   string        $checkbox   An optional prefix for checkboxes.
	 *
	 * @return   string The Html code
	 *
	 * @since    Kunena 3.0
	 *
	 */
	public static function action(int $i, string $task, $prefix = '', $alt = '', $title = '', $class = '', $bootstrap = false, $img = '', $checkbox = 'cb'): string
	{
		if (is_array($prefix))
		{
			$options        = $prefix;
			$text           = array_key_exists('text', $options) ? $options['text'] : '';
			$active_title   = array_key_exists('active_title', $options) ? $options['active_title'] : '';
			$inactive_title = array_key_exists('inactive_title', $options) ? $options['inactive_title'] : '';
			$active_class   = array_key_exists('active_class', $options) ? $options['active_class'] : '';
			$inactive_class = array_key_exists('inactive_class', $options) ? $options['inactive_class'] : '';
			$enabled        = array_key_exists('enabled', $options) ? $options['enabled'] : '';
			$translate      = array_key_exists('translate', $options) ? $options['translate'] : '';
			$checkbox       = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix         = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}

		$active        = $task == 'publish' ? 'active' : '';
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($bootstrap && $topicicontype == 'B2')
		{
			$html[] = '<a class="btn btn-micro ' . $active . '" ';
			$html[] = ' href="javascript:void(0);" onclick="return Joomla.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
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

	        $html[] = '<a class="btn btn-outline-primary btn-xs ' . $active . '" ';
	        $html[] = ' href="javascript:void(0);" onclick="return Joomla.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
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

	        $html[] = '<a class="btn btn-outline-primary btn-xs ' . $active . '" ';
	        $html[] = ' href="javascript:void(0);" onclick="return Joomla.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
	        $html[] = ' title="' . $title . '">';
	        $html[] = '<i class="fa fa-' . $class . '" aria-hidden="true">';
	        $html[] = '</i>';
	        $html[] = '</a>';
	    }
	    else
	    {
	        if ($task == 'publish')
	        {
	            $img = '<img loading=lazy src="media/kunena/images/unpublish.png"/>';
	        }

	        if ($task == 'unpublish')
	        {
	            $img = '<img loading=lazy src="media/kunena/images/tick.png"/>';
	        }

	        if ($task == 'edit')
	        {
	            $img = '<img loading=lazy src="media/kunena/images/edit.png"/>';
	        }

	        if ($task == 'delete')
	        {
	            $img = '<img loading=lazy src="media/kunena/images/delete.png"/>';
	        }

	        $html[] = '<a class="grid_' . $task . ' hasTip" alt="' . $alt . '"';
	        $html[] = ' href="#" onclick="return Joomla.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
	        $html[] = 'title="' . $title . '">';
	        $html[] = $img;
	        $html[] = '</a>';
		}

		return (string) $html;
	}
}
