<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Templates
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>
<div id="j-sidebar-container" class="span2">
	<div id="sidebar">
		<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
	</div>
</div>
<div id="j-main-container" class="span10">
	<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<div class="btn-group pull-right hidden-phone">
			<?php echo  $this->pagination->getListFooter(); ?>
		</div>

		<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%"></th>
				<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NAME'); ?></th>
				<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT'); ?></th>
				<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?></th>
				<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?></th>
				<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DATE'); ?></th>
				<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR_URL'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<div class="pagination">
						<?php echo $this->pagination->getPagesLinks (); ?>
					</div>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ( $this->templates as $id => $row) : ?>
			<tr>
				<td>
					<input type="radio" id="cb<?php echo $this->escape($row->directory);?>" name="cid[]" value="<?php echo $this->escape($row->directory); ?>" onclick="Joomla.isChecked(this.checked);" />
				</td>
				<td>
					<?php $img_path = JUri::root(true).'/components/com_kunena/template/'.$row->directory.'/images/template_thumbnail.png'; ?>
					<span class="editlinktip hasTip" title="<?php echo $this->escape($row->name . '::<img border="1" src="' . $this->escape($img_path) . '" name="imagelib" alt="' . JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW' ) . '" width="200" height="145" />'); ?>">
						<a href="#edit" onclick="return listItemTask('cb<?php echo $this->escape($row->directory); ?>','edit')"><?php echo $this->escape($row->name);?></a>
					</span>
				</td>
				<td>
					<?php if ($row->published == 1) : ?>
						<img src="<?php echo JUri::base(true); ?>/components/com_kunena/images/icons/default.png" alt="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT' ); ?>" />
					<?php else : ?>
						<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo urlencode($row->directory);?>','publish')">
							<img src="<?php echo JUri::base(true); ?>/components/com_kunena/images/icons/default_off.png" alt="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_NO_DEFAULT' ); ?>" />
						</a>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $row->authorEmail ? '<a href="mailto:' . $this->escape($row->authorEmail) . '">' . $this->escape($row->author) . '</a>' : $this->escape($row->author); ?>
				</td>
				<td>
					<?php echo $this->escape($row->version); ?>
				</td>
				<td>
					<?php echo $this->escape($row->creationdate); ?>
				</td>
				<td>
					<a href="<?php echo substr($row->authorUrl, 0, 7) == 'http://' ? $this->escape($row->authorUrl) : 'http://' . $this->escape($row->authorUrl); ?>" target="_blank"><?php echo $this->escape($row->authorUrl); ?></a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		</table>
	</form>
</div>


<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
