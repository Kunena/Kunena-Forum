<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Trash
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
	<div class="kadmin-functitle icon-trash">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="trash" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHTML::_( 'form.token' ); ?>

			<?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?>
			<a class="icon-32-delete" style="border: 1px dotted gray; width: 70px; padding: 7px; margin-left: 400px; background-repeat: no-repeat; padding-left: 40px;"  href="javascript:void submitbutton('topics')">
			&nbsp;<?php echo JText::_( 'COM_KUNENA_TRASH_TOPICS' ); ?></a>
			<a class="icon-32-delete" style="border: 1px dotted gray; width: 70px; padding: 7px; margin-left: 50px; background-repeat: no-repeat; padding-left: 40px; "  href="javascript:void submitbutton('messages')">				&nbsp;<?php echo JText::_( 'COM_KUNENA_TRASH_MESSAGES' ); ?></a>
		</form>
	</div>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
