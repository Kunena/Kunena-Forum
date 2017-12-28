<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Layout.Category.Index
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutCategoryIndex
 *
 * @since  K4.0
 *
 */
class KunenaLayoutCategoryIndex extends KunenaLayout
{
	/**
	 * Method to return a KunenaPagination object
	 *
	 * @param   int $maxpages Maximum that are allowed for pagination
	 *
	 * @return KunenaPagination
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
	 * @param   KunenaCategory $category The KunenaCategory object
	 *
	 * @return string
	 */
	public function getCategoryIcon($category)
	{
		$template = KunenaFactory::getTemplate();
		$catimagepath = $template->params->get('DefaultCategoryicon');

		$path = JPATH_ROOT . '/media/kunena/' . $catimagepath . '/';
		$uri  = JUri::root(true) . '/media/kunena/' . $catimagepath . '/';

		if ($category->getNewCount())
		{
			if (!empty($category->icon))
			{
				return KunenaIcons::caticon($category->icon, true, true);
			}
			else
			{
				return KunenaIcons::caticon($catimagepath, true, true);
			}
		}
		else
		{
			if (!empty($category->icon))
			{
				return KunenaIcons::caticon($category->icon, false, true);
			}
			else
			{
				return KunenaIcons::caticon($catimagepath, false, true);
			}
		}
	}

	/**
	 * Method to retrieve small category icon
	 *
	 * @param   KunenaSubCategory $subcategory The KunenaCategory object
	 *
	 * @return string
	 */
	public function getSmallCategoryIcon($subcategory)
	{
		$this->ktemplate = KunenaFactory::getTemplate();
		$defaultcategoryicon = $this->ktemplate->params->get('DefaultCategoryicon');

		if ($subcategory->getNewCount())
		{
			if (!empty($subcategory->icon))
			{
				return KunenaIcons::caticon($subcategory->icon, true, false);
			}
			else
			{
				return KunenaIcons::caticon($defaultcategoryicon, true, false);
			}
		}
		else
		{
			if (!empty($subcategory->icon))
			{
				return KunenaIcons::caticon($subcategory->icon, false, false);
			}
			else
			{
				return KunenaIcons::caticon($defaultcategoryicon, false, false);
			}
		}
	}

	/**
	 * Method to retrieve the URL of mark read button
	 *
	 * @param   int $category_id The category Id
	 * @param   int $numTopics   The number of topics
	 *
	 * @return string|null
	 */
	public function getMarkReadButtonURL($category_id, $numTopics)
	{
		// Is user allowed to mark forums as read?
		if (KunenaUserHelper::getMyself()->exists() && $numTopics)
		{
			$token = '&' . JSession::getFormToken() . '=1';

			$url = KunenaRoute::_("index.php?option=com_kunena&view=category&task=markread&catid={$category_id}{$token}");

			return $url;
		}

		return null;
	}

	/**
	 * Method to retrieve the URL of category RSS feed
	 *
	 * @param   int       $catid The Id of category
	 * @param bool|string $xhtml Replace & by & for XML compliance.
	 *
	 * @return null|string
	 */
	public function getCategoryRSSURL($catid, $xhtml = true)
	{
		if (KunenaConfig::getInstance()->enablerss)
		{
			$params = '&catid=' . (int) $catid;

			return KunenaRoute::_("index.php?option=com_kunena&view=category&format=feed&layout=default{$params}", $xhtml);
		}

		return null;
	}
}
