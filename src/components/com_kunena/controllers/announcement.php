<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Kunena Announcements Controller
 *
 * @since  2.0
 */
class KunenaControllerAnnouncement extends KunenaController
{
	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function none()
	{
		// FIXME: This is workaround for task=none on edit.
		$this->edit();
	}

	/**
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function publish()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->app->input->post->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);

		foreach ($cid as $id)
		{
			$announcement = KunenaForumAnnouncementHelper::get($id);
			$date_today   = Factory::getDate();

			if ($announcement->published == 1 && $announcement->publish_up > $date_today && $announcement->publish_down > $date_today)
			{
				continue;
			}

			$announcement->published = 1;

			try
			{
				$announcement->isAuthorised('edit');
			}
			catch (\Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
			}

			try
			{
				$announcement->save();
			}
			catch (\Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
				$this->setRedirectBack();

				return;
			}

			if ($announcement->isAuthorised('edit') || $announcement->save())
			{
				if ($this->config->log_moderation)
				{
					KunenaLog::log(KunenaLog::TYPE_MODERATION, KunenaLog::LOG_ANNOUNCEMENT_PUBLISH, array('id' => $announcement->id));
				}

				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_ANN_SUCCESS_PUBLISH', $this->escape($announcement->title)));
			}
		}

		$this->setRedirectBack();
	}

	/**
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function unpublish()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		foreach ($cid as $id)
		{
			$announcement = KunenaForumAnnouncementHelper::get($id);
			$date_today   = Factory::getDate();

			if ($announcement->published == 0 && $announcement->publish_down > $date_today && $announcement->publish_down > $date_today)
			{
				continue;
			}

			$announcement->published = 0;

			try
			{
				$announcement->isAuthorised('edit');
			}
			catch (\Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
			}

			try
			{
				$announcement->save();
			}
			catch (\Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
				$this->setRedirectBack();

				return;
			}

			if ($announcement->isAuthorised('edit') || !$announcement->save())
			{
				if ($this->config->log_moderation)
				{
					KunenaLog::log(KunenaLog::TYPE_MODERATION, KunenaLog::LOG_ANNOUNCEMENT_UNPUBLISH, array('id' => $announcement->id));
				}

				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_ANN_SUCCESS_UNPUBLISH', $this->escape($announcement->title)));
			}
		}

		$this->setRedirectBack();
	}

	/**
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function edit()
	{
		$cid = $this->app->input->post->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);

		$announcement = KunenaForumAnnouncementHelper::get(array_pop($cid));

		$this->setRedirect($announcement->getUrl('edit', false));
	}

	/**
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function delete()
	{
		if (!Session::checkToken('request'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->app->input->get('cid', (array) $this->app->input->getInt('id'), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		foreach ($cid as $id)
		{
			$announcement = KunenaForumAnnouncementHelper::get($id);

			try
			{
				$announcement->isAuthorised('delete');
			}
			catch (\Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
			}

			try
			{
				$announcement->delete();
			}
			catch (\Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
				$this->setRedirectBack();

				return;
			}

			if ($announcement->isAuthorised('delete') || $announcement->delete())
			{
				if ($this->config->log_moderation)
				{
					KunenaLog::log(KunenaLog::TYPE_MODERATION, KunenaLog::LOG_ANNOUNCEMENT_DELETE, array('id' => $announcement->id));
				}

				$this->app->enqueueMessage(Text::_('COM_KUNENA_ANN_DELETED'));
			}
		}

		$this->setRedirect(KunenaForumAnnouncementHelper::getUrl('list', false));
	}

	/**
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function save()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$now                    = new \Joomla\CMS\Date\Date;
		$fields                 = array();
		$fields['title']        = $this->app->input->getString('title', '', 'post', 'raw');
		$fields['description']  = $this->app->input->getString('description', '', 'post', 'raw');
		$fields['sdescription'] = $this->app->input->getString('sdescription', '', 'post', 'raw');
		$fields['created']      = $this->app->input->getString('created', $now->toSql());
		$fields['publish_up']   = $this->app->input->getString('publish_up', $now->toSql());
		$fields['publish_down'] = $this->app->input->getString('publish_down', $now->toSql());
		$fields['published']    = $this->app->input->getInt('published', 1);
		$fields['showdate']     = $this->app->input->getInt('showdate', 1);

		$id           = $this->app->input->getInt('id');
		$announcement = KunenaForumAnnouncementHelper::get($id);
		$announcement->bind($fields);

		try
		{
			$announcement->isAuthorised($id ? 'edit' : 'create');
		}
		catch (\Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirectBack();

			return;
		}

		try
		{
			$announcement->save();
		}
		catch (\Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->config->log_moderation)
		{
			KunenaLog::log(KunenaLog::TYPE_MODERATION, $id ? KunenaLog::LOG_ANNOUNCEMENT_EDIT : KunenaLog::LOG_ANNOUNCEMENT_CREATE, array('id' => $announcement->id));
		}

		$this->app->enqueueMessage(Text::_($id ? 'COM_KUNENA_ANN_SUCCESS_EDIT' : 'COM_KUNENA_ANN_SUCCESS_ADD'));
		$this->setRedirect($announcement->getUrl('default', false));
	}
}
