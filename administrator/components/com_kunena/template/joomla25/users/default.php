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

$document = JFactory::getDocument();
$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-profiles"><?php echo JText::_('COM_KUNENA_FUM'); ?></div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="view" value="users" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $this->escape ( $this->state->get('list.ordering') ) ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
		<input type="hidden" name="limitstart" value="<?php echo intval ( $this->pagination->limitstart ) ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<fieldset id="filter-bar">
			<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_KUNENA_FILTER'); ?>:</label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('list.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />

				<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="filter-select fltrt">
				<select name="filter_order_Dir" id="filter_order_Dir" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('All');?></option>
						<?php echo JHtml::_('select.options', $this->signatureOptions(), 'value', 'text', $this->filterSignature); ?>
				</select>

				<select name="filter_block" id="filter_block" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('All');?></option>
						<?php echo JHtml::_('select.options', $this->blockOptions(), 'value', 'text', $this->filterBlock, true); ?>
				</select>

				<select name="filter_banned" id="filter_banned" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('All');?></option>
						<?php echo JHtml::_('select.options', $this->bannedOptions(), 'value', 'text', $this->filterBanned); ?>
				</select>

				<select name="filter_moderator" id="filter_moderator" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('All');?></option>
					<?php echo JHtml::_('select.options', $this->moderatorOptions(), 'value', 'text', $this->filterModerator); ?>
				</select>
			</div>
			</fieldset>
		<div class="clr"> </div>

		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th align="center" width="5">#</th>
					<th align="center" width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" /></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
					<th align="left"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_EMAIL', 'email', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_GEN_SIGNATURE', 'signature', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_ENABLED', 'enabled', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_BANNED', 'banned', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="5%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th width="1%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
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
			<?php
			if (!empty($this->items)) {
					$k = 1;
					//foreach ($profileList as $pl)
					$i = 0;
					foreach($this->items as $user) {
						$kunena_user = KunenaFactory::getUser($user->id);
						$k = 1 - $k;
						$userEnabled = $kunena_user->isBlocked() ? 'publish_x.png' : 'tick.png';
						$altUserEnabled = $kunena_user->isBlocked() ? JText::_( 'Blocked' ) : JText::_( 'Enabled' );
						$userBlockTask =  $kunena_user->isBlocked() ? 'unblock' : 'block';
						$userbanned = $kunena_user->isBanned() ? 'tick.png' : 'publish_x.png';
						$userBannedTask = $kunena_user->isBanned() ? 'ban' : 'ban';
						$altUserBanned = $kunena_user->isBanned() ? JText::_( 'Banned' ) : JText::_( 'Not banned' );
					?>
			<tr class="row<?php echo $k; ?>">
			<td class="right"><?php echo $i + $this->pagination->limitstart + 1; ?></td>
				<td align="center">
					<?php echo JHtml::_('grid.id', $i, intval($user->id)) ?>
				</td>
				<td align="center" width="1%"><?php echo $kunena_user->getAvatarImage('kavatar', 36, 36); ?></td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->username); ?></a>
				</td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->email); ?></a></td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->signature); ?></a>
				</td>
				<td align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBlockTask; ?>')">
						<img src="<?php echo JUri::base(true) ?>/components/com_kunena/images/<?php echo $userEnabled;?>" width="16" height="16" border="0" alt="<?php echo $altUserEnabled; ?>" />
					</a></td>
				<td align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $userBannedTask; ?>')">
						<img src="<?php echo JUri::base(true) ?>/components/com_kunena/images/<?php echo $userbanned;?>" width="16" height="16" border="0" alt="<?php echo $altUserBanned; ?>" />
					</a>
				</td>
<?php /*
				<td width="100"><?php echo $this->escape($kunena_user->email);
						?>&nbsp;</td>
*/ ?>
				<td align="center">
					<?php
					if ($kunena_user->moderator) {
						echo JText::_('COM_KUNENA_YES');
					} else {
						echo JText::_('COM_KUNENA_NO');
					}
					?>
				</td>
				<td align="center" width="1%"><?php echo $this->escape($kunena_user->userid); ?></td>
			</tr>
		<?php $i++; }
		} else { ?>
			<tr><td colspan="11"><?php echo JText::_('COM_KUNENA_NOUSERSFOUND') ?></td></tr>
		<?php } ?>
		</table>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
