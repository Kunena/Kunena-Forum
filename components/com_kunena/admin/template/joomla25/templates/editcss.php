<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Templates
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTemplates $this */

$document = JFactory::getDocument();
$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
		</div>
	</div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-editcss"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_EDIT_CSS_TEMPLATE'); ?></div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="view" value="templates" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->escape($this->templatename); ?>" />
		<input type="hidden" name="cid[]" value="<?php echo $this->escape($this->templatename); ?>" />
		<input type="hidden" name="filename" value="<?php echo $this->escape($this->filename); ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>

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
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
