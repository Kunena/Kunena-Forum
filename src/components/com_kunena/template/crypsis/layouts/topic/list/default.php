<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$cols = !empty($this->actions) ? 6 : 5;
$colspan = !empty($this->actions) ? 4 : 3;
$view = JFactory::getApplication()->input->getWord('view');
$this->ktemplate = KunenaFactory::getTemplate();
$social = $this->ktemplate->params->get('socialshare');

$this->addStyleSheet('assets/css/rating.css');
?>
<div><?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_list_top'); ?></div>
<div class="row-fluid">
	<div class="span12">
	<?php if ($social == 1) : ?>
		<div><?php echo $this->subLayout('Widget/Social'); ?></div>
	<?php endif; ?>
	<?php if ($social == 2) : ?>
		<div><?php echo $this->subLayout('Widget/Socialcustomtag'); ?></div>
	<?php endif; ?>
		<div class="pull-left">
			<h1>
				<?php echo $this->escape($this->headerText); ?>
				<small class="hidden-phone">
					(<?php echo KunenaForumCategory::getInstance()->totalCount($this->pagination->total); ?>)
				</small>

				<?php // ToDo:: <span class="badge badge-success"> <?php echo $this->topics->count->unread; ?/></span> ?>
			</h1>
		</div>

		<?php if ($view != 'user') : ?>
		<div class="filter-time pull-right">
			<h2 class="filter-sel">
				<form action="<?php echo $this->escape(JUri::getInstance()->toString()); ?>" id="timeselect" name="timeselect"
					method="post" target="_self" class="form-inline hidden-phone">
					<div>
						<?php $this->displayTimeFilter('sel'); ?>
					</div>
				</form>
			</h2>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php
if ($this->config->enableforumjump && !$this->embedded  && $this->topics)
{
	echo $this->subLayout('Widget/Forumjump')->set('categorylist', $this->categorylist);
} ?>
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
	<table class="table<?php echo KunenaTemplate::getInstance()->borderless();?>">
		<thead>
		<tr>
			<td class="span1 center hidden-phone">
				<a id="forumtop"> </a>
				<a href="#forumbottom" rel="nofollow">
					<?php echo KunenaIcons::arrowdown(); ?>
				</a>
			</td>
			<td class="span<?php echo $cols ?>" id="recent-list">
				<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>
			</td>
			<td class="span2 hidden-phone">
				<?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?> / <?php echo JText::_('COM_KUNENA_GEN_HITS');?>
			</td>
			<td class="span3 hidden-phone">
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
					<?php echo KunenaIcons::arrowup(); ?>
				</a>
			</td>
			<?php if (empty($this->actions)) : ?>
			<td colspan="<?php echo $colspan; ?>" class="hidden-phone">
			<?php else : ?>
			<td colspan="<?php echo $colspan; ?>">
			<?php endif; ?>
				<?php if (!empty($this->actions) || !empty($this->moreUri)) : ?>
				<div class="input-append">
					<?php if (!empty($this->topics) && !empty($this->moreUri)) { echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, 'btn btn-primary', 'follow'); } ?>
					<?php if (!empty($this->actions)) : ?>
						<?php echo JHtml::_('select.genericlist', $this->actions, 'task', 'class="inputbox kchecktask" ', 'value', 'text', 0, 'kchecktask'); ?>
						<?php if (isset($this->actions['move'])) :
							$options = array (JHtml::_('select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION')));
							echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="inputbox fbs" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
						endif;?>
						<input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php if (empty($this->topics) && empty($this->subcategories)) : ?>
			<tr>
				<td colspan="4" class="center"><?php echo JText::_('COM_KUNENA_VIEW_NO_TOPICS') ?></td>
			</tr>
		<?php else : ?>
			<?php $counter = 2; ?>

			<?php foreach ($this->topics as $i => $topic)
			{
				echo $this->subLayout('Topic/Row')
					->set('topic', $topic)
					->set('position', 'kunena_topic_' . $i)
					->set('checkbox', !empty($this->actions));

				if ($this->ktemplate->params->get('displayModule'))
				{
					echo $this->subLayout('Widget/Module')
						->set('position', 'kunena_topic_' . $counter++)
						->set('cols', $cols)
						->setLayout('table_row');
				}
			} ?>
		<?php endif; ?>
		</tbody>
	</table>
</form>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
	->set('pagination', $this->pagination->setDisplayedPages(4))
	->set('display', true); ?>
</div>

<div class="clearfix"></div>

