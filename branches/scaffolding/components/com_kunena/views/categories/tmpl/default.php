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
echo $this->loadCommonTemplate('header');

for ($i=0,$n=count($this->categories); $i < $n; $i++) :

	$cat = $this->categories[$i];
?>
<table class="forum-category" width="100%" id="forum-category-<?php echo $cat->id; ?>" border="1" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th colspan="5">
				<a class="title" href="<?php echo JRoute::_('index.php?option=com_kunena&view=category&cat_path='.$cat->path) ;?>"><?php echo $cat->title; ?></a>
				<p class="description">
					<?php echo $cat->summary; ?>
				</p>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr class="">
			<th class="" width="5%">&nbsp;</th>
			<th class="" align="left">
				<?php echo JText::_('KUNENA_GEN_FORUM'); ?></th>
			<th class="" align="center" width="5%">
				<?php echo JText::_('KUNENA_GEN_TOPICS'); ?></th>
			<th class="" align="center" width="5%">
				<?php echo JText::_('KUNENA_GEN_REPLIES'); ?></th>
			<th class="" align="left" width="25%">
				<?php echo JText::_('KUNENA_GEN_LAST_POST'); ?></th>
		</tr>
<?php
	$k = 0;
	while (!empty($this->categories[$i+1]) && ($this->categories[$i+1]->parent_id == $cat->id)) :
		$forum = $this->categories[$i+1];
		$k = 1-$k;
?>

		<tr class="forum row<?php echo $k; ?>" id="forum-<?php echo $forum->id ?>">
			<td class="icon" align="center">
				<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=category&cat_path='.$forum->path) ;?>">
					<!-- ICON --></a>
			</td>
			<td class="metadata" align="left">
				<div class="title">
					<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=category&cat_path='.$forum->path) ;?>"> <?php echo $forum->title; ?> </a>
				</div>
<?php
			if (!empty($forum->summary)) {
?>
				<div class="description">
					<?php echo $forum->summary; ?>
				</div>
<?php
			}
			// loop over subcategories to show them under
			if (!empty($forum->parents)) {
?>
				<div class="children">
					<div class="Kunenacc-childcat-title fbs">
						<b><?php if(count($forum->parents)==1) { echo JText::_('KUNENA_KUNENA_CHILD_BOARD'); } else { echo JText::_('KUNENA_KUNENA_CHILD_BOARDS'); } ?>:</b>
					</div>

					<table cellpadding="0" cellspacing="0" border="0" class="Kunenacc-table">

					</table>
				</div>
<?php
			}
?>
				<div class="moderators">
					<?php echo JText::_('KUNENA_GEN_MODERATORS'); ?>:
				</div>
			</td>
			<td class="topics" align="center">
				<?php echo $forum->total_threads; ?></td>
			<td class="replies" align="center">
				<?php echo $forum->total_posts; ?></td>
			<td class="latest" align="left">
<?php
		if (!empty($forum->total_threads)) :
?>
				<div class="subject">
					<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=thread&cat_path='.$forum->path.'&id='.$forum->id_last_msg).'#'.$forum->id_last_msg;?>"><?php echo $forum->subject; ?></a>
				</div>
				<div class="author">
					<?php echo JText::_('KUNENA_GEN_BY'); ?>
					<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=profile&u_id='.$forum->userid)?>"><?php echo $forum->mname; ?></a>
					<br />
					<?php echo JHTML::date($forum->time_last_msg, '%c'); ?>
					<br />
					<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=thread&cat_path='.$forum->path.'&id='.$forum->id_last_msg).'#'.$forum->id_last_msg;?>">
					<?php echo $this->icons['latestpost'] ? '<img src="'.$this->baseurl.'/components/com_kunena/template/default/images/'.$this->icons['latestpost'].'" border="0" alt="'.JText::_('KUNENA_SHOW_LAST').'" title="'.JText::_('KUNENA_SHOW_LAST').'" />' : '  <img src="'.JB_URLEMOTIONSPATH.'icon_newest_reply.gif" border="0"  alt="'.JText::_('KUNENA_SHOW_LAST').'" />'; ?></a>
				</div>
<?php
		else :
			echo JText::_('KUNENA_NO_POSTS');
		endif;
?>
			</td>
		</tr>

<?php
		$i++;
	endwhile;
?>
	</tbody>
</table>
<?php
endfor;

// render the layout footer
echo $this->loadCommonTemplate('footer');