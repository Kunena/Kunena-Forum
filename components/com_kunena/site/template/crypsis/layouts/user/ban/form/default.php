<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user') ?>" id="kform-ban" name="kformban" method="post">
	<input type="hidden" name="task" value="ban" />
	<input type="hidden" name="userid" value="<?php echo intval($this->profile->userid); ?>" />
	<?php echo JHtml::_('form.token'); ?>

	<table class="table table-bordered table-striped table-hover">
		<tbody>
			<tr>
				<td class="span4">
					<b><?php echo JText::_('COM_KUNENA_BAN_USERNAME'); ?></b>
				</td>
				<td class="span8">
					<?php echo $this->escape($this->profile->username); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_BAN_USERID'); ?></b>
				</td>
				<td>
					<?php echo $this->escape($this->profile->userid); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_BAN_BANLEVEL'); ?></b>
				</td>
				<td>
					<?php
					// make the select list for the view type
					$block[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA'));
					$block[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA'));
					// build the html select list
					echo JHtml::_('select.genericlist', $block, 'block', '', 'value', 'text', $this->escape($this->banInfo->blocked));
					?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></b>
					<br />
					<span><?php echo JText::_('COM_KUNENA_BAN_STARTEXPIRETIME_DESC'); ?></span>
				</td>
				<td>
					<?php echo JHtml::_('calendar', $this->escape($this->banInfo->expiration), 'expiration', 'expiration', '%Y-%m-%d',array('onclick'=>'$(\'expiration\').setStyle(\'display\')')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b>
					<br />
					<span><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON_DESC'); ?></span>
				</td>
				<td>
					<textarea class="required" name="reason_public" id="reason_public" ><?php echo $this->escape($this->banInfo->reason_public) ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b>
					<br />
					<span class="ks"><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON_DESC'); ?></span>
				</td>
				<td>
					<textarea class="required" name="reason_private" id="reason_private"><?php echo $this->escape($this->banInfo->reason_private) ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_BAN_ADDCOMMENT'); ?></b>
					<br />
					<span><?php echo JText::_('COM_KUNENA_BAN_ADDCOMMENT_DESC'); ?></span>
				</td>
				<td>
					<textarea class="required" name="comment" id="comment"></textarea>
				</td>
			</tr>
			<?php if($this->banInfo->id): ?>
				<tr>
					<td>
						<b><?php echo JText::_('COM_KUNENA_MODERATE_REMOVE_BAN'); ?></b>
					</td>
					<td>
						<input type="checkbox" id="ban-delban" name="delban" value="delban" class="" />
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_SIGNATURE'); ?></b>
				</td>
				<td>
					<input type="checkbox" id="ban-delsignature" name="delsignature" value="delsignature" class="" />
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_AVATAR'); ?></b>
				</td>
				<td>
					<input type="checkbox" id="ban-delavatar" name="delavatar" value="delavatar" />
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_BAD_PROFILEINFO'); ?></b>
				</td>
				<td>
					<input type="checkbox" id="ban-delprofileinfo" name="delprofileinfo" value="delprofileinfo" />
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo JText::_('COM_KUNENA_MODERATE_DELETE_ALL_POSTS'); ?></b>
				</td>
				<td>
					<input type="checkbox" id="ban-delposts" name="bandelposts" value="bandelposts" />
				</td>
			</tr>
			<tr>
				<td class="center" colspan="2">
					<input class="btn btn-primary" type="submit" value="<?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ); ?>" name="Submit" />
				</td>
			</tr>
		</tbody>
	</table>
</form>
