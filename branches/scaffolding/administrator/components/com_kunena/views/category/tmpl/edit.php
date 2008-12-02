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

// Include the component HTML helpers.
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');

// Load the gallery default stylesheet.
JHTML::stylesheet('default.css', 'administrator/components/com_kunena/media/css/');

// Build the toolbar.
$this->buildDefaultToolBar();

// Get the form fields.
$fields	= $this->form->getFields();
?>

<script type="text/javascript">
function submitbutton(task)
{
	if (task == 'category.cancel' || document.formvalidator.isValid(document.adminForm)) {
		<?php echo $fields['description']->editor->save('jxform[description]'); ?>
		submitform(task);
	}
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_kunena&view=categories');?>" method="post" name="adminForm">
	<fieldset>
		<?php if (is_object($this->item) && $this->item->id > 0) : ?>
		<legend><?php echo JText::sprintf('Record #', $this->item->id); ?></legend>
		<?php endif; ?>

		<table class="adminform">
			<tbody>
				<tr>
					<td>
						<?php echo $fields['title']->label; ?><br />
						<?php echo $fields['title']->field; ?>
					</td>
					<td>
						<?php echo $fields['alias']->label; ?><br />
						<?php echo $fields['alias']->field; ?>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $fields['parent_id']->label; ?><br />
						<?php echo $fields['parent_id']->field; ?>
					</td>
					<td>
						<?php echo $fields['path']->label; ?><br />
						<?php echo $fields['path']->field; ?>
					</td>
					<td>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

		<table width="100%">
			<tbody>
				<tr valign="top">
					<td width="70%">
						<label><?php echo $fields['description']->label; ?></label>
						<?php echo $fields['description']->field; ?>
					</td>
					<td width="30%">
						<fieldset>
							<legend><?php echo JText::_('FB Fieldset Publishing');?></legend>
							<ol>
								<li>
									<?php echo $fields['summary']->label; ?><br />
									<?php echo $fields['summary']->field; ?>
								</li>
								<li>
									<?php echo $fields['published']->label; ?><br />
									<?php echo $fields['published']->field; ?>
								</li>
								<li>
									<?php echo $fields['ordering']->label; ?><br />
									<?php echo $fields['ordering']->field; ?>
								</li>
								<li>
									<?php echo $fields['access']->label; ?><br />
									<?php echo $fields['access']->field; ?>
								</li>
								<li>
									<?php echo $fields['admin_access']->label; ?><br />
									<?php echo $fields['admin_access']->field; ?>
								</li>
								<li>
									<?php echo $fields['locked']->label; ?><br />
									<?php echo $fields['locked']->field; ?>
								</li>
								<li>
									<?php echo $fields['moderated']->label; ?><br />
									<?php echo $fields['moderated']->field; ?>
								</li>
								<li>
									<?php echo $fields['alert_admin']->label; ?><br />
									<?php echo $fields['alert_admin']->field; ?>
								</li>
								<li>
									<?php echo $fields['review']->label; ?><br />
									<?php echo $fields['review']->field; ?>
								</li>
							</ol>
						</fieldset>
						<fieldset>
							<legend><?php echo JText::_('FB Fieldset Formatting');?></legend>
							<ol>
								<li>
									<?php echo $fields['icon']->label; ?><br />
									<?php echo $fields['icon']->field; ?>
								</li>
								<li>
									<?php echo $fields['class_sfx']->label; ?><br />
									<?php echo $fields['class_sfx']->field; ?>
								</li>
							</ol>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>


		<table>
			<tbody>
				<tr valign="top">
					<td>
						<table class="adminform">
							<tr>
								<th>
									<?php echo $fields['hits']->label; ?>
								</th>
								<td>
									<?php echo $fields['hits']->field; ?>
								</td>
							</tr>
							<tr>
								<th>
									<?php echo $fields['last_post_id']->label; ?>
								</th>
								<td>
									<?php echo $fields['last_post_id']->field; ?>
								</td>
							</tr>
							<tr>
								<th>
									<?php echo $fields['last_post_time']->label; ?>
								</th>
								<td>
									<?php echo $fields['last_post_time']->field; ?>
								</td>
							</tr>
							<tr>
								<th>
									<?php echo $fields['total_threads']->label; ?>
								</th>
								<td>
									<?php echo $fields['total_threads']->field; ?>
								</td>
							</tr>
							<tr>
								<th>
									<?php echo $fields['total_posts']->label; ?>
								</th>
								<td>
									<?php echo $fields['total_posts']->field; ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>

	<?php echo $fields['id']->field; ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
<div class="clr"></div>

<script type="text/javascript">
// Attach the onblur event to auto-create the alias
e = document.getElementById('jxform_title');
e.onblur = function(){
	title = document.getElementById('jxform_title');
	alias = document.getElementById('jxform_alias');
	if (alias.value=='') {
		alias.value = title.value.replace(/[\s\-]+/g,'-').replace(/&/g,'and').replace(/[^A-Z0-9\~\.\-\_]/ig,'').toLowerCase();
	}
}
</script>
