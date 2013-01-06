<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Trash
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-trash">
		<?php echo $this->state->get( 'list.view_selected') == 'topics' ? JText::_('COM_KUNENA_TRASH_VIEW').' '.JText::_( 'COM_KUNENA_TRASH_TOPICS' ) : JText::_('COM_KUNENA_TRASH_VIEW').' '.JText::_( 'COM_KUNENA_TRASH_MESSAGES') ?>
	</div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=trash') ?>" method="post" id="adminForm" name="adminForm">
			<table class="kadmin-sort">
				<tr>
					<td class="left" width="90%">
						<?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
						<input type="text" name="filter_search" id="search" value="<?php echo $this->escape($this->state->get('list.search'));?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
					</td>
					<td>
						<?php echo $this->view_options_list;?>
					</td>
				</tr>
			</table>
			<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5" align="left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->trash_items ); ?>);" /></th>
					<th width="5" align="left"><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_ID', 'tt.id', $this->state->get('list.direction'), $this->state->get('list.ordering')) :  JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_ID', 'm.id', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_TITLE', 'tt.subject', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_TITLE', 'm.subject', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo JText::_('COM_KUNENA_TRASH_CATEGORY');
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JText::_('COM_KUNENA_TRASH_IP') : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_IP', 'm.ip', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR_USERID', 'tt.first_post_userid', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR_USERID', 'm.userid', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR', 'tt.first_post_guest_name', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR', 'm.name', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_DATE', 'tt.first_post_time', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_DATE', 'm.time', $this->state->get('list.direction'), $this->state->get('list.ordering'));
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
					foreach ( $this->trash_items as $id => $row ) {
						$k = 1 - $k;
						?>
				<tr class="row<?php
						echo $k;
						?>">
					<td align="center"><?php echo JHtml::_('grid.id', $i++, intval($row->id)) ?></td>
					<td >
						<?php
						echo intval($row->id);
						?>
						</td>
					<td ><?php
						echo isset($row->subject) ? $this->escape($row->subject) : $this->escape($row->title);
						?></td>
					<td ><?php if ($this->state->get( 'list.view_selected') == 'topics') {
						$cat = KunenaForumCategoryHelper::get($row->category_id);
						echo $this->escape($cat->name);
					} else {
						 $cat = KunenaForumCategoryHelper::get($row->catid);
						echo $this->escape($cat->name);
					}
						?></td>
					<td ><?php
						if ($this->state->get( 'list.view_selected') == 'topics') {
							$message = KunenaForumMessageHelper::get($row->id);
							echo $this->escape($message->ip);
						} else {
							echo $this->escape($row->ip);
						}
						?></td>
					<td ><?php
					if ($this->state->get( 'list.view_selected') == 'topics') {
						echo intval($row->first_post_userid);
					} else {
						echo intval($row->userid);
					}

						?></td>
					<td ><?php
						echo $this->escape($row->getAuthor()->getName());
						?></td>
					<td ><?php
					if ($this->state->get( 'list.view_selected') == 'topics') {
						echo strftime('%Y-%m-%d %H:%M:%S',$row->last_post_time);
					} else {
						echo strftime('%Y-%m-%d %H:%M:%S',$row->time);
					}
						?></td>
				</tr>
				<?php
					}
					?>
			</table>
			<input type="hidden" name="type" value="<?php echo $this->escape ($this->state->get('list.view_selected')) ?>" />
			<input type="hidden" name="filter_order" value="<?php echo intval ( $this->state->get('list.ordering') ) ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<input type="hidden" name="view" value="trash" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_( 'form.token' ); ?>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
