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
		<?php echo $this->getTopicLink ( $this->topic, 'unread', $this->topic->getIcon() ) ?>
	</td>

	<td class="kcol-mid ktopictittle">
	<?php
		/*if ($this->message->attachments) {
			echo $this->getIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') );
		}*/
	?>
		<div class="ktopic-title-cover">
			<?php echo $this->getTopicLink ( $this->topic, $this->message->id, KunenaHtmlParser::parseText ($this->message->subject, 30), KunenaHtmlParser::stripBBCode ($this->message->message), 'ktopic-title km' ) ?>
		</div>
		<div style="display:none"><?php echo KunenaHtmlParser::parseBBCode ($this->message->message);?></div>
	</td>

	<td class="kcol-mid ktopictittle">
		<div class="ktopic-title-cover">
			<?php
			echo $this->getTopicLink ( $this->topic, null, null, KunenaHtmlParser::stripBBCode ( $this->topic->first_post_message, 500), 'ktopic-title km' );
			if ($this->topic->getUserTopic()->favorite) {
				echo $this->getIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
			}
			if ($this->topic->unread) {
				echo $this->getTopicLink ( $this->topic, 'unread', '<sup dir="ltr" class="knewchar">(' . $this->topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>' );
			}
			if ($this->topic->locked != 0) {
				echo $this->getIcon ( 'ktopiclocked', JText::_('COM_KUNENA_GEN_LOCKED_TOPIC') );
			}
			?>
		</div>
		<div class="ks">
			<!-- Category -->
			<span class="ktopic-category">
				<?php echo JText::_('COM_KUNENA_CATEGORY') . ' ' . $this->getCategoryLink ( $this->topic->getCategory() ) ?>
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
				$useravatar = $profile->getAvatarImage('klist-avatar', 'list');
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
			<span class="ktopic-posted-time" title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover'); ?>">
				<?php echo JText::_('COM_KUNENA_POSTED_AT') . ' ' . KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat'); ?>&nbsp;
			</span>

			<?php if ($this->message->userid) : ?>
			<br />
			<span class="ktopic-by"><?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($this->message->userid), $this->escape($this->message->name) ); ?></span>
			<?php endif; ?>
			<!-- /By -->
		</div>
	</td>

<?php if (!empty($this->postActions)) : ?>
	<td class="kcol-mid ktopicmoderation"><input class ="kcheck" type="checkbox" name="posts[<?php echo $this->message->id?>]" value="1" /></td>
<?php endif; ?>
</tr>
<?php if ($this->module) : ?>
<tr>
	<td class="ktopicmodule" colspan="<?php echo empty($this->postActions) ? 5 : 6 ?>"><?php echo $this->module; ?></td>
</tr>
<?php endif; ?>