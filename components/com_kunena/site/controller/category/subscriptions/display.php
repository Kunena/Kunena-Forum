<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerCategorySubscriptionsDisplay
 */
class ComponentKunenaControllerCategorySubscriptionsDisplay extends KunenaControllerDisplay
{
	protected $total = array();
	protected $categories = array();

	protected function display()
	{
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Category/List')
			->set('header', $this->title)
			->set('categories', $this->categories)
			->set('pagination', $this->pagination)
			->set('config', $this->config)
			->set('actions', $this->getActions())
			->setLayout('flat');
		return $content;
	}

	protected function before()
	{
		parent::before();

		$limit = $this->input->getInt('limit', 0);
		if ($limit < 1 || $limit > 100) $limit = 20;

		$limitstart = $this->input->getInt('limitstart', 0);
		if ($limitstart < 0) $limitstart = 0;

		$this->config = KunenaConfig::getInstance();
		$this->title = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS');

		$me = KunenaUserHelper::getMyself();
		list($total, $this->categories) = KunenaForumCategoryHelper::getLatestSubscriptions($me->userid);

		$topicIds = array();
		$userIds = array();
		$postIds = array();
		foreach ($this->categories as $category) {
			// Get list of topics.
			if ($category->last_topic_id) {
				$topicIds[$category->last_topic_id] = $category->last_topic_id;
			}
		}

		// Pre-fetch topics (also display unauthorized topics as they are in allowed categories).
		$topics = KunenaForumTopicHelper::getTopics($topicIds, 'none');

		// Pre-fetch users (and get last post ids for moderators).
		foreach ($topics as $topic) {
			$userIds[$topic->last_post_userid] = $topic->last_post_userid;
			$postIds[$topic->id] = $topic->last_post_id;
		}
		KunenaUserHelper::loadUsers($userIds);
		KunenaForumMessageHelper::getMessages($postIds);

		// Pre-fetch user related stuff.
		if ($me->exists() && !$me->isBanned()) {
			// Load new topic counts.
			KunenaForumCategoryHelper::getNewTopics(array_keys($this->categories));
		}
		$this->pagination = new JPagination($total, $limitstart, $limit);
	}

	public function getActions() {
		$options = array();
		$options[] = JHtml::_('select.option', 'none', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		$options[] = JHtml::_('select.option', 'unsubscribe', JText::_('COM_KUNENA_UNSUBSCRIBE_SELECTED'));

		return $options;
	}
}
