<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="status-<?php echo ($this->params->get('avatarPosition') == 'left') ? 'left-':'right-'; ?><?php echo $this->profile->isOnline() ? 'yes':''; ?>"></div>
						<ul class="kpost-user-details">
							<li class="kpost-user-username"><?php echo $this->profile->getLink($this->message->name) ?></li>
							<?php if (!empty($this->usertype)) : ?><li class="kpost-user-type">( <?php echo $this->escape($this->usertype) ?> )</li><?php endif ?>
							<?php $avatar = $this->profile->getAvatarImage ('kavatar', 'post'); if ($avatar) : ?>
							<li class="kpost-user-avatar">
								<span class="kavatar"><?php echo $this->profile->getLink($avatar); ?></span>
							</li>
							<?php endif; ?>
							<?php if ($this->profile->exists()): ?>
							<?php /*?>
							<li class="kpost-user-status" style="overflow: auto;">
								<span class="kicon-button kbuttononline-<?php echo $this->profile->isOnline('yes', 'no') ?>">
									<span class="online-<?php echo $this->profile->isOnline('yes', 'no') ?>">
										<span><?php echo $this->profile->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')) ?></span>
									</span>
								</span>
							</li><?php */?>
							<?php if (!empty($this->userranktitle)) : ?><li class="kpost-user-rank"><?php echo $this->escape($this->userranktitle) ?></li><?php endif ?>
							<?php if (!empty($this->userrankimage)) : ?><li class="kpost-user-rank-img"><?php echo $this->userrankimage ?></li><?php endif ?>
							<?php if ($this->userposts) : ?><li class="kpost-user-posts"><?php echo JText::_('COM_KUNENA_POSTS') .' '. intval($this->userposts) ?></li><?php endif ?>
							<?php if ($this->userpoints) : ?><li class="kpost-user-points"><?php echo JText::_('COM_KUNENA_AUP_POINTS') .' '. intval($this->userpoints); ?></li><?php endif ?>
							<?php if ( $this->userkarma ) : ?><li class="kpost-user-karma"><?php echo $this->userkarma ?></li><?php endif ?>
							<?php if ( $this->usertyr ) : ?><li class="kpost-user-karma"><?php echo JText::_('COM_KUNENA_MYPROFILE_THANKYOU_RECEIVED') .' '. intval($this->usertyr ) ?></li><?php endif ?>
							<?php if ( !empty($this->usermedals) ) : ?>
							<li class="kpost-user-medals">
								<?php foreach ( $this->usermedals as $medal ) : ?><?php echo $medal; ?><?php endforeach ?>
							</li>
							<?php endif ?>
							<li class="kpost-user-icons">
								<?php echo $this->profile->profileIcon('gender') ?>
								<?php echo $this->profile->profileIcon('birthdate') ?>
								<?php echo $this->profile->profileIcon('location') ?>
								<?php echo $this->profile->profileIcon('website') ?>
								<?php /*if ($this->me->userid != $this->profile->userid && $this->params->get('uddeimPopup') == '1') : ?>
									<span class="kicon-profile" style="background-image:none;"><a class="tk-mb-profile-pmlink" style="color:#fff;" href="#mb_inline-<?php echo intval($this->message->id);?>" rel="lightbox[pm-<?php echo intval($this->message->id);?> 400 200]"></a></span>
								<?php else:*/?>
								<?php echo $this->profile->profileIcon('private'); ?>
								<?php //endif; ?>
								<?php echo $this->profile->profileIcon('email') ?>
							</li>
							<?php if (!empty($this->personalText)) : ?><li class="kpost-user-perstext"><?php echo $this->personalText ?></li><?php endif ?>
							<?php if (!empty($this->ipLink)) : ?><li class=""><?php echo $this->ipLink ?></li><?php endif ?>
							<?php endif ?>
						</ul>

			<div id="mb_inline-<?php echo intval($this->message->id);?>" style="display: none;color:#999;background:#fff;position:relative;">
				<div class="tk-mb-header-pm">
					<span class="tk-mb-first"><?php echo JText::_('COM_KUNENA_TEMPLATE_CONTACT'); ?> <?php echo $this->escape($this->message->name);?></span>
				</div>
				<div class="" style="border:0px;padding:5px;">
					<form method="post" name='sendeform' action="<?php echo JRoute::_( 'index.php?option=com_uddeim' ); ?>" enctype="multipart/form-data">
						<div class="tk-mb-avatar" style="float:left;">
							<?php echo $this->profile->getLink($avatar); ?>
						</div>
						<div class="" style="float:left;margin-left:10px;">
							<div class="" style="">
								<input type="text" style="border:1px solid #dfdfdf;padding: 4px;background: #FAFAFA;margin:0;" name="to_name" class="inputbox" readonly="readonly" size="35" value="<?php echo $this->escape($this->message->name);?>"/>
							</div>
							<div class="" style="padding-top:5px;">
								<textarea name="pmessage" class="inputbox required" rows="5" cols="37" style="border:1px solid #dfdfdf;"> <?php echo JText::_('COM_KUNENA_TEMPLATE_WRITE_HERE'); ?></textarea>
							</div>
							<div class="" style="border:0px;float:left;margin-top: 10px;">
								<span class="tk-mb-submit">
									<a style="text-decoration:none;" href="javascript:this.onclick(Mediabox.open('#mb_pmresult', '', '400 80'));">
										<input type="submit" class="tk-mb-submit" name="reply" value="Send" />
									</a>
								</span>
								<span class="tk-mb-submit">
									<input type="reset" class="tk-mb-cancel" name="cancel" value="Cancel" onclick="Mediabox.close();" />
								</span>
							</div>
						</div>
						<span class="tk-date1" style="color:#fff;font-size: 12px;float: left;line-height: 5.5em;"><?php $now = &JFactory::getDate(); echo $now->toFormat(); ?></span>
						<input type="hidden"  name="to_id" value="<?php $this->profile->userid ?>" />
						<input type="hidden"  name="task" value="save" />
						<?php echo JHTML::_ ( 'form.token' ); ?>
					</form>
				</div>
			</div>
