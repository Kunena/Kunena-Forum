<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use RuntimeException;

\defined('_JEXEC') or die();

/**
 * Kunena Rank Controller
 *
 * @since   Kunena 3.0
 */
class RankController extends AdminController
{
	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $baseurl = null;

	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 *                                         Recognized key values include 'name', 'default_task', 'model_path', and
	 *                                         'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   Kunena 2.0
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=ranks';
	}

	/**
	 * Add a new rank
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 */
	public function add()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(Route::_('index.php?option=com_kunena&view=rank&layout=add', false));
	}

	/**
	 * Save
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function save($key = null, $urlVar = null)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$rankTitle   = $this->app->input->getString('rankTitle');
		$rankImage   = basename($this->app->input->getString('rankImage'));
		$rankSpecial = $this->app->input->getInt('rankSpecial', 0);
		$rankMin     = $this->app->input->getInt('rankMin');
		$rankid      = $this->app->input->getInt('rankid', 0);

		if (!$rankid)
		{
			$query = $db->getQuery(true);

			// Insert columns.
			$columns = array('rankTitle', 'rankImage', 'rankSpecial', 'rankMin');

			// Insert values.
			$values = array($db->quote($rankTitle), $db->quote($rankImage), $db->quote($rankSpecial), $db->quote($rankMin));

			// Prepare the insert query.
			$query
				->insert($db->quoteName('#__kunena_ranks'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');

				return;
			}
		}
		else
		{
			$query = $db->getQuery(true)
				->update("{$db->quoteName('#__kunena_ranks')}")
				->set("rankTitle={$db->quote($rankTitle)}")
				->set("rankImage={$db->quote($rankImage)}")
				->set("rankSpecial={$db->quote($rankSpecial)}")
				->set("rankMin={$db->quote($rankMin)}")
				->where("rankId={$db->quote($rankid)}");

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');

				return;
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_RANK_SAVED'), 'success');
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
