<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');
?>

		<table width="100%">
			<tbody>
				<tr valign="top">
					<td valign="top" width="25%">
						<fieldset>
							<legend><?php echo JText::_('Apply User Groups');?></legend>
							<?php echo JHTML::_('acl.usergroups', $this->usergroups, $this->item->references->getAroGroups()); ?>
						</fieldset>
					</td>
					<td valign="top" width="25%">
						<fieldset>
							<legend class="hasTip" title="Permissions::Select the permissions that this group will be allowed, or not allowed to do.">
							<?php echo JText::_('Apply Permissions') ?>
							</legend>
							<?php echo JHTML::_('acl.actions', $this->actions, $this->item->references->getAcos()); ?>
						</fieldset>
					</td>
			</tbody>
		</table>
