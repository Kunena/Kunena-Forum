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
defined('_JEXEC') or die();

/** @var KunenaLayout $this */
/** @var KunenaForumTopic $this->topic */
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
	<td class="span1 hidden-phone">
		<div class="pull-left">
			<?php echo $this->getTopicLink ( $topic, 'unread', $topic->getIcon() ) ?>
		</div>
		<div class="clearfix"></div>
	</td>
	<td width="span8">
		<div class="ItemContent Discussion">
			<div class="Title">
				<?php echo $this->getTopicLink ( $topic, null, null, KunenaHtmlParser::stripBBCode ( $topic->first_post_message, 500), 'hasTooltip' ) ;?>
			</div>
			<div class="Meta">
				<span>
					<i class="icon-comments-2"></i>
					<?php echo $this->formatLargeNumber ( max(0,$topic->getTotal()-1) ).' '. JText::_('COM_KUNENA_GEN_REPLIES')?>
				</span>
				<i class="icon-eye"></i>
				<span class="LastCommentBy"><?php echo $this->formatLargeNumber ( $topic->hits ).' '.  JText::_('COM_KUNENA_GEN_HITS');?></span>
				<span>
					<i class="icon-user"></i>
					Started by <?php echo $topic->getFirstPostAuthor()->getLink(); ?>
				</span>
				<i class="icon-calendar"></i>
				<span title="<?php echo KunenaDate::getInstance($topic->first_post_time)->toKunena('config_post_dateformat_hover'); ?>">
					<?php echo KunenaDate::getInstance($topic->first_post_time)->toKunena('config_post_dateformat');?>
				</span>
			</div>
			<div id="one">
				<div id="tow">
					<div class="well">
						<div class="avatar"> <?php echo $topic->getLastPostAuthor()->getLink( $topic->avatar ) ?> by
							<?php
							echo $topic->getLastPostAuthor()->getLink();
							?>
							<div class="info_title pull-right"> <?php echo KunenaDate::getInstance($topic->last_post_time)->toKunena('config_post_dateformat_hover'); ?> </div>
							<div class="post_msg"> <?php echo KunenaHtmlParser::stripBBCode ( $topic->first_post_message, 100); ?></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="jfku_useractions">
						<a href="" rel="nofollow" title="View Topic 'Test'">
							<span class="gototopic">
							<button class="btn btn-small">Go To Topic</button>
            			</span>
						</a>
						<span class="jf_ku_preview_close">
							<a class="btn btn-micro" id="test2" href="javascript:void(0);" onclick="javascript:hideMessage();" title="Hide Message">
								<i class="icon-uparrow"></i>
							</a>
						</span>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</td>
	<td class="hidden-phone" width="2%">
		<div class="kfrontend" id="test2">
			<a class="btn icon-eye" id="test1"  href="javascript:void(0);" onclick="javascript:showMessage();" title="Show Message"></a>
		</div>
		<div class="clearfix"></div>
	</td>
	<td class="span1 hidden-phone">
		<?php if (!empty($topic->avatar)) : ?>
		<span class="ktopic-latest-post-avatar hidden-phone">
			<?php echo $topic->getLastPostAuthor()->getLink( $topic->avatar ) ?>
		</span>
		<?php endif; ?>
	</td>
	<td width="span3">
		<span class="hasTooltip" title="<?php echo $topic->getLastPostAuthor() ;?>">
			<?php echo $topic->getLastPostAuthor()->getLink(); ?>
		</span>
		<br />
		<span class="ktopic-date hasTooltip" title="<?php echo KunenaDate::getInstance($topic->last_post_time)->toKunena('config_post_dateformat_hover'); ?>">
			<?php echo $this->getTopicLink ( $topic, 'last', JText::_('COM_KUNENA_GEN_LAST_POST') ); ?>
		</span>
	</td>
	<?php if (!empty($this->checkbox)) : ?>
	<td  width="1%">
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
