<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutTopicEditHistory
 *
 * @since  K4.0
 *
 */
class KunenaLayoutTopicEditHistory extends KunenaLayout
{
	/**
	 * Method to get the anker link with number
	 *
	 * @param   int  $mesid     The Id of the messsage
	 * @param   int  $replycnt  The number of replies
	 *
	 * @return string
	 */
	public function getNumLink($mesid, $replycnt)
	{
		if ($this->config->ordering_system == 'replyid')
		{
			$this->numLink = $this->getSamePageAnkerLink($mesid, '#' . $replycnt);
		}
		else
		{
			$this->numLink = $this->getSamePageAnkerLink($mesid, '#' . $mesid);
		}

		return $this->numLink;
	}

	/**
	 * Method to get anker link on the same page
	 *
	 * @param   int     $anker  The anker number
	 * @param   string  $name   The name for the link
	 * @param   string  $rel    The rel attribute for the link
	 * @param   string  $class  The class attibute for the link
	 *
	 * @return string
	 */
	public function getSamePageAnkerLink($anker, $name, $rel = 'nofollow', $class = '')
	{
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . 'href="#' . $anker . '"' . ($rel ? ' rel="' . $rel . '"' : '') . '>' . $name . '</a>';
	}
}
