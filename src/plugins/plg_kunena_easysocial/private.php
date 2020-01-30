<?php
/**
 * @package        EasySocial
 * @copyright      Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

namespace Kunena\Forum\Plugin\Kunena\Easysocial;

defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Integration\KunenaPrivate;
use function defined;

/**
 * @package     Kunena
 *
 * @since   Kunena 6.0
 */
class KunenaPrivateEasySocial extends KunenaPrivate
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
	 * KunenaPrivateEasySocial constructor.
	 *
	 * @param   object  $params params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($params)
	{
		$this->params = $params;

		// Process scripts
		ES::initialize();
	}

	/**
	 * @param   int  $userid userid
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected function getOnClick($userid)
	{
		$userid = (int) $userid;

		return ' data-es-conversations-compose data-es-conversations-id="' . $userid . '"';
	}

	/**
	 * @param   int  $userid userid
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected function getURL($userid)
	{
		return "javascript:void(0)";
	}

	/**
	 * @param   string  $text text
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getInboxLink($text)
	{
		if (!$text)
		{
			$text = Text::_('COM_KUNENA_PMS_INBOX');
		}

		$url = $this->getInboxURL();

		return '<a href="' . $url . '" rel="follow">' . $text . '</a>';
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getInboxURL()
	{
		return FRoute::conversations();
	}
}
