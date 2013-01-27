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

		<table class="kadmin-sort">
			<tr>
				<td class="left" width="90%">
					<?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
					<input type="text" name="filter_search" id="search" value="<?php echo $this->escape ($this->state->get('filter.search'));?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th align="center" width="5">#</th>
					<th align="center" width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" /></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
					<th class="title" align="center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th align="left"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th align="left"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_USRL_REALNAME', 'name', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_LOGGEDIN'); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_ENABLED'); ?></th>
					<th align="center"><?php echo JText::_('COM_KUNENA_USRL_BANNED'); ?></th>
<?php /*
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?></th>
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_USERGROUP'); ?></th>
*/ ?>
					<th align="left"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
					<th align="left"><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?></th>
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
						$userLogged = $kunena_user->isOnline() ? '<img src="components/com_kunena/images/tick.png" width="16" height="16" border="0" alt="" />': '';
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
				<td align="center" width="1%"><?php echo $this->escape($kunena_user->userid); ?></td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->username); ?></a>
				</td>
				<td>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $this->escape($kunena_user->name); ?></a></td>
				<td align="center"><?php echo $userLogged; ?></td>
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
				<td width="*"><?php echo $this->escape ( $kunena_user->signature ); ?></td>
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
