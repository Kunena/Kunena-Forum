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

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-trash"><?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?></div>
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm" class="adminform">
			<a class="icon-32-delete" style="border: 1px dotted gray; width: 70px; padding: 10px; margin-left: 50px; background-repeat: no-repeat; padding-left: 40px; "  href="javascript:void submitbutton('topics')">
				&nbsp;<?php echo JText::_( 'COM_KUNENA_TRASH_TOPICS' ); ?></a>
			<a class="icon-32-delete" style="border: 1px dotted gray; width: 70px; padding: 10px; margin-left: 50px; background-repeat: no-repeat; padding-left: 40px; "  href="javascript:void submitbutton('messages')">
				&nbsp;<?php echo JText::_( 'COM_KUNENA_TRASH_MESSAGES' ); ?></a>
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="view" value="trash" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
