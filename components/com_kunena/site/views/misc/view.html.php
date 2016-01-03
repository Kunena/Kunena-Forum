<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Misc View
 */
class KunenaViewMisc extends KunenaView
{
	function displayDefault($tpl = null)
	{
		$params       = $this->app->getParams('com_kunena');
		$this->header = $params->get('page_title');
		$this->body   = $params->get('body');

		$this->_prepareDocument();

		$format = $params->get('body_format');

		$this->header = $this->escape($this->header);
		if ($format == 'html')
		{
			$this->body = trim($this->body);
		}
		elseif ($format == 'text')
		{
			$this->body = $this->escape($this->body);
		}
		else
		{
			$this->body = KunenaHtmlParser::parseBBCode($this->body);
		}

		$this->render('Widget/Custom', $tpl);
	}

	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item
		$params = $menu_item->params; // get the params
		$params_title = $params->get('page_title');
		$params_keywords = $params->get('menu-meta_keywords');
		$params_description = $params->get('menu-description');

		if (!empty($params_title))
		{
			$title = $params->get('page_title');
			$this->setTitle($title);
		}
		else
		{
			$this->setTitle($this->header);
		}

		if (!empty($params_keywords))
		{
			$keywords = $params->get('menu-meta_keywords');
			$this->setKeywords($keywords);
		}
		else
		{
			$this->setKeywords($this->header);
		}

		if (!empty($params_description))
		{
			$description = $params->get('menu-meta_description');
			$this->setDescription($description);
		}
		else
		{
			$this->setDescription($this->header);
		}
	}
}
