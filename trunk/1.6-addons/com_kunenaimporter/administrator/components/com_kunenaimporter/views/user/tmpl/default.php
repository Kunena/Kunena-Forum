<?php
/**
 * @version $Id$
 * Kunena Forum Importer Component
 * @package com_kunenaimporter
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2009 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
defined('_JEXEC') or die();

JHTML::_('behavior.tooltip');
?>

<form action="index.php?option=com_kunenaimporter" method="post" name="adminForm">
	<table class="adminlist" cellpadding="1">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th width="1%">
					<?php echo JText::_( 'X' ); ?>
				</th>
				<th width="1%" class="title" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort',   'Name', 'a.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title" >
					<?php echo JHTML::_('grid.sort',   'Username', 'a.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="5%" class="title" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'Enabled', 'a.block', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title">
					<?php echo JHTML::_('grid.sort',   'Group', 'groupname', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title">
					<?php echo JHTML::_('grid.sort',   'E-Mail', 'a.email', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title">
					<?php echo JHTML::_('grid.sort',   'Registered', 'a.registerDate', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title">
					<?php echo JHTML::_('grid.sort',   'Last Visit', 'a.lastvisitDate', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="<?php echo "row"; ?>">
			<?php
				$img = $this->user->block ? 'publish_x.png' : 'tick.png';
				$alt = $this->user->block ? JText::_( 'Enabled' ) : JText::_( 'Blocked' );
				if ($this->user->lastvisitDate == "0000-00-00 00:00:00") {
					$lvisit = JText::_( 'Never' );
				} else {
					$lvisit = JHTML::_('date', $this->user->lastvisitDate, '%Y-%m-%d %H:%M:%S');
				}
				$rdate = JHTML::_('date', $this->user->registerDate, '%Y-%m-%d %H:%M:%S');
			?>
				<td>
					<?php echo '#';?>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>
					<?php echo $this->user->extid; ?>
				</td>
				<td>
					<?php echo $this->user->name; ?>
				</td>
				<td>
					<?php echo $this->user->username; ?>
				</td>
				<td align="center">
					<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" />
				</td>
				<td>
					&nbsp;
				</td>
				<td>
					<?php echo $this->user->email; ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $rdate; ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $lvisit; ?>
				</td>
			</tr>
			<tr><th colspan="11">Map to Joomla user:</th></tr>
		<?php
			$k = 0;
			$i = 0;
			foreach ($this->items as $row)
			{
				$img = $row->block ? 'publish_x.png' : 'tick.png';
				$task = $row->block ? 'unblock' : 'block';
				$alt = $row->block ? JText::_( 'Enabled' ) : JText::_( 'Blocked' );
				$link = 'index.php?option=com_kunenaimporter&amp;view=user&amp;task=edit&amp;cid[]='. $row->id. '';

				if ($row->lastvisitDate == "0000-00-00 00:00:00") {
					$lvisit = JText::_( 'Never' );
				} else {
					$lvisit = JHTML::_('date', $row->lastvisitDate, '%Y-%m-%d %H:%M:%S');
				}
				$rdate = JHTML::_('date', $row->registerDate, '%Y-%m-%d %H:%M:%S');
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1;?>
				</td>
				<td>
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td align="center">
					<?php if ( $row->id == $this->user->id ) : ?>
					<img src="templates/khepri/images/menu/icon-16-default.png" alt="<?php echo JText::_( 'Default' ); ?>" />
					<?php else : ?>
					&nbsp;
					<?php endif; ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>">
						<?php echo $row->name; ?></a>
				</td>
				<td>
					<?php echo $row->username; ?>
				</td>
				<td align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></a>
				</td>
				<td>
					<?php echo JText::_( $row->groupname ); ?>
				</td>
				<td>
					<a href="mailto:<?php echo $row->email; ?>">
						<?php echo $row->email; ?></a>
				</td>
				<td nowrap="nowrap">
					<?php echo $rdate; ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $lvisit; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				$i++;
				}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_kunenaimporter" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="user" />
	<input type="hidden" name="extid" value="<?php echo $this->user->extid ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>