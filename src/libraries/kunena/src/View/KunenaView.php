<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\View;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Controller\SearchController;
use LogicException;
use function defined;

/**
 * Kunena View Class
 *
 * @since   Kunena 6.0
 */
class KunenaView extends HtmlView
{
	/**
	 * @var     Document|null
	 * @since   Kunena 6.0
	 */
	public $document = null;

	/**
	 * @var     CMSApplication|null
	 * @since   Kunena 6.0
	 */
	public $app = null;

	/**
	 * @var     KunenaUser|null
	 * @since   Kunena 6.0
	 */
	public $me = null;

	/**
	 * @var     KunenaConfig|null
	 * @since   Kunena 6.0
	 */
	public $config = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $embedded = false;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $templateFiles = [];

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $teaser = null;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	protected $inLayout = 0;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	protected $_row = 0;

	/**
	 * @var KunenaTemplate
	 * @since version
	 */
	protected $ktemplate;

	/**
	 * @var KunenaProfiler
	 * @since version
	 */
	private $profiler;
	protected $state;
	public $common;

	/**
	 * @param   array  $config  config
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct($config = [])
	{
		$name           = isset($config['name']) ? $config['name'] : $this->getName();
		$this->document = Factory::getApplication()->getDocument();
		$this->document->setBase('');
		$this->profiler  = KunenaProfiler::instance('Kunena');
		$this->app       = Factory::getApplication();
		$this->me        = KunenaUserHelper::getMyself();
		$this->config    = KunenaFactory::getConfig();
		$this->ktemplate = KunenaFactory::getTemplate();

		// Set the default template search path
		if ($this->app->isClient('site') && !isset($config['template_path']))
		{
			$config['template_path'] = $this->ktemplate->getTemplatePaths("html/$name", true);
		}

		if ($this->app->isClient('administrator'))
		{
			$templateAdmin = KunenaFactory::getAdminTemplate();
			$templateAdmin->initialize();

			$config['template_path'] = $templateAdmin->getTemplatePaths($name);
		}

		parent::__construct($config);

		if ($this->app->isClient('site'))
		{
			// Add another template file lookup path specific to the current template
			$fallback = JPATH_THEMES . "/{$this->app->getTemplate()}/html/com_kunena/{$this->ktemplate->name}/{$this->getName()}";
			$this->addTemplatePath($fallback);
		}

		// Use our own browser side cache settings.
		Factory::getApplication()->allowCache(false);
		Factory::getApplication()->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);
		Factory::getApplication()->setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT', true);
		Factory::getApplication()->setHeader('Cache-Control', 'no-store, must-revalidate, post-check=0, pre-check=0', true);
		Factory::getApplication()->sendHeaders();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayAll(): void
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		$this->displayLayout();
	}

	/**
	 * @param   null  $layout  layout
	 * @param   null  $tpl     tmpl
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayLayout($layout = null, $tpl = null)
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if ($layout)
		{
			$this->setLayout($layout);
		}

		$view           = $this->getName();
		$layout         = $this->getLayout();
		$viewName       = ucfirst($view);
		$layoutName     = ucfirst($layout);
		$layoutFunction = 'display' . $layoutName;

		KunenaProfiler::getInstance() ? $this->profiler->start("display {$viewName}/{$layoutName}") : null;

		if (!$this->embedded && isset($this->common))
		{
			if ($this->config->boardOffline && !$this->me->isAdmin())
			{
				// Forum is offline
				Factory::getApplication()->setHeader('Status', '503 Service Temporarily Unavailable', true);
				Factory::getApplication()->sendHeaders();
				$this->common->header = Text::_('COM_KUNENA_FORUM_IS_OFFLINE');
				$this->common->body   = $this->config->offlineMessage;
				$this->common->html   = true;
				$this->common->display('default');
				KunenaProfiler::getInstance() ? $this->profiler->stop("display {$viewName}/{$layoutName}") : null;

				return;
			}
			elseif ($this->config->regOnly && !$this->me->exists() && !$this->teaser)
			{
				// Forum is for registered users only
				Factory::getApplication()->setHeader('Status', '403 Forbidden', true);
				Factory::getApplication()->sendHeaders();
				$this->common->header = Text::_('COM_KUNENA_LOGIN_NOTIFICATION');
				$this->common->body   = Text::_('COM_KUNENA_LOGIN_FORUM');
				$this->common->display('default');
				KunenaProfiler::getInstance() ? $this->profiler->stop("display {$viewName}/{$layoutName}") : null;

				return;
			}
			elseif (!method_exists($this, $layoutFunction) && !is_file(KPATH_SITE . "/views/{$view}/{$layout}.php"))
			{
				// Layout was not found (don't allow Joomla to raise an error)
				$this->displayError([Text::_('COM_KUNENA_NO_ACCESS')], 404);
				KunenaProfiler::getInstance() ? $this->profiler->stop("display {$viewName}/{$layoutName}") : null;

				return;
			}
		}

		$this->state = $this->get('State');

		if (method_exists($this, $layoutFunction))
		{
			$contents = $this->$layoutFunction($tpl ? $tpl : null);
		}
		elseif (method_exists($this, 'displayDefault'))
		{
			// TODO: should raise error instead, used just in case..
			$contents = $this->displayDefault($tpl ? $tpl : null);
		}
		else
		{
			// TODO: should raise error instead..
			$contents = $this->display($tpl ? $tpl : null);
		}

		KunenaProfiler::getInstance() ? $this->profiler->stop("display {$viewName}/{$layoutName}") : null;

		return $contents;
	}

	/**
	 * @param   array  $messages  messages
	 * @param   int    $code      code
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayError($messages = [], $code = 404): void
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		$title = Text::_('COM_KUNENA_ACCESS_DENIED');
		$app   = Factory::getApplication();

		switch ((int) $code)
		{
			case 401:
			case 503:
			case 500:
			case 410:
			case 404:
			case 403:
			case 400:
				$app->sendHeaders();
				break;
			default:
		}

		$output = '';

		foreach ($messages as $message)
		{
			$output .= "<p>{$message}</p>";
		}

		$this->common->setLayout('default');
		$this->common->header = $title;
		$this->common->body   = $output;
		$this->common->html   = true;
		$this->common->display();

		$this->setTitle($title);
	}

	/**
	 * Method to display title in page
	 *
	 * @param   string  $title  Show the title on the browser
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function setTitle(string $title): void
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if (!$this->state->get('embedded'))
		{
			// Check for empty title and add site name if param is set
			$title = strip_tags($title);

			if ($this->app->get('sitename_pagetitles', 0) == 1)
			{
				$title = Text::sprintf('JPAGETITLE', $this->app->get('sitename'), $title . ' - ' . $this->config->boardTitle);
			}
			elseif ($this->app->get('sitename_pagetitles', 0) == 2)
			{
				if ($this->config->boardTitle == $this->app->get('sitename'))
				{
					$title = Text::sprintf('JPAGETITLE', $title . ' - ' . $this->config->boardTitle);
				}
				else
				{
					$title = Text::sprintf('JPAGETITLE', $title . ' - ' . $this->config->boardTitle, $this->app->get('sitename'));
				}
			}
			else
			{
				$title = $title . ' - ' . KunenaFactory::getConfig()->boardTitle;
			}

			$this->document->setTitle($title);
		}
	}

	/**
	 * @param   array  $errors  errors
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayNoAccess($errors = []): void
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		// Backward compatibility
		$this->displayError($errors, 200);
	}

	/**
	 * @param   mixed  $position  position
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayModulePosition($position): void
	{
		echo $this->getModulePosition($position);
	}

	/**
	 * @param   mixed  $position  position
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getModulePosition($position): string
	{
		$html     = '';
		$document = Factory::getApplication()->getDocument();

		if (method_exists($document, 'countModules') && $document->countModules($position))
		{
			$renderer = $document->loadRenderer('modules');
			$options  = ['style' => 'xhtml'];
			$html     .= '<div class="' . $position . '">';
			$html     .= $renderer->render($position, $options, null);
			$html     .= '</div>';
		}

		return $html;
	}

	/**
	 * @param   mixed  $position  position
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function isModulePosition($position): int
	{
		$document = Factory::getApplication()->getDocument();

		return method_exists($document, 'countModules') ? $document->countModules($position) : 0;
	}

	/**
	 * @param   string  $text    text
	 * @param   int     $len     len
	 * @param   mixed   $parent  parent
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function parse(string $text, int $len, $parent)
	{
		if ($this instanceof SearchController)
		{
			$parentinternalObject = $parent;
		}
		else
		{
			$parentinternalObject = $this;
		}

		return KunenaParser::parseBBCode($text, $parentinternalObject, $len);
	}

	/**
	 * Render new layout if available, otherwise continue to the old logic.
	 *
	 * @param   string  $layout      layout
	 * @param   string  $tpl         tmpl
	 * @param   array   $hmvcParams  params
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 * @throws LogicException
	 */
	public function render(string $layout, string $tpl, array $hmvcParams = []): void
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if (isset($tpl) && $tpl == 'default')
		{
			$tpl = null;
		}

		if ($this->embedded)
		{
			// Support legacy embedded views.
			$file = isset($tpl) ? $this->getLayout() . '_' . $tpl : $this->getLayout();

			foreach ($this->_path['template'] as $path)
			{
				$found = !strstr($path, '/com_kunena/') && is_file($path . $file . '.php');

				if ($found)
				{
					$this->display($tpl);

					return;
				}
			}
		}

		// Support new layouts.
		$hmvc = KunenaLayout::factory($layout);

		if ($hmvc->getPath())
		{
			$this->inLayout++;

			if ($hmvcParams)
			{
				$hmvc->setProperties($hmvcParams);
			}

			echo $hmvc->setLegacy($this)->setLayout($tpl ? $tpl : $this->getLayout());

			$this->inLayout--;
		}
		else
		{
			$this->display($tpl);
		}
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param   string  $tpl         The name of the template source file ...
	 *                               automatically searches the template paths and compiles as needed.
	 * @param   array   $hmvcParams  Extra parameters for HMVC.
	 *
	 * @return  string|void The output of the the template script.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadTemplateFile($tpl = null, $hmvcParams = null): string
	{
		KunenaProfiler::getInstance() ? $this->profiler->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$layout = $this->getLayout();

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout . '_' . $tpl : $layout;

		if (!isset($this->templateFiles[$file]))
		{
			// Clean the file name
			$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
			$tpl  = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

			// Load the template script
			$filetofind                 = $this->_createFileName('template', ['name' => $file]);
			$this->templateFiles[$file] = KunenaPath::find($this->_path['template'], $filetofind);
		}

		$this->_template = $this->templateFiles[$file];

		if ($this->_template != false)
		{
			$templateFile = preg_replace('%' . KunenaPath::clean(JPATH_ROOT, '/') . '/%', '', KunenaPath::clean($this->_template, '/'));

			// Unset so as not to introduce into template scope
			unset($tpl);
			unset($file);

			// Never allow a 'this' property
			if (isset($this->this))
			{
				unset($this->this);
			}

			// Start capturing output into a buffer
			ob_start();

			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;

			// Done with the requested template; get the buffer and
			// clear it.
			$output = ob_get_contents();
			ob_end_clean();

			if (JDEBUG || $this->config->get('debug'))
			{
				$output = trim($output);
				$output = "\n<!-- START {$templateFile} -->\n{$output}\n<!-- END {$templateFile} -->\n";
			}

			return $output;
		}

		KunenaProfiler::getInstance() ? $this->profiler->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if ($this->app->scope == 'com_kunena')
		{
			throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $this->getName() . '/' . $file), 500);
		}
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getTemplateMD5(): string
	{
		return md5(serialize($this->_path['template']) . '-' . $this->ktemplate->name);
	}

	/**
	 * @param   KunenaCategory  $category  category
	 * @param   null            $content   content
	 * @param   null            $title     title
	 * @param   null            $class     class
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getCategoryLink(KunenaCategory $category, $content = null, $title = null, $class = null)
	{
		if (!$content)
		{
			$content = $this->escape($category->name);
		}

		if ($title === null)
		{
			$title = Text::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($category->name));
		}

		return HTMLHelper::_('link', $category->getUri(), $content, $title, $class, '');
	}

	/**
	 * @param   KunenaTopic          $topic     topic
	 * @param   null                 $action    action
	 * @param   null                 $content   content
	 * @param   null                 $title     title
	 * @param   null                 $class     class
	 * @param   KunenaCategory|null  $category  category
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getTopicLink(KunenaTopic $topic, $action = null, $content = null, $title = null, $class = null, KunenaCategory $category = null)
	{
		$uri = $topic->getUri($category ? $category : (isset($this->category) ? $this->category : $topic->category_id), $action);

		if (!$content)
		{
			$content = KunenaParser::parseText($topic->subject);
		}

		$rel = '';

		if ($title === null)
		{
			if ($action instanceof KunenaMessage)
			{
				$title = Text::sprintf('COM_KUNENA_TOPIC_MESSAGE_LINK_TITLE', $this->escape($topic->subject));
			}
			else
			{
				switch ($action)
				{
					case 'first':
						$title = Text::sprintf('COM_KUNENA_TOPIC_FIRST_LINK_TITLE', $this->escape($topic->subject));
						break;
					case 'last':
						$title = Text::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', $this->escape($topic->subject));
						break;
					case 'unread':
						$title = Text::sprintf('COM_KUNENA_TOPIC_UNREAD_LINK_TITLE', $this->escape($topic->subject));
						break;
					default:
						$title = Text::sprintf('COM_KUNENA_TOPIC_LINK_TITLE', $this->escape($topic->subject));
				}
			}
		}

		return HTMLHelper::_('link', $uri, $content, $title, $class, $rel);
	}
}
