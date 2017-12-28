<?php
/**
 * Kunena Component
 * @package     Kunena.Administrator.Template
 * @subpackage  Templates
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

// @var KunenaAdminViewTemplates $this

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>

			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo KunenaLayout::factory('pagination/limitbox')->set('pagination', $this->pagination); ?>
			</div>

			<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%"></th>
					<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NAME'); ?></th>
					<th class="center"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT'); ?></th>
					<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?></th>
					<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?></th>
					<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DATE'); ?></th>
					<th><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR_URL'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo KunenaLayout::factory('pagination/footer')->set('pagination', $this->pagination); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->templates as $id => $row) : ?>
				<tr>
					<td>
						<input type="radio" id="cb<?php echo $this->escape($row->directory);?>" name="cid[]" value="<?php echo $this->escape($row->directory); ?>" onclick="Joomla.isChecked(this.checked);" />
					</td>
					<td>
						<?php $img_path = JUri::root(true) . '/components/com_kunena/template/' . $row->directory . '/assets/images/template_thumbnail.png'; ?>
						<span class="editlinktip hasTip" title="<?php echo $this->escape($row->name . '::<img border="1" src="' . $this->escape($img_path) . '" name="imagelib" alt="' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW') . '" width="200" height="145" />'); ?>">
							<a href="#edit" onclick="return listItemTask('cb<?php echo $this->escape($row->directory); ?>','edit')"><?php echo $this->escape($row->name);?></a>
						</span>
					</td>
					<td class="center">
						<?php if ($row->published == 1) : ?>
							<i class="icon-star" alt="<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT'); ?>"></i>
						<?php else : ?>
							<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo urlencode($row->directory);?>','publish')">
								<i class="icon-star-empty" alt="<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_DEFAULT'); ?>"></i>
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
						<a href="<?php echo $this->escape($row->authorUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo $this->escape($row->authorUrl); ?></a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			</table>
			<table class="table table-striped" style="padding-top: 200px;">
				<thead>
				<tr>
					<td colspan="7"><strong><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_PREMIUM'); ?></strong></td>
				</tr>
				</thead>
				<tbody>
					<tr>
						<th width="10%">Price</th>
						<th width="10%"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NAME'); ?></th>
						<th width="5%"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?></th>
						<th width="5%"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?></th>
						<th width="5%"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DOWNLOAD'); ?></th>
						<th width="25%"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR_URL'); ?></th>
						<th width="30%"></th>
					</tr>

					<tr>
						<td style="width: 5%;">€10,00
						</td>
						<td style="width: 7%;">
							<?php $img_path = JUri::root(true) . '/media/kunena/images/template_thumbnail.png'; ?>
							<span class="editlinktip hasTip" title="<?php echo $this->escape('Blue Eagle 5' . '::<img border="1" src="' . $this->escape($img_path) . '" name="imagelib" alt="' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW') . '" width="200" height="145" />'); ?>">
								<a href="https://www.kunena.org/download/templates/product/blue-eagle-5" target="_blank" rel="noopener noreferrer">Blue Eagle 5</a>
							</span>
						</td>
						<td style="width: 7%;">
							<a href="mailto:team@kunena.org">Kunena Team</a>
						</td>
						<td style="width: 5%;">
							K5.0.X
						</td>
						<td style="width: 5%;">
							<a href="https://www.kunena.org/download/templates" target="_blank" rel="noopener noreferrer"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DOWNLOAD'); ?></a>
						</td>
						<td style="width: 25%;">
							<a href="https://www.kunena.org" target="_blank" rel="noopener noreferrer">https://www.kunena.org</a>
						</td>
						<td style="width: 30%;">
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">$8.99 - $20.00
						</td>
						<td style="width: 7%;">
							<span class="editlinktip hasTip" title="<?php echo $this->escape('9themestore.com' . '::<img border="1" src="http://www.9themestore.com/images/dms/documents/nts_kmax.jpg" name="imagelib" alt="' . JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW') . '" width="200" height="145" />'); ?>">
								<a href="http://www.9themestore.com/index.php/our-themes/kunena-templates" target="_blank" rel="noopener noreferrer">9themestore.com</a>
							</span>
						</td>
						<td style="width: 7%;">
							<a href="mailto:info@9themestore.com">9themestore.com</a>
						</td>
						<td style="width: 5%;">
							K5.0.X
						</td>
						<td style="width: 5%;">
							<a href="http://www.9themestore.com/index.php/our-themes/kunena-templates" target="_blank" rel="noopener noreferrer"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_DOWNLOAD'); ?></a>
						</td>
						<td style="width: 25%;">
							<a href="http://www.9themestore.com/index.php/our-themes/kunena-templates" target="_blank" rel="noopener noreferrer">http://www.9themestore.com</a>
						</td>
						<td style="width: 30%;">
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
