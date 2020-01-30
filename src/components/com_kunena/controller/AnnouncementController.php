<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Forum\Announcement\Helper;
use Joomla\Utilities\ArrayHelper;
use function defined;

/**
 * Kunena Announcements Controller
 *
 * @since   Kunena 2.0
 */
class AnnouncementController extends KunenaController
{
	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function none()
	{
		// FIXME: This is workaround for task=none on edit.
		$this->edit();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function publish()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		foreach ($cid as $id)
		{
			$announcement = Helper::get($id);
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
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
			}

			try
			{
				$announcement->save();
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
				$this->setRedirectBack();

				return;
			}

			if ($announcement->isAuthorised('edit') || $announcement->save())
			{
				if ($this->config->log_moderation)
				{
					\Kunena\Forum\Libraries\Log\Log::log(\Kunena\Forum\Libraries\Log\Log::TYPE_MODERATION, \Kunena\Forum\Libraries\Log\Log::LOG_ANNOUNCEMENT_PUBLISH, ['id' => $announcement->id]);
				}

				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_ANN_SUCCESS_PUBLISH', $this->escape($announcement->title)));
			}
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function unpublish()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		foreach ($cid as $id)
		{
			$announcement = Helper::get($id);
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
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
			}

			try
			{
				$announcement->save();
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
				$this->setRedirectBack();

				return;
			}

			if ($announcement->isAuthorised('edit') || !$announcement->save())
			{
				if ($this->config->log_moderation)
				{
					\Kunena\Forum\Libraries\Log\Log::log(\Kunena\Forum\Libraries\Log\Log::TYPE_MODERATION, \Kunena\Forum\Libraries\Log\Log::LOG_ANNOUNCEMENT_UNPUBLISH, ['id' => $announcement->id]);
				}

				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_ANN_SUCCESS_UNPUBLISH', $this->escape($announcement->title)));
			}
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function edit()
	{
		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		$announcement = Helper::get(array_pop($cid));

		$this->setRedirect($announcement->getUrl('edit', false));
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 * @throws  null
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
			$announcement = Helper::get($id);

			try
			{
				$announcement->isAuthorised('delete');
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
			}

			try
			{
				$announcement->delete();
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
				$this->setRedirectBack();

				return;
			}

			if ($announcement->isAuthorised('delete') || $announcement->delete())
			{
				if ($this->config->log_moderation)
				{
					\Kunena\Forum\Libraries\Log\Log::log(\Kunena\Forum\Libraries\Log\Log::TYPE_MODERATION, \Kunena\Forum\Libraries\Log\Log::LOG_ANNOUNCEMENT_DELETE, ['id' => $announcement->id]);
				}

				$this->app->enqueueMessage(Text::_('COM_KUNENA_ANN_DELETED'));
			}
		}

		$this->setRedirect(Helper::getUrl('list', false));
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function save()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$now                    = new Date;
		$fields                 = [];
		$fields['title']        = $this->app->input->getString('title', '');
		$fields['description']  = $this->app->input->getString('description', '');
		$fields['sdescription'] = $this->app->input->getString('sdescription', '');
		$fields['created']      = $this->app->input->getString('created');
		$fields['publish_up']   = $this->app->input->getString('publish_up');
		$fields['publish_down'] = $this->app->input->getString('publish_down');
		$fields['published']    = $this->app->input->getInt('published', 1);
		$fields['showdate']     = $this->app->input->getInt('showdate', 1);

		$id           = $this->app->input->getInt('id');
		$announcement = Helper::get($id);

		if ($fields['created'] == null)
		{
			$fields['created'] = $now->toSql();
		}

		if ($fields['publish_up'] == null)
		{
			$fields['publish_up'] = $now->toSql();
		}

		if ($fields['publish_down'] == null)
		{
			$fields['publish_down'] = '1000-01-01 00:00:00';
		}

		$announcement->bind($fields);

		try
		{
			$announcement->isAuthorised($id ? 'edit' : 'create');
		}
		catch (Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirectBack();

			return;
		}

		try
		{
			$announcement->save();
		}
		catch (Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->config->log_moderation)
		{
			\Kunena\Forum\Libraries\Log\Log::log(\Kunena\Forum\Libraries\Log\Log::TYPE_MODERATION, $id ? \Kunena\Forum\Libraries\Log\Log::LOG_ANNOUNCEMENT_EDIT : \Kunena\Forum\Libraries\Log\Log::LOG_ANNOUNCEMENT_CREATE, ['id' => $announcement->id]);
		}

		$this->app->enqueueMessage(Text::_($id ? 'COM_KUNENA_ANN_SUCCESS_EDIT' : 'COM_KUNENA_ANN_SUCCESS_ADD'));
		$this->setRedirect($announcement->getUrl('default', false));
	}
}
