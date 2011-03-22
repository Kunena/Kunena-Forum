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

/**
 * Kunena View Class
 */
class KunenaView extends JView {
	function displayAll() {
		$this->app = JFactory::getApplication ();
		$this->config = KunenaFactory::getConfig();
		$this->me = KunenaFactory::getUser();
		if ($this->config->board_offline) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_FORUM_IS_OFFLINE'), $this->me->isAdmin () ? 'notice' : 'error');
		}
		if ($this->config->debug && $this->me->isAdmin ()) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_WARNING_DEBUG'), 'notice');
		}

		$this->assignRef ( 'state', $this->get ( 'State' ) );
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		$this->template = KunenaFactory::getTemplate();
		$this->template->loadTemplate('initialize.php');

		echo '<div id="Kunena">';
		$this->common->display('menu');
		$this->common->display('loginbox');
		$this->displayLayout ();
		$this->common->display('footer');
		echo '</div>';
	}

	function displayLayout($layout=null, $tpl = null) {
		$this->config = KunenaFactory::getConfig();
		$this->me = KunenaFactory::getUser();
		if (isset($this->common)) {
			if ($this->config->board_offline && ! $this->me->isAdmin ()) {
				// Forum is offline
				$this->common->header = JText::_('COM_KUNENA_FORUM_IS_OFFLINE');
				$this->common->body = $this->config->offline_message;
				$this->common->display('default');
				return;
			} elseif ($this->config->regonly && ! $this->me->exists()) {
				// Forum is for registered users only
				$this->common->header = JText::_('COM_KUNENA_LOGIN_NOTIFICATION');
				$this->common->body = JText::_('COM_KUNENA_LOGIN_FORUM');
				$this->common->display('default');
				return;
			}
		}

		if ($layout) $this->setLayout ($layout);
		$this->assignRef ( 'state', $this->get ( 'State' ) );
		$layoutFunction = 'display'.ucfirst($this->getLayout ());
		if (method_exists($this, $layoutFunction)) {
			return $this->$layoutFunction ($tpl);
		} else {
			return $this->display($tpl);
		}
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

	function getButton($name, $text) {
		return '<span class="'.$name.'"><span>'.$text.'</span></span>';
	}

	function getIcon($name, $title='') {
		return '<span class="kicon '.$name.'" title="'.$title.'"></span>';
	}

	function topicIcon($topic) {
		$template = KunenaFactory::getTemplate ();
		return $template->getTopicIcon($topic);
	}

	function getTime() {
		$time = JProfiler::getmicrotime() - $this->starttime;
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
	function formatLargeNumber($number, $precision = 4) {
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

	function addStyleSheet($filename) {
		if (!JDEBUG && !KunenaFactory::getConfig ()->debug && !KunenaForum::isSvn()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.css$/u', '-min.css', $filename );
		}
		$document = JFactory::getDocument ();
		$template = KunenaFactory::getTemplate();
		return $document->addStyleSheet ( $template->getFile($filename) );
	}

	function addScript($filename) {
		if (!JDEBUG && !KunenaFactory::getConfig ()->debug && !KunenaForum::isSvn()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.js$/u', '-min.js', $filename );
		}
		$document = JFactory::getDocument ();
		$template = KunenaFactory::getTemplate();
		return $document->addScript ( $template->getFile($filename) );
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
}