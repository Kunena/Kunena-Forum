<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerUserAttachmentsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserAttachmentsDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'User/Attachments';

	/**
	 * @var KunenaUser
	 * @since Kunena
	 */
	public $me;

	/**
	 * @var KunenaUser
	 * @since Kunena
	 */
	public $profile;

	/**
	 * @var array
	 * @since Kunena
	 */
	public $attachments;

	/**
	 * @var string
	 * @since Kunena
	 */
	public $headerText;

	/**
	 * Prepare user attachments list.
	 *
	 * @return void
	 * @throws Exception
	 * @throws null
	 * @throws void
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$userid = $this->input->getInt('userid');
		$start  = $this->input->getInt('limitstart', 0);
		$limit  = $this->input->getInt('limit', 30);

		$this->template = KunenaFactory::getTemplate();
		$this->me       = KunenaUserHelper::getMyself();
		$this->profile  = KunenaUserHelper::get($userid);
		$this->moreUri  = null;

		$this->embedded = $this->getOptions()->get('embedded', false);

		if ($this->embedded)
		{
			$this->moreUri = new \Joomla\CMS\Uri\Uri('index.php?option=com_kunena&view=user&layout=attachments&userid=' . $userid . '&limit=' . $limit);
			$this->moreUri->setVar('Itemid', KunenaRoute::getItemID($this->moreUri));
		}

		$finder = new KunenaAttachmentFinder;
		$finder->where('userid', '=', $userid);

		$this->total      = $finder->count();
		$this->pagination = new KunenaPagination($this->total, $start, $limit);

		if (!KunenaConfig::getInstance()->show_imgfiles_manage_profile || !$this->me->exists() && !KunenaConfig::getInstance()->pubprofile)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
		}

		if ($this->moreUri)
		{
			$this->pagination->setUri($this->moreUri);
		}

		$this->attachments = $finder
			->order('id', -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		// Pre-load messages.
		$messageIds = array();

		foreach ($this->attachments as $attachment)
		{
			$messageIds[] = (int) $attachment->mesid;
		}

		$messages = KunenaForumMessageHelper::getMessages($messageIds, 'none');

		// Pre-load topics.
		$topicIds = array();

		foreach ($messages as $message)
		{
			$topicIds[] = $message->thread;
		}

		KunenaForumTopicHelper::getTopics($topicIds, 'none');

		$this->headerText = Text::_('COM_KUNENA_MANAGE_ATTACHMENTS');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle($this->headerText);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$this->setKeywords($this->headerText);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription($this->headerText);
			}
		}
	}
}
