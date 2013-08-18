<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Layout.Announcement.Edit
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutAnnouncementEdit extends KunenaLayout
{
	public function displayInput($name, $attributes='', $id=null) {
		switch ($name) {
			case 'id':
				return '<input type="hidden" name="id" value="'.intval($this->announcement->id).'" />';
			case 'title':
				return '<input type="text" name="title" $attributes value="'.$this->escape($this->announcement->title).'"/>';
			case 'sdescription':
				return '<textarea name="sdescription" $attributes>'.$this->escape($this->announcement->sdescription).'</textarea>';
			case 'description':
				return '<textarea name="description" $attributes>'.$this->escape($this->announcement->description).'</textarea>';
			case 'created':
				return JHtml::_('calendar', $this->escape($this->announcement->created), 'created', $id);
			case 'showdate':
				$options	= array();
				$options[]	= JHtml::_('select.option',  '0', JText::_('COM_KUNENA_NO') );
				$options[]	= JHtml::_('select.option',  '1', JText::_('COM_KUNENA_YES') );
				return JHtml::_('select.genericlist',  $options, 'showdate', $attributes, 'value', 'text', $this->announcement->showdate, $id );
			case 'published':
				$options	= array();
				$options[]	= JHtml::_('select.option',  '0', JText::_('COM_KUNENA_NO') );
				$options[]	= JHtml::_('select.option',  '1', JText::_('COM_KUNENA_YES') );
				return JHtml::_('select.genericlist',  $options, 'published', $attributes, 'value', 'text', $this->announcement->published, $id );
		}
		return '';
	}
}
