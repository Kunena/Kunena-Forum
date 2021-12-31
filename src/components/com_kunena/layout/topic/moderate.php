<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * KunenaLayoutTopicModerate
 *
 * @since  K4.0
 */
class KunenaLayoutTopicModerate extends KunenaLayout
{
	/**
	 * Method to get the options of the topic
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getTopicOptions()
	{
		$options = array();

		// Start with default options.
		if (!$this->message)
		{
			$options[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_MODERATION_MOVE_TOPIC'));
		}
		else
		{
			$options[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_MODERATION_CREATE_TOPIC'));
		}

		$options[] = HTMLHelper::_('select.option', -1, Text::_('COM_KUNENA_MODERATION_ENTER_TOPIC'));

		// Then list a few topics.
		$db     = Factory::getDbo();
		$params = array(
			'orderby' => 'tt.last_post_time DESC',
			'where'   => " AND tt.id != {$db->Quote($this->topic->id)} ", );
		list($total, $topics) = KunenaForumTopicHelper::getLatestTopics($this->category->id, 0, 30, $params);

		foreach ($topics as $topic)
		{
			$options[] = HTMLHelper::_('select.option', $topic->id, $this->escape($topic->subject));
		}

		return $options;
	}

	/**
	 * Method to get the list of categories
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getCategoryList()
	{
		$options = array();
		$params  = array('sections' => 0, 'catid' => 0);

		return HTMLHelper::_(
			'kunenaforum.categorylist', 'targetcategory', 0, $options, $params, 'class="inputbox kmove_selectbox form-control"',
			'value', 'text', $this->category->id, 'kmod_categories'
		);
	}
}
