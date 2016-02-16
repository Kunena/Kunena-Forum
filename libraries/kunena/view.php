<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena View Class
 */
class KunenaView extends JViewLegacy
{

	public $document = null;
	public $app = null;
	public $me = null;
	public $config = null;
	public $embedded = false;
	public $templatefiles = array();
	public $teaser = null;

	protected $inLayout = 0;
	protected $_row = 0;

	public function __construct($config = array())
	{
		$name = isset($config['name']) ? $config['name'] : $this->getName();
		$this->document = JFactory::getDocument();
		$this->document->setBase('');
		$this->profiler = KunenaProfiler::instance('Kunena');
		$this->app = JFactory::getApplication ();
		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();
		$this->ktemplate = KunenaFactory::getTemplate();

		// Set the default template search path
		if ($this->app->isSite() && !isset($config['template_path']))
		{
			$config['template_path'] = $this->ktemplate->getTemplatePaths("html/$name", true);
		}

		if ($this->app->isAdmin())
		{
			$templateAdmin = KunenaFactory::getAdminTemplate();
			$templateAdmin->initialize();

			$config['template_path'] = $templateAdmin->getTemplatePaths($name);
		}

		parent::__construct($config);

		if ($this->app->isSite())
		{
			// Add another template file lookup path specific to the current template
			$fallback = JPATH_THEMES . "/{$this->app->getTemplate()}/html/com_kunena/{$this->ktemplate->name}/{$this->getName()}";
			$this->addTemplatePath($fallback);
		}

		// Use our own browser side cache settings.
		JResponse::allowCache(false);
		JResponse::setHeader( 'Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true );
		JResponse::setHeader( 'Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT', true );
		JResponse::setHeader( 'Cache-Control', 'no-store, must-revalidate, post-check=0, pre-check=0', true );
	}

	public function displayAll()
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if ($this->me->isAdmin())
		{
			if ($this->config->board_offline)
			{
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_FORUM_IS_OFFLINE'), 'notice');
			}

			if ($this->config->debug)
			{
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_WARNING_DEBUG'), 'notice');
			}
		}
		if ($this->me->isBanned())
		{
			$banned = KunenaUserBan::getInstanceByUserid($this->me->userid, true);

			if (!$banned->isLifetime())
			{
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY', KunenaDate::getInstance($banned->expiration)->toKunena('date_today')), 'notice');
			}
			else
			{
				$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS' ), 'notice');
			}
		}

		$this->state = $this->get ( 'State' );
		$this->ktemplate->initialize();
		$menu = $this->app->getMenu();
		$home = $menu->getItems('type', 'alias');
		$juricurrent = JURI::current();

		if (JFactory::getApplication()->isAdmin())
		{
			$this->displayLayout();
		}
		elseif ($home)
		{
			$this->document->addHeadLink( $juricurrent, 'canonical', 'rel');
			include JPATH_SITE .'/'. $this->ktemplate->getFile('html/display.php');

			if ($this->config->get('credits', 1))
			{
				$this->poweredBy();
			}
		}
		else
		{
			$this->document->addHeadLink( KunenaRoute::_(), 'canonical', 'rel');
			include JPATH_SITE .'/'. $this->ktemplate->getFile('html/display.php');

			if ($this->config->get('credits', 1))
			{
				$this->poweredBy();
			}
		}
	}

	public function displayLayout($layout = null, $tpl = null)
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if ($layout)
		{
			$this->setLayout ($layout);
		}

		$view = $this->getName();
		$layout = $this->getLayout();
		$viewName = ucfirst($view);
		$layoutName = ucfirst($layout);
		$layoutFunction = 'display' . $layoutName;

		KUNENA_PROFILER ? $this->profiler->start("display {$viewName}/{$layoutName}") : null;

		if (!$this->embedded && isset($this->common))
		{

			if ($this->config->board_offline && !$this->me->isAdmin())
			{
				// Forum is offline
				JResponse::setHeader('Status', '503 Service Temporarily Unavailable', true);
				$this->common->header = JText::_('COM_KUNENA_FORUM_IS_OFFLINE');
				$this->common->body = $this->config->offline_message;
				$this->common->html = true;
				$this->common->display('default');
				KUNENA_PROFILER ? $this->profiler->stop("display {$viewName}/{$layoutName}") : null;

				return;
			}
			elseif ($this->config->regonly && ! $this->me->exists() && ! $this->teaser)
			{
				// Forum is for registered users only
				JResponse::setHeader('Status', '403 Forbidden', true);
				$this->common->header = JText::_('COM_KUNENA_LOGIN_NOTIFICATION');
				$this->common->body = JText::_('COM_KUNENA_LOGIN_FORUM');
				$this->common->display('default');
				KUNENA_PROFILER ? $this->profiler->stop("display {$viewName}/{$layoutName}") : null;

				return;
			}
			elseif (!method_exists($this, $layoutFunction) && !is_file(KPATH_SITE."/views/{$view}/{$layout}.php"))
			{
				// Layout was not found (don't allow Joomla to raise an error)
				$this->displayError(array(JText::_('COM_KUNENA_NO_ACCESS')), 404);
				KUNENA_PROFILER ? $this->profiler->stop("display {$viewName}/{$layoutName}") : null;

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
		KUNENA_PROFILER ? $this->profiler->stop("display {$viewName}/{$layoutName}") : null;

		return $contents;
	}

	/**
	 * Render new layout if available, otherwise continue to the old logic.
	 *
	 * @param string $layout
	 * @param string $tpl
	 * @param array  $hmvcParams
	 * @throws LogicException
	 */
	public function render($layout, $tpl, array $hmvcParams = array())
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
				$found = !strstr($path, '/com_kunena/') && is_file($path.$file.'.php');

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

	public function displayModulePosition($position)
	{
		echo $this->getModulePosition($position);
	}

	public function isModulePosition($position)
	{
		$document = JFactory::getDocument();

		return method_exists($document, 'countModules') ? $document->countModules ( $position ) : 0;
	}

	public function getModulePosition($position)
	{
		$html = '';
		$document = JFactory::getDocument();

		if (method_exists($document, 'countModules') && $document->countModules ( $position ))
		{
			$renderer = $document->loadRenderer ( 'modules' );
			$options = array ('style' => 'xhtml' );
			$html .= '<div class="'.$position.'">';
			$html .= $renderer->render ( $position, $options, null );
			$html .= '</div>';
		}

		return $html;
	}

	public function parse($text, $len=0, $parent)
	{
		if ($this instanceof KunenaViewSearch)
		{
			$parent_object = $parent;
		}
		else
		{
			$parent_object = $this;
		}

		return KunenaHtmlParser::parseBBCode($text, $parent_object, $len);
	}

	public function getButton($link, $name, $scope, $type, $id = null)
	{
		return $this->ktemplate->getButton(KunenaRoute::_($link), $name, $scope, $type, $id);
	}

	public function getIcon($name, $title='')
	{
		return $this->ktemplate->getIcon($name, $title);
	}

	public function getImage($image, $alt='')
	{
		return $this->ktemplate->getImage($image, $alt);
	}

	public function getClass($class, $class_sfx='')
	{
		return $this->ktemplate->getClass($class, $class_sfx);
	}

	public function get($property, $default = null)
	{
		KUNENA_PROFILER ? $this->profiler->start("model get{$property}") : null;
		$result = parent::get($property, $default);
		KUNENA_PROFILER ? $this->profiler->stop("model get{$property}") : null;

		return $result;
	}

	public function getTime()
	{
		if (!$this->config->time_to_create_page)
		{
			return;
		}

		$time = $this->profiler->getTime('Total Time');

		return sprintf('%0.3f', $time);
	}

	/**
	 * This function formats a number to n significant digits when above
	 * 10,000. Starting at 10,0000 the out put changes to 10k, starting
	 * at 1,000,000 the output switches to 1m. Both k and m are defined
	 * in the language file. The significant digits are used to limit the
	 * number of digits displayed when in 10k or 1m mode.
	 *
	 * @param int $number 		Number to be formated
	 * @param int $precision	Significant digits for output
	 * @return string
	 */
	public function formatLargeNumber($number, $precision = 3)
	{
		// Do we need to reduce the number of significant digits?
		if ($number >= 10000)
		{
			// Round the number to n significant digits
			$number = round ($number, -1*(log10($number)+1) + $precision);
		}

		if ($number < 10000)
		{
			$output = $number;
		}
		elseif ($number >= 1000000)
		{
			$output = $number / 1000000 . JText::_('COM_KUNENA_MILLION');
		}
		else
		{
			$output = $number / 1000 . JText::_('COM_KUNENA_THOUSAND');
		}

		return $output;
	}

	public function getCategoryLink(KunenaForumCategory $category, $content = null, $title = null, $class = null)
	{
		if (!$content)
		{
			$content = $this->escape($category->name);
		}

		if ($title === null)
		{
			$title = JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($category->name));
		}

		return JHtml::_('kunenaforum.link', $category->getUri(), $content, $title, $class, 'follow');
	}

	public function getTopicLink(KunenaForumTopic $topic, $action = null, $content = null, $title = null, $class = null, KunenaForumCategory $category = NULL)
	{
		$uri = $topic->getUri($category ? $category : (isset($this->category) ? $this->category : $topic->category_id), $action);

		if (!$content)
		{
			$content = KunenaHtmlParser::parseText($topic->subject);
		}

		$rel = 'follow';

		if ($title === null)
		{
			$rel = 'nofollow';

			if ($action instanceof KunenaForumMessage)
			{
				$title = JText::sprintf('COM_KUNENA_TOPIC_MESSAGE_LINK_TITLE', $this->escape($topic->subject));
			}
			else
			{
				switch ($action)
				{
					case 'first':
						$title = JText::sprintf('COM_KUNENA_TOPIC_FIRST_LINK_TITLE', $this->escape($topic->subject));
						break;
					case 'last':
						$title = JText::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', $this->escape($topic->subject));
						break;
					case 'unread':
						$title = JText::sprintf('COM_KUNENA_TOPIC_UNREAD_LINK_TITLE', $this->escape($topic->subject));
						break;
					default:
						$title = JText::sprintf('COM_KUNENA_TOPIC_LINK_TITLE', $this->escape($topic->subject));
				}
			}
		}

		return JHtml::_('kunenaforum.link', $uri, $content, $title, $class, $rel);
	}

	public function addStyleSheet($filename)
	{
		return KunenaFactory::getTemplate()->addStyleSheet ( $filename );
	}

	public function addScript($filename)
	{
		return KunenaFactory::getTemplate()->addScript ( $filename );
	}

	public function displayError($messages = array(), $code = 404)
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		$title = JText::_('COM_KUNENA_ACCESS_DENIED');	// can be overriden

		switch ((int) $code)
		{
			case 400:
				JResponse::setHeader('Status', '400 Bad Request', true);
				break;
			case 401:
				JResponse::setHeader('Status', '401 Unauthorized', true);
				break;
			case 403:
				JResponse::setHeader('Status', '403 Forbidden', true);
				break;
			case 404:
				JResponse::setHeader('Status', '404 Not Found', true);
				break;
			case 410:
				JResponse::setHeader('Status', '410 Gone', true);
				break;
			case 500:
				JResponse::setHeader('Status', '500 Internal Server Error', true);
				break;
			case 503:
				JResponse::setHeader('Status', '503 Service Temporarily Unavailable', true);
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
		$this->common->body = $output;
		$this->common->html = true;
		$this->common->display();

		$this->setTitle($title);
	}

	public function displayNoAccess($errors = array())
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		// Backward compatibility
		$this->displayError($errors, 200);
	}

	public function displayMenu()
	{
		echo $this->common->display('menu');
	}

	public function displayLoginBox()
	{
		echo $this->common->display('loginbox');
	}

	public function displayFooter()
	{
		echo $this->common->display('footer');
	}

	public function displayBreadcrumb()
	{
		echo $this->common->display('breadcrumb');
	}

	public function displayForumJump()
	{
		if (KunenaFactory::getConfig()->enableforumjump)
		{
			$this->common->catid = !empty($this->category->id) ? $this->category->id : 0;

			echo $this->common->display('forumjump');
		}
	}

	public function displayWhoIsOnline($tpl = null)
	{
		if (KunenaFactory::getConfig()->showwhoisonline > 0)
		{
			echo $this->common->display('whosonline');
		}
	}

	public function displayStatistics()
	{
		$config = KunenaFactory::getConfig();

		if ($config->showstats > 0 && ($config->statslink_allowed || KunenaUserHelper::get()->exists()))
		{
				echo $this->common->display('statistics');
		}
	}

	public function displayAnnouncement()
	{
		if (KunenaFactory::getConfig()->showannouncement > 0)
		{
			echo $this->common->display('announcement');
		}
	}

	public function displayFormToken()
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		echo '[K=TOKEN]';
	}

	public function row($start = false)
	{
		if ($start)
		{
			$this->_row = 0;
		}

		return ++$this->_row & 1 ? 'odd' : 'even';
	}

	public function displayTemplateFile($view, $layout, $template = null)
	{
		// HMVC legacy support.
		list($name, $override) = $this->ktemplate->mapLegacyView("{$view}/{$layout}_{$template}");
		$hmvc = KunenaLayout::factory($name)->setLayout($override);

		if ($hmvc->getPath())
		{
			return $hmvc->setLegacy($this);
		}

		// Old code.
		if (!isset($this->_path['template_'.$view]))
		{
			$this->_path['template_'.$view] = $this->_path['template'];

			foreach ($this->_path['template_'.$view] as &$dir)
			{
				$dir = preg_replace("#/{$this->_name}/$#", "/{$view}/", $dir);
			}
		}

		if ($template)
		{
			$template = '_'.$template;
		}

		$file = "{$layout}{$template}.php";
		$file = KunenaPath::find($this->_path['template_'.$view], $file);

		if (!is_file($file))
		{
			JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file));
		}

		ob_start();
		include $file;
		$output = ob_get_contents();
		ob_end_clean();

		if (JDEBUG || $this->config->get('debug'))
		{
			$output = trim($output);
			$output = "\n<!-- START {$file} -->\n{$output}\n<!-- END {$file} -->\n";
		}

		echo $output;
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param   string  $tpl	The name of the template source file ...
	 * 					automatically searches the template paths and compiles as needed.
	 * @param   array   $hmvcParams	Extra parameters for HMVC.
	 * @return  string   The output of the the template script.
	 */
	public function loadTemplateFile($tpl = null, $hmvcParams = null)
	{
		KUNENA_PROFILER ? $this->profiler->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		// HMVC legacy support.
		$view = $this->getName();
		$layout = $this->getLayout();
		list($name, $override) = $this->ktemplate->mapLegacyView("{$view}/{$layout}_{$tpl}");
		$hmvc = KunenaLayout::factory($name)->setLayout($override);

		if ($hmvc->getPath())
		{
			if ($hmvcParams)
			{
				$hmvc->setProperties($hmvcParams);
			}

			KUNENA_PROFILER ? $this->profiler->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

			return $hmvc->setLegacy($this);
		}

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout.'_'.$tpl : $layout;

		if (!isset($this->templatefiles[$file]))
		{
			// Clean the file name
			$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
			$tpl  = isset($tpl)? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

			// Load the template script
			$filetofind	= $this->_createFileName('template', array('name' => $file));
			$this->templatefiles[$file] = KunenaPath::find($this->_path['template'], $filetofind);
		}

		$this->_template = $this->templatefiles[$file];

		if ($this->_template != false)
		{
			$templatefile = preg_replace('%'.KunenaPath::clean(JPATH_ROOT,'/').'/%', '', KunenaPath::clean($this->_template, '/'));

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
				$output = "\n<!-- START {$templatefile} -->\n{$output}\n<!-- END {$templatefile} -->\n";
			}
		}
		else
		{
			$output = JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $this->getName().'/'.$file));
		}

		KUNENA_PROFILER ? $this->profiler->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $output;
	}

	final public function poweredBy()
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		$credits = '<div style="text-align:center">';
		$credits .= JHtml::_('kunenaforum.link', 'index.php?option=com_kunena&view=credits', JText::_('COM_KUNENA_POWEREDBY'), '', '', 'follow', array('style'=>'display: inline; visibility: visible; text-decoration: none;'));
		$credits .= ' <a href="https://www.kunena.org" rel="follow" target="_blank" style="display: inline; visibility: visible; text-decoration: none;">'.JText::_('COM_KUNENA').'</a>';

		if ($this->ktemplate->params->get('templatebyText'))
		{
			$credits .= ' :: <a href ="'. $this->ktemplate->params->get('templatebyLink').'" rel="follow" target="_blank" style="text-decoration: none;">' . $this->ktemplate->params->get('templatebyText') .' '. $this->ktemplate->params->get('templatebyName') .'</a>';
		}

		$credits .= '</div>';

		echo $credits;
	}

	// Caching
	public function getTemplateMD5()
	{
		return md5(serialize($this->_path['template']).'-'.$this->ktemplate->name);
	}

	public function setTitle($title)
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if (!$this->state->get('embedded'))
		{
			// Check for empty title and add site name if param is set
			$title = strip_tags($title);
			if ($this->app->getCfg('sitename_pagetitles', 0) == 1)
			{
				$title = JText::sprintf('JPAGETITLE', $this->app->getCfg('sitename'), $this->config->board_title .' - '. $title);
			}
			elseif ($this->app->getCfg('sitename_pagetitles', 0) == 2)
			{
				$title = JText::sprintf('JPAGETITLE', $title .' - '. $this->config->board_title, $this->app->getCfg('sitename'));
			}
			else
			{
				// TODO: allow translations/overrides (also above)
				$title = KunenaFactory::getConfig()->board_title .': '. $title;
			}
			$this->document->setTitle($title);
		}
	}

	public function setKeywords($keywords)
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if (!$this->state->get('embedded'))
		{
			if (!empty($keywords))
			{
				$this->document->setMetadata ( 'keywords', $keywords );
			}
		}
	}

	public function setDescription($description)
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if (!$this->state->get('embedded'))
		{
			// TODO: allow translations/overrides
			$lang = JFactory::getLanguage();
			$length = JString::strlen($lang->getName());
			$length = 137 - $length;

			if (JString::strlen($description) > $length)
			{
				$description = JString::substr($description, 0, $length) . '...';
			}

			$this->document->setMetadata('description', $description);
		}
	}
}
