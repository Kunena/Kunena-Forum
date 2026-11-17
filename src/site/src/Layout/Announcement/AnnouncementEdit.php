<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Announcement.Edit
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Announcement;

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Forum\Announcement\KunenaAnnouncement;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutAnnouncementEdit
 *
 * @since   Kunena 4.0
 */
class AnnouncementEdit extends KunenaLayout
{
    /**
     * @var     KunenaAnnouncement
     * @since   Kunena 6.0
     */
    public mixed $announcement = null;

    public mixed $output = null;

    public mixed $user = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $config = null;

    /**
     * Method to create an input in function of name given
     *
     * @param   string  $name        Name of input to create
     * @param   string  $attributes  Attibutes to be added to input
     * @param   int|false  $id       Id to be added to the input
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function displayInput($name, $attributes = '', $id = false)
    {
        switch ($name) {
            case 'id':
                return '<input type="hidden" name="id" value="' . \intval($this->announcement->id) . '" />';
            case 'title':
                return '<input type="text" name="title" ' . $attributes . ' value="' . $this->escape($this->announcement->title) . '"/>';
            case 'sdescription':
                return '<textarea name="sdescription" ' . $attributes . '>' . $this->escape($this->announcement->sdescription) . '</textarea>';
            case 'description':
                return '<textarea name="description" ' . $attributes . '>' . $this->escape($this->announcement->description) . '</textarea>';
            case 'created':
            case 'publish_up':
            case 'publish_down':
                // The date and time field of the browser (its calendar and its clock, in the format of the language of the user):
                // it sends Y-m-dTH:i, which the controller reads with KunenaDate::fromDateTimeLocal().
                return '<input type="datetime-local" class="form-control"' . ($id ? ' id="' . $this->escape($id) . '"' : '') . ' name="' . $name
                    . '" value="' . $this->escape(KunenaDate::toDateTimeLocal((string) $this->announcement->{$name})) . '">' . $attributes;
            case 'showdate':
                $options   = [];
                $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_NO'));
                $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_YES'));

                return HTMLHelper::_('select.genericlist', $options, 'showdate', $attributes, 'value', 'text', $this->announcement->showdate, $id);
            case 'published':
                $options   = [];
                $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_NO'));
                $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_YES'));

                return HTMLHelper::_('select.genericlist', $options, 'published', $attributes, 'value', 'text', $this->announcement->published, $id);
        }

        return '';
    }
}
