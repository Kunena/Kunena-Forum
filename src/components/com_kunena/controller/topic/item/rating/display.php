<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicItemRatingDisplay
 *
 * @since  K5.0
 */
class ComponentKunenaControllerTopicItemRatingDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Topic/Item/Rating';

	/**
	 * @var KunenaForumTopic
	 * @since Kunena
	 */
	public $topic;

	/**
	 * Prepare topic actions display.
	 *
	 * @return void
	 * @since Kunena
	 * @throws Exception
	 */
	protected function before()
	{
		parent::before();
	}
}
