<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Controllers
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Kunena Attachments Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerAttachments extends KunenaController
{
	protected $baseurl = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=attachments';
	}

	public function delete()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		if (!$cid)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_ATTACHMENTS_SELECTED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		foreach ($cid as $id)
		{
			$attachment = KunenaAttachmentHelper::get($id);

			$message = $attachment->getMessage();
			$attachments = array($attachment->id, 1);
			$attach = array();
			$removeList = array_keys(array_diff_key($attachments, $attach));
			JArrayHelper::toInteger($removeList);
			$message->removeAttachments($removeList);
			$message->save();

			$topic = $message->getTopic();
			$attachment->delete();

			if ( $topic->attachments > 0 )
			{
				$topic->attachments = $topic->attachments - 1;
				$topic->save(false);
			}
		}

		$this->app->enqueueMessage(JText::_('COM_KUNENA_ATTACHMENTS_DELETED_SUCCESSFULLY'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
