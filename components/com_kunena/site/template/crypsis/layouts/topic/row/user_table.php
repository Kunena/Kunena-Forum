<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaLayout $this */
/** @var KunenaForumTopic $this->topic */
/** @var bool $this->spacing */
/** @var bool $this->checkbox */

/** @var KunenaForumTopic $topic */
$topic = $this->topic;

$cols = empty($this->checkbox) ? 5 : 6;
if ($this->spacing) : ?>
<tr>
	<td class="kcontenttablespacer" colspan="<?php echo $cols; ?>">&nbsp;</td>
</tr>
<?php endif; ?>

<tr>
	<td>
		<strong><?php echo $this->formatLargeNumber ( max(0,$topic->getTotal()-1) ); ?></strong>
		<?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?>
	</td>
	<td class="hidden-phone">
		<?php echo $this->getTopicLink ( $topic, 'unread', $topic->getIcon() ) ?>
	</td>
	<td>
		<?php if ($topic->attachments) echo $this->getIcon ( 'ktopicattach', JText::_('COM_KUNENA_ATTACH') ); ?>
		<?php if ($topic->poll_id) echo $this->getIcon ( 'ktopicpoll', JText::_('COM_KUNENA_ADMIN_POLLS') ); ?>
		<div>
			<?php
			echo $this->getTopicLink ( $topic, null, null, KunenaHtmlParser::stripBBCode ( $topic->first_post_message, 500), 'ktopic-title km' );
			if ($topic->getUserTopic()->favorite) {
				echo $this->getIcon ( 'kfavoritestar', JText::_('COM_KUNENA_FAVORITE') );
			}
			if ($topic->unread) {
				echo $this->getTopicLink ( $topic, 'unread', '<sup dir="ltr" class="knewchar">(' . $topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>' );
			}
			if ($topic->locked != 0) {
				echo $this->getIcon ( 'ktopiclocked', JText::_('COM_KUNENA_LOCKED_TOPIC') );
			}
			?>
		</div>
		<div><?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->getCategoryLink ( $topic->getCategory() ) ) ?></div>
		<div title="<?php echo KunenaDate::getInstance($topic->first_post_time)->toKunena('config_post_dateformat_hover'); ?>">
			<?php echo JText::_('COM_KUNENA_TOPIC_STARTED_ON') . ' ' .
				KunenaDate::getInstance($topic->first_post_time)->toKunena('config_post_dateformat');?>
			<?php echo JText::_('COM_KUNENA_BY') . ' ' . $topic->getFirstPostAuthor()->getLink() ?>
		</div>
		<div style="clear:both;">
		<?php //TODO:
		if (0 && $this->pages > 1) : ?>
			<ul class="pagination" style="margin:0;">
				<li><?php echo $this->GetTopicLink ( $topic, 0, 1 ) ?></li>
				<?php if ($this->pages > 4) : $startPage = $this->pages - 3; ?>
				<li class="more">...</li>
				<?php else: $startPage = 1; endif;
				for($hopPage = $startPage; $hopPage < $this->pages; $hopPage ++) : ?>
					<li><?php echo $this->getTopicLink ( $topic, $hopPage, $hopPage+1 ) ?></li>
				<?php endfor; ?>
			</ul>
		<?php endif; ?>
		</div>
	</td>
	<td>
		<?php echo $this->formatLargeNumber ( $topic->hits );?>
		<?php echo JText::_('COM_KUNENA_GEN_HITS');?>
	</td>
	<td>
		<?php if (!empty($topic->avatar)) : ?>
		<?php echo $topic->getLastPostAuthor()->getLink( $topic->avatar ) ?>
		<?php endif; ?>
	</td>
	<td>
		<?php
			echo $this->getTopicLink ( $topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST') );
			echo ' ' . JText::_('COM_KUNENA_BY') . ' ' . $topic->getLastPostAuthor()->getLink();
			?>
		<br />
		<span title="<?php echo KunenaDate::getInstance($topic->last_post_time)
			->toKunena('config_post_dateformat_hover'); ?>">
			<?php echo KunenaDate::getInstance($topic->last_post_time)->toKunena('config_post_dateformat'); ?>
		</span>
	</td>
	<?php if (!empty($this->checkbox)) : ?>
	<td>
		<input class ="kcheck" type="checkbox" name="topics[<?php echo $topic->id?>]" value="1" />
	</td>
	<?php endif; ?>
	<?php
	if (!empty($this->position))
		echo $this->subLayout('Page/Module')
			->set('position', $this->position)
			->set('cols', $cols)
			->setLayout('table_row');
	?>
</tr>

