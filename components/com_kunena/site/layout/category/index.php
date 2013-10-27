<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Layout.Category.Index
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutCategoryIndex
 *
 * @since  3.1
 *
 */
class KunenaLayoutCategoryIndex extends KunenaLayout
{
	/**
	 * Method to return a KunenaPagination object
	 *
	 * @param   int  $maxpages  Maximum that are allowed for pagination
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
	 * @param   KunenaCategory  $category  The KunenaCategory object
	 * @param   boolean         $thumb     Define if it's the thumb which will be loaded
	 *
	 * @return string
	 */
	public function getCategoryIcon($category, $thumb = false)
	{
		$path	= JPATH_ROOT . '/media/kunena/' . $this->config->catimagepath . '/';
		$uri	= JUri::root(true) . '/media/kunena/' . $this->config->catimagepath . '/';

		if (!$thumb)
		{
			if ($category->getNewCount())
			{
				// Check Unread Cat Images
				$file = $this->getCategoryIconFile($category->id . '_on', $path);

				if ($file)
				{
					return '<img src="' . $uri . $file . '" border="0" class="kforum-cat-image" alt=" " />';
				}

				return $this->getIcon($this->ktemplate->categoryIcons[1], JText::_('COM_KUNENA_GEN_FORUM_NEWPOST'));
			}
			else
			{
				// Check Read Cat Images
				$file = $this->getCategoryIconFile($category->id . '_off', $path);

				if ($file)
				{
					return '<img src="' . $uri . $file . '" border="0" class="kforum-cat-image" alt=" " />';
				}

				return $this->getIcon($this->ktemplate->categoryIcons[0], JText::_('COM_KUNENA_GEN_FORUM_NOTNEW'));
			}
		}
		elseif ($this->config->showchildcaticon)
		{
			if ($category->getNewCount())
			{
				// Check Unread Cat Images
				$file = $this->getCategoryIconFile($category->id . '_on_childsmall', $path);

				if ($file)
				{
					return '<img src="' . $uri . $file . '" border="0" class="kforum-cat-image" alt=" " />';
				}

				return $this->getIcon($this->ktemplate->categoryIcons[1] . '-sm', JText::_('COM_KUNENA_GEN_FORUM_NEWPOST'));
			}
			else
			{
				// Check Read Cat Images
				$file = $this->getCategoryIconFile($category->id . '_off_childsmall', $path);

				if ($file)
				{
					return '<img src="' . $uri . $file . '" border="0" class="kforum-cat-image" alt=" " />';
				}

				return $this->getIcon($this->ktemplate->categoryIcons[0] . '-sm', JText::_('COM_KUNENA_GEN_FORUM_NOTNEW'));
			}
		}

		return '';
	}

	/**
	 * Method to retrieve the category icon file
	 *
	 * @param   string  $filename  The filename for the category icon
	 * @param   string  $path      The path for the category icon
	 *
	 * @return string|boolean
	 */
	private function getCategoryIconFile($filename, $path = '')
	{
		$types	= array('.gif', '.png', '.jpg');

		foreach ($types as $ext)
		{
			if (is_file($path . $filename . $ext))
			{
				return $filename . $ext;
			}
		}

		return false;
	}

	/**
	 * Method to retrieve the URL of mark read button
	 *
	 * @param   int  $category_id  The category Id
	 * @param   int  $numTopics    The number of topics
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
	 * @param   int     $catid  The Id of category
	 * @param   string  $xhtml  Replace & by & for XML compliance.
	 *
	 * @return string|null
	 */
	public function getCategoryRSSURL($catid, $xhtml = true)
	{
		if (KunenaConfig::getInstance()->enablerss)
		{
			$params = '&catid=' . (int) $catid;

			return KunenaRoute::_("index.php?option=com_kunena&view=topics&format=feed&layout=default&mode=topics{$params}", $xhtml);
		}

		return null;
	}
}
