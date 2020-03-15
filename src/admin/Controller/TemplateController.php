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

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Input\Input;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Kunena Template Controller
 *
 * @since   Kunena 6.0
 */
class TemplateController extends FormController
{
	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $baseurl = null;

	/**
	 * Constructor.
	 *
	 * @see     BaseController
	 *
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The CMSApplication for the dispatcher
	 * @param   Input                $input    Input
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->baseurl = 'administrator/index.php?option=com_kunena&view=templates';
	}

	/**
	 * Save template settings
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  urlvar
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 */
	public function save($key = null, $urlVar = null)
	{
		$template = $this->app->input->get('templatename', '', 'cmd');
		$menus    = $this->app->input->get('selections', [], 'array');
		$menus    = ArrayHelper::toInteger($menus);

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$template)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->_saveParamFile($template);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to save param.ini file on filesystem.
	 *
	 * @param   string  $template  The name of the template.
	 *
	 * @return  void
	 *
	 * @since   Kunena 3.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function _saveParamFile($template)
	{
		$params = $this->app->input->get('jform', [], 'array');

		$params['wysibb'] = '';

		if ($params['bold'])
		{
			$params['wysibb'] .= 'bold,';
		}

		if ($params['italic'])
		{
			$params['wysibb'] .= 'italic,';
		}

		if ($params['underline'])
		{
			$params['wysibb'] .= 'underline,';
		}

		if ($params['wysibb'])
		{
			$params['wysibb'] .= 'strike,';
		}

		if ($params['supscript'])
		{
			$params['wysibb'] .= 'sup,';
		}

		if ($params['subscript'])
		{
			$params['wysibb'] .= 'sub,';
		}

		if ($params['alignleft'])
		{
			$params['wysibb'] .= 'justifyleft,';
		}

		if ($params['center'])
		{
			$params['wysibb'] .= 'justifycenter,';
		}

		if ($params['alignright'])
		{
			$params['wysibb'] .= 'justifyright,';
		}

		if ($params['divider'])
		{
			$params['wysibb'] .= '|,';
		}

		if ($params['picture'])
		{
			$params['wysibb'] .= 'img,';
		}

		if ($params['video'])
		{
			$params['wysibb'] .= 'video,';
		}

		if ($params['link'])
		{
			$params['wysibb'] .= 'link,';
		}

		if ($params['divider'])
		{
			$params['wysibb'] .= '|,';
		}

		if ($params['bulletedlist'])
		{
			$params['wysibb'] .= 'bullist,';
		}

		if ($params['numericlist'])
		{
			$params['wysibb'] .= 'numlist,';
		}

		if ($params['divider'])
		{
			$params['wysibb'] .= '|,';
		}

		if ($params['colors'])
		{
			$params['wysibb'] .= 'fontcolor,';
		}

		if ($params['wysibb'])
		{
			$params['wysibb'] .= 'fontsize,';
		}

		if ($params['wysibb'])
		{
			$params['wysibb'] .= 'fontfamily,';
		}

		if ($params['divider'])
		{
			$params['wysibb'] .= '|,';
		}

		if ($params['quote'])
		{
			$params['wysibb'] .= 'quote,';
		}

		if ($params['code'])
		{
			$params['wysibb'] .= 'code,';
		}

		if ($params['table'])
		{
			$params['wysibb'] .= 'table,';
		}

		if ($params['wysibb'])
		{
			$params['wysibb'] .= 'removeFormat';
		}

		// Set FTP credentials, if given
		ClientHelper::setCredentialsFromRequest('ftp');
		$ftp  = ClientHelper::getCredentials('ftp');
		$file = KPATH_SITE . '/template/' . $template . '/config/params.ini';

		if (count($params))
		{
			$registry = new Registry;
			$registry->loadArray($params);
			$txt    = $registry->toString('INI');
			$return = File::write($file, $txt);

			if (!$return)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::sprintf('COM_KUNENA_A_TEMPLATE_MANAGER_FAILED_WRITE_FILE', $file));
				$this->app->redirect(KunenaRoute::_($this->baseurl, false));
			}
		}
	}

	/**
	 * Apply
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function apply()
	{
		$template = $this->app->input->get('templatename', '', 'cmd');
		$menus    = $this->app->input->get('selections', [], 'array');
		$menus    = ArrayHelper::toInteger($menus);

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (!$template)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_OPERATION_FAILED') . ': ' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NOT_SPECIFIED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->_saveParamFile($template);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_CONFIGURATION_SAVED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=edit&cid[]=' . $template, false));
	}

	/**
	 * Method to restore the default settings of the template selected
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.1
	 *
	 * @throws  Exception
	 */
	public function restore()
	{
		$template = $this->app->input->get('templatename', '', 'cmd');
		$file     = KPATH_SITE . '/template/' . $template . '/config/params.ini';

		if (file_exists($file))
		{
			$result = File::delete($file);

			if ($result)
			{
				File::write($file, '');
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_TEMPLATES_SETTINGS_RESTORED_SUCCESSFULLY'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

}
