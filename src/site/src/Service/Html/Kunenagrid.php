<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Service
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 */

namespace Kunena\Forum\Site\Service\Html;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;

/**
 * Utility class for creating HTML Grids
 *
 * @since  6.0
 */
class Kunenagrid
{
    /**
     * @param   string       $title          The link title
     * @param   string       $order          The order field for the column
     * @param   string       $direction      The current direction
     * @param   string|int   $selected       The selected ordering
     * @param   string|null  $task           An optional task override
     * @param   string       $new_direction  An optional direction for the new column
     * @param   string|null  $form           form
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public static function sort(string $title, string $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc', $form = null): string
    {
        $direction = strtolower($direction);

        if ($order != $selected) {
            $direction = $new_direction;
        } else {
            $direction = ($direction == 'desc') ? 'asc' : 'desc';
        }

        $html = '<a href="javascript:kunenatableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\',\'' . $form . '\');" data-bs-toggle="tooltip" title="' . Text::_('COM_KUNENA_LIB_CLICK_TO_SORT_THIS_COLUMN') . '">';
        $html .= Text::_($title);

        if ($order == $selected) {
            $html .= '<span class="grid_' . $direction . '"></span>';
        }

        $html .= '</a>';

        return $html;
    }

    /**
     * @param   integer  $rowNum      The row index
     * @param   integer  $recId       The record id
     * @param   boolean  $checkedOut  checkout
     * @param   string   $name        The name of the form element
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public static function id(int $rowNum, int $recId, $checkedOut = false, $name = 'cid'): string
    {
        if ($checkedOut) {
            return '';
        }

        return '<input type="checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId . '" onclick="Joomla.isChecked(this.checked);" data-bs-toggle="tooltip" title="' . Text::sprintf('COM_KUNENA_LIB_CHECKBOX_ROW_N', ($rowNum + 1)) . '" />';
    }

    /**
     * @param   integer  $i          i
     * @param   mixed    $value      Either the scalar value, or an object (for backward compatibility, deprecated)
     * @param   string   $prefix     An optional prefix for the task
     * @param   bool     $bootstrap  bootstrap
     *
     * @return  string
     *
     * @throws Exception
     * @throws Exception
     * @since   Kunena 6.0
     */
    public static function published(int $i, $value, $prefix = '', $bootstrap = false)
    {
        if (\is_object($value)) {
            $value = $value->published;
        }

        $task   = $value ? 'unpublish' : 'publish';
        $alt    = $value ? Text::_('COM_KUNENA_PUBLISHED') : Text::_('COM_KUNENA_UNPUBLISHED');
        $action = $value ? Text::_('COM_KUNENA_LIB_UNPUBLISH_ITEM') : Text::_('COM_KUNENA_LIB_PUBLISH_ITEM');
        $class  = $task == 'unpublish' ? 'publish' : 'unpublish';

        $title = $alt . '::' . $action;

        return self::action($i, $task, $prefix, $alt, $title, $class, $bootstrap);
    }

    /**
     * Returns an action on a grid
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
     * @throws Exception
     * @internal param string $text An optional text to display
     *
     * @since    Kunena 3.0
     */
    public static function action(int $i, string $task, $prefix = '', $alt = '', $title = '', $class = '', $bootstrap = false, $img = '', $checkbox = 'cb')
    {
        if (\is_array($prefix)) {
            $options        = $prefix;
            $checkbox       = \array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix         = \array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }

        $active        = $task == 'publish' ? 'active' : '';
        $ktemplate     = KunenaFactory::getTemplate();
        $topicicontype = $ktemplate->params->get('topicicontype');

        if ($bootstrap || $topicicontype == 'svg') {
            $icon = KunenaIcons::check();

            if ($class == 'publish') {
                $icon  = KunenaIcons::check();
            }

            if ($class == 'unpublish') {
                $icon  = KunenaIcons::cancel();
            }

            if ($class == 'edit') {
                $icon  = KunenaIcons::pencil();
            }

            if ($class == 'delete') {
                $icon  = KunenaIcons::delete();
            }

            $html[] = '<a class="btn btn-outline-primary btn-xs ' . $active . '" ';
            $html[] = ' href="javascript:void(0);" onclick="return Joomla.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
            $html[] = ' data-bs-toggle="tooltip" title="' . $title . '" aria-label="' . htmlspecialchars((string) $alt, ENT_QUOTES, 'UTF-8') . '">';
            $html[] = $icon;
            $html[] = '</a>';
        } elseif ($bootstrap && $topicicontype == 'fa') {
            if ($class == 'publish') {
                $class = 'check';
            }

            if ($class == 'unpublish') {
                $class = 'times';
            }

            if ($class == 'edit') {
                $class = 'pencil-alt';
            }

            if ($class == 'delete') {
                $class = 'trash';
            }

            $html[] = '<a class="btn btn-outline-primary btn-xs ' . $active . '" ';
            $html[] = ' href="javascript:void(0);" onclick="return Joomla.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
            $html[] = ' data-bs-toggle="tooltip" title="' . $title . '" aria-label="' . htmlspecialchars((string) $alt, ENT_QUOTES, 'UTF-8') . '">';
            $html[] = '<i class="fa fa-' . $class . '" aria-hidden="true">';
            $html[] = '</i>';
            $html[] = '</a>';
        } else {
            if ($task == 'publish') {
                $img = '<img alt="" loading=lazy src="media/kunena/images/unpublish.png"/>';
            }

            if ($task == 'unpublish') {
                $img = '<img alt="" loading=lazy src="media/kunena/images/tick.png"/>';
            }

            if ($task == 'edit') {
                $img = '<img alt="" loading=lazy src="media/kunena/images/edit.png"/>';
            }

            if ($task == 'delete') {
                $img = '<img alt="" loading=lazy src="media/kunena/images/delete.png"/>';
            }

            $html[] = '<a class="grid_' . $task . ' hasTip" alt="' . $alt . '"';
            $html[] = ' href="#" onclick="return Joomla.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
            $html[] = 'data-bs-toggle="tooltip" title="' . $title . '">';
            $html[] = $img;
            $html[] = '</a>';
        }

        return implode($html);
    }

    /**
     * @param   integer  $i          i
     * @param   string   $img        Image for a positive or on value
     * @param   string   $alt        alt
     * @param   string   $task       task
     * @param   string   $prefix     An optional prefix for the task
     * @param   bool     $bootstrap  bootstrap
     *
     * @return  string
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public static function task(int $i, string $img, string $alt, string $task, $prefix = '', $bootstrap = false)
    {
        return self::action($i, $task, $prefix, $alt, '', $task, $bootstrap, '<img loading=lazy src="' . KunenaFactory::getTemplate()->getImagePath($img) . '" alt="' . $alt . '" data-bs-toggle="tooltip" title="' . $alt . '" />');
    }
}
