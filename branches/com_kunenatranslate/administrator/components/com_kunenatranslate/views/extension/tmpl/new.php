<?php
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010-2011 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::save();
JToolBarHelper::cancel();
?>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_KUNENATRANSLATE_UPLOAD_INSTALL_XML' ); ?></legend>
		<input class="input_box" id="install_xml" name="install_xml" type="file" size="57" />
		<input class="button" type="button" value="<?php echo JText::_('COM_KUNENATRANSLATE_UPLOAD_INSTALL_XML'); ?>" onclick="submitbutton()" />
	</fieldset>

<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="controller" value="extension" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="task" value="doinstall" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
