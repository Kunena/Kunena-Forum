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

// Include the JXtended HTML helpers.
JHTML::addIncludePath(JPATH_ROOT.'/plugins/system/jxtended/html/html');

// Load the tooltip and form vaildation behaviors.
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');

// Load the default stylesheet.
JHTML::stylesheet('default.css', 'administrator/components/com_kunena/media/css/');

// Build the toolbar.
$this->buildDefaultToolBar();
?>
<script language="javascript" type="text/javascript">
<!--
	function submitbutton(task)
	{
		var form = document.adminForm;
		if (task == 'rule.cancel' || document.formvalidator.isValid(document.adminForm)) {
			submitform(task);
		}
	}
-->
</script>
<form action="<?php echo JRoute::_('index.php?option=com_kunena&view=rules');?>" method="post" name="adminForm">
	<fieldset>
		<?php if ($this->item->id) : ?>
		<legend><?php echo JText::sprintf('Rule #%d', $this->item->id); ?></legend>
		<?php endif; ?>

		<table class="adminform">
			<tbody>
				<tr>
					<td width="33%">
						<label id="jxform_note-lbl" for="jxform_note">Note</label><br />
						<input type="text" name="jxform[note]" id="jxform_note" value="<?php echo $this->item->note; ?>" class="inputbox validate required" size="60" />
					</td>

					<td width="33%">
						<label id="jxform_allow-lbl" for="jxform_allow">Allowed</label><br />

						<input type="radio" name="jxform[allow]" id="jxform_allow0" value="0"<?php echo (!$this->item->allow) ? ' checked="checked"' : ''; ?>  />
						<label for="jxform_allow0">Deny</label>
						<input type="radio" name="jxform[allow]" id="jxform_allow1" value="1"<?php echo ($this->item->allow) ? ' checked="checked"' : ''; ?>  />
						<label for="jxform_allow1">Allow</label>
					</td>
				</tr>
				<tr>
					<td>
						<label id="jxform_return_value-lbl" for="jxform_return_value">Return Value</label><br />

						<input type="text" name="jxform[return_value]" id="jxform_return_value" value="<?php echo $this->item->return_value; ?>" class="inputbox" size="30" />
					</td>
					<td>
						<label id="jxform_enabled-lbl" for="jxform_enabled">Enabled</label><br />

						<input type="radio" name="jxform[enabled]" id="jxform_enabled0" value="0"<?php echo (!$this->item->enabled) ? ' checked="checked"' : ''; ?>  />
						<label for="jxform_enabled0">No</label>
						<input type="radio" name="jxform[enabled]" id="jxform_enabled1" value="1"<?php echo ($this->item->enabled) ? ' checked="checked"' : ''; ?>  />
						<label for="jxform_enabled1">Yes</label>
					</td>
				</tr>
			</tbody>
		</table>

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
					<?php if ($this->item->acl_type == 2) : ?>
					<td valign="top">
						<fieldset>
							<legend class="hasTip" title="Items::These are the items that are associated with the permission">
							<?php echo JText::_('Apply to Items') ?>
							</legend>
							<?php echo JHTML::_('acl.assets', $this->assets, $this->item->references->getAxos()); ?>
						</fieldset>
					</td>
					<?php endif; ?>
					<?php if ($this->item->acl_type == 3) : ?>
					<td valign="top">
						<fieldset>
							<legend class="hasTip" title="Item Groups::These are the item groups that are associated with the permission">
							<?php echo JText::_('Apply to Access Groups') ?>
							</legend>
							<?php echo JHTML::_('acl.assetgroups', $this->assetgroups, $this->item->references->getAxoGroups()); ?>
						</fieldset>
					</td>
					<?php endif; ?>
			</tbody>
		</table>
		<div class="clr"></div>
		<div style="display:none;">
			<?php echo $fields['id']->field; ?>
			<?php echo $fields['acl_type']->field; ?>
		</div>

		<input type="hidden" name="task" value="" />
	</fieldset>
	<?php echo JHTML::_('form.token'); ?>
</form>
