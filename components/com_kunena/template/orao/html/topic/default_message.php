<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
	if ($this->message->id == $this->message->thread) {
		$subject = 'first';
		} else {
		$subject = 'other';
	}
	if ($this->message->hold == 2) {
		$back = '-deleted';
	} elseif ($this->message->hold == 1){
		$back = '-unaproved';
	} else {
		$back = '-';
	}
$template = KunenaFactory::getTemplate();
$this->params = $template->params;
?>
<div class="post clearboth">
	<div class="inner inner-[K=ROW]">
		<span class=""><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt>
						<span class="msgtitle<?php echo $this->msgsuffix ?>" title="">
						[K=DATE:<?php echo $this->message->time ?>]
						</span>
						</dt>
						<dd class="topics" style="float:right;">
							<span class="tk-view-msgid"><a name="<?php echo intval($this->id); ?>"></a><?php echo $this->numLink ?></span>
						</dd>
					</dl>
				</li>
			</ul>
			<?php if ($this->message->id == $this->message->thread) { ?>
			<?php // FIXME : Missing translations ?>
			<ul class="ktopic-taglist">
				<?php if (empty($this->keywords)) : ?>
				<li class="ktopic-taglist-title">Topic Tags:</li>
				<li><a href="#">templates</a></li>
				<li><a href="#">design</a></li>
				<li><a href="#">css</a></li>
				<li><a href="#">colors</a></li>
				<li><a href="#">help</a></li>
				<?php else: ?>
				<li class="ktopic-taglist-title">No Tags</li>
				<?php endif ?>
				<li class="ktopic-taglist-edit"><a href="#">Add/edit tags</a></li>
			</ul>
			<?php }?>
		<div id="profilebox-post" class="postprofile-left">
			[K=MESSAGE_PROFILE]
		</div>
			<?php /*?><div class="tk-bubble-left"></div><?php */?>
			<div class="postbody tk-avleft">

				<div class="postbackground-left">
					<h3 class="<?php echo $subject; ?>"><?php echo $this->escape($this->message->subject) ?></h3>
				</div>

				<div class="tk-msgcontent">
				<div class="msgcontent<?php echo $back; ?>"></div>
					<?php echo $this->parse($this->message->message) ?>

				<?php if ($this->message->modified_by && $this->config->editmarkup) : ?>
				<ul>
					<li class="kpost-body-lastedit">
						<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ": [K=DATE:{$this->message->modified_time}] " . JText::_('COM_KUNENA_BY') . ' ' . CKunenaLink::getProfileLink( $this->message->modified_by ) . '.'; ?>
					</li>
				<?php if ($this->message->modified_reason) : ?>
					<li class="kpost-body-editreason">
						<?php echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape ( $this->message->modified_reason ); ?>
					</li>
				<?php endif ?>
				</ul>
				<?php endif ?>

				</div>
				<?php if (!empty($this->attachments)) : ?>
				<ul>
					<li class="kpost-body-attach">
						<span class="kattach-title"><?php echo JText::_('COM_KUNENA_ATTACHMENTS') ?></span>
					<ul>
					<?php foreach($this->attachments as $attachment) : ?>
						<li class="kattach-details">
							<?php echo $attachment->getThumbnailLink(); ?> <span><?php echo $attachment->getTextLink(); ?></span>
						</li>
					<?php endforeach; ?>
					</ul>
					<div class="clr"></div>
					</li>
				</ul>
					<?php endif; ?>
			</div>
			<div class="clr"></div>
			<div class="msgaction-left">[K=MESSAGE_ACTIONS]</div>
			<?php if ($this->params->get('socialShow') == '1'): ?>
			<div class="tk-social-left"><?php //CKunenaTools::loadTemplate('/profile/socialbuttons.php'); ?></div>
			<?php endif; ?>
		<span class=""><span></span></span>
	</div>
</div>

<?php if ($this->isModulePosition('kunena_msg_' . $this->mmm)) : ?><li class="kmodules"><?php $this->getModulePosition('kunena_msg_' . $this->mmm) ?></li><?php endif ?>

<?php if ( $this->topic->authorise('reply') ) : ?>
<?php echo $this->loadTemplate("quickreply");?>
<?php endif ?>