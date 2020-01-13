<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\String\StringHelper;
use function defined;

/**
 * Kunena Backend Config Controller
 *
 * @since   Kunena 2.0
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
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  \Exception
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl       = 'administrator/index.php?option=com_kunena&view=config';
		$this->kunenabaseurl = 'administrator/index.php?option=com_kunena';
	}

	/**
	 * Apply
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function apply()
	{
		$this->save($this->baseurl);
	}

	/**
	 * Save
	 *
	 * @param   null  $url  url
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function save($url = null)
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$properties  = $this->config->getProperties();
		$post_config = $this->app->input->post->getArray();

		foreach ($post_config as $postsetting => $postvalue)
		{
			if (StringHelper::strpos($postsetting, 'cfg_') === 0)
			{
				// Remove cfg_ and force lower case

				if (is_array($postvalue))
				{
					$postvalue = implode(',', $postvalue);
				}

				$postname = StringHelper::strtolower(StringHelper::substr($postsetting, 4));

				if ($postname == 'imagewidth' || $postname == 'imageheight')
				{
					if (empty($postvalue))
					{
						$this->app->enqueueMessage(Text::_('COM_KUNENA_IMAGEWIDTH_IMAGEHEIGHT_EMPTY_CONFIG_NOT_SAVED'));
						$this->setRedirect(KunenaRoute::_($url, false));

						return;
					}
				}

				// No matter what got posted, we only store config parameters defined
				// in the config class. Anything else posted gets ignored.
				if (array_key_exists($postname, $properties))
				{
					$this->config->set($postname, $postvalue);
				}
			}
		}

		$this->config->save();

		$this->app->enqueueMessage(Text::_('COM_KUNENA_CONFIGSAVED'));

		if (empty($url))
		{
			$this->setRedirect(KunenaRoute::_($this->kunenabaseurl, false));

			return;
		}

		$this->setRedirect(KunenaRoute::_($url, false));
	}

	/**
	 * Set default
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  \Exception
	 * @throws  null
	 */
	public function setdefault()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->config->reset();
		$this->config->save();

		$this->setRedirect('index.php?option=com_kunena&view=config', Text::_('COM_KUNENA_CONFIG_DEFAULT'));
	}
}
