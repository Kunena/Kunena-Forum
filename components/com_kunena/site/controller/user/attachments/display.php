<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserAttachmentsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserAttachmentsDisplay extends KunenaControllerDisplay
{
	protected $name = 'User/Attachments';

	/**
	 * @var KunenaUser
	 */
	public $me;

	/**
	 * @var KunenaUser
	 */
	public $profile;

	/**
	 * @var array|KunenaAttachments[]
	 */
	public $attachments;

	public $headerText;

	/**
	 * Prepare user attachments list.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$userid = $this->input->getInt('userid');
		$params = array('file' => '1', 'image' => '1', 'orderby' => 'desc', 'limit' => '30');

		$this->template = KunenaFactory::getTemplate();
		$this->me = KunenaUserHelper::getMyself();
		$this->profile = KunenaUserHelper::get($userid);
		$this->attachments = KunenaAttachmentHelper::getByUserid($this->profile, $params);

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

		$this->headerText = JText::_('COM_KUNENA_MANAGE_ATTACHMENTS');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}
}
