<?php
/**
 * @version		$Id$
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
	function displayDefault($tpl = null) {
		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		if ($this->category->id && !$this->get ( 'Category')->authorise('read')) {
			$this->setError($this->categories->getError());
		}
		$this->assignRef ( 'categories', $this->get ( 'Categories' ) );
		$this->assignRef ( 'new', $this->get ( 'New' ) );
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
		} else {
			// meta description and keywords
			$metaDesc = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title );
			$metaKeys = (JText::_('COM_KUNENA_CATEGORIES') . ', ' . $this->config->board_title . ', ' . JFactory::getApplication ()->getCfg ( 'sitename' ));

			$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;
			$this->document->setMetadata ( 'keywords', $metaKeys );
			$this->document->setDescription ( $metaDesc );

			$this->display ($tpl);
		}
	}

	function displayUser($tpl = null) {
		$this->assignRef ( 'categories', $this->get ( 'Categories' ) );
		$this->me = KunenaFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->config = KunenaFactory::getConfig();

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
		} else {
			$this->header = $this->title = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS');

			// meta description and keywords
			$metaDesc = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title );
			$metaKeys = (JText::_('COM_KUNENA_CATEGORIES') . ', ' . $this->config->board_title . ', ' . JFactory::getApplication ()->getCfg ( 'sitename' ));

			$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;
			$this->document->setMetadata ( 'keywords', $metaKeys );
			$this->document->setDescription ( $metaDesc );

			$this->display ($tpl);
		}
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