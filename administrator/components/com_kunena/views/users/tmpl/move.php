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
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.rtl.css' );
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
						<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
							<input type="hidden" name="view" value="users" />
							<input type="hidden" name="task" value="" />
							<input type="hidden" name="boxchecked" value="1" />
							<?php echo JHTML::_( 'form.token' ); ?>

							<?php
								echo $this->catslist;
							?>
						</form>
						</td>
						<td><strong><?php echo JText::_('COM_KUNENA_MOVEUSERMESSAGES_USERS_CURRENT'); ?></strong>
						<ol>
						<?php
						foreach($this->users as $user) {
							echo '<li>'.$this->escape($user->username).' ('.JText::_('COM_KUNENA_TRASH_AUTHOR_USERID').' '.$this->escape($user->id).')</li> ';
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
