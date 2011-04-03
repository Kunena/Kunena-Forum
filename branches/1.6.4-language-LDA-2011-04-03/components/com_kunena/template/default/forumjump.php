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

$options = array ();
$options [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_FORUM_TOP') );
$lists ['parent'] = CKunenaTools::forumSelectList ( 'forumjump', isset($this->catid) ? intval($this->catid) : 0, $options, 'class="inputbox fbs" size="1" onchange = "this.form.submit()"', true );
?>
<form id="jumpto" name="jumpto" method="post" target="_self" action="<?php echo CKunenaLink::GetKunenaURL ();?>">
	<span class="kright">
		<input type="hidden" name="func" value="showcat" />
		<?php echo $lists ['parent']; ?>
		<input type="submit" name="Go" class="kbutton ks" value="<?php echo JText::_('COM_KUNENA_GO'); ?>" />
	</span>
</form>