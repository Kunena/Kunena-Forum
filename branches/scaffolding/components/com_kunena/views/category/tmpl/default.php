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
	background: none;
}
</style>


<h1><?php echo $this->category->title; ?></h1>
<p><?php echo $this->category->summary; ?></p>
<div><?php echo $this->category->description; ?></div>

<?php if (count($this->category->children)) : ?>
<!-- Begin Threads Table -->
<h2><?php echo JText::_('Forums'); ?></h2>
<?php echo $this->loadTemplate('children'); ?>
<!-- End Threads Table -->
<?php endif; ?>

<!-- Begin Threads Table -->
<h2><?php echo JText::_('Threads'); ?></h2>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_kunena&task=post.add&cat_id='.$this->category->id); ?>" >
		Post a new Thread</a>
</p>
<table width="100%">
	<thead>
		<tr>
			<th>
				<?php echo JText::_('Topic'); ?>
			</th>
			<th>
				<?php echo JText::_('Author'); ?>
			</th>
			<th>
				<?php echo JText::_('Replies'); ?>
			</th>
			<th>
				<?php echo JText::_('Views'); ?>
			</th>
			<th>
				<?php echo JText::_('Last Post'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
<?php
	if (count($this->threads)) {
		foreach ($this->threads as $t)
		{
			$this->thread = &$t;
			echo $this->loadTemplate('thread');
		}
	}
?>
	</tbody>
	<tfoot>
		<tr colspan="10">
			<td>
			</td>
		</tr>
	</tfoot>
</table>
<!-- End Threads Table -->
