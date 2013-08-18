<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php if (!empty($this->moderators)) : ?>
<div class="row-fluid">
	<?php
	echo JText::_('COM_KUNENA_MODERATORS') . ": ";
	foreach ($this->moderators as $moderator) echo "{$moderator->getLink()} &nbsp;";
	?>
</div>
<?php endif; ?>
