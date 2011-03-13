<?php
/**
 * @version $Id: default.php 4381 2011-02-05 20:55:31Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-trash"><?php echo JText::_('COM_KUNENA_TRASH_VIEW').' '.JText::_( 'COM_KUNENA_TRASH_MESSAGES') ?></div>
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm" class="adminform">
			<table class="kadmin-sort">
				<tr>
					<td class="left" width="90%">
						<?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
						<input type="text" name="search" id="search" value="<?php echo $this->escape($this->state->get('list.search'));?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
					</td>
				</tr>
			</table>
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<thead>
				<tr>
					<th width="5" align="center">#</th>
					<th width="5" align="left"><input type="checkbox" name="toggle" value=""
						onclick="checkAll(<?php
					echo count ( $this->messages );
					?>);" /></th>
					<th width="5" align="left"><?php
					echo  JHTML::_( 'grid.sort', 'COM_KUNENA_TRASH_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', 'COM_KUNENA_TRASH_TITLE', 'subject', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo JText::_('COM_KUNENA_TRASH_CATEGORY');
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', 'COM_KUNENA_TRASH_IP', 'ip', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR_USERID', 'userid', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo JHTML::_( 'grid.sort', 'COM_KUNENA_TRASH_DATE', 'time', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<div class="pagination">
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'). $this->navigation->getLimitBox (); ?></div>
							<?php echo $this->navigation->getPagesLinks (); ?>
							<div class="limit"><?php echo $this->navigation->getResultsCounter (); ?></div>
						</div>
					</td>
				</tr>
			</tfoot>
				<?php
					$k = 0;
					$i = 0;
					foreach ( $this->messages as $id => $row ) {
						$k = 1 - $k;
						?>
				<tr class="row<?php
						echo $k;
						?>">
					<td align="center"><?php
						echo ($id + $this->navigation->limitstart + 1);
						?></td>
					<td align="center"><input type="checkbox"
						id="cb<?php
						echo $id;
						?>" name="cid[]"
						value="<?php
						echo $this->escape($row->id);
						?>"
						onclick="isChecked(this.checked);" /></td>
					<td >
						<?php
						echo $this->escape($row->id);
						?>
						</td>
					<td ><?php
						echo $this->escape($row->subject);
						?></td>
					<td ><?php $cat = KunenaForumCategoryHelper::get($row->catid);
						echo $this->escape($cat->name);
						?></td>
					<td ><?php
						echo $this->escape($row->ip);
						?></td>
					<td ><?php
						echo $this->escape($row->userid);
						?></td>
					<td ><?php
						if(empty($row->username)){
							echo JText::_('COM_KUNENA_VIEW_VISITOR');
						} else {
							echo $this->escape($row->username);
						}
						?></td>
					<td ><?php
						echo strftime('%Y-%m-%d %H:%M:%S',$row->time);
						?></td>
				</tr>
				<?php
					}
					?>
			</table>
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="view" value="trash" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo intval ( $this->state->get('list.ordering') ) ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="messages" value="1" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>