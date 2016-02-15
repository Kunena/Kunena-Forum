<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Application
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationTopicUnreadDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerApplicationTopicUnreadDisplay extends KunenaControllerApplicationDisplay
{
	/**
	 * Return true if layout exists.
	 *
	 * @return bool
	 */
	public function exists()
	{
		return KunenaFactory::getTemplate()->isHmvc();
	}

	/**
	 * Redirect unread layout to the page that contains the first unread message.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		$catid = $this->input->getInt('catid', 0);
		$id = $this->input->getInt('id', 0);

		$category = KunenaForumCategoryHelper::get($catid);
		$category->tryAuthorise();

		$topic = KunenaForumTopicHelper::get($id);
		$topic->tryAuthorise();

		KunenaForumTopicHelper::fetchNewStatus(array($topic->id => $topic));
		$message = KunenaForumMessageHelper::get($topic->lastread ? $topic->lastread : $topic->last_post_id);
		$message->tryAuthorise();

		while (@ob_end_clean());

		$this->app->redirect($topic->getUrl($category, false, $message));
	}
}
