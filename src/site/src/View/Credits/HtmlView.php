<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\View\Credits;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\View\KunenaView;

/**
 * Topics View
 *
 * @since   Kunena 6.0
 */
class HtmlView extends KunenaView
{
	/**
	 * Display the view
	 *
	 * @param   null  $tpl  The name of the template file to parse
	 *
	 * @return  void  A string if successful, otherwise a JError object.
	 *
	 * @since   1.0
	 * @throws \Exception
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();

		$params = $app->getParams();

		$title = $params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			$this->document->setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			$this->document->setMetaData('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			$this->document->setMetaData('robots', $params->get('robots'));
		}

		return parent::display($tpl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function _prepareDocument()
	{
	}
}
