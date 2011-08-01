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
		<form action="#" method="post" class="kform klogout">
			<button type="submit" value="<?php echo JText::_('COM_KUNENA_TEMPLATE_SURE'); ?>" class="tk-logout-button"><?php echo JText::_('COM_KUNENA_TEMPLATE_SURE') ?></button>
			<input type="hidden" name="option" value="<?php echo $this->logout['option'] ?>" />
			<?php if (!empty($this->logout['view'])) : ?>
			<input type="hidden" name="view" value="<?php echo $this->logout['view'] ?>" />
			<?php endif ?>
			<input type="hidden" name="task" value="<?php echo $this->logout['task'] ?>" />
			<input type="hidden" name="<?php echo $this->logout['field_return'] ?>" value="[K=RETURN_URL]" />
			[K=TOKEN]
		</form>
	</div>
</div>
