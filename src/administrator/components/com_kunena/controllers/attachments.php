<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Kunena Attachments Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerAttachments extends KunenaController
{
	/**
	 * @since    2.0.0-BETA2
	 *
	 * @var null|string
	 */
	protected $baseurl = null;

	/**
	 * Constructor
	 *
	 * @param   array $config Construct
	 *
	 * @throws Exception
	 * @since 2.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=attachments';
	}

	/**
	 * Delete
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since 2.0
	 * @throws null
	 */
	public function delete()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		if (!$cid)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_ATTACHMENTS_SELECTED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		foreach ($cid as $id)
		{
			$attachment = KunenaAttachmentHelper::get($id);

			$message     = $attachment->getMessage();
			$attachments = array($attachment->id, 1);
			$attach      = array();
			$removeList  = array_keys(array_diff_key($attachments, $attach));
			$removeList  = ArrayHelper::toInteger($removeList);
			$message->removeAttachments($removeList);

			if ($attachment->inline)
			{
				$find             = array('/\[attachment='.$id.'\](.*?)\[\/attachment\]/su');
				$replace          = '';
				$text             = preg_replace($find, $replace, $message->message);
				$message->message = $text;
			}

			$message->save();

			$topic = $message->getTopic();
			$attachment->delete();

			if ($topic->attachments > 0)
			{
				$topic->attachments = $topic->attachments - 1;
				$topic->save(false);
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_ATTACHMENTS_DELETED_SUCCESSFULLY'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
