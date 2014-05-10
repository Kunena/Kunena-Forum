<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="ktopicsform">
	<input type="hidden" name="view" value="topics" />
	<?php echo JHtml::_( 'form.token' ); ?>
<div class="kblock kflat">
	<div class="kheader">
		<?php if (!empty($this->postActions)) : ?>
		<span class="kcheckbox select-toggle"><input class="kcheckall" type="checkbox" name="toggle" value="" /></span>
		<?php endif; ?>
		<h2><span><?php echo $this->escape($this->headerText); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
				<table class="kblocktable" id="kflattable">

					<?php if (empty ( $this->messages ) && empty ( $this->subcategories )) : ?>
					<tr class="krow2"><td class="kcol-first"><?php echo JText::_('COM_KUNENA_NO_POSTS') ?></td></tr>

					<?php else : ?>
						<?php $this->displayRows (); ?>

					<?php  if ( !empty($this->postActions) || !empty($this->embedded) ) : ?>
					<tr class="krow1">
						<td colspan="<?php echo empty($this->postActions) ? 5 : 6 ?>" class="kcol krowmoderation">
							<?php if (!empty($this->moreUri)) echo JHtml::_('kunenaforum.link', $this->moreUri, JText::_('COM_KUNENA_MORE'), null, null, 'follow'); ?>
							<?php if (!empty($this->postActions)) : ?>
							<?php echo JHtml::_('select.genericlist', $this->postActions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
							<input type="submit" name="kcheckgo" class="kbutton" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
							<?php endif; ?>
						</td>
					</tr>
					<?php endif; ?>
					<?php endif; ?>
				</table>
		</div>
	</div>
</div>
</form>
