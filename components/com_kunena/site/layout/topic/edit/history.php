<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Layout.Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutTopicEditHistory extends KunenaLayout
{
	public function getNumLink($mesid, $replycnt) {
		if ($this->config->ordering_system == 'replyid') {
			$this->numLink = $this->getSamePageAnkerLink ( $mesid, '#' . $replycnt );
		} else {
			$this->numLink = $this->getSamePageAnkerLink ( $mesid, '#' . $mesid );
		}

		return $this->numLink;
	}

	public function getSamePageAnkerLink($anker, $name, $rel = 'nofollow', $class = '') {
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . 'href="#' . $anker .'"'. ($rel ? ' rel="' . $rel . '"' : '') . '>' . $name . '</a>';
	}
}
