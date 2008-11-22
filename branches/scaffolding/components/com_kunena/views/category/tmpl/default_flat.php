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
?>
<form action="index.php" method="post" name="fbBulkActionForm">

<table class="" id="" border="1" cellspacing="0" cellpadding="1" width="100%">
	<thead>
		<tr>
			<th colspan="<?php echo ((/* is a mod or admin */true)?7:6);?>">
				<div class="">
					<strong><?php echo JText::_('KUNENA_KUNENA_THREADS_IN_FORUM'); ?>:</strong> <?php echo $this->category->total_threads; ?>
				</div>
				<!-- FORUM TOOLS -->
				<!-- /FORUM TOOLS -->
			</th>
		</tr>
	</thead>
	<tbody>
		<tr class="">
			<th class="" width="1%">&nbsp;</th>
			<th class="" width="1%">&nbsp;</th>
			<th class="" align="left"><?php echo JText::_('KUNENA_GEN_TOPICS'); ?></th>
			<th class="" width="5%" align="center"><?php echo JText::_('KUNENA_GEN_REPLIES'); ?></th>
			<th class="" width="5%" align="center"><?php echo JText::_('KUNENA_GEN_HITS'); ?></th>
			<th class="" width="20%" align="left"><?php echo JText::_('KUNENA_GEN_LAST_POST'); ?></th>
<?php if (/* is a mod or admin */true) : ?>
			<th class="" width="1%" align="center">[X]</th>
<?php endif; ?>
		</tr>
<?php foreach ($this->threads as $thread) : ?>
		<tr id="thread-<?php echo $thread->id; ?>">
			<td class="icon" align="center">
				<!-- ICON -->
			</td>
			<td class="td-2">
				<a href="#<?php echo $thread->id; ?>"></a>
				<img src="arrow.gif" alt="Icon" />
			</td>
			<td class="td-3">
				<div class="title">
					<a class="title" href="<?php echo JRoute::_('index.php?option=com_kunena&view=thread&t_id='.$thread->thread);?>"><?php echo $thread->subject; ?></a>
				</div>
			</td>
			<td class="td-4 fbm" align="center">
				<?php echo $thread->moved ? JText::_('KUNENA_KUNENA_TOPIC_MOVED') : (int)$thread->numMessages; ?>
			</td>
			<td class="td-5 fbm" align="center">
				<?php echo $thread->moved ? JText::_('KUNENA_KUNENA_TOPIC_MOVED') : (int)$thread->hits; ?>
			</td>
			<td class="td-6">
				<div class="fb-latest-subject-date fbs">
					<?php echo $thread->moved ? JText::_('KUNENA_KUNENA_TOPIC_MOVED_LONG') : date(JText::_('KUNENA_DATETIME'), $thread->lastpost); ?>
<?php
		if (!$thread->moved) {
			echo JText::_('KUNENA_GEN_BY'), $this->lastReplies[$thread->thread]->name;
		}
?>
					<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=thread&c_id='.$thread->catid.'&t_id='.$thread->thread),'#',$this->lastReplies[$thread->thread]->id; ?>"><?php
		if (!$thread->moved) {
			echo JHTML::_('Kunena.icon', $this->icons, 'latestpost', JText::_('KUNENA_SHOW_LAST'));
		}
?></a>
				</div>
			</td>
<?php if (/* is a mod or admin */true) : ?>
			<td class="td-7" align="center">
				<input type="checkbox" name="t_id[<?php echo $thread->id?>]" value="1" />
			</td>
<?php endif; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<input type="hidden" name="return" value="<?php echo JRequest::getURI(); ?>" />
</form>
