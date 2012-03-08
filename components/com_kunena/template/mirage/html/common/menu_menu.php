<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
?>

<ul class="menu<?php echo $this->class_sfx;?>"<?php
	$tag = '';
	if ($this->parameters->get('tag_id')!=NULL) {
		$tag = $this->parameters->get('tag_id').'';
		echo ' id="'.$tag.'"';
	}
?>>
<?php
foreach ($this->list as $i => &$item) :
	if (version_compare(JVERSION, '1.6', '<')) {
		$itemparams = new JParameter($item->params);
	}

	$class = 'item-'.$item->id;
	if ($item->id == $this->active_id) {
		$class .= ' current';
	}

	if (in_array($item->id, $this->path)) {
		$class .= ' active';
	}
	elseif ($item->type == 'alias') {
		$aliasToId = !empty($itemparams) ? $itemparams->get('aliasoptions') : $item->params->get('aliasoptions');
		if (count($this->path) > 0 && $aliasToId == $this->path[count($this->path)-1]) {
			$class .= ' active';
		}
		elseif (in_array($aliasToId, $this->path)) {
			$class .= ' alias-parent-active';
		}
	}

	if ($item->deeper) {
		$class .= ' deeper';
	}

	if ($item->parent) {
		$class .= ' parent';
	}

	if (!empty($class)) {
		$class = ' class="'.trim($class) .'"';
	}

	echo '<li'.$class.'>';

	$flink = ' href="'.htmlspecialchars($item->flink).'" ';
	$class = $item->anchor_css ? ' class="'.$item->anchor_css.'" ' : '';
	$title = $item->anchor_title ? ' title="'.$item->anchor_title.'" ' : '';
	if ($item->menu_image) {
		$menu_text = !empty($itemparams) ? $itemparams->get('menu_text', 1) : $item->params->get('menu_text', 1);
		$menu_text ?
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
	} else { $linktype = $item->title;
	}

	switch ($item->browserNav) :
	default:
	case 0:
		$extra = '';
	break;
	case 1:
		// _blank
		$extra = ' target="_blank"';
		break;
	case 2:
		// window.open
		$extra = ' onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes\');return false;"';
		break;
	endswitch;

	// Render the menu item.
	switch ($item->type) :
		case 'separator':
			echo "<span class=\"separator\">{$title}>{$linktype}</span>";
			break;
		case 'url':
		case 'component':
		default:
			echo "<a {$flink}{$class}{$title}{$extra}>{$linktype}</a>";
			break;
	endswitch;

	// The next item is deeper.
	if ($item->deeper) {
		echo '<ul>';
	}
	// The next item is shallower.
	elseif ($item->shallower) {
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
endforeach;
?></ul>
