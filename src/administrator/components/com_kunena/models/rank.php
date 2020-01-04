<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;

/**
 * Rank Model for Kunena
 *
 * @since  3.0
 */
class KunenaAdminModelRank extends KunenaModel
{
	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getRankspaths()
	{
		$template = KunenaFactory::getTemplate();

		$selected = $this->getRank();

		$rankpath = $template->getRankPath();
		$files1   = (array) Folder::Files(JPATH_SITE . '/' . $rankpath, false, false, false, array('index.php', 'index.html'));
		$files1   = (array) array_flip($files1);

		foreach ($files1 as $key => &$path)
		{
			$path = $rankpath . $key;
		}

		$rankpath = 'media/kunena/ranks/';
		$files2   = (array) Folder::Files(JPATH_SITE . '/' . $rankpath, false, false, false, array('index.php', 'index.html'));
		$files2   = (array) array_flip($files2);

		foreach ($files2 as $key => &$path)
		{
			$path = $rankpath . $key;
		}

		$rank_images = $files1 + $files2;
		ksort($rank_images);

		$rank_list = array();

		foreach ($rank_images as $file => $path)
		{
			$rank_list[] = HTMLHelper::_('select.option', $path, $file);
		}

		$list = HTMLHelper::_('select.genericlist', $rank_list, 'rank_image', 'class="inputbox form-control" onchange="update_rank(this.options[selectedIndex].value);" onmousemove="update_rank(this.options[selectedIndex].value);"', 'value', 'text', isset($selected->rank_image) ? $rank_images[$selected->rank_image] : '');

		return $list;
	}

	/**
	 * @return  mixed|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getRank()
	{
		$db = Factory::getDBO();

		$id = $this->getState($this->getName() . '.id');

		if ($id)
		{
			$query = $db->getQuery(true);
			$query->select('*')
				->from($db->quoteName('#__kunena_ranks'))
				->where('rank_id=' . $db->quote($id));
			$db->setQuery($query);

			try
			{
				$selected = $db->loadObject();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}

			return $selected;
		}

		return;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function populateState()
	{
		$this->context = 'com_kunena.admin.rank';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$value = Factory::getApplication()->input->getInt('id');
		$this->setState($this->getName() . '.id', $value);
		$this->setState('item.id', $value);
	}
}
