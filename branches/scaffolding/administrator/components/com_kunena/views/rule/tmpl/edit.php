<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

// Include the JXtended HTML helpers.
JHTML::addIncludePath(JPATH_ROOT.'/plugins/system/jxtended/html/html');

// Load the tooltip and form vaildation behaviors.
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');

// Load the default stylesheet.
JHTML::stylesheet('default.css', 'administrator/components/com_kunena/media/css/');

// Build the toolbar.
$this->buildDefaultToolBar();
?>
<script language="javascript" type="text/javascript">
<!--
	function submitbutton(task)
	{
		var form = document.adminForm;
		if (task == 'rule.cancel' || document.formvalidator.isValid(document.adminForm)) {
			submitform(task);
		}
	}
-->
</script>
<form action="<?php echo JRoute::_('index.php?option=com_kunena&view=rule');?>" method="post" name="adminForm">
	<fieldset>
		<?php if ($this->item->id) : ?>
		<legend><?php echo JText::sprintf('Rule #%d', $this->item->id); ?></legend>
		<?php endif; ?>


<?php
	// Now split out on the Rule Type
	$ruleType = $this->item->acl_type;
	if ($ruleType < 1 || $ruleType > 3) {
		$ruleType = 1;
	}

	echo $this->loadTemplate('type'.$ruleType);
?>
		<div class="clr"></div>
	</fieldset>

	<input type="hidden" name="jxform[id]" value="<?php echo (int) $this->item->id; ?>" />
	<input type="hidden" name="jxform[acl_type]" value="<?php echo (int) $this->item->acl_type; ?>" />
	<input type="hidden" name="jxform[name]" value="<?php echo $this->escape($this->item->name); ?>" />
	<input type="hidden" name="jxform[note]" value="<?php echo $this->escape($this->item->note); ?>" />
	<input type="hidden" name="jxform[allow]" value="<?php echo (int) $this->item->allow; ?>"  />
	<input type="hidden" name="jxform[return_value]" value="<?php echo $this->escape($this->item->return_value); ?>" />
	<input type="hidden" name="jxform[enabled]" value="<?php echo (int) $this->item->enabled; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
