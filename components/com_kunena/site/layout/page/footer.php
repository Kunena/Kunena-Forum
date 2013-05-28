<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutPageFooter extends KunenaLayout
{
	public $rss = null;

	protected function getTime() {
		$config = KunenaFactory::getConfig();
		if (!$config->time_to_create_page) {
			return '';
		}
		$profiler = KunenaProfiler::instance('Kunena');
		$time = $profiler->getTime('Total Time');
		return sprintf('%0.3f', $time);
	}
}
