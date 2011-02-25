<?php
/**
 * @version $Id: default.php 4416 2011-02-16 08:43:29Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.css' )
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-profiles"><?php echo JText::_('COM_KUNENA_A_MOVE_USERMESSAGES'); ?></div>
		<table class="adminform">
				<tbody>
					<tr>
						<td>
						<strong><?php echo JText::_('COM_KUNENA_CATEGORY_TARGET'); ?></strong>
						<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
						<?php
							echo $this->catslist;
						?>
						<input type="hidden" name="option" value="com_kunena" />
						<input type="hidden" name="view" value="users" />
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="1" />
						<input type="hidden" name="uid[]" value="<?php echo $this->userid; ?>" />
						<?php echo JHTML::_( 'form.token' ); ?>
						</form>
						</td>
						<td><strong><?php echo JText::_('COM_KUNENA_MOVEUSERMESSAGES_USERS_CURRENT'); ?></strong>
						<ol>
						<?php
						foreach($this->user as $id){
							echo '<li>'.$this->escape($id->username).' ('.JText::_('COM_KUNENA_TRASH_AUTHOR_USERID').' '.$this->escape($id->id).')</li> ';
						}

						?>
						</ol>
						</td>
					</tr>
				</tbody>
			</table>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
