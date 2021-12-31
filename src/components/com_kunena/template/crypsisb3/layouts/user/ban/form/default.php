<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user'); ?>"
      id="kform-ban" name="kformban" method="post">
	<input type="hidden" name="task" value="ban"/>
	<input type="hidden" name="userid" value="<?php echo (int) $this->profile->userid; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>

	<table class="table table-bordered table-striped table-hover">
		<tbody>
		<tr>
			<td class="col-md-4">
				<label><?php echo Text::_('COM_KUNENA_BAN_USERNAME'); ?></label>
			</td>
			<td class="col-md-8">
				<?php echo $this->escape($this->profile->username); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label><?php echo Text::_('COM_KUNENA_BAN_USERID'); ?></label>
			</td>
			<td>
				<?php echo $this->escape($this->profile->userid); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ban-level"><?php echo Text::_('COM_KUNENA_BAN_BANLEVEL'); ?></label>
			</td>
			<td>
				<?php
				// Make the select list for the view type
				$block[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_BAN_BANLEVEL_KUNENA'));
				$block[] = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA'));

				// Build the html select list
				echo HTMLHelper::_(
					'select.genericlist', $block, 'banlevel', '', 'value', 'text',
					$this->escape($this->banInfo->blocked), 'ban-level'
				);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ban-expiration"><?php echo Text::_('COM_KUNENA_BAN_EXPIRETIME'); ?></label>
				<small><?php echo Text::_('COM_KUNENA_BAN_STARTEXPIRETIME_DESC'); ?></small>
			</td>
			<td>
				<?php echo HTMLHelper::_(
					'calendar', $this->escape($this->banInfo->expiration), 'expiration',
					'ban-expiration', '%Y-%m-%d %H:%M:%S'
				); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ban-public"><?php echo Text::_('COM_KUNENA_BAN_PUBLICREASON'); ?></label>
				<small><?php echo Text::_('COM_KUNENA_BAN_PUBLICREASON_DESC'); ?></small>
			</td>
			<td>
						<textarea id="ban-public" class="required" name="reason_public" id="reason_public"
						><?php echo $this->escape($this->banInfo->reason_public) ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ban-private"><?php echo Text::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></label>
				<small><?php echo Text::_('COM_KUNENA_BAN_PRIVATEREASON_DESC'); ?></small>
			</td>
			<td>
						<textarea id="ban-private" class="required" name="reason_private" id="reason_private"
						><?php echo $this->escape($this->banInfo->reason_private) ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ban-comment"><?php echo Text::_('COM_KUNENA_BAN_ADDCOMMENT'); ?></label>
				<small><?php echo Text::_('COM_KUNENA_BAN_ADDCOMMENT_DESC'); ?></small>
			</td>
			<td>
				<textarea id="ban-comment" class="required" name="comment" id="comment"></textarea>
			</td>
		</tr>

		<?php if ($this->banInfo->exists()) : ?>
			<tr>
				<td>
					<label for="ban-remove"><?php echo Text::_('COM_KUNENA_MODERATE_REMOVE_BAN'); ?></label>
				</td>
				<td>
					<input id="ban-remove" type="checkbox" id="ban-delban" name="delban" value="delban" class=""/>
				</td>
			</tr>
		<?php endif; ?>

		<tr>
			<td>
				<label for="ban-delsignature">
					<?php echo Text::_('COM_KUNENA_MODERATE_DELETE_BAD_SIGNATURE'); ?>
				</label>
			</td>
			<td>
				<input type="checkbox" id="ban-delsignature" name="delsignature" value="delsignature" class=""/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ban-delavatar">
					<?php echo Text::_('COM_KUNENA_MODERATE_DELETE_BAD_AVATAR'); ?>
				</label>
			</td>
			<td>
				<input type="checkbox" id="ban-delavatar" name="delavatar" value="delavatar"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ban-delprofileinfo">
					<?php echo Text::_('COM_KUNENA_MODERATE_DELETE_BAD_PROFILEINFO'); ?>
				</label>
			</td>
			<td>
				<input type="checkbox" id="ban-delprofileinfo" name="delprofileinfo" value="delprofileinfo"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ban-delposts">
					<?php echo Text::_('COM_KUNENA_MODERATE_DELETE_ALL_POSTS'); ?>
				</label>
			</td>
			<td>
				<input type="checkbox" id="ban-delposts" name="bandelposts" value="bandelposts"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="ban-delposts">
					<?php echo Text::_('COM_KUNENA_MODERATE_DELETE_PERM_ALL_POSTS'); ?>
				</label>
			</td>
			<td>
				<input type="checkbox" id="ban-delposts" name="bandelpostsperm" value="bandelpostsperm"/>
			</td>
		</tr>
		<tr>
			<td class="center" colspan="2">
				<input class="btn btn-primary" type="submit" value="<?php echo $this->banInfo->id
					? Text::_('COM_KUNENA_BAN_EDIT')
					: Text::_('COM_KUNENA_BAN_NEW'); ?>" name="Submit"/>
			</td>
		</tr>
		</tbody>
	</table>
</form>
