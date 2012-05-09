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

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.rtl.css' );
JHTML::_('behavior.tooltip');
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-template"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?></div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="templates" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHTML::_( 'form.token' ); ?>

			<table class="adminlist">
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
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY')?><?php echo $this->navigation->getLimitBox (); ?></div>
							<?php echo $this->navigation->getPagesLinks (); ?>
							<div class="limit"><?php echo $this->navigation->getResultsCounter (); ?></div>
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
					<td><?php $img_path = JURI::root(true).'/components/com_kunena/template/'.$row->directory.'/images/template_thumbnail.png'; ?>
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
							<img src="<?php echo JURI::base(true); ?>/components/com_kunena/images/icons/default.png" alt="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT' ); ?>" />
						<?php } else { ?>
							<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo urlencode($row->directory);?>','publish')">
								<img src="<?php echo JURI::base(true); ?>/components/com_kunena/images/icons/default_off.png" alt="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_NO_DEFAULT' ); ?>" />
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
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
