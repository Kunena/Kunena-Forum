<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Message
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerCategoryIndexActionsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerCategoryIndexActionsDisplay extends KunenaControllerDisplay
{
	protected $name = 'Category/Index/Actions';

	/**
	 * @var KunenaForumTopic
	 */
	public $category;

	public $categoryButtons;
	/**
	 * Prepare message actions display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$catid = $this->input->getInt('id');
		$me = KunenaUserHelper::getMyself();

		$this->category = KunenaForumCategory::getInstance($catid);

		$token = JSession::getFormToken();

		$task = "index.php?option=com_kunena&view=category&task=%s&catid={$catid}&{$token}=1";
		$layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}";

		$this->template = KunenaFactory::getTemplate();
		$this->categoryButtons = new JObject;

		// Is user allowed to post new topic?
		if ($this->category->isAuthorised('topic.create')) {
			$this->categoryButtons->set('create',
				$this->getButton(sprintf($layout, 'create'), 'create', 'topic', 'communication', true)
			);
		}

		// Is user allowed to mark forums as read?
		if ($me->exists()) {
			$this->categoryButtons->set('markread',
				$this->getButton(sprintf($task, 'markread'), 'markread', 'category', 'user', true)
			);
		}

		// Is user allowed to subscribe category?
		if ($this->category->isAuthorised( 'subscribe' )) {
			$subscribed = $this->category->getSubscribed($me->userid);

			if (!$subscribed) {
				$this->categoryButtons->set('subscribe',
					$this->getButton(sprintf($task, 'subscribe'), 'subscribe', 'category', 'user', true)
				);
			} else {
				$this->categoryButtons->set('unsubscribe',
					$this->getButton(sprintf($task, 'unsubscribe'), 'unsubscribe', 'category', 'user', true)
				);
			}
		}

		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onKunenaGetButtons', array('category.action', $this->categoryButtons, $this));
	}

	/**
	 * Get button.
	 *
	 * @param   string       $url    Target link (do not route it).
	 * @param   string       $name   Name of the button.
	 * @param   string       $scope  Scope of the button.
	 * @param   string       $type   Type of the button.
	 * @param   bool         $id     Id of the button.
	 *
	 * @return  string
	 */
	public function getButton($url, $name, $scope, $type, $id = null)
	{
		return KunenaLayout::factory('Widget/Button')
			->setProperties(array('url' => KunenaRoute::_($url), 'name' => $name, 'scope' => $scope, 'type' => $type, 'id' => $id));
	}
}
