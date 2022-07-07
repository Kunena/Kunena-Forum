<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * Class plgKunenaComprofiler
 *
 * @since   Kunena 6.0
 */
class plgKunenaComprofiler extends CMSPlugin
{
	/**
	 * @var     string  CB version 2.7.2 works with Php 8.1 and with Joomla! 4.0/4.1
	 * @since   Kunena 6.0
	 */
	public $minCBVersion = '2.7.2';

	/**
	 * plgKunenaComprofiler constructor.
	 *
	 * @param   object  $subject                The object to observe
	 * @param   array   $config                 An optional associative array of configuration settings.
	 *                                          Recognized key values include 'name', 'group', 'params', 'language'
	 *                                          (this list is not meant to be comprehensive).
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function __construct(object &$subject, $config = [])
	{
		global $ueConfig;

		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.0') && KunenaForum::enabled()))
		{
			return;
		}

		$app = Factory::getApplication();

		// Do not load if CommunityBuilder is not installed
		if ((!file_exists(JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php')) ||
			(!file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php'))
		)
		{
			return;
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';

		cbimport('cb.html');
		cbimport('language.front');

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_comprofiler.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_comprofiler.sys', KPATH_ADMIN);

		if ($app->isClient('administrator') && (!isset($ueConfig ['version']) || version_compare($ueConfig ['version'], $this->minCBVersion) < 0))
		{
			$app->enqueueMessage(Text::sprintf('PLG_KUNENA_COMPROFILER_WARN_VERSION', $this->minCBVersion), 'notice');
		}
	}

	/**
	 * @param   string  $type    type
	 * @param   null    $view    view
	 * @param   null    $params  params
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function onKunenaDisplay(string $type, $view = null, $params = null): void
	{
		$integration = KunenaFactory::getProfile();

		if (!$integration instanceof KunenaProfileComprofiler)
		{
			return;
		}

		switch ($type)
		{
			case 'start':
				$integration->open();
				break;
			case 'end':
				$integration->close();
		}
	}

	/**
	 * @param   string  $context  context
	 * @param   int     $item     items
	 * @param   object  $params   params
	 * @param   int     $page     page
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function onKunenaPrepare(string $context, &$item, object $params, $page = 0): void
	{
		if ($context == 'kunena.user')
		{
			$triggerParams = ['userid' => $item->userid, 'userinfo' => &$item];
			$integration   = KunenaFactory::getProfile();

			if ($integration instanceof KunenaProfileComprofiler)
			{
				KunenaProfileComprofiler::trigger('profileIntegration', $triggerParams);
			}
		}
	}

	/**
	 * Get Kunena access control object.
	 *
	 * @return  KunenaAccessComprofiler|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAccessControl()
	{
		if (!isset($this->params))
		{
			return;
		}

		if (!$this->params->get('access', 1))
		{
			return;
		}

		require_once __DIR__ . "/KunenaAccessComprofiler.php";

		return new KunenaAccessComprofiler($this->params);
	}

	/**
	 * Get Kunena login integration object.
	 *
	 * @return  KunenaLoginComprofiler|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetLogin()
	{
		if (!isset($this->params))
		{
			return;
		}

		if (!$this->params->get('login', 1))
		{
			return;
		}

		require_once __DIR__ . "/KunenaLoginComprofiler.php";

		return new KunenaLoginComprofiler($this->params);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return  KunenaAvatarComprofiler|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAvatar()
	{
		if (!isset($this->params))
		{
			return;
		}

		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		require_once __DIR__ . "/KunenaAvatarComprofiler.php";

		return new KunenaAvatarComprofiler($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return  KunenaProfileComprofiler|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetProfile()
	{
		if (!isset($this->params))
		{
			return;
		}

		if (!$this->params->get('profile', 1))
		{
			return;
		}

		require_once __DIR__ . "/KunenaProfileComprofiler.php";

		return new KunenaProfileComprofiler($this->params);
	}

	/**
	 * Get Kunena private message integration object.
	 *
	 * @return  KunenaPrivateComprofiler|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetPrivate()
	{
		if (!isset($this->params))
		{
			return;
		}

		if (!$this->params->get('private', 1))
		{
			return;
		}

		require_once __DIR__ . "/KunenaPrivateComprofiler.php";

		return new KunenaPrivateComprofiler($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return  KunenaActivityComprofiler|void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetActivity()
	{
		if (!isset($this->params))
		{
			return;
		}

		if (!$this->params->get('activity', 1))
		{
			return;
		}

		require_once __DIR__ . "/KunenaActivityComprofiler.php";

		return new KunenaActivityComprofiler($this->params);
	}
}
