<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$colspan = empty($this->actions) ? 5 : 6;
?>

<h3> <?php echo $this->escape($this->headerText); ?> <span class="badge badge-info"><?php echo $this->pagination->total; ?></span>
	<?php //To Do:: <span class="badge badge-success"> <?php echo $this->topics->count->unread; ?/></span> ?>
	<div class="pull-right" style="font-size:60%">
		<form action="<?php echo $this->escape(JUri::getInstance()->toString()); ?>" id="timeselect" name="timeselect"
			  method="post" target="_self" class="hidden-phone">
			<?php $this->displayTimeFilter('sel'); ?>
		</form>
	</div>
</h3>
<div class="clearfix"></div>

<?php if (!empty($this->topics) && empty($this->subcategories)) : ?>
<div class="pagination pull-left"> <?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination->setDisplayedPages(4))->set('display', true); ?> </div>
<?php endif; ?>

<div class="pull-right">
	<?php echo $this->subLayout('Widget/Search')->set('catid', 'all'); ?>
</div>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post" name="ktopicsform" id="ktopicsform">
	<?php echo JHtml::_('form.token'); ?>
	<table class="table table-bordered table-condensed">
		<?php if (empty($this->topics) && empty($this->subcategories)) : ?>
			<tr>
				<td colspan="<?php echo $colspan; ?>"><?php echo JText::_('COM_KUNENA_VIEW_NO_TOPICS'); ?></td>
			</tr>
		<?php else : ?>
			<thead>
			<tr>
				<td class="span1 center hidden-phone">
					<a id="forumtop"> </a>
					<a href="#forumbottom">
						<i class="icon-arrow-down hasTooltip"></i>
					</a>
				</td>
				<td class="span1">
					<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>
				</td>
				<td class="span2 hidden-phone">
					<?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?> / <?php echo JText::_('COM_KUNENA_GEN_HITS');?>
				</td>
				<td class="span1">
					<?php echo JText::_('COM_KUNENA_GEN_LAST_POST'); ?>
				</td>
				<?php if (!empty($this->topicActions)) : ?>
				<td class="span1 center">
					<label>
						<input class="kcheckall" type="checkbox" name="toggle" value="" />
					</label>
				</td>
				<?php endif; ?>
			</tr>
			</thead>
			<?php if (!empty($this->actions) || !empty($this->embedded)) : ?>
				<tfoot>
				<tr>
					<td class="center hidden-phone"><a id="forumbottom"> </a> <a href="#forumtop" rel="nofollow"> <i class="icon-arrow-up hasTooltip"></i> </a>
						<?php // FIXME: $this->displayCategoryActions() ?></td>
					<td colspan="<?php echo $colspan; ?>">
						<div class="input-append">
							<?php if (!empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, 'btn btn-primary', 'follow'); ?>
							<?php if (!empty($this->actions)) : ?>
								<?php echo JHtml::_('select.genericlist', $this->actions, 'task', 'class="inputbox kchecktask" ', 'value', 'text', 0, 'kchecktask'); ?>
								<?php if (isset($this->actions['move'])) :
									$options = array (JHtml::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
									echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="inputbox fbs" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
								endif;?>
								<input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
							<?php endif; ?>
						</div>
					</td>
				</tr>
				</tfoot>
			<?php endif; ?>
			<tbody>
			<?php
			foreach ($this->topics as $i => $topic) {
				?>
				<?php
				echo $this->subLayout('Topic/Row')
					->set('topic', $topic)
					->set('position', 'kunena_topic_' . $i)
					->set('checkbox', !empty($this->actions));
				?>
			<?php
			}
			?>
			</tbody>
		<?php endif; ?>
	</table>
</form>

<?php if (!empty($this->topics) && empty($this->subcategories)) : ?>
<div class="pagination pull-left"><?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination->setDisplayedPages(4)); ?></div>
<?php endif; ?>
<div class="clearfix"></div>
