<?php
/**
 * Kunena System Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      System
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class plgSystemKunena
 * @since Kunena
 */
class plgSystemKunena extends \Joomla\CMS\Plugin\CMSPlugin
{
	/**
	 * @param   object $subject Subject
	 * @param   array  $config  Config
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function __construct(&$subject, $config)
	{
		// Check if Kunena API exists
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

		if (!is_file($api))
		{
			return;
		}

		jimport('joomla.application.component.helper');

		// Check if Kunena component is installed/enabled
		if (!\Joomla\CMS\Component\ComponentHelper::isEnabled('com_kunena'))
		{
			return;
		}

		// Load Kunena API
		require_once $api;

		// Do not load if Kunena version is not supported or Kunena is not installed
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		parent::__construct($subject, $config);

		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if (!empty($format) && $format != 'html')
		{
			if ($app->scope == 'com_kunena')
			{
				if (!\Joomla\CMS\Plugin\PluginHelper::isEnabled('kunena', 'powered'))
				{
					$styles = <<<EOF
		.layout#kunena + div { display: block !important;}
		#kunena + div { display: block !important;}
EOF;

					KunenaTemplate::getInstance()->addStyleDeclaration($styles);
				}
			}

			if (!method_exists(KunenaControllerApplicationDisplay::class, 'poweredBy'))
			{
				Factory::getApplication()->enqueueMessage('Please Buy Official powered by remover plugin on: https://www.kunena.org/downloads',
					'notice');
			}
		}

		// ! Always load language after parent::construct else the name of plugin isn't yet set
		$this->loadLanguage('plg_system_kunena.sys');
	}

	/**
	 * @internal
	 *
	 * @param   string  $context Context
	 * @param   boolean $params  Params
	 *
	 * @since Kunena
	 * @return void
	 */
	public function onKunenaGetConfiguration($context, &$params)
	{
		if ($context == 'kunena.configuration')
		{
			$params["plg_{$this->_type}_{$this->_name}"] = $this->params;
		}
	}

	/**
	 * @param   mixed   $user    User
	 * @param   boolean $isnew   Is new
	 * @param   boolean $success Success
	 * @param   string  $msg     Message
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		// Don't continue if the user wasn't stored successfully
		if (!$success)
		{
			return;
		}

		if ($isnew && intval($user ['id']))
		{
			$kuser = KunenaFactory::getUser(intval($user ['id']));
			$kuser->save();
		}
	}

	/**
	 * Prevent downgrades to Kunena 1.7 and older releases
	 *
	 * @param   string $method   method
	 * @param   string $type     type
	 * @param   string $manifest manifest
	 * @param   int    $eid      id
	 *
	 * @return boolean|null
	 * @throws Exception
	 * @since Kunena
	 */
	public function onExtensionBeforeInstall($method, $type, $manifest, $eid)
	{
		// We don't want to handle discover install (where there's no manifest provided)
		if (!$manifest)
		{
			return;
		}

		return $this->onExtensionBeforeUpdate($type, $manifest);
	}

	/**
	 * Prevent downgrades to Kunena 1.7 and older releases
	 *
	 * @param   boolean $type     type
	 * @param   string  $manifest manifest
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function onExtensionBeforeUpdate($type, $manifest)
	{
		if ($type != 'component')
		{
			return true;
		}

		// Generate component name
		$name    = strtolower(\Joomla\CMS\Filter\InputFilter::getInstance()->clean((string) $manifest->name, 'cmd'));
		$element = (substr($name, 0, 4) == "com_") ? $name : "com_{$name}";

		if ($element != 'com_kunena')
		{
			return true;
		}

		// Kunena 2.0.0-BETA2 and later support this feature in their installer
		if (version_compare($manifest->version, '2.0.0', '>='))
		{
			return true;
		}

		// Check if we can downgrade to the current version
		if (class_exists('KunenaInstaller') && KunenaInstaller::canDowngrade($manifest->version))
		{
			return true;
		}

		// Old version detected: emulate failed installation
		$app = Factory::getApplication();
		$app->enqueueMessage(sprintf('Sorry, it is not possible to downgrade Kunena %s to version %s.',
			KunenaForum::version(), $manifest->version), 'warning');
		$app->enqueueMessage(Text::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_CUSTOM_INSTALL_FAILURE'), 'error');
		$app->enqueueMessage(Text::sprintf('COM_INSTALLER_MSG_UPDATE_ERROR', Text::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($type))));
		$app->redirect('index.php?option=com_installer');

		return true;
	}

	/**
	 * Runs all Joomla content plugins on a single KunenaForumMessage
	 *
	 * @access protected
	 * @see    self::onKunenaPrepare()
	 * @since  Kunena 2.0
	 *
	 * @param   string $text   String to run events on
	 * @param   object $params \Joomla\Registry\Registry object holding eventual parameters
	 * @param   int    $page   An integer holding page number
	 *
	 * @return object KunenaForumMessage
	 * @throws Exception
	 */
	protected function runJoomlaContentEvent(&$text, &$params, $page = 0)
	{

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('content');

		$row       = new stdClass;
		$row->text = &$text;

		Factory::getApplication()->triggerEvent('onContentPrepare', array('text', &$row, &$params, 0));

		$text = &$row->text;

		return $text;
	}

	/**
	 * Adds the Kunena Privacy Information to Joomla Privacy plugin.
	 *
	 * @return array
	 *
	 * @since Kunena 5.1.6
	 */
	public function onPrivacyCollectAdminCapabilities()
	{
		$capabilities = array(
			'Kunena' => array(
				Text::_('PLG_SYSTEM_KUNENA_PRIVACY_CAPABILITY_EMAIL'),
				Text::_('PLG_SYSTEM_KUNENA_PRIVACY_CAPABILITY_IP_ADDRESS'),
				Text::_('PLG_SYSTEM_KUNENA_PRIVACY_CAPABILITY_USERPROFILE'),
				Text::_('PLG_SYSTEM_KUNENA_PRIVACY_CAPABILITY_POSTS'),
				Text::_('PLG_SYSTEM_KUNENA_PRIVACY_CAPABILITY_RATINGS'),
				Text::_('PLG_SYSTEM_KUNENA_PRIVACY_CAPABILITY_STATISTICS'),
				Text::_('PLG_SYSTEM_KUNENA_PRIVACY_CAPABILITY_COOKIES'),
				Text::_('PLG_SYSTEM_KUNENA_PRIVACY_CAPABILITY_LOGS'),
				Text::_('PLG_SYSTEM_KUNENA_PRIVACY_CAPABILITY_SOCIAL')
			),
		);

		return $capabilities;
	}
}
