<?php
/**
 * @version		$Id: view.html.php 3901 2010-11-15 14:14:02Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * Categories View
 */
class KunenaViewCategories extends KunenaView {
	function display() {
		$this->assignRef ( 'state', $this->get ( 'State' ) );
		$this->assignRef ( 'categories', $this->get ( 'Items' ) );
		switch ($this->getLayout ()) {
			case 'default' :
				$this->displayDefault ();
				break;
		}

		require_once KPATH_SITE . '/class.kunena.php';
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		require_once KPATH_SITE . '/lib/kunena.timeformat.class.php';
		$template = KunenaFactory::getTemplate();
		$template->loadTemplate('initialize.php');
		echo '<div id="Kunena">';
		$this->displayCommon('menu');
		$this->displayCommon('loginbox');
		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
		} else {
			parent::display (null,true);
		}
		$this->displayCommon('footer');
		echo '</div>';
	}

	function displayDefault() {
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();
	}

	public function getCategoryIcon($catid, $thumb = false) {
		if (! $thumb) {
			if ($this->config->shownew && $this->me->userid != 0) {
				if (! empty ( $this->new [$catid] )) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_on.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $catid . "_on.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return $this->getIcon ( 'kunreadforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_off.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $catid . "_off.gif\" border=\"0\" class='kforum-cat-image' alt=\" \"  />";
					} else {
						return $this->getIcon ( 'kreadforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
					}
				}
			} else {
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_notlogin.gif" )) {
					return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $catid . "_notlogin.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return $this->getIcon ( 'knotloginforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
				}
			}
		} elseif ($this->config->showchildcaticon) {
			if ($this->config->shownew && $this->me->userid != 0) {
				if (! empty ( $this->new [$catid] )) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_on_childsmall.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $catid . "_on_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return $this->getIcon ( 'kunreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_off_childsmall.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $catid . "_off_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return $this->getIcon ( 'kreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
					}
				}
			} else {
				// Not Login Cat Images
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $catid . "_notlogin_childsmall.gif" )) {
					return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $catid . "_notlogin_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return $this->getIcon ( 'knotloginforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
				}
			}
		}
		return '';
	}
}