<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Category.Index
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Category;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * KunenaLayoutCategoryIndex
 *
 * @since   Kunena 4.0
 */
class CategoryIndex extends KunenaLayout
{
	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $state;

	/**
	 * @var     KunenaTemplate|void
	 * @since   Kunena 6.0
	 */
	public $ktemplate;

	/**
	 * @var     KunenaTemplate|void
	 * @since   Kunena 6.0
	 */
	public $categorylist;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $total;

	/**
	 * Method to return a KunenaPagination object
	 *
	 * @param   integer  $maxpages  Maximum that are allowed for pagination
	 *
	 * @return  KunenaPagination
	 * @since   Kunena 6.0
	 */
	public function getPaginationObject($maxpages)
	{
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination;
	}

	/**
	 * Method to retrieve category icon
	 *
	 * @param   object  $category  The KunenaCategory object
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getCategoryIcon($category)
	{
		$template    = KunenaFactory::getTemplate();
		$caticonpath = $template->params->get('DefaultCategoryicon');

		if ($category->getNewCount())
		{
			if (!empty($category->icon))
			{
				return KunenaIcons::caticon((string) $category->icon, true, true);
			}

			return KunenaIcons::caticon((string) $caticonpath, true, true);
		}

		if ($category->icon == ' ' || $category->icon == null)
		{
			$category->icon = $caticonpath;
		}

		if (!empty($category->icon))
		{
			return KunenaIcons::caticon((string) $category->icon, false, true);
		}

		return KunenaIcons::caticon((string) $caticonpath, false, true);
	}

	/**
	 * Method to retrieve small category icon
	 *
	 * @param   object  $subcategory  The KunenaCategory object
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getSmallCategoryIcon($subcategory)
	{
		$this->ktemplate     = KunenaFactory::getTemplate();
		$defaultcategoryicon = $this->ktemplate->params->get('DefaultCategoryicon');

		if ($subcategory->getNewCount())
		{
			if (!empty($subcategory->icon))
			{
				return KunenaIcons::caticon($subcategory->icon, true, false);
			}

			return KunenaIcons::caticon($defaultcategoryicon, true, false);
		}

		if (!empty($subcategory->icon))
		{
			return KunenaIcons::caticon($subcategory->icon, false, false);
		}

		return KunenaIcons::caticon($defaultcategoryicon, false, false);
	}

	/**
	 * Method to retrieve the URL of mark read button
	 *
	 * @param   int  $categoryId  The category Id
	 * @param   int  $numTopics   The number of topics
	 *
	 * @return  string|void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function getMarkReadButtonURL($categoryId, $numTopics)
	{
		// Is user allowed to mark forums as read?
		if (KunenaUserHelper::getMyself()->exists() && $numTopics)
		{
			$token = '&' . Session::getFormToken() . '=1';

			return KunenaRoute::_("index.php?option=com_kunena&view=category&task=markread&catid={$categoryId}{$token}");
		}

		return;
	}

	/**
	 * Method to retrieve the URL of category RSS feed
	 *
	 * @param   integer      $catid  The Id of category
	 * @param   bool|string  $xhtml  Replace & by & for XML compliance.
	 *
	 * @return  boolean|string
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function getCategoryRSSURL($catid, $xhtml = true)
	{
		if (KunenaConfig::getInstance()->enableRss)
		{
			$params = '&catid=' . (int) $catid;

			if (CMSApplication::getInstance('site')->get('sef_suffix'))
			{
				return KunenaRoute::_("index.php?option=com_kunena&view=category&format=feed&layout=feed{$params}") . '?format=feed&type=rss';
			}

			return KunenaRoute::_("index.php?option=com_kunena&view=category&format=feed&type=rss&layout=feed{$params}", $xhtml);
		}

		return false;
	}
}
