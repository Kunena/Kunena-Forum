<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

// Render the template header.
//echo $this->loadCommonTemplate('header');
?>
<style>
ul.forum_categories {

}
ul.forum_categories li {
	list-style: none;
}
</style>
<ul class="forum_categories">
<?php
$i = 0;
foreach ($this->categories as $cat) {
	$cat->deeper = (!empty($this->categories[$i-1]) && $cat->level > $this->categories[$i-1]->level);
	$cat->shallower = (!empty($this->categories[$i-1]) && $cat->level < $this->categories[$i-1]->level);

		// The category is deeper down the tree, dig down another UL.
		if ($cat->deeper) {
			echo '<ul class="forum_categories"><li id="category_'.$cat->id.'">';
			$this->_current = &$cat;
			echo $this->loadTemplate('category');
		}
		// The category is deeper down the tree, dig down another UL.
		elseif ($cat->shallower)
		{
			echo '</li></ul></li><li id="category_'.$cat->id.'">';
			$this->_current = &$cat;
			echo $this->loadTemplate('category');
			echo '</li>';
		}
		// The category is th same level as the previous one... continue as normal.
		else
		{
			echo ($i) ? '</li>': '';
			echo '<li id="category_'.$cat->id.'">';
			$this->_current = &$cat;
			echo $this->loadTemplate('category');
		}

	$i++;
}
?>
</ul>
<?php
// render the layout footer
//echo $this->loadCommonTemplate('footer');