<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.view' );
kimport ( 'kunena.html.parser' );
kimport ('kunena.date');
kimport ('kunena.user.helper');
kimport ('kunena.profiler');

/**
 * Kunena View Class
 */
class KunenaView extends JView {
	protected $_row = 0;

	function __construct($config = array()){
		parent::__construct($config);
		$this->profiler = KunenaProfiler::instance('Kunena');
		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();
		$this->template = KunenaFactory::getTemplate();
	}

	function displayAll() {
		$this->app = JFactory::getApplication ();
		if ($this->config->board_offline) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_FORUM_IS_OFFLINE'), $this->me->isAdmin () ? 'notice' : 'error');
		}
		if ($this->config->debug && $this->me->isAdmin ()) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_WARNING_DEBUG'), 'notice');
		}

		$this->assignRef ( 'state', $this->get ( 'State' ) );
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		$this->template->initialize();

		$this->displayLayout ();
	}

	function displayLayout($layout=null, $tpl = null) {
		if ($layout) $this->setLayout ($layout);
		$viewName = ucfirst($this->getName ());
		$layoutName = ucfirst($this->getLayout ());

		KUNENA_PROFILER ? $this->profiler->start("display {$viewName}/{$layoutName}") : null;

		if (isset($this->common)) {
			if ($this->config->board_offline && ! $this->me->isAdmin ()) {
				// Forum is offline
				$this->common->header = JText::_('COM_KUNENA_FORUM_IS_OFFLINE');
				$this->common->body = $this->config->offline_message;
				$this->common->display('default');
				KUNENA_PROFILER ? $this->profiler->start("display {$viewName}/{$layoutName}") : null;
				return;
			} elseif ($this->config->regonly && ! $this->me->exists()) {
				// Forum is for registered users only
				$this->common->header = JText::_('COM_KUNENA_LOGIN_NOTIFICATION');
				$this->common->body = JText::_('COM_KUNENA_LOGIN_FORUM');
				$this->common->display('default');
				KUNENA_PROFILER ? $this->profiler->start("display {$viewName}/{$layoutName}") : null;
				return;
			}
		}

		$this->assignRef ( 'state', $this->get ( 'State' ) );
		$layoutFunction = 'display'.$layoutName;
		if (method_exists($this, $layoutFunction)) {
			$contents = $this->$layoutFunction ($tpl);
		} else {
			$contents = $this->display($tpl);
		}
		KUNENA_PROFILER ? $this->profiler->stop("display {$viewName}/{$layoutName}") : null;
		return $contents;
	}

	function displayModulePosition($position) {
		echo $this->getModulePosition($position);
	}

	function isModulePosition($position) {
		return JDocumentHTML::countModules ( $position );
	}
	function getModulePosition($position) {
		$html = '';
		if (JDocumentHTML::countModules ( $position )) {
			$renderer = JFactory::getDocument ()->loadRenderer ( 'modules' );
			$options = array ('style' => 'xhtml' );
			$html .= '<div class="'.$position.'">';
			$html .= $renderer->render ( $position, $options, null );
			$html .= '</div>';
		}
		echo $html;
	}

	function parse($text, $len=0) {
		return KunenaHtmlParser::parseBBCode($text, $this, $len);
	}

	function getButton($name, $text) {
		return $this->template->getButton($name, $text);
	}

	function getIcon($name, $title='') {
		return $this->template->getIcon($name, $title);
	}

	function getImage($image, $alt='') {
		return $this->template->getImage($image, $alt);
	}

	function getClass($class, $class_sfx='') {
		return $this->template->getClass($class, $class_sfx);
	}

	public function get($property, $default = null) {
		KUNENA_PROFILER ? $this->profiler->start("model get{$property}") : null;
		$result = parent::get($property, $default);
		KUNENA_PROFILER ? $this->profiler->stop("model get{$property}") : null;
		return $result;
	}

	function getTime() {
		$time = $this->profiler->getTime('Total Time');
		return sprintf('%0.3f', $time);
	}

	function isMenu() {
		return JDocumentHTML::countModules ( 'kunena_menu' );
	}

	function getMenu() {
		jimport ( 'joomla.application.module.helper' );
		$position = "kunena_menu";
		$options = array ('style' => 'xhtml' );
		$modules = JModuleHelper::getModules ( $position );
		$html = '';
		foreach ( $modules as $module ) {
			if ($module->module == 'mod_mainmenu') {
				$basemenu = KunenaRoute::getMenu ();
				if ($basemenu) {
					$module = clone $module;
					// FIXME: J1.5 only
					$search = array ('/menutype=(.*)(\s)/', '/startLevel=(.*)(\s)/', '/endLevel=(.*)(\s)/' );
					$replace = array ("menutype={$basemenu->menutype}\\2", 'startLevel=' . ($basemenu->sublevel + 1) . '\2', 'endLevel=' . ($basemenu->sublevel + 2) . '\2' );
					$module->params = preg_replace ( $search, $replace, $module->params );
				}
			}
			$html .= JModuleHelper::renderModule ( $module, $options );
		}
		return $html;
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
	 */
	function formatLargeNumber($number, $precision = 3) {
		$output = '';
		// Do we need to reduce the number of significant digits?
		if ($number >= 10000){
			// Round the number to n significant digits
			$number = round ($number, -1*(log10($number)+1) + $precision);
		}

		if ($number < 10000) {
			$output = $number;
		} elseif ($number >= 1000000) {
			$output = $number / 1000000 . JText::_('COM_KUNENA_MILLION');
		} else {
			$output = $number / 1000 . JText::_('COM_KUNENA_THOUSAND');
		}

		return $output;
	}

	public function getCategoryLink($category, $content = null, $title = null) {
		if (!$content) $content = $this->escape($category->name);
		if ($title === null) $title = JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($category->name));
		return JHTML::_('kunenaforum.link', $category->getUrl(), $content, $title, '', 'follow');
	}

	public function getTopicUrl($topic, $action, $object=false) {
		if ($action instanceof StdClass || $action instanceof KunenaForumMessage) {
			$message = $action;
			$action = 'm'.$message->id;
		}
		$uri = JURI::getInstance("index.php?option=com_kunena&view=topic&id={$topic->id}&action={$action}");
		if ($uri->getVar('action') !== null) {
			$uri->delVar('action');
			$uri->setVar('catid', isset($this->category) ? $this->category->id : $topic->catid);
			$limit = max(1, $this->config->messages_per_page);
			$mesid = 0;
			if (is_numeric($action)) {
				if ($action) $uri->setVar('limitstart', $action * $limit);
			} elseif (isset($message)) {
				$mesid = $message->id;
				$position = $topic->getPostLocation($mesid, $this->message_ordering);
			} else {
				switch ($action) {
					case 'first':
						$mesid = $topic->first_post_id;
						$position = $topic->getPostLocation($mesid, $this->message_ordering);
						break;
					case 'last':
						$mesid = $topic->last_post_id;
						$position = $topic->getPostLocation($mesid, $this->message_ordering);
						break;
					case 'unread':
						$mesid = $topic->lastread ? $topic->lastread : $topic->last_post_id;
						$position = $topic->getPostLocation($mesid, $this->message_ordering);
						break;
				}
			}
			if ($mesid) {
				if (JFactory::getApplication()->getUserState( 'com_kunena.topic_layout', 'default' ) != 'threaded') {
					$uri->setFragment($mesid);
				} else {
					$uri->setVar('mesid', $mesid);
				}
			}
			if (isset($position)) {
				$limitstart = intval($position / $limit) * $limit;
				if ($limitstart) $uri->setVar('limitstart', $limitstart);
			}
		}
		return $object ? $uri : KunenaRoute::_($uri);
	}

	public function getTopicLink($topic, $action, $content = null, $title = null, $class = null) {
		$uri = $this->getTopicUrl($topic, $action, true);
		if (!$content) $content = KunenaHtmlParser::parseText($topic->subject);
		if ($title === null) {
			switch ($action) {
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
		return JHTML::_('kunenaforum.link', $uri, $content, $title, $class, 'nofollow');
	}

	public function addStyleSheet($filename) {
		$template = KunenaFactory::getTemplate();
		return $template->addStyleSheet ( $filename );
	}

	public function addScript($filename) {
		$template = KunenaFactory::getTemplate();
		return $template->addScript ( $filename );
	}

	function displayNoAccess($errors = array()) {
		$output = '';
		foreach ($errors as $error) $output .= "<p>{$error}</p>";
		$this->common->setLayout ( 'default' );
		$this->common->assign ( 'header', JText::_('COM_KUNENA_ACCESS_DENIED'));
		$this->common->assign ( 'body', $output);
		$this->common->assign ( 'html', true);
		$this->common->display();
		$this->setTitle(JText::_('COM_KUNENA_ACCESS_DENIED'));
	}

	function displayMenu() {
		echo $this->common->display('menu');
	}

	function displayLoginBox() {
		echo $this->common->display('loginbox');
	}

	function displayFooter() {
		echo $this->common->display('footer');
	}

	function displayBreadcrumb() {
		echo $this->common->display('breadcrumb');
	}

	function displayForumJump() {
		if (KunenaFactory::getConfig()->enableforumjump) {
			$this->common->catid = !empty($this->category->id) ? $this->category->id : 0;
			echo $this->common->display('forumjump');
		}
	}

	function displayWhoIsOnline($tpl = null) {
		if (KunenaFactory::getConfig()->showwhoisonline > 0) {
			echo $this->common->display('whosonline');
		}
	}

	function displayStatistics() {
		if (KunenaFactory::getConfig()->showstats > 0) {
			echo $this->common->display('statistics');
		}
	}

	function displayAnnouncement() {
		if (KunenaFactory::getConfig()->showannouncement > 0) {
			echo $this->common->display('announcement');
		}
	}

	function setTitle($title) {
		if (!$this->state->get('embedded')) {
			$this->document->setTitle ( KunenaFactory::getConfig()->board_title .' :: '. strip_tags($title) );
		}
	}

	function row($start=false) {
		if ($start) $this->_row = 0;
		return ++$this->_row & 1 ? 'odd' : 'even';
	}

	// Caching
	function getTemplateMD5() {
		return md5(serialize($this->_path['template']).'-'.$this->template->name);
	}
}