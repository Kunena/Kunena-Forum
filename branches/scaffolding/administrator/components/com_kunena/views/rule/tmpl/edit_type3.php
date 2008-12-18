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
	</tbody>
</table>
<?php echo JHTML::_('acl.hiddenactions', $this->actions, $this->item->references->getAcos()); ?>
<?php echo JHTML::_('acl.hiddenassetgroups', $this->assetgroups, $this->item->references->getAxoGroups()); ?>
