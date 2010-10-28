<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.view' );

/**
 * About view for Kunena backend
 */
class KunenaViewCommon extends JView {
	function display($tpl = null) {
		switch ($this->getLayout ()) {
			case 'loginbox' :
				$this->displayLoginBox();
				break;
			case 'footer' :
				$this->displayFooter();
				break;
			case 'default' :
				$this->displayDefault();
				break;
		}
		parent::display ($tpl);
	}

	function displayDefault() {
	}

	function displayLoginBox() {
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		require_once KPATH_SITE . '/lib/kunena.timeformat.class.php';
		$my = JFactory::getUser ();
		$this->assign ( 'me', KunenaFactory::getUser ());

		$uri = JFactory::getURI ();
		$this->assign ( 'return',  base64_encode ( $uri->toString ( array ('path', 'query', 'fragment' ) ) ) );
		$this->assign ( 'moduleHtml', $this->getModulePosition('kunena_profilebox'));

		$login = KunenaFactory::getLogin();
		if ($my->get ( 'guest' )) {
			$this->setLayout('login');
			if ($login) {
				$this->assignRef ( 'login', $login->getLoginFormFields() );
				$this->assignRef ( 'register', $login->getRegistrationURL() );
				$this->assignRef ( 'lostpassword', $login->getResetURL() );
				$this->assignRef ( 'lostusername', $login->getRemindURL() );
			}
		} else {
			$this->setLayout('logout');
			if ($login) $this->assignRef ( 'logout', $login->getLogoutFormFields() );

			// Private messages
			$private = KunenaFactory::getPrivateMessaging();
			if ($private) {
				$count = $private->getUnreadCount($this->me->userid);
				$this->assign ( 'privateMessages', $private->getInboxLink($count ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $count) : JText::_('COM_KUNENA_PMS_INBOX')));
			}

			// Announcements
			$config = KunenaFactory::getConfig();
			$annmods = @explode ( ',', $config->annmodid );
			if (in_array ( $this->me->userid, $annmods ) || $this->me->isAdmin()) {
				$this->assign ( 'announcements', '<a href="' . CKunenaLink::GetAnnouncementURL ( 'show' ).'">'. JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS').'</a>');
			}

		}
	}

	function displayFooter() {
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		$catid = 0;
		if (KunenaFactory::getConfig ()->enablerss) {
			if ($catid > 0) {
				kimport ( 'kunena.forum.category.helper' );
				$category = KunenaForumCategoryHelper::get ( $catid );
				if ($category->pub_access == 0 && $category->parent)
					$rss_params = '&catid=' . ( int ) $catid;
			} else {
				$rss_params = '';
			}
			if (isset ( $rss_params )) {
				$document = JFactory::getDocument ();
				$document->addCustomTag ( '<link rel="alternate" type="application/rss+xml" title="' . JText::_ ( 'COM_KUNENA_LISTCAT_RSS' ) . '" href="' . CKunenaLink::GetRSSURL ( $rss_params ) . '" />' );
				$this->assign ( 'rss', CKunenaLink::GetRSSLink ( $this->getIcon ( 'krss', JText::_('COM_KUNENA_LISTCAT_RSS') ), 'follow', $rss_params ));
			}
		}
		$template = KunenaFactory::getTemplate ();
		$credits = CKunenaLink::GetTeamCreditsLink ( $catid, JText::_('COM_KUNENA_POWEREDBY') ) . ' ' . CKunenaLink::GetCreditsLink ();
		if ($template->params->get('templatebyText') !='') {
			$credits .= ' :: <a href ="'. $template->params->get('templatebyLink').'" rel="follow">' . $template->params->get('templatebyText') .' '. $template->params->get('templatebyName') .'</a>';
		}
		$this->assign ( 'credits', $credits );
	}

	function getModulePosition($position) {
		$html = '';
		if (JDocumentHTML::countModules ( $position )) {
			$document = &JFactory::getDocument ();
			$renderer = $document->loadRenderer ( 'modules' );
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

	function addStyleSheet($filename) {
		if (!KunenaFactory::getConfig ()->debug && !KunenaForum::isSvn()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.css$/u', '-min.css', $filename );
		}
		$document = JFactory::getDocument ();
		$template = KunenaFactory::getTemplate();
		return $document->addStyleSheet ( $template->getFile($filename) );
	}

	function addScript($filename) {
		if (!KunenaFactory::getConfig ()->debug && !KunenaForum::isSvn()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.js$/u', '-min.js', $filename );
		}
		$document = JFactory::getDocument ();
		$template = KunenaFactory::getTemplate();
		return $document->addScript ( $template->getFile($filename) );
	}
}