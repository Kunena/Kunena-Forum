<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use RuntimeException;

/**
 * Rank Model for Kunena
 *
 * @since  3.0
 */
class RankModel extends AdminModel
{
	/**
	 * @inheritDoc
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load data
	 *
	 * @return void
	 *
	 * @since  Kunena 6.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		// TODO: Implement getForm() method.
	}

	/**
	 * @return  mixed
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getRanksPaths()
	{
		$template = KunenaFactory::getTemplate();

		$selected = $this->getRank();

		$rankPath = $template->getRankPath();
		$files1   = (array) Folder::Files(JPATH_SITE . '/' . $rankPath, false, false, false, ['index.php', 'index.html']);
		$files1   = (array) array_flip($files1);

		foreach ($files1 as $key => &$path)
		{
			$path = $rankPath . $key;
		}

		$rankPath = 'media/kunena/ranks/';
		$files2   = (array) Folder::Files(JPATH_SITE . '/' . $rankPath, false, false, false, ['index.php', 'index.html']);
		$files2   = (array) array_flip($files2);

		foreach ($files2 as $key => &$path)
		{
			$path = $rankPath . $key;
		}

		$rankImages = $files1 + $files2;
		ksort($rankImages);

		$rankList = [];

		foreach ($rankImages as $file => $path)
		{
			$rankList[] = HTMLHelper::_('select.option', $path, $file);
		}

		return HTMLHelper::_('select.genericlist', $rankList, 'rankImage', 'class="inputbox form-control" onchange="update_rank(this.options[selectedIndex].value);" onmousemove="update_rank(this.options[selectedIndex].value);"', 'value', 'text', isset($selected->rankImage) ? $rankImages[$selected->rankImage] : '');
	}

	/**
	 * @return  mixed|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getRank()
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		$id = $this->getState($this->getName() . '.id');

		if ($id)
		{
			$query = $db->getQuery(true);
			$query->select('*')
				->from($db->quoteName('#__kunena_ranks'))
				->where('rankId=' . $db->quote($id));
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
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   null  $ordering   ordering
	 * @param   null  $direction  direction
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	protected function populateState($ordering = null, $direction = null): void
	{
		$context = 'com_kunena.admin.rank';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');

		if ($layout)
		{
			$context .= '.' . $layout;
		}

		$value = Factory::getApplication()->input->getInt('id');
		$this->setState($this->getName() . '.id', $value);
		$this->setState('item.id', $value);
	}
}
