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

jimport('joomla.html.html');
kimport('kunena.forum.category.helper');

abstract class JHTMLKunenaForum {
	function categorylist($name, $parent, $options = array(), $params = array(), $attribs = null, $key = 'value', $text = 'text', $selected = array(), $idtag = false, $translate = false) {
		$unpublished = isset($params['unpublished']) ? (bool) $params['unpublished'] : 0;
		$sections = isset($params['sections']) ? (bool) $params['sections'] : 0;
		$ordering = isset($params['ordering']) ? (string) $params['ordering'] : 'ordering';
		$direction = isset($params['direction']) && $params['direction'] == 'desc' ? -1 : 1;
		$action = isset($params['action']) ? (string) $params['action'] : 'read';
		$levels = isset($params['levels']) ? (int) $params['levels'] : 10;
		$toplevel = isset($params['toplevel']) ? (bool) $params['toplevel'] : 0;
		$catid = isset($params['catid']) ? (int) $params['catid'] : 0;

		$me = KunenaFactory::getUser();
		$params = array ();
		$params['ordering'] = $ordering;
		$params['direction'] = $direction;
		$params['unpublished'] = $unpublished;
		$params['action'] = $action;
		$params['selected'] = $catid;
		if ($catid) {
			if (!KunenaForumCategoryHelper::get($catid)->getParent()->authorise($action))
				$categories = KunenaForumCategoryHelper::getParents($catid, $levels, $params);
		}
		if (!isset($categories)) {
			$categories = KunenaForumCategoryHelper::getChildren($parent, $levels, $params);
		}

		if (!is_array($options)) {
			$options = array();
		}
		if ($selected === false || $selected === null) {
			$selected = array();
		} elseif (!is_array($selected)) {
			$selected = array((string) $selected);
		}
		if ($toplevel) {
			$disabled = ($action == 'admin' && !$me->isAdmin());
			$options [] = JHTML::_ ( 'select.option', '0', JText::_ ( 'COM_KUNENA_TOPLEVEL' ), 'value', 'text', $disabled );
			if (empty($selected) && !$disabled) {
				$selected[] = 0;
			}
		}
		foreach ( $categories as $category ) {
			$disabled = !$category->authorise ($action) || (! $sections && $category->section);
			if (empty($selected) && !$disabled) {
				$selected[] = $category->id;
			}
			$options [] = JHTML::_ ( 'select.option', $category->id, str_repeat  ( '- ', $category->level+$toplevel  ).' '.$category->name, 'value', 'text', $disabled );
		}

		reset ( $options );
		if (is_array ( $attribs )) {
			$attribs = JArrayHelper::toString ( $attribs );
		}
		$id = $name;
		if ($idtag) {
			$id = $idtag;
		}
		$id = str_replace ( '[', '', $id );
		$id = str_replace ( ']', '', $id );

		$html = '';
		if (!empty($options)) {
			$html .= '<select name="' . $name . '" id="' . $id . '" ' . $attribs . '>';
			$html .= JHTML::_ ( 'select.options', $options, $key, $text, $selected, $translate );
			$html .= '</select>';
		}

		return $html;
	}
}