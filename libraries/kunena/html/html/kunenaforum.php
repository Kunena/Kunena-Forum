<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage HTML
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class JHtmlKunenaForum
 */
abstract class JHtmlKunenaForum
{
	public static function categorylist($name, $parent, $options = array(), $params = array(), $attribs = null, $key = 'value', $text = 'text', $selected = array(), $idtag = false, $translate = false)
	{
		$preselect = isset($params['preselect']) ? (bool) ($params['preselect'] && $params['preselect'] != 'false') : true;
		$unpublished = isset($params['unpublished']) ? (bool) $params['unpublished'] : 0;
		$sections = isset($params['sections']) ? (bool) $params['sections'] : 0;
		$ordering = isset($params['ordering']) ? (string) $params['ordering'] : 'ordering';
		$direction = isset($params['direction']) && $params['direction'] == 'desc' ? -1 : 1;
		$action = isset($params['action']) ? (string) $params['action'] : 'read';
		$levels = isset($params['levels']) ? (int) $params['levels'] : 10;
		$topleveltxt = isset($params['toplevel']) ? $params['toplevel'] : false;
		$catid = isset($params['catid']) ? (int) $params['catid'] : 0;
		$hide_lonely = isset($params['hide_lonely']) ? (bool) $params['hide_lonely'] : 0;

		$params = array ();
		$params['ordering'] = $ordering;
		$params['direction'] = $direction;
		$params['unpublished'] = $unpublished;
		$params['action'] = $action;
		$params['selected'] = $catid;

		if ($catid)
		{
			$category = KunenaForumCategoryHelper::get($catid);

			if (!$category->getParent()->authorise($action) && !KunenaUserHelper::getMyself()->isAdmin())
			{
				$categories = KunenaForumCategoryHelper::getParents($catid, $levels, $params);
			}
		}
		$channels = array();

		if (!isset($categories))
		{
			$category = KunenaForumCategoryHelper::get($parent);
			$children = KunenaForumCategoryHelper::getChildren($parent, $levels, $params);

			if ($params['action'] == 'topic.create')
			{
				$channels = $category->getChannels();

				if (empty($children) && !isset($channels[$category->id]))
				{
					$category = KunenaForumCategoryHelper::get();
				}

				foreach ($channels as $id=>$channel)
				{
					if (!$id || $category->id == $id || isset($children[$id]) || !$channel->authorise ($action))
					{
						unset ($channels[$id]);
					}
				}
			}

			$categories = $category->id > 0 ? array($category->id=>$category)+$children : $children;

			if ($hide_lonely && count($categories)+count($channels) <= 1)
			{
				return;
			}
		}

		if (!is_array($options))
		{
			$options = array();
		}

		if ($selected === false || $selected === null)
		{
			$selected = array();
		}
		elseif (!is_array($selected))
		{
			$selected = array((string) $selected);
		}

		if ($topleveltxt)
		{
			$me = KunenaUserHelper::getMyself();
			$disabled = ($action == 'admin' && !$me->isAdmin());
			$options [] = JHtml::_ ( 'select.option', '0', JText::_ ( $topleveltxt ), 'value', 'text', $disabled );

			if ($preselect && empty($selected) && !$disabled)
			{
				$selected[] = 0;
			}

			$toplevel = 1;
		}
		else
		{
			$toplevel = -KunenaForumCategoryHelper::get($parent)->level;
		}

		foreach ($categories as $category)
		{
			$disabled = !$category->authorise ($action) || (! $sections && $category->isSection());

			if ($preselect && empty($selected) && !$disabled)
			{
				$selected[] = $category->id;
			}

			$options [] = JHtml::_ ( 'select.option', $category->id, str_repeat  ( '- ', $category->level+$toplevel  ).' '.$category->name, 'value', 'text', $disabled );
		}
		$disabled = false;
		foreach ($channels as $category)
		{
			if ($preselect && empty($selected))
			{
				$selected[] = $category->id;
			}

			$options [] = JHtml::_ ( 'select.option', $category->id, '+ '. $category->getParent()->name.' / '.$category->name, 'value', 'text', $disabled );
		}

		reset ($options);

		if (is_array ($attribs))
		{
			$attribs = JArrayHelper::toString ($attribs);
		}

		$id = $name;

		if ($idtag)
		{
			$id = $idtag;
		}

		$id = str_replace ( '[', '', $id );
		$id = str_replace ( ']', '', $id );

		$html = '';
		if (!empty($options))
		{
			$html .= '<select name="' . $name . '" id="' . $id . '" ' . $attribs . '>';
			$html .= JHtml::_ ( 'select.options', $options, $key, $text, $selected, $translate );
			$html .= '</select>';
		}

		return $html;
	}

	/**
	 *
	 * Creates link pointing to a Kunena page
	 *
	 * @param mixed $uri Kunena URI, either as string, JUri or array
	 * @param string $content
	 * @param string $class Link class
	 * @param string $title Link title
	 * @param string $rel Link relationship, see: http://www.w3.org/TR/html401/types.html#type-links
	 * @param mixed $attributes Tag attributes as: 'accesskey="a" lang="en"' or array('accesskey'=>'a', 'lang'=>'en')
	 *
	 * @return string
	 */
	public static function link($uri, $content, $title = '', $class = '', $rel = 'nofollow', $attributes = '')
	{
		$list['href'] = (is_string($uri) && $uri[0]=='/') ? $uri : KunenaRoute::_($uri);
		if ($title)
		{
			$list['title'] = $title;
		}

		if ($class)
		{
			$list['class'] = $class;
		}

		if ($rel)
		{
			$list['rel'] = $rel;
		}

		if (is_array($attributes))
		{
			$list += $attributes;
		}

		// Parse attributes
		$attr = array();
		foreach ($list as $key=>$value)
		{
			$attr[] = "{$key}=\"{$value}\"";
		}

		if (!empty($attributes) && !is_array($attributes))
		{
			$attr[] = (string) $attributes;
		}

		$attributes = implode (' ', $attr);

		return "<a {$attributes}>{$content}</a>";
	}

	public static function checklist($name, $options, $selected = array())
	{
		if ($selected !== true && !is_array($selected))
		{
			$selected = (array) $selected;
		}

		$html = array();
		$html[] = '<ul class="checklist">';

		foreach ($options as $item)
		{
			// Setup  the variable attributes.
			$eid = "checklist_{$name}_{$item}";
			$checked = $selected === true || in_array($item, $selected) ? ' checked="checked"' : '';

			// Build the HTML for the item.
			$html[] = '	<li>';
			$html[] = '		<input type="checkbox" name="' . $name . '[]" value="' . $item . '" id="' . $eid . '"';
			$html[] = '			' . $checked . ' />';
			$html[] = '		<label for="' . $eid . '">';
			$html[] = '			' . $item;
			$html[] = '		</label>';
			$html[] = '	</li>';
		}

		$html[] = '</ul>';

		if ($selected === true)
		{
			$html[] = '<input type="hidden" name="' . $name . '_all" value="' . implode(',', $options) . '" />';
		}

		return implode("\n", $html);
	}
}
