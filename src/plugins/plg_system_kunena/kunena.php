<?php
/**
 * Kunena System Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      System
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class plgSystemKunena
 * @since Kunena
 */
class plgSystemKunena extends \Joomla\CMS\Plugin\CMSPlugin
{
	/**
	 * @param   object $subject  Subject
	 * @param   array  $config   Config
	 *
	 * @since Kunena
	 */
	function __construct(&$subject, $config)
	{
		// Check if Kunena API exists
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

		if (!is_file($api))
		{
			return;
		}

		jimport('joomla.application.component.helper');

		// Check if Kunena component is installed/enabled
		if (!\Joomla\CMS\Component\ComponentHelper::isEnabled('com_kunena', true))
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

		if (!\Joomla\CMS\Plugin\PluginHelper::isEnabled('kunena', 'powered'))
		{
			$styles = <<<EOF
		.layout#kunena + div { display: block !important;}
		#kunena + div { display: block !important;}
EOF;

			$document = \Joomla\CMS\Factory::getDocument();
			$document->addStyleDeclaration($styles);
		}

		if (!method_exists(KunenaControllerApplicationDisplay::class, 'poweredBy'))
		{
			\Joomla\CMS\Factory::getApplication()->enqueueMessage('Please Buy Official powered by remover plugin on: https://www.kunena.org/downloads',
				'notice');
		}

		// ! Always load language after parent::construct else the name of plugin isn't yet set
		$this->loadLanguage('plg_system_kunena.sys');
	}

	/**
	 * @internal
	 *
	 * @param   string  $context  Context
	 * @param   boolean $params   Params
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
	 */
	protected function runJoomlaContentEvent(&$text, &$params, $page = 0)
	{
		$dispatcher = JEventDispatcher::getInstance();
		\Joomla\CMS\Plugin\PluginHelper::importPlugin('content');

		$row       = new stdClass;
		$row->text = &$text;

		$dispatcher->trigger('onContentPrepare', array('text', &$row, &$params, 0));

		$text = &$row->text;

		return $text;
	}

	/**
	 * @param   mixed    $user     User
	 * @param   boolean  $isnew    Is new
	 * @param   boolean  $success  Success
	 * @param   string   $msg      Message
	 *
	 * @return void
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
	 * @since Kunena
	 */
	public function onExtensionBeforeInstall($method, $type, $manifest, $eid)
	{
		// We don't want to handle discover install (where there's no manifest provided)
		if (!$manifest)
		{
			return null;
		}

		return $this->onExtensionBeforeUpdate($type, $manifest);
	}

	/**
	 * Prevent downgrades to Kunena 1.7 and older releases
	 *
	 * @param   boolean $type      type
	 * @param   string  $manifest  manifest
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
		$app = \Joomla\CMS\Factory::getApplication();
		$app->enqueueMessage(sprintf('Sorry, it is not possible to downgrade Kunena %s to version %s.',
			KunenaForum::version(), $manifest->version), 'warning');
		$app->enqueueMessage(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_CUSTOM_INSTALL_FAILURE'), 'error');
		$app->enqueueMessage(JText::sprintf('COM_INSTALLER_MSG_UPDATE_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($type))));
		$app->redirect('index.php?option=com_installer');

		return true;
	}
}
