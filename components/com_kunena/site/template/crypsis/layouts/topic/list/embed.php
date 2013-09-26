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

$colspan = empty($this->topicActions) ? 5 : 6;
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="ktopicsform" id="ktopicsform">
	<input type="hidden" name="view" value="topics" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<h3><?php echo $this->escape($this->headerText); ?></h3>

	<div class="clearfix"></div>
	<table class="table table-striped table-bordered table-condensed">
		<?php if (empty ( $this->topics ) && empty ( $this->subcategories )) : ?>
		<tr>
			<td colspan="<?php echo $colspan; ?>">
				<?php echo JText::_('COM_KUNENA_VIEW_NO_TOPICS') ?>
			</td>
		</tr>
		<?php else : ?>
		<thead>
			<tr>
				<td colspan="<?php echo $colspan-1; ?>">
					<div class="pagination pull-right"><?php echo $this->getPagination(5); ?></div>
					<div class="clearfix"></div>
				</td>
				<td>
					<?php if (!empty($this->topicActions)) : ?>
					<input class="kcheckall" type="checkbox" name="toggle" value="" />
					<?php endif; ?>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php $this->displayRows(); ?>
		</tbody>
		<?php if (!empty($this->topicActions) || !empty($this->embedded)) : ?>
		<tfoot>
			<tr>
				<td colspan="<?php echo $colspan; ?>">
					<?php if (!empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, null, 'follow'); ?>
					<?php if (!empty($this->topicActions)) : ?>
						<?php echo JHtml::_('select.genericlist', $this->topicActions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
						<?php if ($this->actionMove) :
							$options = array (JHtml::_ ( 'select.option', '0', JText::_('COM_KUNENA_BULK_CHOOSE_DESTINATION') ));
							echo JHtml::_('kunenaforum.categorylist', 'target', 0, $options, array(), 'class="inputbox fbs" size="1" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
						endif;?>
						<input type="submit" name="kcheckgo" class="btn" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
					<?php endif; ?>
				</td>
			</tr>
		</tfoot>
		<?php endif; ?>
		<?php endif; ?>
	</table>
</form>
