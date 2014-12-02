<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Disable caching
$this->cache = false;

// Show one topic row
?>
<?php if ($this->spacing) : ?>
<tr class="hidden-phone">
	<td class="kcontenttablespacer hidden-phone" colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>">&nbsp;</td>
</tr>
<?php endif; ?>

<tr class="<?php echo $this->getTopicClass('k', 'row') ?>">

	<td class="kcol-first kcol-ktopicreplies hidden-phone">
		<strong><?php echo $this->formatLargeNumber ( max(0,$this->topic->getTotal()-1) ); ?></strong> <?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?>
	</td>

	<td class="kcol-mid kcol-ktopicicon hidden-phone">
		<?php echo $this->getTopicLink ( $this->topic, 'unread', $this->topic->getIcon() ) ?>
	</td>

	<td class="kcol-mid kcol-ktopictitle">
		<?php if ($this->topic->attachments) echo $this->getIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') ); ?>
		<?php if ($this->topic->poll_id) echo $this->getIcon ( 'ktopicpoll', JText::_('COM_KUNENA_ADMIN_POLLS') ); ?>

		<div class="ktopic-title-cover">
			<?php
			echo $this->getTopicLink ( $this->topic, null, null, KunenaHtmlParser::stripBBCode ( $this->topic->first_post_message, 500), 'ktopic-title km' ); ?>
			<?php
			if ($this->topic->getUserTopic()->favorite) {
				echo $this->getIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
			}
			if ($this->me->exists() && $this->topic->getUserTopic()->posts) {
				echo $this->getIcon ( 'ktopicmy', JText::_('COM_KUNENA_MYPOSTS') );
			}
			if ($this->topic->unread) {
				echo $this->getTopicLink ( $this->topic, 'unread', '<sup dir="ltr" class="knewchar">(' . $this->topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>' );
			}
			?>
		</div>

		<div class="ktopic-details">
			<span class="ktopic-category"> <?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink ( $this->topic->getCategory() ) ) ?></span>
		</div>
		<div class="ktopic-details-kcategory" style="clear:both;">
			<span class="ktopic-posted-time" title="<?php echo KunenaDate::getInstance($this->topic->first_post_time)->toKunena('config_post_dateformat_hover'); ?>">
				<?php echo JText::_('COM_KUNENA_TOPIC_STARTED_ON') . ' ' . KunenaDate::getInstance($this->topic->first_post_time)->toKunena('config_post_dateformat');?>
			</span>
			<span class="ktopic-by ks"><?php echo JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getFirstPostAuthor()->getLink() ?></span>
            </div>
            <div class="ktopic-details-kcategory" style="clear:both;">
		<?php if ($this->pages > 1) : ?>
		<ul class="kpagination">
			<li class="page"><?php echo JText::_('COM_KUNENA_PAGE') ?></li>
			<li><?php echo $this->GetTopicLink ( $this->topic, 0, 1 ) ?></li>
			<?php if ($this->pages > 4) : $startPage = $this->pages - 3; ?>
			<li class="more">...</li>
			<?php else: $startPage = 1; endif;
			for($hopPage = $startPage; $hopPage < $this->pages; $hopPage ++) : ?>
			<li><?php echo $this->getTopicLink ( $this->topic, $hopPage, $hopPage+1 ) ?></li>
			<?php endfor; ?>
		</ul>
		<?php endif; ?>
		</div>

		<?php if (!empty($this->keywords)) : ?>
		<div class="ktopic-keywords">
			<?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->escape($this->keywords)) ?>
		</div>
		<?php endif; ?>
	</td>

	<td class="kcol-mid kcol-ktopicviews hidden-phone">
		<span class="ktopic-views-number"><?php echo $this->formatLargeNumber ( $this->topic->hits );?></span>
		<span class="ktopic-views"> <?php echo JText::_('COM_KUNENA_GEN_HITS');?> </span>
	</td>

	<td class="kcol-mid kcol-ktopiclastpost">
		<div class="klatest-post-info">
			<?php if (!empty($this->topic->avatar)) : ?>
			<span class="ktopic-latest-post-avatar hidden-phone"> <?php echo $this->topic->getLastPostAuthor()->getLink( $this->topic->avatar ) ?></span>
			<?php endif; ?>

			<span class="ktopic-latest-post">
			<?php
				echo $this->getTopicLink ( $this->topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST') );
				echo ' ' . JText::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink();
			?>
			</span>

			<br />
			<span class="ktopic-date" title="<?php echo KunenaDate::getInstance($this->topic->last_post_time)->toKunena('config_post_dateformat_hover'); ?>">
				<?php echo KunenaDate::getInstance($this->topic->last_post_time)->toKunena('config_post_dateformat'); ?>
			</span>
		</div>
	</td>

<?php if (!empty($this->topicActions)) : ?>
	<td class="kcol-mid ktopicmoderation"><input class ="kcheck" type="checkbox" name="topics[<?php echo $this->topic->id?>]" value="1" /></td>
<?php endif; ?>
</tr>
<!-- Module position -->
<?php if ($this->module) : ?>
<tr>
	<td class="ktopicmodule" colspan="<?php echo empty($this->topicActions) ? 5 : 6 ?>"><?php echo $this->module; ?></td>
</tr>
<?php endif; ?>
