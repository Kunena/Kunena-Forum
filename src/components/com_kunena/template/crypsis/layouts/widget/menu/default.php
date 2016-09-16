<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

// Basic logic has been taken from Joomla! 2.5 (mod_menu)
// Note. It is important to remove spaces between elements.
?>

<ul class="nav">
	<?php

	$columninx  = 0;
	$lastlevel  = 4;
	$extrastyle = '';
	foreach ($this->list as $i => &$item)
	{
		// The next item is deeper.
		if (($item->level == 2) and ($lastlevel == 1))
		{
			$columninx++;
		}

		$class = 'item-' . $item->id;
		if ($item->id == $this->active_id)
		{
			$class .= ' current';
		}

		if (in_array($item->id, $this->path))
		{
			$class .= ' active';
		}
		elseif ($item->type == 'alias')
		{
			$aliasToId = $item->params->get('aliasoptions');
			if (count($this->path) > 0 && $aliasToId == $this->path[count($this->path) - 1])
			{
				$class .= ' active';
			}
			elseif (in_array($aliasToId, $this->path))
			{
				$class .= ' alias-parent-active';
			}
		}

		if ($item->deeper)
		{
			if ($item->level > 1)
			{
				$class .= ' deeper dropdown dropdown-submenu';
			}
			else
			{
				$class .= ' deeper dropdown';
			}
		}

		if ($item->parent)
		{
			$class .= ' parent';
		}

		if (!empty($class))
		{
			$class = ' class="' . trim($class) . '"';
		}

		if ($item->level == 2)
		{
			echo '<li' . $class . ' >';
		}
		else
		{
			echo '<li' . $class . '>';
		}

		// Render the menu item.
		if ($item->type == 'separator')
		{
			if ($item->level == 2)
			{
				echo '</ul><ul class="unstyled">';
			}
			else
			{
				echo '<li class="divider"></li>';
			}
		}
		elseif ($item->deeper)
		{
			if ($item->level > 1)
			{
				require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
				echo '</a>';
			}
			else
			{
				echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
				require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
				echo ' <b class="caret"></b></a>';
			}
		}
		else
		{
			switch ($item->type)
			{
				case 'separator':
				case 'url':
				case 'component':
					require JModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
					break;

				default:
					require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
					break;
			}
		}
		// The next item is deeper.
		if ($item->deeper)
		{
			echo '<ul class="dropdown-menu" role="menu" style="left:0;top:40px;">';
			if ($item->level == 1)
			{
				echo '<li><a>';
			}
		}
		// The next item is shallower.
		elseif ($item->shallower)
		{
			echo '</li>';
			$nlevel = $item->level;
			for ($x = 0; $x < $item->level_diff; $x++)
			{
				$nlevel--;
				if ($nlevel == 1)
				{
					echo '</ul></div></li>';
				}
				echo '</ul></li>';
				$extrastyle = '';
			}
		}
		// The next item is on the same level.
		else
		{
			echo '</li>';
		}
		$lastlevel = $item->level;
	}
	?>
</ul>
