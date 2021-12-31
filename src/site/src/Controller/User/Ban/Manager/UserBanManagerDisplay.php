<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\User\Ban\Manager;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class ComponentUserControllerBanManagerDisplay
 *
 * @since   Kunena 4.0
 */
class UserBanManagerDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $profile;

	/**
	 * @var     KunenaBan
	 * @since   Kunena 6.0
	 */
	public $userBans;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $headerText;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'User/Ban/Manager';

	/**
	 * Prepare ban manager.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function before()
	{
		parent::before();

		$userid        = $this->input->getInt('userid');
		$this->me      = KunenaUserHelper::getMyself();
		$start         = $this->input->getInt('limitstart', 0);
		$limit         = $this->input->getInt('limit', 30);
		$this->moreUri = null;

		$this->embedded = $this->getOptions()->get('embedded', false);

		if ($this->embedded)
		{
			$this->moreUri = new Uri('index.php?option=com_kunena&view=user&layout=banmanager&userid=' . $userid . '&limit=' . $limit);
			$this->moreUri->setVar('Itemid', KunenaRoute::getItemID($this->moreUri));
		}

		// TODO: add authorisation
		$userBanspre = KunenaBan::getBannedUsers(0, 100);
		$count       = \count($userBanspre);

		$this->pagination = new KunenaPagination($count, $start, $limit);
		$this->userBans   = KunenaBan::getBannedUsers($this->pagination->limitstart, $this->pagination->limit);

		if ($this->moreUri)
		{
			$this->pagination->setUri($this->moreUri);
		}

		if (!empty($this->userBans))
		{
			KunenaUserHelper::loadUsers(array_keys($this->userBans));
		}

		$this->headerText = Text::_('COM_KUNENA_BAN_LIST_OF_BANNED_USERS');
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function prepareDocument()
	{
		$menu_item = $this->app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle($this->headerText);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription($this->headerText);
			}
		}
	}
}
