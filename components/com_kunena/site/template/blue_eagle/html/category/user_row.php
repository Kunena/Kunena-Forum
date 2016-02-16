<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
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
		<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_TOPICS'); ?> </span>
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
			<span class="klatest-avatar"> <?php echo $last->getLastPostAuthor()->getLink( $useravatar, null, 'nofollow', '', null, $this->category->id ); ?></span>
		<?php endif; ?>
	<!-- /Avatar -->
	<?php endif; ?>
	<div class="klatest-subject ks">
		<?php echo JText::_('COM_KUNENA_GEN_LAST_POST') . ': '. $this->getTopicLink($last, 'last', KunenaHtmlParser::parseText($last->subject, 30)) ?>
	</div>

	<div class="klatest-subject-by ks">
	<?php
			echo JText::_('COM_KUNENA_BY') . ' ';
			echo $last->getLastPostAuthor()->getLink(null, null, 'nofollow', '', null, $this->category->id);
			echo '<br /><span class="nowrap" title="' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat_hover') . '">' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat') . '</span>';
			?>
	</div>
	</td>

	<?php } else { ?>
	<td class="kcol-mid kcol-knoposts"><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></td>
	<?php } ?>

	<td class="kcol-mid ktopicmoderation">
		<input class ="kcheck" type="checkbox" name="categories[<?php echo $this->category->id?>]" value="1" />
	</td>

</tr>
