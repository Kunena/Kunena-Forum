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

$cols = !empty($this->actions) ? 6 : 5;
$colspan = !empty($this->actions) ? 5 : 4;
?>

<h2>
	<?php echo $this->escape($this->headerText); ?>
	<small class="hidden-phone">
		(<?php echo (JText::plural('COM_KUNENA_X_TOPICS', $this->formatLargeNumber($this->pagination->total))); ?>)
	</small>
	<?php //To Do:: <span class="badge badge-success"> <?php echo $this->topics->count->unread; ?/></span> ?>
	<div class="pull-right">
		<form action="<?php echo $this->escape(JUri::getInstance()->toString()); ?>" id="timeselect" name="timeselect"
			  method="post" target="_self" class="form-inline hidden-phone">
			<div>
				<?php $this->displayTimeFilter('sel'); ?>
			</div>
		</form>
	</div>
</h2>

<div class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
		->set('catid', 'all')
		->setLayout('topic'); ?>
</div>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination->setDisplayedPages(4))
		->set('display', true);	?>
</div>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post" name="ktopicsform" id="ktopicsform">
	<?php echo JHtml::_('form.token'); ?>
	<table class="table table-bordered">
		<thead>
			<tr>
				<td class="span1 center hidden-phone">
					<a id="forumtop"> </a>
					<a href="#forumbottom">
						<i class="icon-arrow-down hasTooltip"></i>
					</a>
				</td>
				<td class="span<?php echo $cols ?>">
					<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>
				</td>
				<td class="span2 hidden-phone">
					<?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?> / <?php echo JText::_('COM_KUNENA_GEN_HITS');?>
				</td>
				<td class="span3">
					<?php echo JText::_('COM_KUNENA_GEN_LAST_POST'); ?>
				</td>
				<?php if (!empty($this->actions)) : ?>
				<td class="span1 center">
					<label>
						<input class="kcheckall" type="checkbox" name="toggle" value="" />
					</label>
				</td>
				<?php endif; ?>
			</tr>
		</thead>
			<tfoot>
				<tr>
					<td class="center hidden-phone">
						<a id="forumbottom"> </a>
						<a href="#forumtop" rel="nofollow">
							<i class="icon-arrow-up hasTooltip"></i>
						</a>
						<?php // FIXME: $this->displayCategoryActions() ?>
					</td>
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
		<tbody>
			<?php if (empty($this->topics) && empty($this->subcategories)) : ?>
				<tr>
					<td colspan="<?php echo $colspan; ?>">
						<div class="well center filter-state">
							<span><?php echo JText::_('COM_KUNENA_FILTERACTIVE'); ?>
								<?php /*<a href="#" onclick="document.getElements('.filter').set('value', '');this.form.submit();return false;"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></a> */?>
								<?php //if($this->filterActive) : ?>
								<?php if(true) : ?>
									<a class="btn" type="button"  onclick="document.getElements('.filter').set('value', 'All');this.form.submit();"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></a>
								<?php else : ?>
									<a class="btn btn-success" type="button"  onclick="Joomla.submitbutton('add');"><?php echo JText::_('COM_KUNENA_NEW_CATEGORY'); ?></a>
								<?php endif; ?>
							</span>
						</div>
					</td>
				</tr>
			<?php else : ?>
				<?php foreach ($this->topics as $i => $topic) { ?>
					<?php echo $this->subLayout('Topic/Row')
						->set('topic', $topic)
						->set('position', 'kunena_topic_' . $i)
						->set('checkbox', !empty($this->actions));
					?>
				<?php } ?>
			<?php endif; ?>
		</tbody>
	</table>
</form>

<?php if (!empty($this->topics) && empty($this->subcategories)) : ?>
	<div class="pagination pull-left">
		<?php echo $this->subLayout('Widget/Pagination/List')
			->set('pagination', $this->pagination->setDisplayedPages(4));
		?>
	</div>
<?php endif; ?>
<div class="clearfix"></div>
