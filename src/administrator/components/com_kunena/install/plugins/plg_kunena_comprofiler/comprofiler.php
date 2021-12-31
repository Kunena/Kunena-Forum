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

/**
 * Class plgKunenaComprofiler
 * @since Kunena
 */
class plgKunenaComprofiler extends \Joomla\CMS\Plugin\CMSPlugin
{
	/**
	 * @var string
	 * @since Kunena
	 */
	public $minCBVersion = '2.0.0';

	/**
	 * plgKunenaComprofiler constructor.
	 *
	 * @param $subject
	 * @param $config
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function __construct(&$subject, $config)
	{
		global $ueConfig;

		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		$app = Factory::getApplication();

		// Do not load if CommunityBuilder is not installed
		if ((!file_exists(JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php')) || (!file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php')))
		{
			return;
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';

		cbimport('cb.html');
		cbimport('language.front');

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_comprofiler.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_comprofiler.sys', KPATH_ADMIN);

		require_once __DIR__ . "/integration.php";

		if ($app->isClient('administrator') && (!isset($ueConfig ['version']) || version_compare($ueConfig ['version'], $this->minCBVersion) < 0))
		{
			$app->enqueueMessage(Text::sprintf('PLG_KUNENA_COMPROFILER_WARN_VERSION', $this->minCBVersion), 'notice');
		}
	}

	/**
	 * @param        $type
	 * @param   null $view   view
	 * @param   null $params params
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onKunenaDisplay($type, $view = null, $params = null)
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
	 * @param       $context
	 * @param       $item
	 * @param       $params
	 * @param   int $page page
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onKunenaPrepare($context, &$item, &$params, $page = 0)
	{
		if ($context == 'kunena.user')
		{
			$triggerParams = array('userid' => $item->userid, 'userinfo' => &$item);
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
	 * @return KunenaAccess|KunenaAccessComprofiler
	 * @since Kunena
	 */
	public function onKunenaGetAccessControl()
	{
		if (!$this->params->get('access', 1))
		{
			return;
		}

		require_once __DIR__ . "/access.php";

		return new KunenaAccessComprofiler($this->params);
	}

	/**
	 * Get Kunena login integration object.
	 *
	 * @return KunenaLogin|KunenaLoginComprofiler
	 * @since Kunena
	 */
	public function onKunenaGetLogin()
	{
		if (!$this->params->get('login', 1))
		{
			return;
		}

		require_once __DIR__ . "/login.php";

		return new KunenaLoginComprofiler($this->params);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return KunenaAvatar
	 * @since Kunena
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarComprofiler($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return KunenaProfile
	 * @since Kunena
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileComprofiler($this->params);
	}

	/**
	 * Get Kunena private message integration object.
	 *
	 * @return KunenaPrivate
	 * @since Kunena
	 */
	public function onKunenaGetPrivate()
	{
		if (!$this->params->get('private', 1))
		{
			return;
		}

		require_once __DIR__ . "/private.php";

		return new KunenaPrivateComprofiler($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return KunenaActivity
	 * @since Kunena
	 */
	public function onKunenaGetActivity()
	{
		if (!$this->params->get('activity', 1))
		{
			return;
		}

		require_once __DIR__ . "/activity.php";

		return new KunenaActivityComprofiler($this->params);
	}
}
