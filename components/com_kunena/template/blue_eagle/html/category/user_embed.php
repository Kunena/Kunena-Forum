<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="kcategoryform">
	<input type="hidden" name="view" value="category" />
	<?php echo JHTML::_( 'form.token' ); ?>

<div class="kblock kflat">
	<div class="kheader">
		<?php if (!empty($this->topicActions)) : ?>
		<span class="kcheckbox select-toggle"><input class="kcheckall" type="checkbox" name="toggle" value="" /></span>
		<?php endif; ?>
		<h2><span><?php echo $this->escape($this->header); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
				<table class="kblocktable" id="kflattable">

					<?php if (!count ( $this->categories ) )  : ?>
					<tr class="krow2"><td class="kcol-first"><?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE') ?></td></tr>

					<?php else : ?>
						<?php foreach ($this->categories as $this->category) {
						$this->displayTemplateFile('category', 'user', 'row');
						} ?>


					<?php endif; ?>
				</table>
		</div>
	</div>
</div>
</form>
