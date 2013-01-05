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

            <div class="well well-small" style="min-height:120px;">
                       <div class="nav-header"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?></div>
                         <div class="row-striped">
                         <br />
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="templates" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_( 'form.token' ); ?>
<div class="btn-group pull-right hidden-phone">
				<?php echo  $this->navigation->getLimitBox (); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo  $this->navigation->getLimitBox (); ?>
				</select>
			</div>
		<div class="clr">&nbsp;</div>
			<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5" class="title"> # </th>
					<th class="title" colspan="2"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NAME' ); ?></th>
					<th width="5%"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT' ); ?></th>
					<th width="20%"  class="title"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR' ); ?></th>
					<th width="5%" align="center"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_VERSION' ); ?></th>
					<th width="7%" class="title"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DATE' ); ?></th>
					<th width="20%"  class="title"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR_URL' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="14">
						<div class="pagination">
							<?php echo $this->navigation->getPagesLinks (); ?>
						</div>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
				$k = 0;
				$i = 0;
				foreach ( $this->templates as $id => $row) {
			?>
				<tr <?php echo 'class = "row' . $k . '"'; ?>>
					<td align="center"><?php
						echo ($i + $this->navigation->limitstart + 1);
						?></td>
					<td width="5">
						<input type="radio" id="cb<?php echo $this->escape($row->directory);?>" name="cid[]" value="<?php echo $this->escape($row->directory); ?>" onclick="isChecked(this.checked);" />
					</td>
					<td><?php $img_path = JUri::root(true).'/components/com_kunena/template/'.$row->directory.'/images/template_thumbnail.png'; ?>
						<span class="editlinktip hasTip" title="<?php
							echo $this->escape($row->name . '::<img border="1" src="' . $this->escape($img_path) . '" name="imagelib" alt="' . JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW' ) . '" width="200" height="145" />'); ?> ">
							<a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $this->escape($row->directory);
						?>','edit')"><?php echo $this->escape($row->name);?></a>
						</span>
					</td>
					<td align="center">
						<?php if ($row->published == 1) { ?>
							<img src="<?php echo JUri::base(true); ?>/components/com_kunena/images/icons/default.png" alt="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT' ); ?>" />
						<?php } else { ?>
							<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo urlencode($row->directory);?>','publish')">
								<img src="<?php echo JUri::base(true); ?>/components/com_kunena/images/icons/default_off.png" alt="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_NO_DEFAULT' ); ?>" />
							</a>
						<?php } ?>
					</td>
					<td align="center">
						<span class="editlinktip" title="">
							<?php echo $row->authorEmail ? '<a href="mailto:' . $this->escape($row->authorEmail) . '">' . $this->escape($row->author) . '</a>' : $this->escape($row->author); ?>
						</span>
					</td>
					<td align="center">
						<?php echo $this->escape($row->version); ?>
					</td>
					<td align="center">
						<?php echo $this->escape($row->creationdate); ?>
					</td>
					<td align="center">
						<span class="editlinktip" title="">
							<a href="<?php echo substr($row->authorUrl, 0, 7) == 'http://' ? $this->escape($row->authorUrl) : 'http://' . $this->escape($row->authorUrl); ?>" target="_blank"><?php echo $this->escape($row->authorUrl); ?></a>
						</span>
					</td>
				</tr>
				<?php $k = 1 - $k;
					$i++;
				} ?>
			</tbody>
			</table>
		</form>
	</div>
    </div>
    </div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
