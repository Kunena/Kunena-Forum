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
<div id="Kunena">
<?php
$this->displayMenu ();
$this->displayLoginBox ();
?>
<div class="kblock">
	<div class="kheader">
		<h2><span><?php if (!empty($this->header)) echo $this->escape($this->header); ?></span></h2>
	</div>

	<div class="kcontainer">
		<div class="kbody">
			<table class="kblocktable" id="kflattable">
			<?php if (!count ( $this->categories ) ) : ?>
			<tr class="krow2">
				<td class="kcol-first">
					<?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS_NONE') ?>
				</td>
			</tr>
			<?php
			else :
				foreach ($this->categories as $this->category) {
					echo $this->loadTemplate('row');
				}
			endif;
			?>
			</table>
		</div>
	</div>
</div>
<?php $this->displayFooter (); ?>
</div>