<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Item\Rating;

defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Forum\Topic\Topic;
use function defined;

/**
 * Class ComponentTopicControllerItemRatingDisplay
 *
 * @since   Kunena 5.0
 */
class ComponentTopicControllerItemRatingDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     Topic
	 * @since   Kunena 6.0
	 */
	public $topic;
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Item/Rating';

	/**
	 * Prepare topic actions display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();
	}
}
