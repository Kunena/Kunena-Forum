<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Controller
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

class KunenaControllerApplicationDisplay extends KunenaControllerDisplay
{
	/**
	 * @var KunenaLayout
	 */
	protected $page;
	/**
	 * @var KunenaLayout
	 */
	protected $content;
	/**
	 * @var JPathway
	 */
	protected $breadcrumb;
	/**
	 * @var KunenaUser
	 */
	protected $me;
	/**
	 * @var KunenaConfig
	 */
	public $config;
	/**
	 * @var KunenaTemplate
	 */
	protected $template;
	/**
	 * @var JDocument
	 */
	protected $document;

	/**
	 * @return boolean
	 */
	public function exists()
	{
		if ($this->input->getWord('format', 'html') != 'html')
		{
			// TODO: we need to deal with other formats in the future.
			return false;
		}

		$name = "{$this->input->getWord('view')}/{$this->input->getWord('layout', 'default')}";
		$this->page = KunenaLayoutPage::factory($name);

		return (bool) $this->page->getPath();
	}

	/**
	 * @return KunenaLayout
	 */
	protected function display()
	{
		// Display layout with given parameters.
		$this->page
			->set('input', $this->input)
			->setLayout($this->input->getWord('layout', 'default'))
			->setOptions($this->getOptions());

		return $this->page;
	}

	/**
	 * @return JLayoutBase
	 */
	public function execute()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;

		// Run before executing action.
		$result = $this->before();

		if ($result === false)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		// Wrapper layout.
		$this->output = KunenaLayout::factory('Page')
			->set('me', $this->me)
			->setOptions($this->getOptions());

		if ($this->config->board_offline && !$this->me->isAdmin())
		{
			// Forum is offline.
			$this->setResponseStatus(503);
			$this->output->setLayout('offline');

			$this->content = KunenaLayout::factory('Widget/Custom')
				->set('header', JText::_('COM_KUNENA_FORUM_IS_OFFLINE'))
				->set('body', $this->config->offline_message);
		}
		elseif ($this->config->regonly && !$this->me->exists())
		{
			// Forum is for registered users only.
			$this->setResponseStatus(403);
			$this->output->setLayout('offline');

			$this->content = KunenaLayout::factory('Widget/Custom')
				->set('header', JText::_('COM_KUNENA_LOGIN_NOTIFICATION'))
				->set('body', JText::_('COM_KUNENA_LOGIN_FORUM'));
		}
		else
		{
			// Display real content.
			try
			{
				// Split into two lines for exception handling.
				$content = $this->display()->set('breadcrumb', $this->breadcrumb);
				$this->content = $content->render();
			}
			catch (KunenaExceptionAuthorise $e)
			{
				$banned = KunenaUserHelper::getMyself()->banned;
				$userid =  $this->input->getInt('userid');

				if (JFactory::getUser()->guest && KunenaUserHelper::get($userid)->exists())
				{
					$this->setResponseStatus($e->getResponseCode());
					$this->output->setLayout('login');
					$this->content = KunenaLayout::factory('Widget/Login/Login')->setLayout('login');
					$this->document->setTitle(JText::_('COM_KUNENA_LOGIN_FORUM'));
					$this->document->setMetaData('robots', 'noindex, follow');
				}
				elseif ($banned)
				{
					$this->setResponseStatus($e->getResponseCode());
					$this->output->setLayout('unauthorized');
					$this->document->setTitle($e->getResponseStatus());

					$bannedtime = KunenaUserBan::getInstanceByUserid(KunenaUserHelper::getMyself()->userid, true);

					$this->content = KunenaLayout::factory('Widget/Custom')
						->set('header', JText::_('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS'))
						->set('body', JText::sprintf('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY',
							KunenaDate::getInstance($bannedtime->getExpirationDate())->toKunena('date_today')));
					$this->document->setMetaData('robots', 'noindex, follow');
				}
				elseif (!KunenaUserHelper::get($userid)->exists())
				{
					$this->setResponseStatus($e->getResponseCode());
					$this->document->setTitle($e->getResponseStatus());

					$this->content = KunenaLayout::factory('Widget/Error')
					->set('header', $e->getResponseStatus());
				}
				else
				{
					$this->setResponseStatus($e->getResponseCode());
					$this->output->setLayout('unauthorized');
					$this->document->setTitle($e->getResponseStatus());

					$this->content = KunenaLayout::factory('Widget/Error')
						->set('header', $e->getResponseStatus());
					$this->document->setMetaData('robots', 'noindex, follow');
				}
			}
			catch (Exception $e)
			{
				if (!($e instanceof KunenaExceptionAuthorise))
				{
					$header = 'Error while rendering layout';
					$content = isset($content) ? $content->renderError($e) : $this->content->renderError($e);
					$e = new KunenaExceptionAuthorise($e->getMessage(), $e->getCode(), $e);
				}
				else
				{
					$header = $e->getResponseStatus();
					$content = $e->getMessage();
				}

				$this->setResponseStatus($e->getResponseCode());
				$this->output->setLayout('unauthorized');
				$this->document->setTitle($header);

				$this->content = KunenaLayout::factory('Widget/Custom')
					->set('header', $header)
					->set('body', $content);
			}
		}

		// Display wrapper layout with given parameters.
		$this->output
			->set('content', $this->content)
			->set('breadcrumb', $this->breadcrumb);

		// Run after executing action.
		$this->after();

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;

		return $this->output;
	}

	/**
	 *
	 */
	protected function before()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;

		if (!$this->exists())
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;
			throw new RuntimeException("Layout '{$this->input->getWord('view')}/{$this->input->getWord('layout', 'default')}' does not exist!", 404);
		}

		// Load language files.
		KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
		KunenaFactory::loadLanguage('com_kunena.templates');
		KunenaFactory::loadLanguage('com_kunena.models');
		KunenaFactory::loadLanguage('com_kunena.views');

		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaConfig::getInstance();
		$this->document = JFactory::getDocument();
		$this->template = KunenaFactory::getTemplate();
		$this->template->initialize();

		if ($this->me->isAdmin())
		{
			// Display warnings to the administrator if forum is either offline or debug has been turned on.
			if ($this->config->board_offline)
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_FORUM_IS_OFFLINE'), 'notice');
			}

			if ($this->config->debug)
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_WARNING_DEBUG'), 'notice');
			}
		}

		if ($this->config->read_only)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_WARNING_READONLY'), 'notice');
		}

		if ($this->me->isBanned())
		{
			// Display warnings to the banned users.
			$banned = KunenaUserBan::getInstanceByUserid($this->me->userid, true);

			if (!$banned->isLifetime())
			{
				$this->app->enqueueMessage(
     JText::sprintf(
	 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY',
	KunenaDate::getInstance($banned->expiration)->toKunena('date_today')), 'notice');
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS'), 'notice');
			}
		}

		// Remove base and add canonical link.
		$this->document->setBase('');
		$jinput = JFactory::getApplication()->input;
		$limitstart = $jinput->getInt('limitstart', 'limitstart', 0);

		if (!$limitstart)
		{
			$this->document->addHeadLink(KunenaRoute::_(), 'canonical', 'rel');
		}

		// Initialize breadcrumb.
		$this->breadcrumb = $this->app->getPathway();

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 *
	 */
	protected function after()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;

		// Use our own browser side cache settings.
		JFactory::getApplication()->allowCache(false);
		JFactory::getApplication()->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);
		JFactory::getApplication()->setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT', true);
		JFactory::getApplication()->setHeader('Cache-Control', 'no-store, must-revalidate, post-check=0, pre-check=0', true);

		if ($this->config->get('credits', 1))
		{
			$this->output->appendAfter($this->poweredBy());
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . get_class($this) . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @param   int $code
	 *
	 * @throws Exception
	 */
	public function setResponseStatus($code = 404)
	{
		switch ((int) $code)
		{
			case 400:
				JFactory::getApplication()->setHeader('Status', '400 Bad Request', true);
				break;
			case 401:
				JFactory::getApplication()->setHeader('Status', '401 Unauthorized', true);
				break;
			case 403:
				JFactory::getApplication()->setHeader('Status', '403 Forbidden', true);
				break;
			case 404:
				JFactory::getApplication()->setHeader('Status', '404 Not Found', true);
				break;
			case 410:
				JFactory::getApplication()->setHeader('Status', '410 Gone', true);
				break;
			case 503:
				JFactory::getApplication()->setHeader('Status', '503 Service Temporarily Unavailable', true);
				break;
			case 500:
			default:
				JFactory::getApplication()->setHeader('Status', '500 Internal Server Error', true);
		}
	}

	/**
	 * @return string
	 */
	final public function poweredBy()
	{
		$templateText = (string) $this->template->params->get('templatebyText');
		$templateName = (string) $this->template->params->get('templatebyName');
		$templateLink = (string) $this->template->params->get('templatebyLink');
		$credits = '<div style="text-align:center">';
		$credits .= JHtml::_(
	'kunenaforum.link', 'index.php?option=com_kunena&view=credits',
			JText::_('COM_KUNENA_POWEREDBY'), '', '', '',
	array('style' => 'display: inline; visibility: visible; text-decoration: none;'));
		$credits .= ' <a href="https://www.kunena.org"
			target="_blank" rel="noopener noreferrer" style="display: inline; visibility: visible; text-decoration: none;">'
			. JText::_('COM_KUNENA') . '</a>';
		if (trim($templateText)) {
			$credits .= ' :: <a href ="' . $templateLink . '" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">'
				. $templateText . ' ' . $templateName . '</a>';
		}

		$credits .= '</div>';

		$powered = JPluginHelper::isEnabled('kunena', 'powered');
		if ($powered)
		{
			$credits = '';
		}
		else
		{
			$styles = <<<EOF
		.layout#kunena + div { display: block !important;}
		#kunena + div { display: block !important;}
EOF;


			$document = JFactory::getDocument();
			$document->addStyleDeclaration($styles);
		}
		return $credits;
	}
}
