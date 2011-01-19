<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined( '_JEXEC' ) or die();

// Show one topic row
?>
<?php if ($this->spacing) : ?>
<tr>
	<td class="kcontenttablespacer" colspan="<?php echo empty($this->actionDropdown) ? 5 : 6 ?>">&nbsp;</td>
</tr>
<?php endif; ?>

<tr class="<?php echo $this->getTopicClass('k', 'row') ?>">

	<td class="kcol-first kcol-ktopicreplies">
		<strong><?php echo CKunenaTools::formatLargeNumber ( max(0,$this->topic->posts-1) ); ?></strong> <?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?>
	</td>

	<td class="kcol-mid kcol-ktopicicon">
		<?php echo CKunenaLink::GetThreadPageLink ( 'view', $this->topic->category_id, $this->topic->id, $this->message_position, intval($this->config->messages_per_page), $this->topicIcon($this->topic), $this->topic->lastread ) ?>
	</td>

	<td class="kcol-mid kcol-ktopictitle">
		<?php if ($this->topic->attachments) echo $this->getIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') ); ?>

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

		<div class="ktopic-details">
			<?php if (!isset($this->category) || $this->category->id != $this->topic->getCategory()->id) : ?>
			<span class="ktopic-category"> <?php echo JText::_('COM_KUNENA_CATEGORY') . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', $this->topic->getCategory()->id, $this->escape( $this->topic->getCategory()->name) ) ?></span>
			<span class="divider fltlft">|</span>
			<?php endif; ?>
			<span class="ktopic-posted-time" title="<?php echo CKunenaTimeformat::showDate($this->topic->first_post_time, 'config_post_dateformat_hover'); ?>">
				<?php echo JText::_('COM_KUNENA_TOPIC_STARTED_ON') . ' ' . CKunenaTimeformat::showDate($this->topic->first_post_time, 'config_post_dateformat'); ?>
			</span>
			<span class="ktopic-by ks">
				<?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( $this->topic->first_post_userid, $this->escape($this->topic->first_post_guest_name) ); ?>
			</span>
		</div>

		<?php if ($this->topic->posts > $this->config->messages_per_page) : ?>
		<ul class="kpagination">
			<li class="page"><?php echo JText::_('COM_KUNENA_PAGE') ?></li>
			<li><?php echo CKunenaLink::GetThreadPageLink ( 'view', $this->topic->category_id, $this->topic->id, 0, intval($this->config->messages_per_page), 1 ) ?></li>
			<?php if ($this->pages > 3) : $startPage = $this->pages - 3; ?>
			<li class="more">...</li>
			<?php else: $startPage = 1; endif;
			for($hopPage = $startPage; $hopPage < $this->pages; $hopPage ++) : ?>
			<li><?php echo CKunenaLink::GetThreadPageLink ( 'view', $this->topic->category_id, $this->topic->id, $hopPage*$this->config->messages_per_page, intval($this->config->messages_per_page), $hopPage+1 ) ?></li>
			<?php endfor; ?>
		</ul>
		<?php endif; ?>

		<?php if (!empty($this->keywords)) : ?>
		<div class="ktopic-keywords">
			<?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->escape($this->keywords)) ?>
		</div>
		<?php endif; ?>
	</td>

	<td class="kcol-mid kcol-ktopicviews">
		<span class="ktopic-views-number"><?php echo CKunenaTools::formatLargeNumber ( $this->topic->hits );?></span>
		<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_GEN_HITS');?> </span>
	</td>

	<td class="kcol-mid kcol-ktopiclastpost">
		<div class="klatest-post-info">
			<?php if ($this->topic->ordering) echo $this->getIcon ( 'ktopicsticky', JText::_('COM_KUNENA_GEN_ISSTICKY') ); ?>
			<?php if (!empty($this->topic->avatar)) : ?>
			<span class="ktopic-latest-post-avatar"> <?php echo CKunenaLink::GetProfileLink ( $this->topic->last_post_userid, $this->topic->avatar ) ?></span>
			<?php endif; ?>

			<span class="ktopic-latest-post">
			<?php
			if ($this->topic->moved_id) :
				echo JText::_('COM_KUNENA_MOVED');
			elseif ($this->topic_ordering == 'ASC') :
				echo CKunenaLink::GetThreadPageLink ( 'view', $this->topic->category_id, $this->topic->id, $this->pages*$this->config->messages_per_page, intval($this->config->messages_per_page), JText::_('COM_KUNENA_GEN_LAST_POST'), $this->topic->last_post_id );
			else :
				echo CKunenaLink::GetThreadPageLink ( 'view', $this->topic->category_id, $this->topic->id, 0, intval($this->config->messages_per_page), JText::_('COM_KUNENA_GEN_LAST_POST'), $this->topic->last_post_id );
			endif;

			echo ' ' . JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( $this->topic->last_post_userid, $this->escape($this->topic->last_post_guest_name), '', 'nofollow' );
			?>
			</span>

			<br />
			<span class="ktopic-date" title="<?php echo CKunenaTimeformat::showDate($this->topic->last_post_time, 'config_post_dateformat_hover'); ?>">
				<?php echo CKunenaTimeformat::showDate($this->topic->last_post_time, 'config_post_dateformat'); ?>
			</span>
		</div>
	</td>

<?php if (!empty($this->actionDropdown)) : ?>
	<td class="kcol-mid ktopicmoderation"><input class ="kDelete_bulkcheckboxes" type="checkbox" name="cb[<?php echo $this->topic->id?>]" value="1" /></td>
<?php endif; ?>
</tr>
<?php if ($this->module) : ?>
<tr>
	<td class="ktopicmodule" colspan="<?php echo empty($this->actionDropdown) ? 5 : 6 ?>"><?php echo $this->module; ?></td>
</tr>
<?php endif; ?>