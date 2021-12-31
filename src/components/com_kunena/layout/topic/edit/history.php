<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutTopicEditHistory
 *
 * @since  K4.0
 */
class KunenaLayoutTopicEditHistory extends KunenaLayout
{
	/**
	 * Method to get the anchor link with number
	 *
	 * @param   int $mesid    The Id of the message
	 * @param   int $replycnt The number of replies
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getNumLink($mesid, $replycnt)
	{
		if ($this->config->ordering_system == 'replyid')
		{
			$this->numLink = $this->getSamePageAnchorLink($mesid, '#' . $replycnt);
		}
		else
		{
			$this->numLink = $this->getSamePageAnchorLink($mesid, '#' . $mesid);
		}

		return $this->numLink;
	}

	/**
	 * Method to get anchor link on the same page
	 *
	 * @param   int    $anchor The anchor number
	 * @param   string $name   The name for the link
	 * @param   string $rel    The rel attribute for the link
	 * @param   string $class  The class attribute for the link
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getSamePageAnchorLink($anchor, $name, $rel = 'nofollow', $class = '')
	{
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . 'href="#' . $anchor . '"' . ($rel ? ' rel="' . $rel . '"' : '') . '>' . $name . '</a>';
	}
}
