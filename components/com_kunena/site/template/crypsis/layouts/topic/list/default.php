<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$colspan = empty($this->actions) ? 5 : 6;
?>

<?php if (!empty($this->topics) && empty($this->subcategories)) : ?>
<div class="pagination pull-right">
	<?php echo $this->subLayout('Pagination/List')->set('pagination', $this->pagination); ?>
</div>
<?php endif; ?>
<?php if (!empty($this->embedded)) : ?>
<form action="<?php echo $this->escape(JUri::getInstance()->toString()); ?>" id="timeselect" name="timeselect"
      method="post" target="_self" class="pull-right">
	<?php $this->displayTimeFilter('sel'); ?>
</form>
<?php endif; ?>

<h3>
	<?php echo $this->escape($this->headerText); ?>
	<span class="badge badge-info"><?php echo $this->pagination->total; ?></span>
</h3>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post" name="ktopicsform" id="ktopicsform">
	<?php echo JHtml::_('form.token'); ?>

	<table class="table table-striped table-bordered table-condensed">

		<?php if (empty($this->topics) && empty($this->subcategories)) : ?>
		<tr>
			<td colspan="<?php echo $colspan; ?>">
				<?php echo JText::_('COM_KUNENA_VIEW_NO_TOPICS'); ?>
			</td>
		</tr>

		<?php else : ?>
		
		<?php if (!empty($this->actions)) : ?>
		<thead>
			<tr>
				<td colspan="6">
					<label>
						<input class="kcheckall pull-right" type="checkbox" name="toggle" value="" />
					</label>
				</td>
			</tr>
		</thead>
		<?php endif; ?>

		<?php if (!empty($this->actions) || !empty($this->embedded)) : ?>
		<tfoot>
			<tr>
				<td colspan="<?php echo $colspan; ?>">
					<?php if (!empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, null, 'follow'); ?>
					<?php if (!empty($this->actions)) : ?>
					<?php echo JHtml::_('select.genericlist', $this->actions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
					<?php if (isset($this->actions['move'])) :
						$options = array (JHtml::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
						echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="inputbox fbs" size="1" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
					endif;?>
					<input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
					<?php endif; ?>
				</td>
			</tr>
		</tfoot>
		<?php endif; ?>

		<tbody>
			<?php
			foreach ($this->topics as $i => $topic) {
				echo $this->subLayout('Topic/Row')
					->set('topic', $topic)
					->set('position', 'kunena_topic_' . $i)
					->set('checkbox', !empty($this->actions));
			}
			?>
		</tbody>
		<?php endif; ?>

	</table>
</form>
