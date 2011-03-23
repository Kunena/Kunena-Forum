<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="Kunena">
<?php
$this->displayMenu ();
$this->displayLoginBox ();
?>
<script language="javascript" type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	if (typeof form.onsubmit == "function") form.onsubmit();
	if (form.name.value == "") {
		alert("<?php echo JText::_('COM_KUNENA_ERROR1'); ?>");
	} else {
		submitform(pressbutton);
	}
}
</script>
<div class="kblock kmanage">
	<div class="kheader">
		<h2><?php echo $this->header; ?></h2>
	</div>

	<div class="kcontainer">
		<div class="kbody">
		<form action="<? echo KunenaRoute::_('index.php?option=com_kunena&view=category') ?>" method="post" name="adminForm">
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="catid" value="<?php echo intval($this->category->id); ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		<div class="kbuttons">
			<button onclick="javascript: submitbutton('save')"><?php echo JText::_( 'Save' ); ?></button>
			<button onclick="javascript: submitbutton('cancel')"><?php echo JText::_( 'Cancel' ); ?></button>
		</div>
		<?php jimport('joomla.html.pane');
		$myTabs = JPane::getInstance('tabs', array('startOffset'=>0)); ?>
		<dl class="tabs" id="pane">
		<dt title="<?php echo JText::_('COM_KUNENA_CATEGORY_INFO'); ?>"><?php echo JText::_('COM_KUNENA_CATEGORY_INFO'); ?></dt>
		<dd>
		<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_BASICSFORUMINFO'); ?></legend>
				<table class="kadmin-adminform">
					<tr>
						<td valign="top"><?php echo JText::_('COM_KUNENA_PARENT'); ?></td>
						<td><?php echo $this->options ['categories']; ?><br /><br /><?php echo JText::_('COM_KUNENA_PARENTDESC'); ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_KUNENA_NAMEADD'); ?></td>
						<td><input class="inputbox" type="text" name="name" size="80" value="<?php echo $this->escape ( $this->category->name ); ?>" /></td>
					</tr>
					<tr>
						<td valign="top"><?php echo JText::_('COM_KUNENA_DESCRIPTIONADD'); ?></td>
						<td>
							<textarea class="inputbox" cols="50" rows="6" name="description" id="description" style="width: 500px"><?php echo $this->escape ( $this->category->description ); ?></textarea>
						</td>
					</tr>
					<tr>
						<td valign="top"><?php echo JText::_('COM_KUNENA_HEADERADD'); ?></td>
						<td>
							<textarea class="inputbox" cols="50" rows="6" name="headerdesc" id="headerdesc" style="width: 500px"><?php echo $this->escape ( $this->category->headerdesc ); ?></textarea>
						</td>
					</tr>
				</table>
			</fieldset>
			</dd>
			<dt title="<?php echo JText::_('COM_KUNENA_ADVANCEDDESC'); ?>"><?php echo JText::_('COM_KUNENA_ADVANCEDDESC'); ?></dt>
			<dd>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_ADVANCEDDESCINFO'); ?></legend>
					<table class="kadmin-adminform">
						<?php if (!$this->category->id || $this->category->parent_id): ?>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_LOCKED1'); ?></td>
 							<td><?php echo $this->options ['forumLocked']; ?></td>
							<td><?php echo JText::_('COM_KUNENA_LOCKEDDESC'); ?></td>
						</tr>
						<?php endif; ?>
						<?php if ($this->category->accesstype != 'none') : ?>
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_A_ACCESSTYPE'); ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_INTEGRATION_'.strtoupper($this->category->accesstype)); ?></td>
							<td><?php echo JText::_('COM_KUNENA_A_ACCESSTYPE_DESC'); ?></td>
						</tr>
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_A_ACCESS'); ?></td>
							<td valign="top"><?php echo $this->options ['access']; ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_A_ACCESS_DESC'); ?></td>
						</tr>
						<?php endif; ?>
						<?php if ($this->me->isAdmin() && $this->category->accesstype == 'none') : ?>
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_PUBACC'); ?></td>
							<td valign="top"><?php echo $this->options ['pub_access']; ?></td>
							<td><?php echo JText::_('COM_KUNENA_PUBACCDESC'); ?></td>
						</tr>
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CGROUPS'); ?></td>
							<td valign="top"><?php echo $this->options ['pub_recurse']; ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_CGROUPSDESC'); ?></td>
						</tr>
						<tr>
							<td valign="top"><?php echo JText::_('COM_KUNENA_ADMINLEVEL'); ?></td>
							<td valign="top"><?php echo $this->options ['admin_access']; ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_ADMINLEVELDESC'); ?></td>
						</tr>
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CGROUPS1'); ?></td>
							<td valign="top"><?php echo $this->options ['admin_recurse']; ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_CGROUPS1DESC'); ?></td>
						</tr>
						<?php endif; ?>
						<?php if (!$this->category->id || $this->category->parent_id): ?>
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_REV'); ?></td>
							<td valign="top"><?php echo $this->options ['forumReview']; ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_REVDESC'); ?></td>
						</tr>
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW'); ?>:</td>
							<td valign="top"><?php echo $this->options ['allow_anonymous']; ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW_DESC'); ?></td>
						</tr>
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT'); ?>:</td>
							<td valign="top"><?php echo $this->options ['post_anonymous']; ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT_DESC'); ?></td>
						</tr>
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED'); ?>:</td>
							<td valign="top"><?php echo $this->options ['allow_polls']; ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED_DESC'); ?></td>
						</tr>
						<?php endif; ?>
					</table>
				</fieldset>

				<?php if (!$this->category->id || $this->category->parent_id): ?>

				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_ADVANCEDDISPINFO'); ?></legend>
					<table class="kadmin-adminform">
						<tr>
							<td><?php echo JText::_('COM_KUNENA_CLASS_SFX'); ?></td>
							<td><input class="inputbox" type="text" name="class_sfx" size="20" maxlength="20" value="<?php echo $this->escape ( $this->category->class_sfx ); ?>" /></td>
							<td><?php echo JText::_('COM_KUNENA_CLASS_SFXDESC'); ?></td>
						</tr>
					</table>
				</fieldset>
				</dd>
				<dt title="<?php echo JText::_('COM_KUNENA_MODNEWDESC'); ?>"><?php echo JText::_('COM_KUNENA_MODNEWDESC'); ?></dt>
				<dd>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_MODHEADER'); ?></legend>
					<table class="kadmin-adminform">
						<tr>
							<td class="nowrap" valign="top"><?php echo JText::_('COM_KUNENA_MOD'); ?></td>
							<td valign="top"><?php echo $this->options ['forumModerated']; ?></td>
							<td valign="top"><?php echo JText::_('COM_KUNENA_MODDESC'); ?></td>
						</tr>
					</table>

					<?php if ($this->category->moderated) : ?>

					<div class="kadmin-funcsubtitle"><?php echo JText::_('COM_KUNENA_MODSASSIGNED'); ?></div>

					<table class="adminlist">
						<thead>
							<tr>
								<th width="5">#</th>
								<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->moderators ); ?>);" /></th>
								<th align="left"><?php echo JText::_('COM_KUNENA_USRL_NAME'); ?></th>
								<th align="left"><?php echo JText::_('COM_KUNENA_USRL_USERNAME'); ?></th>
								<th align="center"><?php echo JText::_('COM_KUNENA_PUBLISHED'); ?></th>
							</tr>
						</thead>

						<tbody>
						<?php if (count ( $this->moderators ) == 0) : ?>
							<tr>
								<td colspan="5" align="center"><?php echo JText::_('COM_KUNENA_NOMODS') ?></td>
							</tr>
						<?php else :
							$k = 1;
							$i = 0;
							foreach ( $this->moderators as $ml ) : $k = 1 - $k; ?>
							<tr class="row<?php echo $k; ?>">
								<td width="5"><?php echo $i + 1; ?></td>
								<td width="5">
									<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo intval($ml->userid); ?>" onclick="isChecked(this.checked);" />
								</td>
								<td><?php echo $this->escape($ml->name); ?></td>
								<td><?php echo $this->escape($ml->username); ?></td>
								<td align="center"><img src="images/tick.png" alt="" /></td>
							</tr>
							<?php 	$i ++;
							endforeach;
						endif;
						?>
						</tbody>
					</table>
					<?php endif; ?>
				</fieldset>
				<?php endif; ?>
			</dd>
		</dl>
		</form>
		</div>
	</div>
</div>
<?php $this->displayFooter (); ?>
</div>