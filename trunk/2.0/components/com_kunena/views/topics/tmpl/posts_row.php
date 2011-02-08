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

// Show one topic row
?>
<tr class="<?php echo $this->getTopicClass('k', 'row') ?>">
	<td class="kcol-first kcol-ktopicicon">
		<?php echo CKunenaLink::GetThreadPageLink ( 'view', $this->topic->category_id, $this->topic->id, $this->message_position, intval($this->config->messages_per_page), $this->topicIcon($this->topic), $this->topic->lastread ) ?>
	</td>

	<td class="kcol-mid ktopictittle">
	<?php
		$curMessageNo = $this->topic->posts - ($this->topic->unread ? $this->topic->unread - 1 : 0);
		/*if ($this->message->attachments) {
			echo $this->getIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') );
		}*/
	?>
		<div class="ktopic-title-cover">
			<?php echo CKunenaLink::GetThreadLink ( 'view', intval($this->topic->category_id), intval($this->message->id), KunenaHtmlParser::parseText ($this->message->subject, 30), KunenaHtmlParser::stripBBCode ($this->message->message), 'follow', 'ktopic-title km' ) ?>
		</div>
		<div style="display:none"><?php echo KunenaHtmlParser::parseBBCode ($this->message->message);?></div>
	</td>

	<td class="kcol-mid ktopictittle">
		<div class="ktopic-title-cover">
			<?php
			echo CKunenaLink::GetThreadLink ( 'view', $this->topic->category_id, $this->topic->id, KunenaHtmlParser::parseText ($this->topic->subject), KunenaHtmlParser::stripBBCode ( $this->topic->first_post_message, 500), 'follow', 'ktopic-title km' );
			if ($this->topic->getUserTopic()->favorite) {
				echo $this->getIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
			}
			if ($this->topic->unread) {
				echo CKunenaLink::GetThreadPageLink ( 'view', $this->topic->category_id, $this->topic->id, $this->message_position, intval($this->config->messages_per_page), '<sup dir="ltr" class="knewchar">(' . $this->topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', $this->topic->lastread );
			}
			if ($this->topic->locked != 0) {
				echo $this->getIcon ( 'ktopiclocked', JText::_('COM_KUNENA_GEN_LOCKED_TOPIC') );
			}
			?>
		</div>
		<div class="ks">
			<!-- Category -->
			<span class="ktopic-category">
				<?php echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', intval($this->topic->category_id), $this->escape( $this->topic->getCategory()->name ) ) ?>
			</span>
			<!-- /Category -->
		</div>
	</td>
	<td class="kcol-mid kcol-ktopiclastpost">
		<div class="klatest-post-info">
			<!--  Sticky   -->
			<?php
			if ($this->topic->ordering != 0) :
				echo $this->getIcon ( 'ktopicsticky', JText::_('COM_KUNENA_GEN_ISSTICKY') );
			endif
			?>
			<!--  /Sticky   -->
			<!-- Avatar -->
			<?php
			if ($this->config->avataroncat > 0) :
				$profile = KunenaFactory::getUser((int)$this->message->userid);
				$useravatar = $profile->getAvatarLink('klist-avatar', 'list');
				if ($useravatar) :
			?>
			<span class="ktopic-latest-post-avatar">
			<?php echo CKunenaLink::GetProfileLink ( intval($this->message->userid), $useravatar ) ?>
			</span>
			<?php
				endif;
			endif;
			?>
			<!-- /Avatar -->
			<!-- By -->
			<span class="ktopic-posted-time" title="<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat_hover'); ?>">
				<?php echo JText::_('COM_KUNENA_POSTED_AT') . ' ' . CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat'); ?>&nbsp;
			</span>

			<?php if ($this->message->userid) : ?>
			<br />
			<span class="ktopic-by"><?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($this->message->userid), $this->escape($this->message->name) ); ?></span>
			<?php endif; ?>
			<!-- /By -->
		</div>
	</td>

<?php if (!empty($this->actionDropdown)) : ?>
	<td class="kcol-mid ktopicmoderation"><input class ="kcheck" type="checkbox" name="posts[<?php echo $this->topic->id?>]" value="1" /></td>
<?php endif; ?>
</tr>
<?php if ($this->module) : ?>
<tr>
	<td class="ktopicmodule" colspan="<?php echo empty($this->actionDropdown) ? 5 : 6 ?>"><?php echo $this->module; ?></td>
</tr>
<?php endif; ?>