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

static $k=0;
$tabclass = array ("row1", "row2" );
?>
<tr class="k<?php echo $tabclass [$k^=1] ?>">
	<td class="kcol-mid kcol-ktopictitle">
		<div class="ktopic-title-cover">
		<?php echo CKunenaLink::GetCategoryPageLink('showcat', intval($this->category->id), 1, $this->escape($this->category->name), 'follow', 'ktopic-title km' ) ?>
		</div>
	</td>

	<td class="kcol-mid kcol-ktopicviews">
		<!-- Views -->
		<span class="ktopic-views-number"><?php echo $this->formatLargeNumber ( ( int ) $this->category->numTopics );?></span>
		<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_DISCUSSIONS'); ?> </span>
		<!-- /Views -->
	</td>

	<td class="kcol-mid kmycount">
		<!-- Posts -->
		<span class="ktopic-views-number"><?php echo $this->formatLargeNumber ( ( int ) $this->category->numPosts ); ?></span>
		<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_MY_POSTS'); ?> </span>
		<!-- /Posts -->
	</td>

	<?php
	$last = $this->category->getLastPosted();
	if ($last->last_topic_id) { ?>
	<td class="kcol-mid kcol-kcatlastpost">
	<?php if ($this->config->avataroncat > 0) : ?>
	<!-- Avatar -->
	<?php
		$profile = KunenaFactory::getUser((int)$last->last_post_userid);
		$useravatar = $profile->getAvatarLink('klist-avatar', 'list');
		if ($useravatar) : ?>
			<span class="klatest-avatar"> <?php echo CKunenaLink::GetProfileLink ( intval($last->last_post_userid), $useravatar ); ?></span>
		<?php endif; ?>
	<!-- /Avatar -->
	<?php endif; ?>
	<div class="klatest-subject ks">
		<?php echo JText::_('COM_KUNENA_GEN_LAST_POST') . ': '. CKunenaLink::GetThreadPageLink ( 'view', intval($last->id), intval($last->last_topic_id), intval($last->getLastPostLocation()), intval($this->config->messages_per_page), KunenaHtmlParser::parseText($last->last_topic_subject, 30), intval($last->last_post_id) );?>
	</div>

	<div class="klatest-subject-by ks">
	<?php
			echo JText::_('COM_KUNENA_BY') . ' ';
			echo CKunenaLink::GetProfileLink ( intval($last->last_post_userid), $this->escape($last->last_post_guest_name) );
			echo '<br /><span class="nowrap" title="' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat_hover') . '">' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat') . '</span>';
			?>
	</div>
	</td>

	<?php } else { ?>
	<td class="kcol-mid kcol-knoposts"><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></td>
	<?php } ?>

	<td class="kcol-mid">
		<?php echo CKunenaLink::GetCategoryActionLink ( 'unsubscribe', $this->category->id, JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY'), 'nofollow', '', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY_LONG'), '&userid='.$this->me->userid ); ?>
	</td>

</tr>