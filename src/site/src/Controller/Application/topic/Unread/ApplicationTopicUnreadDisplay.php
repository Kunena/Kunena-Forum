<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Application\Topic\Unread;

defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use function defined;

/**
 * Class ComponentKunenaControllerApplicationTopicUnreadDisplay
 *
 * @since   Kunena 4.0
 */
class ApplicationTopicUnreadDisplay extends KunenaControllerDisplay
{
	/**
	 * Return true if layout exists.
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function exists()
	{
		return KunenaFactory::getTemplate()->isHmvc();
	}

	/**
	 * Redirect unread layout to the page that contains the first unread message.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 */
	protected function before()
	{
		$catid = $this->input->getInt('catid', 0);
		$id    = $this->input->getInt('id', 0);

		$category = KunenaCategoryHelper::get($catid);
		$category->tryAuthorise();

		$topic = KunenaTopicHelper::get($id);
		$topic->tryAuthorise();

		KunenaTopicHelper::fetchNewStatus([$topic->id => $topic]);
		$message = KunenaMessageHelper::get($topic->lastread ? $topic->lastread : $topic->last_post_id);
		$message->tryAuthorise();

		while (@ob_end_clean())
		{
		}

		$this->app->redirect($topic->getUrl($category, false, $message));
	}

	/**
	 * Prepare document.
	 *
	 * @return boolean
	 *
	 * @since   Kunena 6.0
	 *
	 */
	protected function prepareDocument()
	{
		$this->setMetaData('robots', 'follow, noindex');
	}
}
