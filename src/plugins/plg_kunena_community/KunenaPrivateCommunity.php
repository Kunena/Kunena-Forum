<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Community
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Integration\KunenaPrivate;

/**
 * Class KunenaPrivateCommunity
 *
 * @since   Kunena 6.0
 */
class KunenaPrivateCommunity extends KunenaPrivate
{
	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $loaded = false;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaPrivateCommunity constructor.
	 *
	 * @param   object  $params  params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
		CFactory::load('libraries', 'messaging');
	}

	/**
	 * @param   string  $text
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getInboxLink(string $text): string
	{
		if (!$text)
		{
			$text = Text::_('COM_KUNENA_PMS_INBOX');
		}

		return '<a href="' . CRoute::_('index.php?option=com_community&view=inbox') . '" rel="follow">' . $text . '</a>';
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getInboxURL(): string
	{
		return CRoute::_('index.php?option=com_community&view=inbox');
	}

	/**
	 * @param   int  $userid
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	protected function getOnClick(int $userid): string
	{
		if (!$this->loaded)
		{
			// PM popup requires JomSocial css to be loaded from selected template
			$config   = CFactory::getConfig();
			$document = Factory::getApplication()->getDocument();
			$document->addStyleSheet('components/com_community/assets/window.css');
			$document->addStyleSheet('components/com_community/templates/' . $config->get('template') . '/assets/css/style.css');
			$this->loaded = true;
		}

		return ' onclick="' . CMessaging::getPopup($userid) . '"';
	}

	/**
	 * @param   int  $userid
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected function getURL(int $userid): string
	{
		return "javascript:void(0)";
	}
}
