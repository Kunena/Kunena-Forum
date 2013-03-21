<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Users
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div id="kunena" class="admin override">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div id="j-sidebar-container" class="span2">
					<div id="sidebar">
						<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
					</div>
				</div>
				<div id="j-main-container" class="span10">
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="view" value="users" />
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="filter_order" value="<?php echo $this->escape ( $this->state->get('list.ordering') ) ?>" />
						<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
						<input type="hidden" name="limitstart" value="<?php echo intval ( $this->pagination->limitstart ) ?>" />
						<input type="hidden" name="boxchecked" value="0" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<div id="filter-bar" class="btn-toolbar">
							<div class="filter-search btn-group pull-left">
								<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN');?></label>
								<input type="text" name="filter_search" id="filter_search" class="filter" placeholder="<?php echo JText::_('COM_KUNENA_ATTACHMENTS_FIELD_INPUT_SEARCHFILE'); ?>" value="<?php echo $this->escape($this->state->get('list.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
							</div>
							<div class="btn-group pull-left">
								<button class="btn tip" type="submit" ><?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?></button>
								<button class="btn tip" type="button"  onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?></button>
							</div>
							<div class="btn-group pull-right hidden-phone">
								<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
								<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
									<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
									<?php echo JHtml::_('select.options', $this->sortDirectionOrdering, 'value', 'text', $this->escape($this->state->get('list.direction')));?>
								</select>
							</div>
							<div class="clearfix"></div>
						</div>

						<table class="adminlist table table-striped">
							<thead>
								<tr>
									<th align="center" width="1%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" /></th>
									<th align="left"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
									<th><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_EMAIL', 'email', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
									<th width="5%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_SIGNATURE', 'signature', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
									<th width="5%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_ENABLED', 'enabled', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
									<th width="5%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_BANNED', 'banned', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
									<th width="5%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
									<th width="1%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
								</tr>
								<tr>
									<td>
									</td>
									<td class="nowrap">
										<label for="filter_username" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter filter" type="text" name="filter_username" id="filter_username" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterUsername; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap">
										<label for="filter_email" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter filter" type="text" name="filter_email" id="filter_email" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterEmail; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap center">
										<select name="filter_signature" id="filter_signature" class="select-filter filter">
											<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
											<?php echo JHtml::_('select.options', $this->signatureOptions(), 'value', 'text', $this->filterSignature); ?>
										</select>
									</td>
									<td class="nowrap center">
										<select name="filter_block" id="filter_block" class="select-filter filter">
											<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
											<?php echo JHtml::_('select.options', $this->blockOptions(), 'value', 'text', $this->filterBlock, true); ?>
										</select>
									</td>
									<td class="nowrap center">
										<select name="filter_banned" id="filter_banned" class="select-filter filter">
											<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
											<?php echo JHtml::_('select.options', $this->bannedOptions(), 'value', 'text', $this->filterBanned); ?>
										</select>
									</td>
									<td class="nowrap center">
										<select name="filter_moderator" id="filter_moderator" class="select-filter filter">
											<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
											<?php echo JHtml::_('select.options', $this->moderatorOptions(), 'value', 'text', $this->filterModerator); ?>
										</select>
									</td>
									<td class="nowrap center">
									</td>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="11">
										<div class="pagination">
											<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $this->pagination->getLimitBox (); ?></div>
											<?php echo $this->pagination->getPagesLinks (); ?>
											<div class="limit"><?php echo $this->pagination->getResultsCounter (); ?></div>
										</div>
									</td>
								</tr>
							</tfoot>
							<tbody>
								<?php
								if (!empty($this->items)) {
									$k = 1;
									//foreach ($profileList as $pl)
									$i = 0;
									foreach($this->items as $user) {
										$kunena_user = KunenaFactory::getUser($user->id);
										$k = 1 - $k;
										$img_yes = '<span class="state publish"><span class="text">Enabled</span></span>';
										$img_no = '<span class="state unpublish"><span class="text">Disabled</span></span>';
										$userEnabled = $kunena_user->isBlocked() ? $img_no : $img_yes;
										$userBlockTask =  $kunena_user->isBlocked() ? 'unblock' : 'block';
										$titleUserBLock = $kunena_user->isBlocked() ?  JText::_( 'Enabled' ) : JText::_( 'Blocked' );
										$userBanned = $kunena_user->isBanned() ? $img_yes : $img_no;
										$userBannedTask = $kunena_user->isBanned() ? 'ban' : 'ban';
										$titleUserBanned = $kunena_user->isBanned() ? JText::_( 'Banned' ) : JText::_( 'Not banned' );
										?>
										<tr class="row<?php echo $k; ?>">
											<td class="center">
												<?php echo JHtml::_('grid.id', $i, intval($user->id)) ?>
											</td>
											<td>
												<span class="editlinktip hasTip" title="<?php echo $this->escape($user->username. '::'.$kunena_user->getAvatarImage('kavatar', 128, 128)); ?> ">
													<?php echo $kunena_user->getAvatarImage('kavatar', 24, 24); ?>
													<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($user->username); ?></a>
													<small>
														<?php echo JText::sprintf('(Name: %s)', $this->escape($user->name));?>
													</small>
												</span>
											</td>
											<td>
												<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->email); ?></a>
											</td>
											<td>
												<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->signature); ?></a>
											</td>
											<td class="center">
												<a class="jgrid" title="<?php echo $titleUserBlock ?>" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBlockTask; ?>')">
													<?php echo $userEnabled; ?>
												</a>
											</td>
											<td class="center">
												<a class="jgrid" title="<?php echo $titleUserBanned ?>" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBannedTask; ?>')">
													<?php echo $userBanned; ?>
												</a>
											</td>
											<td class="center">
												<?php
												if ($kunena_user->moderator) {
													echo JText::_('COM_KUNENA_YES');
												} else {
													echo JText::_('COM_KUNENA_NO');
												}
												?>
											</td>
											<td width="1%" class="center">
												<?php echo $this->escape($kunena_user->userid); ?>
											</td>
										</tr>
									<?php $i++; }
								} else { ?>
									<tr><td colspan="8"><?php echo JText::_('COM_KUNENA_NOUSERSFOUND') ?></td></tr>
							<?php } ?>
							</tbody>
						</table>
					</form>
				</div>
				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML (); ?>
				</div>
			</div>
		</div>
	</div>
</div>
