<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="mb_logout" style="display:none;">
	<div class="tk-mb-header-logout">
		<span class="tk-mb-first"><?php echo JText::_('COM_KUNENA_TEMPLATE_MEMBER_LOGOUT'); ?></span>
	</div>
	<?php // TODO : Move style to css file ?>
	<div class="" style="text-align:center;margin-top:15px;">
		<span class="tk-mb-first" style="color:#666;"><?php echo JText::_('COM_KUNENA_TEMPLATE_ARE_YOU_SURE'); ?></span>
	</div>
	<div class="" style="text-align:center;margin-top:15px;">
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" class="kform klogout">
			<input type="hidden" name="view" value="user" />
			<input type="hidden" name="task" value="logout" />
			[K=TOKEN]

			<button type="submit" value="<?php echo JText::_('COM_KUNENA_TEMPLATE_SURE'); ?>" class="tk-logout-button"><?php echo JText::_('COM_KUNENA_TEMPLATE_SURE') ?></button>
		</form>
	</div>
</div>
