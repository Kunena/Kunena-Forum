<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Controllers
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Kunena Announcements Controller
 *
 * @since        2.0
 */
class KunenaControllerAnnouncement extends KunenaController
{

	public function none()
	{
		// FIXME: This is workaround for task=none on edit.
		$this->edit();
	}

	public function publish()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		foreach ($cid as $id)
		{
			$announcement = KunenaForumAnnouncementHelper::get($id);
			if ($announcement->published == 1)
			{
				continue;
			}
			$announcement->published = 1;
			if (!$announcement->authorise('edit') || !$announcement->save())
			{
				$this->app->enqueueMessage($announcement->getError(), 'error');
			}
			else
			{
				$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_ANN_SUCCESS_PUBLISH', $this->escape($announcement->title)));
			}
		}

		$this->setRedirectBack();
	}

	public function unpublish()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		foreach ($cid as $id)
		{
			$announcement = KunenaForumAnnouncementHelper::get($id);

			if ($announcement->published == 0)
			{
				continue;
			}

			$announcement->published = 0;

			if (!$announcement->authorise('edit') || !$announcement->save())
			{
				$this->app->enqueueMessage($announcement->getError(), 'error');
			}
			else
			{
				$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_ANN_SUCCESS_UNPUBLISH', $this->escape($announcement->title)));
			}
		}

		$this->setRedirectBack();
	}

	public function edit()
	{
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$announcement = KunenaForumAnnouncementHelper::get(array_pop($cid));

		$this->setRedirect($announcement->getUrl('edit', false));
	}

	public function delete()
	{
		if (!JSession::checkToken('request'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = JRequest::getVar('cid', (array) JRequest::getInt('id'), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		foreach ($cid as $id)
		{
			$announcement = KunenaForumAnnouncementHelper::get($id);

			if (!$announcement->authorise('delete') || !$announcement->delete())
			{
				$this->app->enqueueMessage($announcement->getError(), 'error');
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_ANN_DELETED'));
			}
		}

		$this->setRedirect(KunenaForumAnnouncementHelper::getUrl('list', false));
	}

	public function save()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$now                    = new JDate();
		$fields                 = array();
		$fields['title']        = JRequest::getString('title', '', 'post', JREQUEST_ALLOWRAW);
		$fields['description']  = JRequest::getString('description', '', 'post', JREQUEST_ALLOWRAW);
		$fields['sdescription'] = JRequest::getString('sdescription', '', 'post', JREQUEST_ALLOWRAW);
		$fields['created']      = JRequest::getString('created', $now->toSql());
		$fields['published']    = JRequest::getInt('published', 1);
		$fields['showdate']     = JRequest::getInt('showdate', 1);

		$id           = JRequest::getInt('id');
		$announcement = KunenaForumAnnouncementHelper::get($id);
		$announcement->bind($fields);

		if (!$announcement->authorise($id ? 'edit' : 'create') || !$announcement->save())
		{
			$this->app->enqueueMessage($announcement->getError(), 'error');
			$this->setRedirectBack();

			return;
		}

		$this->app->enqueueMessage(JText::_($id ? 'COM_KUNENA_ANN_SUCCESS_EDIT' : 'COM_KUNENA_ANN_SUCCESS_ADD'));
		$this->setRedirect($announcement->getUrl('default', false));
	}
}
