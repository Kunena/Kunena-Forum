<?php
/**
* @version $Id$
* Kunena Component - Kunena Factory
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
defined( '_JEXEC' ) or die();

jimport('joomla.html.html.select');

abstract class JHTMLKunena {
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
		kimport('category');
		$params = array ();
		$params['ordering'] = $ordering;
		$params['direction'] = $direction;
		$params['unpublished'] = $unpublished;
		$params['action'] = $action;
		$params['selected'] = $catid;
		if ($catid) {
			if (!KunenaCategory::getInstance($catid)->getParent()->authorize($action))
				$categories = KunenaCategory::getParents($catid, $levels, $params);
		}
		if (!isset($categories)) {
			$categories = KunenaCategory::getChildren($parent, $levels, $params);
		}

		if (!is_array($options)) {
			$options = array();
		}
		if (!is_array($selected)) {
			if ((int)$selected)
				$selected = array((int)$selected);
			else
				$selected = array();
		}
		$empty = empty($options);
		$hide = $action == 'admin' && !$me->isAdmin();
		if ($toplevel) $options [] = JHTML::_ ( 'select.option', '0', JText::_ ( 'COM_KUNENA_TOPLEVEL' ), 'value', 'text', $hide );
		if ($empty && empty($selected) && !$hide) {
			$selected[] = 0;
		}
		foreach ( $categories as $category ) {
			$hide = !$category->authorize ($action) || (! $sections && $category->section);
			if ($empty && empty($selected) && !$hide) {
				$selected[] = $category->id;
			}
			$options [] = JHTML::_ ( 'select.option', $category->id, str_repeat  ( '...', $category->level+$toplevel  ).' '.$category->name, 'value', 'text', $hide );
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

		$html = '<select name="' . $name . '" id="' . $id . '" ' . $attribs . '>';
		$html .= JHTMLSelect::Options ( $options, $key, $text, $selected, $translate );
		$html .= '</select>';

		return $html;
	}
}