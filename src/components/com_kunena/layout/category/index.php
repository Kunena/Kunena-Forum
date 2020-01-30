<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Category.Index
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Category;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Config\Config;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Icons\Icons;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Layout\Layout;
use Kunena\Forum\Libraries\Pagination\Pagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\Template;
use Kunena\Forum\Libraries\User\Helper;
use function defined;

/**
 * KunenaLayoutCategoryIndex
 *
 * @since   Kunena 4.0
 */
class KunenaLayoutCategoryIndex extends Layout
{
	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	private $total;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $state;

	/**
	 * @var     Template|void
	 * @since   Kunena 6.0
	 *
	 */
	public $ktemplate;

	/**
	 * Method to return a KunenaPagination object
	 *
	 * @param   integer  $maxpages  Maximum that are allowed for pagination
	 *
	 * @return  Pagination
	 * @since   Kunena 6.0
	 */
	public function getPaginationObject($maxpages)
	{
		$pagination = new Pagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
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
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getCategoryIcon($category)
	{
		$template    = KunenaFactory::getTemplate();
		$caticonpath = $template->params->get('DefaultCategoryicon');

		if ($category->getNewCount())
		{
			if (!empty($category->icon))
			{
				return Icons::caticon($category->icon, true, true);
			}
			else
			{
				return Icons::caticon($caticonpath, true, true);
			}
		}
		else
		{
			if (!empty($category->icon))
			{
				return Icons::caticon($category->icon, false, true);
			}
			else
			{
				return Icons::caticon($caticonpath, false, true);
			}
		}
	}

	/**
	 * Method to retrieve small category icon
	 *
	 * @param   object  $subcategory  The KunenaCategory object
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getSmallCategoryIcon($subcategory)
	{
		$this->ktemplate     = KunenaFactory::getTemplate();
		$defaultcategoryicon = $this->ktemplate->params->get('DefaultCategoryicon');

		if ($subcategory->getNewCount())
		{
			if (!empty($subcategory->icon))
			{
				return Icons::caticon($subcategory->icon, true, false);
			}
			else
			{
				return Icons::caticon($defaultcategoryicon, true, false);
			}
		}
		else
		{
			if (!empty($subcategory->icon))
			{
				return Icons::caticon($subcategory->icon, false, false);
			}
			else
			{
				return Icons::caticon($defaultcategoryicon, false, false);
			}
		}
	}

	/**
	 * Method to retrieve the URL of mark read button
	 *
	 * @param   int  $category_id  The category Id
	 * @param   int  $numTopics    The number of topics
	 *
	 * @return  string|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getMarkReadButtonURL($category_id, $numTopics)
	{
		// Is user allowed to mark forums as read?
		if (Helper::getMyself()->exists() && $numTopics)
		{
			$token = '&' . Session::getFormToken() . '=1';

			return KunenaRoute::_("index.php?option=com_kunena&view=category&task=markread&catid={$category_id}{$token}");
		}

		return;
	}

	/**
	 * Method to retrieve the URL of category RSS feed
	 *
	 * @param   integer      $catid  The Id of category
	 * @param   bool|string  $xhtml  Replace & by & for XML compliance.
	 *
	 * @return  string|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getCategoryRSSURL($catid, $xhtml = true)
	{
		if (Config::getInstance()->enablerss)
		{
			$params = '&catid=' . (int) $catid;

			if (CMSApplication::getInstance('site')->get('sef_suffix'))
			{
				return KunenaRoute::_("index.php?option=com_kunena&view=category&format=feed&layout=default{$params}") . '?format=feed&type=rss';
			}
			else
			{
				return KunenaRoute::_("index.php?option=com_kunena&view=category&format=feed&type=rss&layout=default{$params}", $xhtml);
			}
		}

		return;
	}
}
