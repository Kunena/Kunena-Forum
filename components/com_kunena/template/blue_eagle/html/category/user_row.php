<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
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
		<?php echo $this->getCategoryLink($this->category, null, null, 'ktopic-title km') ?>
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
	$last = $this->category->getLastTopic();
	if ($last->exists()) { ?>
	<td class="kcol-mid kcol-kcatlastpost">
	<?php if ($this->config->avataroncat > 0) : ?>
	<!-- Avatar -->
	<?php
		$profile = KunenaFactory::getUser((int)$last->last_post_userid);
		$useravatar = $profile->getAvatarImage('klist-avatar', 'list');
		if ($useravatar) : ?>
			<span class="klatest-avatar"> <?php echo $last->getLastPostAuthor()->getLink( $useravatar ); ?></span>
		<?php endif; ?>
	<!-- /Avatar -->
	<?php endif; ?>
	<div class="klatest-subject ks">
		<?php echo JText::_('COM_KUNENA_GEN_LAST_POST') . ': '. $this->getTopicLink($last, 'last', KunenaHtmlParser::parseText($last->subject, 30)) ?>
	</div>

	<div class="klatest-subject-by ks">
	<?php
			echo JText::_('COM_KUNENA_BY') . ' ';
			echo $last->getLastPostAuthor()->getLink();
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