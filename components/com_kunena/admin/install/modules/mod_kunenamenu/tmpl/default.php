<?php
/**
 * Kunena Menu Module
 * @package Kunena.Modules
 * @subpackage Menu
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Basic logic has been taken from Joomla! 2.5 (mod_menu)
// Note. It is important to remove spaces between elements.
?>

<ul class="menu<?php echo $this->class_sfx;?>"<?php echo ($this->parameters->get('tag_id')) ? " id=\"{$this->parameters->get('tag_id')}\"" : '' ?>>
<?php
foreach ($this->list as $i => $item) :
	$class = 'item-'.$item->id;
	$class .= ($item->id == $this->active_id) ? ' current' : '';

	if (in_array($item->id, $this->path)) {
		$class .= ' active';
	} elseif ($item->type == 'alias') {
		$aliasToId = $item->params->get('aliasoptions');
		if (count($this->path) > 0 && $aliasToId == $this->path[count($this->path)-1]) {
			$class .= ' active';
		} elseif (in_array($aliasToId, $this->path)) {
			$class .= ' alias-parent-active';
		}
	}

	$class .= ($item->deeper) ? ' deeper' : '';
	$class .= ($item->parent) ? ' parent' : '';
	$class = !empty($class) ? ' class="'.trim($class) .'"' : '';

	echo '<li'.$class.'>';

	$flink = ' href="'.htmlspecialchars($item->flink).'" ';
	$class = $item->anchor_css ? ' class="'.$item->anchor_css.'" ' : '';
	$title = $item->anchor_title ? ' title="'.$item->anchor_title.'" ' : '';
	if ($item->menu_image) {
		$menu_text = $item->params->get('menu_text', 1);
		$menu_text ?
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
	} else {
		$linktype = $item->title;
	}

	switch ($item->browserNav) {
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
	}

	// Render the menu item.
	if ($item->type == 'separator') {
		echo "<span class=\"separator\"{$title}>{$linktype}</span>";
	} else {
		echo "<a {$flink}{$class}{$title}{$extra}>{$linktype}</a>";
	}

	if ($item->deeper) {
		// The next item is deeper.
		echo '<ul>';
	} elseif ($item->shallower) {
		// The next item is shallower.
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	} else {
		// The next item is on the same level.
		echo '</li>';
	}
endforeach;
?></ul>
