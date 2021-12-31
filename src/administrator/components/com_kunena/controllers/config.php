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
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * Kunena Backend Config Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerConfig extends KunenaController
{
	/**
	 * @since    2.0.0-BETA2
	 * @var null|string
	 */
	protected $baseurl = null;

	/**
	 * @since    2.0.0-BETA2
	 * @var string
	 * @since    Kunena
	 */
	protected $kunenabaseurl = null;

	/**
	 * Construct
	 *
	 * @param   array $config config
	 *
	 * @throws Exception
	 * @since    2.0.0-BETA2
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl       = 'administrator/index.php?option=com_kunena&view=config';
		$this->kunenabaseurl = 'administrator/index.php?option=com_kunena';
	}

	/**
	 * Apply
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0.0-BETA2
	 * @throws null
	 */
	public function apply()
	{
		$this->save($this->baseurl);
	}

	/**
	 * Save
	 *
	 * @param   null $url url
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0.0-BETA2
	 * @throws null
	 */
	public function save($url = null)
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$properties = $this->config->getProperties();

		$post_config = $this->app->input->post->getArray();

		// TODO: fix depricated value
		foreach ($post_config as $postsetting => $postvalue)
		{
			if (Joomla\String\StringHelper::strpos($postsetting, 'cfg_') === 0)
			{
				// Remove cfg_ and force lower case

				if (is_array($postvalue))
				{
					$postvalue = implode(',', $postvalue);
				}

				$postname = Joomla\String\StringHelper::strtolower(Joomla\String\StringHelper::substr($postsetting, 4));

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
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0.0-BETA2
	 * @throws null
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
