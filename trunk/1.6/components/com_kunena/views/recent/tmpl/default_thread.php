<?php
/**
 * @version		$Id:$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
jimport('joomla.utilities.string');
?>
									<tr class="<?php echo ($this->current%2) ? 'row_even' : 'row_odd'; ?>">
										<td class="col1"><div class="post_number"><?php echo $this->escape($this->thread->posts); ?></div><span><?php echo JText::_('K_REPLIES'); ?></span></td>
										<td class="col2"><a href="#" ><img src="components/com_kunena/media/images/emoticons/default.gif" alt="Smiles" /></a></td>
										<td class="col3">
											<h4>
											    <?php echo JHtml::_('klink.thread', 'atag', $this->thread->id, $this->escape($this->thread->subject), $this->escape(JString::substr($this->thread->first_post_message, 0, 300))); ?>
											</h4>
											<div class="post_info">
												<div class="topic_post_time"><?php echo JText::_('K_POSTED_ON'); ?> <?php echo JHTML::_('date', $this->thread->first_post_time); ?></div>
												<div class="topic_author"><?php echo JText::_('K_BY').' '; echo JHtml::_('klink.user', 'atag', $this->thread->first_post_userid, $this->escape($this->thread->first_post_name), $this->escape($this->thread->first_post_name));?></div>
												<div class="topic_category"><?php echo JText::_('K_CATEGORY').' '; echo JHtml::_('klink.category', 'atag', $this->thread->catid, $this->escape($this->thread->catname), $this->escape($this->thread->catname));?></div>
												<div class="topic_views">(<?php echo JText::_('K_VIEWS'); ?>: <?php echo $this->escape($this->thread->hits); ?>)</div>
											</div>
										</td>
										<td class="col4">
												<div class="topic_latest_post_avatar"><?php echo JHtml::_('klink.user', 'atag', $this->thread->last_post_userid, '<img class="avatar" src="components/com_kunena/media/images/no_photo_sm.jpg" alt="'.$this->escape($this->thread->last_post_name).'" />', $this->escape($this->thread->last_post_name));?></div>
												<p class="topic_latest_post">
													<?php echo JText::_('K_LAST_POST_BY').' '; echo JHtml::_('klink.user', 'atag', $this->thread->last_post_userid, $this->escape($this->thread->last_post_name), $this->escape($this->thread->last_post_name));?>
												</p>
												<p class="topic_time"><?php echo JHTML::_('date', $this->thread->last_post_time); ?></p>
										</td>
									</tr>