<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicItemRatingDisplay
 *
 * @since  K5.0
 */
class ComponentKunenaControllerTopicItemRatingDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Item/Rating';

	/**
	 * @var KunenaForumTopic
	 */
	public $topic;

	/**
	 * Prepare topic actions display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();
	}
}
