<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
?>
	<div id="kunena">
<?php echo $this->loadCommonTemplate('header'); ?>

		<div class="top_info_box">
			<div class="choose_time">
				<form name="choose_timeline" method="post" target="_self" action="<?php JRequest::getURI(); ?>">
				<select class="input_time" onchange="this.form.submit();" name="filter_time">
<?php foreach ($this->filter_time_options as $key=>$time): ?>
					<option value="<?php echo $key; ?>"<?php if ($this->filter_time == $key) echo ' selected="selected"'; ?>><?php echo $time; ?></option>
<?php endforeach; ?>
				</select>
				</form>
			</div>
<?php // echo $this->loadCommonTemplate('forumcat'); ?>
			<div class="counter">
				<span><?php echo $this->pagination->getResultsCounter(); ?></span> <?php // echo JText::_('K_DISCUSSIONS'); ?>
			</div>
			<div class="pagination">
				<?php echo JText::_('K_PAGE'); ?>: <?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		</div>
		<div class="clr"></div>
		
		<div class="corner_tl">
			<div class="corner_tr">
				<div class="corner_br">
					<div class="corner_bl">
						<form action="index.php" method="post" name="bodyform">
							<table class="forum_body">
								<thead>
									<tr>
										<th colspan="4"><h1>Recent Discussions</h1></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th class="lcol col_replies"><?php echo JText::_('K_REPLIES'); ?></th>
										<th class="mcol col_emoticon">&nbsp;</th>
										<th class="mcol col_content"><?php echo JText::_('K_TOPICS'); ?></th>
										<th class="rcol col_last"><?php echo JText::_('K_LAST_POST'); ?></th>
									</tr>

<!--
<pre><?php print_r($this->threads[0]); ?></pre> 
-->
<?php
foreach ($this->threads as $this->current=>$this->thread):
?>
									<tr class="<?php echo ($this->current%2) ? 'row_even' : 'row_odd'; ?>">
										<td class="lcol col_replies"><div class="post_number"><?php echo $this->escape($this->thread->posts); ?></div><span><?php echo JText::_('K_REPLIES'); ?></span></td>
										<td class="mcol col_emoticon"><a href="#" ><img src="components/com_kunena/media/images/emoticons/default.gif" alt="Smiles" /></a></td>
										<td class="mcol col_content">
											<div class="post_info">
												<h2>
											    	<?php echo JHtml::_('klink.thread', 'atag', $this->thread->id, $this->escape($this->thread->topic_subject), $this->escape(JString::substr($this->thread->first_post_message, 0, 300))); ?>
												</h2>
												<div class="topic_views"><?php echo JText::_('K_VIEWS'); ?>: <?php echo $this->escape($this->thread->hits); ?></div>
												<div class="topic_post_time"><?php echo JText::_('K_POSTED_ON'); ?> <?php echo JHTML::_('date', $this->thread->first_post_time); ?></div>
												<div class="topic_author"><?php echo JText::_('K_BY').' '; echo JHtml::_('klink.user', 'atag', $this->thread->first_post_userid, $this->escape($this->thread->first_post_name), $this->escape($this->thread->first_post_name));?></div>
												<div class="topic_category"><?php echo JText::_('K_CATEGORY').' '; echo JHtml::_('klink.categories', 'atag', $this->thread->catid, $this->escape($this->thread->catname), $this->escape($this->thread->catname));?></div>
											</div>
										</td>
										<td class="rcol col_last">
												<div class="topic_latest_post_avatar"><?php echo JHtml::_('klink.user', 'atag', $this->thread->last_post_userid, '<img class="avatar" src="components/com_kunena/media/images/no_photo_sm.jpg" alt="'.$this->escape($this->thread->last_post_name).'" />', $this->escape($this->thread->last_post_name));?></div>
												<p class="topic_latest_post">
													<?php echo JText::_('K_LAST_POST_BY').' '; echo JHtml::_('klink.user', 'atag', $this->thread->last_post_userid, $this->escape($this->thread->last_post_name), $this->escape($this->thread->last_post_name));?>
												</p>
												<p class="topic_time"><?php echo JHTML::_('date', $this->thread->last_post_time); ?></p>
										</td>
									</tr>
<?php 
endforeach;
?>

								</tbody>
							</table>
															<input type="hidden" name="Itemid" value="125"/>
															<input type="hidden" name="option" value="com_kunena"/>
															<input type="hidden" name="func" value="bulkactions" />
															<input type="hidden" name="return" value="/forum" />
														</form>
													
												</div>
											</div>
										</div>
									</div>

		<div class="bottom_info_box">
			<div class="counter">
				<span><?php echo $this->pagination->getResultsCounter(); ?></span> <?php // echo JText::_('K_DISCUSSIONS'); ?> 
			</div>
			<div class="pagination">
				<?php echo JText::_('K_PAGE'); ?>: <?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		</div>

<?php echo $this->loadCommonTemplate('footer'); ?>
	</div>