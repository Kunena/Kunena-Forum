<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Controller
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Controller\Application;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\BaseLayout;
use Joomla\CMS\Pathway\Pathway;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Layout\KunenaPage;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * Class KunenaControllerApplicationDisplay
 *
 * @since   Kunena 6.0
 */
class Display extends KunenaControllerDisplay
{
	/**
	 * @var     KunenaConfig
	 * @since   Kunena 6.0
	 */
	public $config;

	/**
	 * @var     KunenaLayout
	 * @since   Kunena 6.0
	 */
	public $page;

	/**
	 * @var     KunenaLayout
	 * @since   Kunena 6.0
	 */
	public $content;

	/**
	 * @var     Pathway
	 * @since   Kunena 6.0
	 */
	public $breadcrumb;

	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var     KunenaTemplate
	 * @since   Kunena 6.0
	 */
	public $template;

	/**
	 * @var     HtmlDocument
	 * @since   Kunena 6.0
	 */
	public $document;

	/**
	 * @return  BaseLayout
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function execute(): BaseLayout
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;

		// Run before executing action.
		$result = $this->before();

		if ($result === false)
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		// Wrapper KunenaLayout.
		$this->output = KunenaLayout::factory('Page')
			->set('me', $this->me)
			->setOptions($this->getOptions());

		if ($this->config->boardOffline && !$this->me->isAdmin())
		{
			// Forum is offline.
			$this->setResponseStatus(503);
			$this->output->setLayout('offline');

			$this->content = KunenaLayout::factory('Widget/Custom')
				->set('header', Text::_('COM_KUNENA_FORUM_IS_OFFLINE'))
				->set('body', $this->config->offlineMessage);
		}
		elseif ($this->config->regOnly && !$this->me->exists())
		{
			// Forum is for registered users only.
			$this->setResponseStatus(403);
			$this->output->setLayout('offline');

			$this->content = KunenaLayout::factory('Widget/Custom')
				->set('header', Text::_('COM_KUNENA_LOGIN_NOTIFICATION'))
				->set('body', Text::_('COM_KUNENA_LOGIN_FORUM'));
		}
		else
		{
			// Display real content.
			try
			{
				// Split into two lines for exception handling.
				$content       = $this->display()->set('breadcrumb', $this->breadcrumb);
				$this->content = $content->render();
			}
			catch (KunenaExceptionAuthorise $e)
			{
				$banned = KunenaUserHelper::getMyself()->isBanned();
				$userid = $this->input->getInt('userid');

				if (Factory::getApplication()->getIdentity()->guest && KunenaUserHelper::get($userid)->exists())
				{
					$this->setResponseStatus($e->getResponseCode());
					$this->output->setLayout('login');
					$this->content = KunenaLayout::factory('Widget/Login/Login')->setLayout('login');
					$this->document->setTitle(Text::_('COM_KUNENA_LOGIN_FORUM'));
					$this->document->setMetaData('robots', 'noindex, follow');
				}
				elseif ($banned)
				{
					$this->setResponseStatus($e->getResponseCode());
					$this->output->setLayout('unauthorized');
					$this->document->setTitle($e->getResponseStatus());

					$bannedtime = KunenaBan::getInstanceByUserid(KunenaUserHelper::getMyself()->userid, true);

					$this->content = KunenaLayout::factory('Widget/Custom')
						->set('header', Text::_('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS'))
						->set(
							'body',
							Text::sprintf(
								'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY',
								KunenaDate::getInstance($bannedtime->getExpirationDate())->toKunena('date_today')
							)
						);
					$this->document->setMetaData('robots', 'noindex, follow');
				}
				elseif (!KunenaUserHelper::get($userid)->exists())
				{
					$layout = $this->input->getCmd('layout');

					if ($layout == 'default')
					{
						$this->content = KunenaLayout::factory('Widget/Error')->set('header', $e->getResponseStatus());
					}
					else
					{
						$this->content = KunenaLayout::factory('Widget/Login/Login')->setLayout('login');
					}

					$this->setResponseStatus($e->getResponseCode());
					$this->document->setTitle($e->getResponseStatus());

					if ($e->getResponseCode() == 401 || $e->getResponseCode() == 403)
					{
						$this->document->setMetaData('robots', 'noindex, nofollow');
					}
				}
				else
				{
					$this->setResponseStatus($e->getResponseCode());
					$this->output->setLayout('unauthorized');
					$this->document->setTitle($e->getResponseStatus());

					$this->content = KunenaLayout::factory('Widget/Error')->set('header', $e->getResponseStatus())->set('message', $e->getMessage());
				}
			}
			catch (Exception $e)
			{
				if (!($e instanceof KunenaExceptionAuthorise))
				{
					$header  = 'Error while rendering KunenaLayout.';
					$content = $e->getMessage();
					$e       = new KunenaExceptionAuthorise($e->getMessage(), $e->getCode(), $e);
				}
				else
				{
					$header  = $e->getResponseStatus();
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

		// Display wrapper KunenaLayout with given parameters.
		$this->output
			->set('content', $this->content)
			->set('breadcrumb', $this->breadcrumb);

		// Run after executing action.
		$this->after();

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;

		return $this->output;
	}

	/**
	 * @return  void
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function before()
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;

		if (!$this->exists())
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;
			throw new RuntimeException("Layout '{$this->input->getWord('view')}/{$this->input->getWord('layout', 'default')}' does not exist!", 404);
		}

		// Load language files.
		KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
		KunenaFactory::loadLanguage('com_kunena.templates');
		KunenaFactory::loadLanguage('com_kunena.models');
		KunenaFactory::loadLanguage('com_kunena.views');

		$this->me       = KunenaUserHelper::getMyself();
		$this->config   = KunenaConfig::getInstance();
		$this->document = Factory::getApplication()->getDocument();
		$this->template = KunenaFactory::getTemplate();
		$this->template->initialize();

		if ($this->me->isAdmin())
		{
			// Display warnings to the administrator if forum is either offline or debug has been turned on.
			if ($this->config->boardOffline)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_FORUM_IS_OFFLINE'), 'notice');
			}

			if ($this->config->debug)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_WARNING_DEBUG'), 'notice');
			}
		}

		if ($this->config->readOnly)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_WARNING_READONLY'), 'notice');
		}

		if ($this->me->isBanned())
		{
			// Display warnings to the banned users.
			$banned = KunenaBan::getInstanceByUserid($this->me->userid, true);

			if (!$banned->isLifetime())
			{
				$this->app->enqueueMessage(
					Text::sprintf(
						'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY',
						KunenaDate::getInstance($banned->expiration)->toKunena('date_today')
					),
					'notice'
				);
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS'), 'notice');
			}
		}

		// Remove base and add canonical link.
		$this->document->setBase('');
		$kinput     = Factory::getApplication()->input;
		$limitstart = $kinput->getInt('limitstart', 0);

		if (!$limitstart)
		{
			$uri = trim(strtok(KunenaRoute::_(), '?'));
			$this->document->addHeadLink($uri, 'canonical', 'rel');
		}

		// Initialize breadcrumb.
		$this->breadcrumb = $this->app->getPathway();

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function exists(): bool
	{
		if ($this->input->getWord('format', 'html') != 'html')
		{
			return false;
		}

		$name       = "{$this->input->getWord('view')}/{$this->input->getWord('layout', 'default')}";
		$this->page = KunenaPage::factory($name);

		return (bool) $this->page->getPath();
	}



	/**
	 * @return  KunenaLayout
	 *
	 * @since   Kunena 6.0
	 */
	protected function display()
	{
		// Display KunenaLayout with given parameters.
		$this->page
			->set('input', $this->input)
			->setLayout($this->input->getWord('layout', 'default'))
			->setOptions($this->getOptions());

		return $this->page;
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function after()
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;

		// Use our own browser side cache settings.
		Factory::getApplication()->allowCache(false);
		Factory::getApplication()->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);
		Factory::getApplication()->setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT', true);
		Factory::getApplication()->setHeader('Cache-Control', 'no-store, must-revalidate, post-check=0, pre-check=0', true);
		Factory::getApplication()->sendHeaders();

		if ($this->config->get('credits', 1))
		{
			$this->output->appendAfter($this->poweredBy());
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . \get_class($this) . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	final public function poweredBy(): string
	{
		$templateText = (string) $this->template->params->get('templatebyText');
		$templateName = (string) $this->template->params->get('templatebyName');
		$templateLink = (string) $this->template->params->get('templatebyLink');
		$credits      = '<div style="text-align:center;">';
		$credits      .= HTMLHelper::_(
			'kunenaforum.link',
			'index.php?option=com_kunena&view=credits',
			Text::_('COM_KUNENA_POWEREDBY'),
			'',
			'',
			'',
			['style' => 'display: inline !important; visibility: visible !important; text-decoration: none !important;']
		);
		$credits      .= ' <a href="https://www.kunena.org"
			target="_blank" rel="noopener noreferrer" style="display: inline !important; visibility: visible !important; text-decoration: none !important;">'
			. Text::_('COM_KUNENA') . '</a>';

		if (trim($templateText))
		{
			$credits .= ' :: <a href ="' . $templateLink . '" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">'
				. $templateText . ' ' . $templateName . '</a>';
		}

		$credits .= '</div>';

		if (PluginHelper::isEnabled('kunena', 'powered'))
		{
			$credits = '';
		}
		else
		{
			$styles = <<<EOF
		.layout#kunena + div { display: block !important;}
		#kunena + div { display: block !important;}
EOF;

			KunenaTemplate::getInstance()->addStyleDeclaration($styles);
		}

		return $credits;
	}
}
