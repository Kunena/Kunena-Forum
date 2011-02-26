<?php
/**
 * @version $Id: default.php 4416 2011-02-16 08:43:29Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-editcss"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_EDIT_CSS_TEMPLATE'); ?></div>
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
		<?php if($this->ftp): ?>
		<fieldset title="<?php echo JText::_('DESCFTPTITLE'); ?>">
			<legend><?php echo JText::_('DESCFTPTITLE'); ?></legend>
			<?php echo JText::_('DESCFTP'); ?>
			<?php if(JError::isError($this->ftp)): ?>
				<p><?php echo JText::_($this->ftp->message); ?></p>
			<?php endif; ?>
			<table class="adminform nospace">
			<tbody>
			<tr>
				<td width="120"><label for="username"><?php echo JText::_('Username'); ?>:</label></td>
				<td><input type="text" id="username" name="username" class="input_box" size="70" value="" /></td>
			</tr>
			<tr>
				<td width="120"><label for="password"><?php echo JText::_('Password'); ?>:</label></td>
				<td><input type="password" id="password" name="password" class="input_box" size="70" value="" /></td>
			</tr>
			</tbody>
			</table>
		</fieldset>
		<?php endif; ?>
		<table class="adminform">
		<tr>
			<th>
				<?php echo $this->escape($this->css_path); ?>
			</th>
		</tr>
		<tr>
			<td><textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $this->content; ?></textarea></td>
		</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $this->templatename; ?>" />
		<input type="hidden" name="cid[]" value="<?php echo $this->templatename; ?>" />
		<input type="hidden" name="filename" value="<?php echo $this->filename; ?>" />
		<input type="hidden" name="option" value="com_kunena" />
		<input type="hidden" name="view" value="templates" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
