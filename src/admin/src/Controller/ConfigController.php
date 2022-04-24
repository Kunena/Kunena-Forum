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

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\String\StringHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Kunena Backend Config Controller
 *
 * @since   Kunena 2.0
 * @property  KunenaConfig
 */
class ConfigController extends FormController
{
	/**
	 * @var     null|string
	 * @since   Kunena 2.0.0-BETA2
	 */
	protected $baseurl = null;

	/**
	 * @var     string
	 * @since   Kunena 2.0.0-BETA2
	 */
	protected $kunenabaseurl = null;

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0.0-BETA2
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl       = 'administrator/index.php?option=com_kunena&view=config';
		$this->kunenabaseurl = 'administrator/index.php?option=com_kunena&view=cpanel';
	}

	/**
	 * Apply
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0.0-BETA2
	 */
	public function apply(): void
	{
		$this->save(null, $this->baseurl);
	}

	/**
	 * Save
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0.0-BETA2
	 */
	public function save($key = null, $urlVar = null)
	{
		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->kunenabaseurl, false));

			return;
		}

		$properties = KunenaConfig::getInstance()->getProperties();
		$postConfig = $this->app->input->post->getArray();

		foreach ($postConfig as $postsetting => $postvalue)
		{
			if (StringHelper::strpos($postsetting, 'cfg_') === 0)
			{
				// Remove cfg_ from config variable

				if (\is_array($postvalue))
				{
					$postvalue = implode(',', $postvalue);
				}

				$postname = StringHelper::substr($postsetting, 4);

				if ($postname == 'imageWidth' || $postname == 'imageHeight')
				{
					if (empty($postvalue))
					{
						$this->app->enqueueMessage(Text::_('COM_KUNENA_IMAGEWIDTH_IMAGEHEIGHT_EMPTY_CONFIG_NOT_SAVED'));
						$this->setRedirect(KunenaRoute::_($urlVar, false));

						return;
					}
				}

				if ($postname == 'avatarTypes')
				{
					$postvalue = preg_replace("/\s+/", "", $postvalue);
				}

				// No matter what got posted, we only store config parameters defined
				// in the config class. Anything else posted gets ignored.
				if (\array_key_exists($postname, $properties))
				{
					KunenaConfig::getInstance()->set($postname, $postvalue);
				}
			}
		}

		KunenaConfig::getInstance()->save();

		KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');

		$this->app->enqueueMessage(Text::_('COM_KUNENA_CONFIGSAVED'));

		if ($this->task == 'apply')
		{
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->setRedirect(KunenaRoute::_($this->kunenabaseurl, false));
	}

	/**
	 * Set default
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 2.0.0-BETA2
	 */
	public function setDefault(): void
	{
		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		KunenaConfig::getInstance()->reset();
		KunenaConfig::getInstance()->save();

		$this->setRedirect(KunenaRoute::_($this->baseurl, false), Text::_('COM_KUNENA_CONFIG_DEFAULT'));
	}
}
